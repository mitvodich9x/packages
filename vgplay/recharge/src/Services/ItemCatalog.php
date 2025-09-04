<?php

namespace Vgplay\Recharge\Services;

use Vgplay\Recharge\Models\Item;
use Vgplay\Recharge\Models\Purchase;


class ItemCatalog
{
    /**
     * Danh sách gói hiển thị cho user theo game+unit
     * - unit=vxu: mua không giới hạn ⇒ không ẩn theo one-time
     * - unit≠vxu & is_one_time=true: ẩn gói đã mua
     * - check_purchase=true: chỉ hiển thị tier <= (max tier đã mua + 1)
     */
    public static function visibleItemsForUser(?int $userId, int $gameId, string $unit)
    {
        // Lấy tất cả items theo game + unit (đã gắn per-game)
        $items = Item::query()
            ->forGameUnit($gameId, $unit)
            ->orderBy('tier')
            ->orderBy('price')
            ->get();

        // Nếu không có user => trả về full list
        if (is_null($userId)) {
            return $items;
        }

        // IDs item user đã mua thành công
        $purchasedIds = Purchase::query()
            ->where('user_id', $userId)
            ->where('game_id', $gameId)
            ->where('status', 'success')
            ->pluck('item_id')
            ->all();

        // Quy tắc "mua 3 gói rẻ nhất trước" (chỉ áp dụng với unit != vxu và khi có >= 3 gói)
        if ($unit !== 'vxu' && $items->count() >= 3) {
            // Lấy 3 gói rẻ nhất theo price (nếu trùng price thì theo tier/id để ổn định)
            $bottom3 = $items->sortBy(fn($i) => [(int) $i->price, (int) $i->tier, (int) $i->id])->take(3);
            $bottom3Ids = $bottom3->pluck('id')->all();

            $hasAll3 = count(array_diff($bottom3Ids, $purchasedIds)) === 0;

            if (!$hasAll3) {
                // Ẩn gói có price > price của gói rẻ thứ 3
                $threshold = (int) $bottom3->max('price');
                $items = $items->filter(fn($i) => (int) $i->price <= $threshold)->values();
            }
        }

        // Ẩn gói one-time đã mua (áp dụng cho unit ≠ vxu)
        if ($unit !== 'vxu' && !empty($purchasedIds) && $items->isNotEmpty()) {
            $items = $items->filter(function ($item) use ($purchasedIds) {
                return !($item->is_one_time && in_array($item->id, $purchasedIds, true));
            })->values();
        }

        return $items;
    }

    public static function unitsForGame(int $gameId, bool $withStats = false, bool $onlyActive = true)
    {
        $base = Item::query()->where('game_id', $gameId);

        if ($onlyActive) {
            $base->where('is_active', true);
        }

        if (!$withStats) {
            return $base->distinct()
                ->orderBy('unit')
                ->pluck('unit');
        }

        // MySQL aggregate thống kê theo unit
        return $base
            ->selectRaw("
                unit,
                COUNT(*)                                         as total,
                SUM(CASE WHEN is_one_time = 1 THEN 1 ELSE 0 END) as one_time_count,
                SUM(CASE WHEN check_purchase = 1 THEN 1 ELSE 0 END) as progressive_count,
                MIN(price)                                       as min_price,
                MAX(price)                                       as max_price
            ")
            ->groupBy('unit')
            ->orderBy('unit')
            ->get()
            ->map(function ($row) {
                return [
                    'unit'              => (string) $row->unit,
                    'total'             => (int) $row->total,
                    'one_time_count'    => (int) $row->one_time_count,
                    'progressive_count' => (int) $row->progressive_count,
                    'min_price'         => (int) $row->min_price,
                    'max_price'         => (int) $row->max_price,
                ];
            });
    }
}
