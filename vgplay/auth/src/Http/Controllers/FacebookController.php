<?php

namespace Vgplay\Auth\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Vgplay\Auth\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Vgplay\ApiHelpers\Helpers\ApiHelper;
use Vgplay\Auth\Http\Requests\FacebookLoginRequest;

class FacebookController extends Controller
{
    private const FB_API_VER      = 'v20.0';
    private const AUTH_ENDPOINT   = 'https://www.facebook.com/%s/dialog/oauth';
    private const TOKEN_ENDPOINT  = 'https://graph.facebook.com/%s/oauth/access_token';
    private const ME_ENDPOINT     = 'https://graph.facebook.com/%s/me';
    private const SESSION_REDIRECT = '_fb_redirect_uri';
    private const SESSION_STATE    = '_fb_oauth_state';
    private const SESSION_ACCESS   = '_fb_access_token';

    /**
     * @example https://backend-nap.vgplay.vn/oauth/facebook/login?game_id=27&redirect_uri=https%3A%2F%2Fgoogle.com.vn%2F
     */
    public function login(FacebookLoginRequest $request): RedirectResponse
    {
        $redirectUri = $this->safeRedirectUrl($request->input('redirect_uri'));
        session()->put(self::SESSION_REDIRECT, $redirectUri);

        $state = Str::random(32);
        session()->put(self::SESSION_STATE, $state);

        $gameId = (string) $request->input('game_id');

        $params = [
            'client_id'     => $this->appId($gameId),
            'redirect_uri'  => $this->callbackUrl($gameId),
            'state'         => $state,
            'scope'         => 'public_profile,email',
            'response_type' => 'code',
        ];

        $authorizeUrl = sprintf(self::AUTH_ENDPOINT, self::FB_API_VER) . '?' . http_build_query($params);
        return redirect()->away($authorizeUrl);
    }

