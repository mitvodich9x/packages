<?php

namespace Vgplay\Recharge\Http\Controllers;

use App\Http\Controllers\Controller;
use Vgplay\Games\Traits\FindGame;
use Vgplay\Recharge\Models\GamePaymentMethod;
use Vgplay\Recharge\Models\Item;
use Vgplay\Recharge\Models\PurchaseHistory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    use FindGame;

    /**
     * Tạo order pending (JSON).
     * Không tìm thấy game/item -> JSON 404.
     */
    public function store(Request $request, string $game, int $item)
    {
        $data = $request->validate([
            'vgp_id'            => ['required', 'integer', 'min:1'],
            'payment_method_id' => ['required', 'integer', 'min:1'],
            'quantity'          => ['nullable', 'integer', 'min:1'],
        ]);

        try {
            $game = $this->findGame($game);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Không tìm thấy nội dung.'], 404);
        }

        $record = Item::query()
            ->whereHas(
                'games',
                fn($q) => $q
                    ->where('games.game_id', $game->game_id)
                    ->where('game_item.is_active', true)
            )
            ->where('is_active', true)
            ->find($item);

        if (! $record) {
            return response()->json(['message' => 'Không tìm thấy nội dung.'], 404);
        }

        $qty = max(1, (int) ($data['quantity'] ?? 1));
        if (! $record->allow_multiple_per_order && $qty > 1) {
            $qty = 1;
        }

        $cfg = GamePaymentMethod::query()
            ->where('game_id', $game->game_id)
            ->where('payment_method_id', $data['payment_method_id'])
            ->where('status', true)
            ->first();

        if (! $cfg || ! $cfg->method || ! $cfg->method->is_active) {
            throw ValidationException::withMessages([
                'payment_method_id' => 'Phương thức không khả dụng cho game này.',
            ]);
        }

        $priceVnd = (int) round(((int)$record->price_units) * (float) $cfg->exchange_rate);
        if ($cfg->max_amount > 0 && ($priceVnd < (int)$cfg->min_amount || $priceVnd > (int)$cfg->max_amount)) {
            throw ValidationException::withMessages([
                'payment_method_id' => 'Giá trị gói không nằm trong giới hạn của phương thức.',
            ]);
        }

        $result = DB::transaction(function () use ($data, $game, $record, $cfg, $priceVnd, $qty) {

            if ((int) $record->limit_per_user > 0) {
                $paidCount = PurchaseHistory::query()
                    ->where('vgp_id', $data['vgp_id'])
                    ->where('game_id', $game->game_id)
                    ->where('item_id', $record->id)
                    ->where('status', 'paid')
                    ->lockForUpdate()
                    ->count();

                if ($paidCount >= (int) $record->limit_per_user) {
                    throw ValidationException::withMessages([
                        'item' => 'Bạn đã mua tối đa gói này.',
                    ]);
                }
            }

            if ($record->type !== 'vxu' && (int)$record->requires_min_tier > 0) {
                $maxTier = Item::query()
                    ->whereIn('id', function ($q) use ($data, $game) {
                        $q->select('item_id')
                            ->from('purchase_histories')
                            ->where('vgp_id', $data['vgp_id'])
                            ->where('game_id', $game->game_id)
                            ->where('status', 'paid');
                    })
                    ->where('type', $record->type)
                    ->max('tier') ?? 0;

                if ((int)$maxTier < (int)$record->requires_min_tier) {
                    throw ValidationException::withMessages([
                        'item' => 'Vui lòng mua các gói nhỏ trước để mở khóa gói này.',
                    ]);
                }
            }

            $order = PurchaseHistory::create([
                'vgp_id'            => (int) $data['vgp_id'],
                'game_id'           => $game->game_id,
                'item_id'           => $record->id,
                'payment_method_id' => (int) $cfg->payment_method_id,
                'quantity'          => $qty,
                'price_units'       => (int) $record->price_units,
                'price_vnd'         => $priceVnd * $qty,
                'status'            => 'pending',
            ]);

            return [
                'order_id'       => $order->id,
                'pay_amount_vnd' => $order->price_vnd,
            ];
        });

        return response()->json([
            'message'        => 'Tạo đơn hàng thành công (pending).',
            'order_id'       => $result['order_id'],
            'pay_amount_vnd' => $result['pay_amount_vnd'],
        ], 201);
    }
}
