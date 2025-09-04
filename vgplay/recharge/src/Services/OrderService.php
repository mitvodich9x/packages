<?php

namespace Vgplay\Recharge\Services;

use Vgplay\Recharge\Models\GamePaymentMethod;
use Vgplay\Recharge\Models\Item;
use Vgplay\Recharge\Models\PurchaseHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        protected PaymentService  $paymentService,
        protected PurchaseService $purchaseService,
    ) {}

    /**
     * Tạo order pending (kèm mọi kiểm tra). Không cache ở luồng ghi.
     */
    public function createPendingOrder(int $gameId, int $itemId, int $vgpId, int $paymentMethodId, int $quantity = 1): array
    {
        $item = Item::query()
            ->whereHas('games', fn($q) => $q
                ->where('games.game_id', $gameId)
                ->where('game_item.is_active', true)
            )
            ->where('is_active', true)
            ->findOrFail($itemId);

        // check multiple per order
        $qty = max(1, (int)$quantity);
        if (!$item->allow_multiple_per_order && $qty > 1) {
            $qty = 1; // hoặc throw lỗi
        }

        // method config
        $cfg = GamePaymentMethod::query()
            ->where('game_id', $gameId)
            ->where('payment_method_id', $paymentMethodId)
            ->where('status', true)
            ->first();

        if (!$cfg || !$cfg->method || !$cfg->method->is_active) {
            throw ValidationException::withMessages(['payment_method_id' => 'Phương thức không khả dụng cho game này.']);
        }

        $priceVnd = (int) round(((int)$item->vxu_amount) * (float)$cfg->exchange_rate);
        if ($cfg->max_amount > 0 && ($priceVnd < (int)$cfg->min_amount || $priceVnd > (int)$cfg->max_amount)) {
            throw ValidationException::withMessages(['payment_method_id' => 'Giá trị gói không nằm trong giới hạn của phương thức.']);
        }

        // Kiểm tra hạn mức & tier trong transaction để tránh race-condition
        return DB::transaction(function () use ($gameId, $item, $vgpId, $cfg, $priceVnd, $qty) {

            // limit per user (đọc DB trực tiếp)
            if ((int)$item->limit_per_user > 0) {
                $paidCount = PurchaseHistory::query()
                    ->where('vgp_id', $vgpId)
                    ->where('game_id', $gameId)
                    ->where('item_id', $item->id)
                    ->where('status', 'paid')
                    ->lockForUpdate()
                    ->count();

                if ($paidCount >= (int)$item->limit_per_user) {
                    throw ValidationException::withMessages(['item' => 'Bạn đã mua tối đa gói này.']);
                }
            }

            // tier (bỏ cho vxu)
            if ($item->type !== 'vxu' && (int)$item->requires_min_tier > 0) {
                $maxTier = Item::query()
                    ->whereIn('id', function ($q) use ($vgpId, $gameId) {
                        $q->select('item_id')
                          ->from('purchase_histories')
                          ->where('vgp_id', $vgpId)
                          ->where('game_id', $gameId)
                          ->where('status', 'paid');
                    })
                    ->where('type', $item->type)
                    ->max('tier') ?? 0;

                if ((int)$maxTier < (int)$item->requires_min_tier) {
                    throw ValidationException::withMessages(['item' => 'Vui lòng mua các gói nhỏ trước để mở khóa gói này.']);
                }
            }

            $order = PurchaseHistory::create([
                'vgp_id'            => $vgpId,
                'game_id'           => $gameId,
                'item_id'           => $item->id,
                'payment_method_id' => (int)$cfg->payment_method_id,
                'quantity'          => $qty,
                'vxu_amount'       => (int)$item->vxu_amount,
                'price_vnd'         => $priceVnd * $qty,
                'status'            => 'pending',
            ]);

            return [
                'order_id'       => $order->id,
                'pay_amount_vnd' => $order->price_vnd,
            ];
        });
    }
}
