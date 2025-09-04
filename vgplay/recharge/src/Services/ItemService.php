<?php

namespace Vgplay\Recharge\Services;

use Vgplay\Games\Models\Game;
use Vgplay\Recharge\Models\Item;
use Illuminate\Support\Facades\Cache;

class ItemService
{
    protected function cacheKey(string $suffix): string
    {
        return "items:{$suffix}";
    }

    /**
     * Danh sách items theo game (đã active ở pivot + item).
     */
    public function listByGame(int $gameId, bool $onlyActive = true): array
    {
        $key = $this->cacheKey("game:{$gameId}:list" . ($onlyActive ? ':active' : ':all'));

        return Cache::remember($key, 3600, function () use ($gameId, $onlyActive) {
            $q = Item::query()
                ->whereHas('games', function ($q) use ($gameId) {
                    $q->where('games.game_id', $gameId)
                        ->where('game_item.is_active', true);
                })
                ->orderBy('type')
                ->orderBy('sort');

            if ($onlyActive) {
                $q->where('items.is_active', true);
            }

            return $q->get([
                'items.id',
                'items.type',
                'items.name',
                'items.code',
                'items.image',
                'items.unit',
                'items.description',
                'items.vxu_amount',
                'items.discount_percent',
                'items.limit_per_user',
                'items.allow_multiple_per_order',
                'items.tier',
                'items.requires_min_tier',
            ])
                ->map(fn($it) => [
                    'id' => (int)$it->id,
                    'type' => $it->type,
                    'name' => $it->name,
                    'code' => $it->code,
                    'image' => $it->image,
                    'unit' => $it->unit,
                    'description' => $it->description,
                    'vxu_amount' => (int)$it->vxu_amount,
                    'discount_percent' => (float)$it->discount_percent,
                    'limit_per_user' => (int)$it->limit_per_user,
                    'allow_multiple_per_order' => (bool)$it->allow_multiple_per_order,
                    'tier' => (int)$it->tier,
                    'requires_min_tier' => (int)$it->requires_min_tier,
                ])
                ->all();
        });
    }

    /**
     * Chi tiết 1 item theo game (đảm bảo item thuộc game).
     */
    public function getOneWithDetails(int $gameId, int $itemId): ?array
    {
        $key = $this->cacheKey("game:{$gameId}:item:{$itemId}:details");

        return Cache::remember($key, 3600, function () use ($gameId, $itemId) {
            $item = Item::query()
                ->whereHas(
                    'games',
                    fn($q) => $q
                        ->where('games.game_id', $gameId)
                        ->where('game_item.is_active', true)
                )
                ->where('items.is_active', true)
                ->with(['details' => fn($q) => $q->orderBy('sort')])
                ->find($itemId);

            if (!$item) return null;

            return [
                'id' => (int)$item->id,
                'type' => $item->type,
                'name' => $item->name,
                'code' => $item->code,
                'image' => $item->image,
                'unit' => $item->unit,
                'description' => $item->description,
                'vxu_amount' => (int)$item->vxu_amount,
                'discount_percent' => (float)$item->discount_percent,
                'limit_per_user' => (int)$item->limit_per_user,
                'allow_multiple_per_order' => (bool)$item->allow_multiple_per_order,
                'tier' => (int)$item->tier,
                'requires_min_tier' => (int)$item->requires_min_tier,
                'details' => $item->details->map(fn($d) => [
                    'name' => $d->name,
                    'image' => $d->image,
                    'description' => $d->description,
                    'quantity' => (int)$d->quantity,
                ])->all(),
            ];
        });
    }

    /**
     * Bust cache khi thay đổi item/pivot.
     */
    public function forgetByGame(int $gameId, ?int $itemId = null): void
    {
        Cache::forget($this->cacheKey("game:{$gameId}:list:active"));
        Cache::forget($this->cacheKey("game:{$gameId}:list:all"));
        if ($itemId) {
            Cache::forget($this->cacheKey("game:{$gameId}:item:{$itemId}:details"));
        }
    }

    public function syncAll(): void
    {
        // Xoá + warm caches danh sách items theo từng game
        $games = Game::query()->pluck('game_id');
        foreach ($games as $gid) {
            $this->forgetByGame((int)$gid);
            // warm
            $this->listByGame((int)$gid, true);
            $this->listByGame((int)$gid, false);
        }
    }
}
