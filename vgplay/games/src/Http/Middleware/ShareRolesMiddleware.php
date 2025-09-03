<?php

namespace Vgplay\Games\Http\Middleware;

use Closure;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Vgplay\Games\Services\GameService;
use Vgplay\Games\Services\RoleService;

class ShareRolesMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $roleData = session('roleData');

        Inertia::share([
            'roleData' => $roleData ?? [
                'servers'    => [],
                'characters' => [],
                'roles'      => [],
                'message'    => 'Chưa có dữ liệu nhân vật',
            ],
        ]);

        return $next($request);
    }
}
