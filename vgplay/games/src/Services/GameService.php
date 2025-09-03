<?php

namespace Vgplay\Games\Services;

use Vgplay\Games\Models\Game;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GameService
{
    protected function cacheKey(string $suffix): string
    {
        return "games:{$suffix}";
    }

    public function getAll(bool $onlyActive = true): array
    {
        $cacheKey = $onlyActive
            ? $this->cacheKey('list:active:full')
            : $this->cacheKey('list:all:full');

        try {
            return Cache::remember($cacheKey, 3600, function () use ($onlyActive) {
                $query = Game::query()
                    ->with(['admins', 'apis', 'socials', 'flags', 'settings']);

                if ($onlyActive) {
                    $query->where('status', true);
                }

                return $query
                    ->reorder()              
                    ->orderByDesc('id') 
                    ->get()
                    ->map(fn($game) => $game->toArray())
                    ->all();
            });
        } catch (\Throwable $e) {
            Log::error('GameService getAll failed', [
                'onlyActive' => $onlyActive,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    public function search(string $keyword): array
    {
        $cacheKey = $this->cacheKey("search:" . md5($keyword));

        try {
            return Cache::remember($cacheKey, 3600, function () use ($keyword) {
                return Game::query()
                    ->where('name', 'LIKE', "%{$keyword}%")
                    ->orWhere('alias', 'LIKE', "%{$keyword}%")
                    ->select(['id', 'game_id', 'name', 'alias', 'icon'])
                    ->get()
                    ->toArray();
            });
        } catch (\Throwable $e) {
            Log::error('GameService search failed', [
                'keyword' => $keyword,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    public function findById(int $id): ?array
    {
        $cacheKey = $this->cacheKey("game_id:{$id}");

        try {
            return Cache::remember($cacheKey, 3600, function () use ($id) {
                return Game::query()
                    ->where('game_id', $id)
                    ->select(['id', 'game_id', 'name', 'alias', 'icon', 'status'])
                    ->first()
                    ?->toArray();
            });
        } catch (\Throwable $e) {
            Log::error('GameService findById failed', [
                'id' => $id,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function findByAlias(string $alias): ?array
    {
        $cacheKey = $this->cacheKey("alias:{$alias}");

        try {
            return Cache::remember($cacheKey, 3600, function () use ($alias) {
                return Game::query()
                    ->where('alias', $alias)
                    ->select(['id', 'game_id', 'name', 'alias', 'icon', 'status'])
                    ->first()
                    ?->toArray();
            });
        } catch (\Throwable $e) {
            Log::error('GameService findByAlias failed', [
                'alias' => $alias,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    public function forgetAll(): void
    {
        Cache::forget($this->cacheKey('list:active:full'));
        Cache::forget($this->cacheKey('list:all:full'));
    }

    public function syncAll(): void
    {
        $this->forgetAll();

        try {
            $all = Game::with(['admins', 'apis', 'socials', 'flags', 'settings'])->get();

            Cache::put($this->cacheKey('list:all:full'), $all->toArray(), 3600);
            Cache::put($this->cacheKey('list:active:full'), $all->where('status', true)->toArray(), 3600);

            foreach ($all as $game) {
                Cache::put($this->cacheKey("game_id:{$game->game_id}"), $game->toArray(), 3600);
                Cache::put($this->cacheKey("alias:{$game->alias}"), $game->toArray(), 3600);
            }
        } catch (\Throwable $e) {
            Log::error('GameService syncAll failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
