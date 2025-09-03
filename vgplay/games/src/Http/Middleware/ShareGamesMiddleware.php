<?php

namespace Vgplay\Games\Http\Middleware;

use Closure;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Vgplay\Games\Services\GameService;
use Vgplay\ApiHelpers\Helpers\ApiHelper;

class ShareGamesMiddleware
{
    public function handle($request, Closure $next)
    {
        $games = app(GameService::class)->getAll(true);

        $props = [
            'allGames' => collect($games)->take(300)->map(function ($game) {
                return [
                    'label' => $game['name'],
                    'value' => $game['game_id'],
                    'icon' => $game['icon'],
                    'alias' => $game['alias'],
                    'flags' => $game['flags'],
                    'settings' => $game['settings'],
                    'admins' => $game['admins'],
                    'socials' => $game['socials'],
                    'apis' => $game['apis'],
                    'data' => $game,
                    'message' => 'Lấy danh sách game thành công'
                ];
            }),
            'balance' => null
        ];

        // if (Auth::user()?->api_data['user']) {
        //     try {
        //         $token = Auth::user()?->api_data['user']['user_token'] ?? $request->bearerToken();
        //         $response = ApiHelper::balance($token);

        //         $props['balance'] = $response;
        //     } catch (\Exception $e) {
        //         $props['error'] = true;
        //     }
        // } else {
        //     $props['error'] = true;
        // }

        Inertia::share($props);

        return $next($request);
    }
}
