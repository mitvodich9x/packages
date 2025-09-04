<?php

namespace Vgplay\Recharge\Services;

use Vgplay\Games\Models\Game;
use Vgplay\Recharge\Models\Item;
use Illuminate\Support\Facades\Cache;
use Vgplay\Recharge\Models\GamePaymentMethod;

class PaymentService
{
    protected function cacheKey(string $suffix): string
    {
        return "payments:{$suffix}";
    }

    /**
     * Cache cấu hình payment + method theo game.
     */
    public function getActiveConfigsByGame(int $gameId): array
    {
        $key = $this->cacheKey("game:{$gameId}:configs:active");

        return Cache::remember($key, 3600, function () use ($gameId) {
            return GamePaymentMethod::query()
                ->with('method')
                ->where('game_id', $gameId)
                ->where('status', true)
                ->get()
                ->filter(fn($cfg) => $cfg->method && $cfg->method->is_active)
                ->map(fn($cfg) => [
                    'payment_method_id' => (int)$cfg->payment_method_id,
                    'alias'             => $cfg->method->alias,
                    'name'              => $cfg->method->name,
                    'image'             => $cfg->method->image,
                    'description'       => $cfg->method->description,
                    'promotion_rate'    => (float)$cfg->method->promotion_rate,
                    'exchange_rate'     => (float)$cfg->exchange_rate,
                    'min_amount'        => (int)$cfg->min_amount,
                    'max_amount'        => (int)$cfg->max_amount,
                ])
                ->values()
                ->all();
        });
    }

    /**
     * Tính danh sách phương thức hợp lệ cho 1 item (có thể cache ngắn).
     */
    public function getValidMethodsForItem(int $gameId, int $itemId): array
    {
        $item = Item::query()
            ->select(['id', 'vxu_amount', 'unit', 'type', 'name'])
            ->whereHas(
                'games',
                fn($q) => $q
                    ->where('games.game_id', $gameId)
                    ->where('game_item.is_active', true)
            )
            ->where('is_active', true)
            ->findOrFail($itemId);

        $priceUnits = (int) $item->vxu_amount;

        $key = $this->cacheKey("game:{$gameId}:item:{$itemId}:valid_methods:{$priceUnits}");

        return Cache::remember($key, 600, function () use ($gameId, $priceUnits) {
            $configs = $this->getActiveConfigsByGame($gameId);

            $out = [];
            foreach ($configs as $cfg) {
                $vnd = (int) round($priceUnits * $cfg['exchange_rate']);
                if ($cfg['max_amount'] > 0) {
                    if ($vnd < $cfg['min_amount'] || $vnd > $cfg['max_amount']) {
                        continue;
                    }
                }
                $out[] = [
                    'id'             => $cfg['payment_method_id'],
                    'alias'          => $cfg['alias'],
                    'name'           => $cfg['name'],
                    'image'          => $cfg['image'],
                    'description'    => $cfg['description'],
                    'promotion_rate' => $cfg['promotion_rate'],
                    'vnd'            => $vnd,
                ];
            }

            usort($out, fn($a, $b) => $a['id'] <=> $b['id']);
            return $out;
        });
    }

    public function forgetByGame(int $gameId): void
    {
        Cache::forget($this->cacheKey("game:{$gameId}:configs:active"));
        // Xoá các keys per item nếu cần: tuỳ cách đặt key pattern mà bạn có thể sweep bằng Cache::tags (nếu dùng Redis + tags).
    }

    public function syncAll(): void
    {
        $games = Game::query()->pluck('game_id');
        foreach ($games as $gid) {
            $this->forgetByGame((int)$gid);
            // warm
            $this->getActiveConfigsByGame((int)$gid);
        }
    }
}
