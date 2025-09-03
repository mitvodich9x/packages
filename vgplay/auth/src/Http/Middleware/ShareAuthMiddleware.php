<?php

namespace Vgplay\Auth\Http\Middleware;

use Closure;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ShareAuthMiddleware
{
     public function handle($request, Closure $next)
    {
        Inertia::share([
            'auth' => fn () => [
                'user' => Auth::check() ? Auth::user() : null,
            ],
        ]);

        return $next($request);
    }
}