    public function callback(Request $request, string $game_id): RedirectResponse
    {
        $redirectBack = session()->pull(self::SESSION_REDIRECT, url('/'));

        if ($request->filled('error')) {
            return $this->redirectWithError($redirectBack, $request->input('error_description', $request->input('error')));
        }

        $code  = $request->input('code');
        $state = $request->input('state');
        if (!$code || !$state) {
            return $this->redirectWithError($redirectBack, 'Thiếu code hoặc state.');
        }

        $sessionState = (string) session()->pull(self::SESSION_STATE, '');
        if (!$sessionState || !hash_equals($sessionState, $state)) {
            return $this->redirectWithError($redirectBack, 'State không hợp lệ (CSRF).');
        }

        $token = $this->exchangeCodeForToken($game_id, $code);
        if (!$token) {
            return $this->redirectWithError($redirectBack, 'Không lấy được access_token.');
        }
        session()->put(self::SESSION_ACCESS, $token);

        $longToken = $this->exchangeLongLivedToken($game_id, $token);
        if ($longToken) {
            $token = $longToken;
            session()->put(self::SESSION_ACCESS, $token);
        }

        $profile = $this->fetchUserProfile($token);
        if (!$profile) {
            return $this->redirectWithError($redirectBack, 'Không lấy được thông tin người dùng.');
        }

        try {
            $cpId  = $this->getCpIdFromUrl($redirectBack);
            $apiRes = $cpId
                ? ApiHelper::facebook_login($profile['id'], $token, $game_id, $cpId)
                : ApiHelper::facebook_login($profile['id'], $token, $game_id);

            if (!is_array($apiRes) || ($apiRes['code'] ?? 0) !== 200) {
                return $this->redirectWithError($redirectBack, 'Lỗi không xác định khi đăng nhập.');
            }

            $apiUser  = $apiRes['user'] ?? [];
            $username = $apiUser['username'] ?? ('fb_' . ($profile['id'] ?? Str::random(8)));

            $user = User::updateOrCreate(
                ['username' => $username],
                ['extras' => (array) ['info' => (array) $apiRes]]
            );

            Auth::guard('web')->login($user);

            if (!empty($apiUser['user_token'])) {
                session(['external_token' => $apiUser['user_token']]);
            }

            $wallet = null;

            try {
                $token = $apiUser['user_token'] ?? null;

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

                    session(['balance' => $wallet ?? null]);
                }
            } catch (\Throwable $e) {
                Log::warning('Fetch balance failed', ['err' => $e->getMessage()]);
            }

            session([
                'user' => [
                    'username'   => $user->username,
                    'token'      => $apiUser['user_token'] ?? null,
                    'id'         => $apiUser['id'] ?? null,
                ],
            ]);

            session([
                'login_type'    => 'facebook',
                'fb_game_id'    => (int) $game_id,
                'fb_game_alias' => null,
            ]);

            $finalUrl = $this->appendQuery($redirectBack, [
                'username'   => $user->username,
                'id'         => $apiUser['id'] ?? '',
                'user_token' => $apiUser['user_token'] ?? '',
            ]);

            return redirect()->away($finalUrl);
        } catch (\Throwable $e) {
            return $this->redirectWithError($redirectBack, $e->getMessage());
        }
    }

    /* ========================== Helpers ========================== */

    private function appId(string $gameId): string
    {
        return (string) config("facebook.$gameId.app_id");
    }

    private function appSecret(string $gameId): string
    {
        return (string) config("facebook.$gameId.app_secret");
    }

    private function callbackUrl(string $gameId): string
    {
        return url('oauth/facebook/callback/' . $gameId);
    }

    private function exchangeCodeForToken(string $gameId, string $code): ?string
    {
        try {
            $res = Http::timeout(20)->acceptJson()->get(sprintf(self::TOKEN_ENDPOINT, self::FB_API_VER), [
                'client_id'     => $this->appId($gameId),
                'redirect_uri'  => $this->callbackUrl($gameId),
                'client_secret' => $this->appSecret($gameId),
                'code'          => $code,
            ]);

            if ($res->successful() && $res->json('access_token')) {
                return (string) $res->json('access_token');
            }
        } catch (\Throwable $e) {
            // log nếu cần
        }
        return null;
    }

    private function exchangeLongLivedToken(string $gameId, string $shortToken): ?string
    {
        try {
            $res = Http::timeout(20)->acceptJson()->get(sprintf(self::TOKEN_ENDPOINT, self::FB_API_VER), [
                'grant_type'        => 'fb_exchange_token',
                'client_id'         => $this->appId($gameId),
                'client_secret'     => $this->appSecret($gameId),
                'fb_exchange_token' => $shortToken,
            ]);

            if ($res->successful() && $res->json('access_token')) {
                return (string) $res->json('access_token');
            }
        } catch (\Throwable $e) {
            // bỏ qua
        }
        return null;
    }

    private function fetchUserProfile(string $accessToken): ?array
    {
        try {
            $res = Http::timeout(20)->acceptJson()->get(sprintf(self::ME_ENDPOINT, self::FB_API_VER), [
                'fields'       => 'id,name,email',
                'access_token' => $accessToken,
            ]);

            if ($res->successful()) {
                return $res->json();
            }
        } catch (\Throwable $e) {
            // log nếu cần
        }
        return null;
    }

    private function appendQuery(string $url, array $params): string
    {
        $parts  = parse_url($url);
        $query  = Arr::get($parts, 'query', '');
        $pairs  = [];
        if ($query) parse_str($query, $pairs);
        $pairs = array_merge($pairs, $params);

        $built =
            ($parts['scheme'] ?? 'https') . '://' .
            ($parts['host'] ?? '') .
            (isset($parts['port']) ? ':' . $parts['port'] : '') .
            ($parts['path'] ?? '') .
            '?' . http_build_query($pairs);

        if (isset($parts['fragment'])) $built .= '#' . $parts['fragment'];
        return $built;
    }

    private function getCpIdFromUrl(string $url): ?string
    {
        $query = Arr::get(parse_url($url), 'query', '');
        if (!$query) return null;
        parse_str($query, $pairs);
        return $pairs['cp_id'] ?? null;
    }

    private function safeRedirectUrl(?string $uri): string
    {
        if (!$uri) return url('/');
        if (Str::startsWith($uri, ['https://', '/'])) return $uri;

        if (Str::startsWith($uri, 'http://')) {
            $p   = parse_url($uri);
            $host = Arr::get($p, 'host');
            $path = Arr::get($p, 'path', '/');
            $qs   = Arr::get($p, 'query');
            $frag = Arr::get($p, 'fragment');
            return 'https://' . $host . $path . ($qs ? "?$qs" : '') . ($frag ? "#$frag" : '');
        }
        return url('/');
    }

    private function redirectWithError(string $backUrl, string $message): RedirectResponse
    {
        return redirect()->away($this->appendQuery($backUrl, ['error' => $message]));
    }
}
