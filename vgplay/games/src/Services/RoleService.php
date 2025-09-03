<?php

namespace Vgplay\Games\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RoleService
{
    protected function cacheKey(string $suffix): string
    {
        return "roles:{$suffix}";
    }

    /**
     * Trả về danh sách server và nhân vật theo cấu hình API
     */
    public function getRolesData(array $game, string $vgpId): array
    {
        $cacheKey = $this->cacheKey("game:{$game['game_id']}:{$vgpId}");

        return Cache::remember($cacheKey, 600, function () use ($game, $vgpId) {
            try {
                $configs = $game['apis']['api_config'] ?? [];

                $hasRolesApi      = collect($configs)->contains(fn($c) => $c['type'] === 'roles');
                $hasServersApi    = collect($configs)->contains(fn($c) => $c['type'] === 'servers');
                $hasCharactersApi = collect($configs)->contains(fn($c) => $c['type'] === 'characters');

                if ($hasRolesApi) {
                    $rolesConfig = collect($configs)->first(fn($c) => $c['type'] === 'roles');
                    $data = $this->callApi($rolesConfig, ['vgp_id' => $vgpId]);

                    $roles = collect($data['roles'] ?? []);

                    $grouped = $roles->groupBy('server_id')->map(function ($group, $serverId) {
                        return [
                            'server_id'   => (string) $serverId,
                            'server_name' => urldecode($group->first()['server_name'] ?? ''),
                            'characters'  => $group->map(function ($char) {
                                return [
                                    'id'   => (string) ($char['id'] ?? ''),
                                    'name' => urldecode($char['name'] ?? ''),
                                    'lv'   => (string) ($char['lv'] ?? ''),
                                ];
                            })->sortBy('name')->values(),
                        ];
                    })->values();

                    return [
                        'type'    => 'roles',
                        'servers' => $grouped,
                    ];
                }

                if ($hasServersApi && $hasCharactersApi) {
                    $serverConfig = collect($configs)->first(fn($c) => $c['type'] === 'servers');
                    $response = $this->callApi($serverConfig, []);

                    $servers = $response['servers_list'] ?? [];

                    return [
                        'type'    => 'servers_characters',
                        'servers' => collect($servers)->map(function ($server) {
                            return [
                                'server_id'   => (string) ($server['id'] ?? ''),
                                'server_name' => urldecode($server['name'] ?? ''),
                            ];
                        })->values(),
                    ];
                }

                Log::warning("RoleService: Missing character configuration for game {$game['game_id']}");
                return ['type' => 'none', 'servers' => []];
            } catch (\Throwable $e) {
                Log::error('RoleService getRolesData failed', [
                    'game_id' => $game['game_id'] ?? null,
                    'vgp_id'  => $vgpId,
                    'error'   => $e->getMessage(),
                ]);
                return ['type' => 'error', 'servers' => []];
            }
        });
    }

    /**
     * Lấy danh sách nhân vật theo server (chỉ dùng cho type servers_characters)
     */
    public function getCharactersByServer(array $game, string $vgpId, int $serverId): array
    {
        try {
            $config = collect($game['apis']['api_config'] ?? [])
                ->first(fn($c) => $c['type'] === 'characters');

            if (!$config) {
                Log::warning("RoleService: Game {$game['game_id']} does not support character listing.");
                return [];
            }

            $data = $this->callApi($config, [
                'vgp_id'    => $vgpId,
                'server_id' => (string) $serverId,
            ]);

            return collect($data['roles'] ?? [])->map(function ($char) {
                return [
                    'id'   => (string) ($char['id'] ?? ''),
                    'name' => urldecode($char['name'] ?? ''),
                    'lv'   => (string) ($char['lv'] ?? ''),
                ];
            })->sortBy('name')->values()->all();
        } catch (\Throwable $e) {
            Log::error('RoleService getCharactersByServer failed', [
                'game_id'   => $game['game_id'] ?? null,
                'vgp_id'    => $vgpId,
                'server_id' => $serverId,
                'error'     => $e->getMessage(),
            ]);
            return [];
        }
    }

    protected function callApi(array $config, array $context): array
    {
        try {
            $method  = strtoupper($config['method'] ?? 'GET');
            $url     = $config['url'] ?? '';
            $headers = $config['headers'] ?? [];

            $params = [];
            foreach ($config['params'] ?? [] as $param) {
                $name = $param['key'];
                $src  = $param['value'] ?? null;

                $params[$name] = match ($src) {
                    'vgp_id'    => (int)($context['vgp_id'] ?? 0),
                    'server_id' => (string)($context['server_id'] ?? ''),
                    'timestamp' => time(),
                    default     => $src,
                };
            }

            if (!isset($params['vgp_id']) && isset($context['vgp_id'])) {
                $params['vgp_id'] = (int)$context['vgp_id'];
            }
            if (!isset($params['timestamp'])) {
                $params['timestamp'] = time();
            }

            $client = Http::timeout(20)->retry(2, 200)->withHeaders($headers);

            $response = $method === 'GET'
                ? $client->get($url, $params)
                : $client->asForm()->post($url, $params);

            Log::info('RoleService API REQUEST', compact('url', 'method', 'params'));
            Log::info('RoleService API RESPONSE', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            return [];
        } catch (\Throwable $e) {
            Log::error('RoleService callApi failed', [
                'url'   => $config['url'] ?? null,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    /**
     * Xóa cache theo game và vgp_id
     */
    public function forget(string $game_id, string $vgp_id): void
    {
        Cache::forget($this->cacheKey("game:{$game_id}:{$vgp_id}"));
    }

    public function forgetAll(): void
    {
        try {
            if (Cache::getStore() instanceof \Illuminate\Cache\RedisStore) {
                $redis = Cache::getRedis();
                $keys = $redis->keys('roles:*');

                foreach ($keys as $key) {
                    $redis->del($key);
                }

                Log::info('RoleService forgetAll: Cleared all role cache keys');
            } else {
                Log::warning('RoleService forgetAll: Không hỗ trợ flush cache roles cho driver này');
            }
        } catch (\Throwable $e) {
            Log::error('RoleService forgetAll failed', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Xoá toàn bộ cache roles (build lại sau khi gọi getRolesData lần tới)
     */
    public function syncAll(): void
    {
        $this->forgetAll();

        // Không preload do phụ thuộc vgp_id của từng user
        Log::info('RoleService syncAll: Flushed all role caches, sẽ cache lại khi user gọi.');
    }
}
