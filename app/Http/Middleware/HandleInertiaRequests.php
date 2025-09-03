<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => fn() => $user ? (function () use ($user) {
                    $extras = is_array($user->extras) ? $user->extras : [];
                    $info   = data_get($extras, 'info', []);
                    $flags   = data_get($extras, 'flags', []);
                    $roles   = data_get($extras, 'roles', []);
                    $wallet = data_get($extras, 'wallet', []);

                    return [
                        'vgp_id'     => $user->vgp_id ?? data_get($info, 'id'),
                        'username'   => $user->username ?? data_get($info, 'username'),
                        'game_id'    => session('current_game_id') ?? $user->current_game_id ?? null,
                        'user_token' => session('user_token') ?? data_get($info, 'user_token'),
                        'info'       => $info,
                        'wallet'     => $wallet,
                        'flags'      => $flags,
                        'roles'      => $roles,
                    ];
                })() : null,
            ],
        ]);
    }
}
