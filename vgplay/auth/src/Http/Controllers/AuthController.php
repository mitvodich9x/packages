<?php

namespace Vgplay\Auth\Http\Controllers;

use Illuminate\Http\Request;
use Vgplay\Auth\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Vgplay\ApiHelpers\Helpers\ApiHelper;
use Vgplay\Auth\Http\Requests\FacebookLoginRequest;
// use Vgplay\Auth\Models\UserGame;

class AuthController extends Controller
{
    const LIMITLOGIN = 10;
    const PROHOBITIONINTERVAL = 900;

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'game_id'  => 'nullable|integer',
        ]);

        $user_ip    = $request->ip();
        $key_ip     = "ip_{$user_ip}";
        $attemptKey = "attempt_{$user_ip}";

        if (Cache::has($key_ip)) {
            return response()->json(['message' => 'Bạn đã đăng nhập sai quá nhiều. Vui lòng thử lại sau.'], 422);
        }

        $response = ApiHelper::login(
            $credentials['username'],
            $credentials['password'],
            $credentials['game_id'] ?? null
        );

        if (!empty($response['error'])) {
            $attempts = Cache::increment($attemptKey);
            if ($attempts >= static::LIMITLOGIN) {
                Cache::put($key_ip, true, static::PROHOBITIONINTERVAL);
                Cache::forget($attemptKey);
            } else {
                Cache::put($attemptKey, $attempts, now()->addMinutes(10));
            }
            return response()->json(['message' => 'Sai tài khoản hoặc mật khẩu'], 422);
        }

        Cache::forget($attemptKey);
        Cache::forget($key_ip);

        $user = User::updateOrCreate(
            ['username' => $credentials['username']],
            ['extras' => (array) ['info' => (array) $response['user']]]
        );

        Auth::guard('web')->login($user);
        $request->session()->regenerate();

        if (!empty($response['user']['user_token'])) {
            session(['external_token' => $response['user']['user_token']]);
        }

        session(['user' => [
            'username' => $response['user']['username'],
            'token' => $response['user']['user_token'],
            'id' => $response['user']['id']
        ]]);

        session([
            'login_type' => 'vgp',
            'fb_game_id' => null,
            'fb_game_alias' => null,
        ]);

        $wallet = null;
        try {
            $token = $response['user']['user_token'] ?? null;

            if ($token) {
                $balanceRaw = ApiHelper::balance($token);
                if (is_array($balanceRaw)) {
                    $wallet = array_merge($balanceRaw, [
                        'fetched_at' => now()->toISOString(),
                    ]);
                } else {
                    $wallet = [
                        'balance'    => (float) $balanceRaw,
                        'currency'   => $response['user']['currency'] ?? 'VND',
                        'fetched_at' => now()->toISOString(),
                    ];
                }

                $user->refresh();
                $extras = (array) ($user->extras ?? []);
                $extras['wallet'] = $wallet;
                $user->extras = $extras;
                $user->save();

                // Lưu vào session để FE dùng nhanh
                session(['balance' => $wallet ?? null]);
            }
        } catch (\Throwable $e) {
            Log::warning('Fetch balance failed', ['err' => $e->getMessage()]);
        }

        return response()->json([
            'user' =>  $user->fresh(),
        ]);
    }

    public function logout(Request $request)
    {
        session()->forget('external_token');
        session()->forget('balance');

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'Đăng xuất thành công']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function getBalance(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'wallet'  => null,
                'message' => 'Bạn chưa đăng nhập.',
            ], 401);
        }

        $user  = $request->user();
        $token = session('external_token') ?? data_get($user->extras, 'info.user_token');

        if (!$token) {
            return response()->json([
                'wallet'  => null,
                'message' => 'Thiếu user_token để lấy số dư.',
            ], 422);
        }

        try {
            $balanceRaw = ApiHelper::balance($token);

            // Chuẩn hoá wallet
            $wallet = is_array($balanceRaw)
                ? array_merge($balanceRaw, ['fetched_at' => now()->toISOString()])
                : [
                    'balance'    => (float) $balanceRaw,
                    'currency'   => data_get($user->extras, 'info.currency', 'VND'),
                    'fetched_at' => now()->toISOString(),
                ];

            // Ghi vào extras.wallet (không đụng info/flags/roles)
            $extras = $user->extras ?? [];
            data_set($extras, 'wallet', $wallet);
            $user->extras = $extras;
            $user->save();

            // Cập nhật session balance để Inertia share hiển thị nhanh
            session(['balance' => $wallet ?? null]);

            return response()->json([
                'wallet'  => $wallet,
                'message' => 'Lấy thông tin ví thành công.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'wallet'  => null,
                'message' => 'Lỗi lấy thông tin ví.',
            ], 500);
        }
    }
}
