<?php

namespace Vgplay\Recharge\Services;

use Vgplay\Recharge\Models\Item;
use Vgplay\Recharge\Models\PurchaseHistory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class PurchaseService
{
    protected function cacheKey(string $suffix): string
    {
        return "purchases:{$suffix}";
    }

    /**
     * Đếm số lần đã mua (PAID) theo item cho 1 user trong 1 game.
     * Cache ngắn để giảm tải, nhưng vẫn an toàn khi Create Order: sẽ re-check DB.
     */
    public function getPaidCountsByItem(int $vgpId, int $gameId): array
    {
        $key = $this->cacheKey("counts:vgp:{$vgpId}:game:{$gameId}");

        return Cache::remember($key, 60, function () use ($vgpId, $gameId) {
            return PurchaseHistory::query()
                ->selectRaw('item_id, COUNT(*) as cnt')
                ->where('vgp_id', $vgpId)
                ->where('game_id', $gameId)
                ->where('status', 'paid')
                ->groupBy('item_id')
                ->pluck('cnt', 'item_id')
                ->map(fn($v) => (int)$v)
                ->toArray();
        });
    }

    /**
     * Max tier theo type cho user/game (để khoá/mở gói).
     */
    public function getMaxTierByType(int $vgpId, int $gameId): array
    {
        $key = $this->cacheKey("max_tier_by_type:vgp:{$vgpId}:game:{$gameId}");

        return Cache::remember($key, 60, function () use ($vgpId, $gameId) {
            $paidItemIds = PurchaseHistory::query()
                ->where('vgp_id', $vgpId)
                ->where('game_id', $gameId)
                ->where('status', 'paid')
                ->pluck('item_id');

            if ($paidItemIds->isEmpty()) return [];

            return Item::query()
                ->whereIn('id', $paidItemIds)
                ->get(['type', 'tier'])
                ->groupBy('type')
                ->map(fn(Collection $c) => (int)$c->max('tier'))
                ->toArray();
        });
    }

    /**
     * Lịch sử gần nhất (hiển thị UI).
     */
    public function getRecentPaid(int $vgpId, int $gameId, int $limit = 100): array
    {
        $key = $this->cacheKey("recent_paid:vgp:{$vgpId}:game:{$gameId}:limit:{$limit}");

        return Cache::remember($key, 60, function () use ($vgpId, $gameId, $limit) {
            return PurchaseHistory::query()
                ->where('vgp_id', $vgpId)
                ->where('game_id', $gameId)
                ->where('status', 'paid')
                ->orderByDesc('id')
                ->limit($limit)
                ->get([
                    'id',
                    'item_id',
                    'payment_method_id',
                    'quantity',
                    'vxu_amount',
                    'price_vnd',
                    'status',
                    'created_at'
                ])->toArray();
        });
    }

    /**
     * Bust caches liên quan user/game (gọi sau khi có giao dịch PAID).
     */
    public function forgetUserGame(int $vgpId, int $gameId): void
    {
        Cache::forget($this->cacheKey("counts:vgp:{$vgpId}:game:{$gameId}"));
        Cache::forget($this->cacheKey("max_tier_by_type:vgp:{$vgpId}:game:{$gameId}"));
        // recent_paid có tham số limit => khó xoá hết: tuỳ bạn dùng Cache::tags để gom nhóm.
    }

    public function syncAll(): void
    {
        // No-op: cache phụ thuộc user+game, không warm toàn cục.
        // Dùng forgetUserGame($vgpId, $gameId) ở các điểm phát sinh giao dịch.
    }
}
