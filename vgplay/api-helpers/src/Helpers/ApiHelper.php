<?php

namespace Vgplay\ApiHelpers\Helpers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;
use Vgplay\Exceptions\Exceptions\Apis\VgpApiException;

class ApiHelper
{
    protected const ENDPOINT_LOGIN = 'account/signin';
    protected const ENDPOINT_LOGOUT = 'account/logout';
    protected const ENDPOINT_FACEBOOK_LOGIN = 'account/social';
    protected const ENDPOINT_PROFILE = 'account/me';
    protected const ENDPOINT_PAYMENT = 'payment';
    protected const ENDPOINT_BALANCE = 'payment/balance';
    protected const ENDPOINT_BUY_ITEM = 'payment/direct';
    protected const ENDPOINT_DAILY_RECHARGE = 'http://wallet.vgplay.vn:2086/user/top_daily_recharge_total';

    public static $SDK_VERSION = 6;

    public static function callApi(string $url, array $data = null, string $type = 'json', bool $verifySsl = false, array $headers = []): array
    {
        try {
            $client = new Client([
                'verify' => $verifySsl,
            ]);

            $options = [];

            if (!empty($data)) {
                if ($type === 'json') {
                    $options['json'] = $data;
                } elseif ($type === 'form') {
                    $options['form_params'] = $data;
                }
            }

            $method = empty($data) ? 'GET' : 'POST';
            $res = $client->request($method, $url, $options);

            $body = $res->getBody()->getContents();

            $result = @json_decode($body, true);

            if (empty($result)) {
                throw new VgpApiException('Không lấy được dữ liệu: ' . $body, 500);
            }

            if (isset($result['code']) && $result['code'] !== 200) {
                $message = $result['message'] ?? ($result['messages'] ?? 'Lỗi không xác định');
                throw new VgpApiException($message, $result['code']);
            }

            return $result;
        } catch (ConnectException $e) {
            throw new VgpApiException('Lỗi kết nối API', 408);
        } catch (ClientException $e) {
            throw new VgpApiException($e->getResponse()->getBody()->getContents(), $e->getResponse()->getStatusCode());
        }
    }

    public static function request($url, $data = null)
    {
        try {
            $client = new Client(['verify' => false]);

            if (empty($data)) {
                $res = $client->request('GET', $url);
            } else {
                $res = $client->request('POST', $url, ['json' => $data]);
            }
            $result = @json_decode($res->getBody()->getContents(), true);

            if (empty($result)) {
                throw new VgpApiException('Không lấy được dữ liệu ' . $res->getBody()->getContents(), $result['code']);
            }
            if ($result['code'] === 200) {
                // dd($result);
                return $result;
            } else {
                if (isset($result['message'])) {
                    throw new VgpApiException($result['message'], $result['code']);
                } else {
                    if (isset($result['messages'])) {
                        throw new VgpApiException($result['messages'], $result['code']);
                    } else {
                        throw new VgpApiException(
                            $url . ' external host error code: ' . json_encode($result),
                            $result['code']
                        );
                    }
                }
            }
        } catch (ConnectException $e) {
            throw new VgpApiException('Lỗi kết nối API', 408);
        } catch (ClientException $e) {
            throw new VgpApiException($e->getResponse()->getBody()->getContents(), $e->getResponse()->getStatusCode());
        }
    }

    public static function request_external($url, $data = null)
    {
        try {

            $client = new Client();

            if (empty($data)) {
                $res = $client->request('GET', $url);
            } else {
                $res = $client->request('POST', $url, ['form_params' => $data]);
            }

            $result = @json_decode($res->getBody()->getContents(), true);

            if (empty($result)) {
                throw new VgpApiException('Không lấy được dữ liệu ' . $res->getBody()->getContents(), $result['code']);
            }

            return $result;
        } catch (ConnectException $e) {

            // This is will catch all connection timeouts
            throw new VgpApiException('Lỗi kết nối API', 408);
        } catch (ClientException $e) {

            // This will catch all 400 level errors.
            throw new VgpApiException($e->getResponse()->getBody()->getContents(), $e->getResponse()->getStatusCode());
        }
    }

    public static function getVgpUrl($game_id = null): string
    {
        $gameId = empty($game_id) ? config('vgp.VGP_API_GAME_ID') : $game_id;
        $baseUrl = rtrim(config('vgp.VGP_API_ENDPOINT'), '/');
        $version = config('vgp.VGP_API_VERSION');

        return "{$baseUrl}/{$gameId}/{$version}";
    }

    protected static function buildApiUrl(string $endpoint, ?int $gameId = null, array $query = [], bool $includeToken = false): string
    {
        if ($includeToken) {
            $query['app_token'] = config('vgp.VGP_API_TOKEN');
        }

        $baseUrl = self::getVgpUrl($gameId);
        $endpoint = ltrim($endpoint, '/');
        $queryString = !empty($query) ? '?' . http_build_query($query) : '';

        return "{$baseUrl}/{$endpoint}{$queryString}";
    }

    public static function get_vgp_url($game_id)
    {
        if (!isset($game_id) || $game_id == 0) {
            $game_id = config('vgp.VGP_API_GAME_ID');
        }
        return config('vgp.VGP_API_ENDPOINT') . '/' . $game_id . '/' . static::$SDK_VERSION;
    }

    public static function login(string $username, string $password, ?int $gameId = null): array
    {
        $url = self::buildApiUrl(self::ENDPOINT_LOGIN, $gameId, [
            'cb' => urlencode(url()->current())
        ], true);

        $body = [
            'username'  => $username,
            'password'  => $password,
            'provider'  => '0',
            'client_ip' => request()->ip(),
        ];

        return self::callApi($url, $body);
    }

    public static function getProfile(string $user_token)
    {
        $url = self::buildApiUrl(self::ENDPOINT_PROFILE, null, [
            'user_token' => $user_token
        ]);

        $data = static::callApi($url);

        return isset($data['user']) ? $data['user'] : null;
    }

    public static function get_role($url, $vgp_id = null, $vgpid = null)
    {
        if (empty($vgp_id)) {
            $body['vgpid'] = $vgpid;
        } else {
            $body['vgp_id'] = $vgp_id;
        }
        $body['timestamp'] = time();

        return static::callApi($url, $body);
    }

    // public static function processPayment(string $endpoint, array $params): array
    // {

    //     // dd($params);
    //     $game_id = $params['game_id'] ?? null;

    //     if ($endpoint != 'balance') {
    //         $url = self::buildApiUrl(self::ENDPOINT_PAYMENT . "/{$endpoint}", $game_id, [
    //             'game_id' => $game_id,
    //             'user_token' => $params['user_token'] ?? null,
    //             'amount' => $params['amount'] ?? null,
    //             'bank' => $params['bank'] ?? null,
    //             'is_tokenization' => $params['is_tokenization'] ?? null,
    //         ], true);

    //         if (isset($params['is_tokenization'])) {
    //             $url .= '&isTokenization=' . $params['is_tokenization'];
    //         }

    //         $body = [
    //             'user' => $params['username'] ?? $params['ip'] ?? request()->ip(),
    //             'amount' => $params['amount'] ?? 0,
    //             'bank' => $params['bank'] ?? null,
    //             'client_ip' => request()->ip(),
    //             'user_token' => $params['user_token'] ?? null,
    //             'app_token' => config('vgp.VGP_API_TOKEN'),
    //             'server_id' => $params['server_id'] ?? null,
    //             'character_id' => $params['character_id'] ?? null,
    //             'partner_token' => $params['partner_token'] ?? null,
    //             'item_id' => $params['item_id'] ?? null,
    //             'serial' => $params['serial'] ?? null,
    //             'code' => $params['code'] ?? null,
    //         ];
    //         Log::info('buy item vnd: ' . $body);
    //     }
    //     return static::request($url, $body);
    // }

    public static function processPayment(string $endpoint, array $params): array
    {
        $game_id = $params['game_id'] ?? null;

        if ($endpoint === 'wallet') {
            $url = self::get_vgp_url($game_id)
                . self::ENDPOINT_PAYMENT . "/{$endpoint}" . '?app_token=' . config('vgp.VGP_API_TOKEN')
                . '&is_web_recharge=1';
        } else {
            $url = self::buildApiUrl(
                self::ENDPOINT_PAYMENT . "/{$endpoint}",
                $game_id,
                [
                    'game_id'        => $game_id,
                    'user_token'     => $params['user_token'] ?? null,
                    'amount'         => $params['amount'] ?? null,
                    'bank'           => $params['bank'] ?? null,
                    'is_tokenization' => $params['is_tokenization'] ?? null,
                ],
                true
            );

            if (isset($params['is_tokenization'])) {
                $url .= '&isTokenization=' . $params['is_tokenization'];
            }
        }

        $body = [
            'user'          => $params['username'] ?? $params['ip'] ?? request()->ip(),
            'amount'        => $params['amount'] ?? 0,
            'bank'          => $params['bank'] ?? null,
            'app_token'     => config('vgp.VGP_API_TOKEN'),
            'client_ip'     => request()->ip(),
            'game_id'       => $game_id,
            'user_token'    => $params['user_token'] ?? null,
            'item_id'       => $params['item_id'] ?? null,
            'server_id'     => $params['server_id'] ?? null,
            'character_id'  => $params['character_id'] ?? null,
            'partner_token' => $params['partner_token'] ?? null,
            'serial'        => $params['serial'] ?? null,
            'code'          => $params['code'] ?? null,
        ];

        Log::info("processPayment {$endpoint}: " . json_encode($body));

        // dd($endpoint, $body);
        return static::request($url, $body);
    }

    public static function facebook_login($username, $password, $game_id, $cp_id = null)
    {
        $url = self::get_vgp_url($game_id) . '/account/social?app_token=' . config('vgp.VGP_API_TOKEN');
        $body['username'] = $username;
        $body['password'] = $password;
        $body['provider'] = '2';
        $body['client_ip'] = request()->ip();
        if (!empty($cp_id)) {
            $body['agency'] = $cp_id;
        }
        return static::request($url, $body);
    }

    public static function balance($user_token, $game_id = null)
    {
        $url = self::buildApiUrl(self::ENDPOINT_BALANCE, $game_id, [
            'app_token' => config('vgp.VGP_API_TOKEN'),
            'user_token' => $user_token
        ], true);

        return static::callApi($url);
    }

    // public static function pay_by_wallet($game_id, $user_token, $item_id, $server_id, $character_id, $partner_token)
    // {
    //     $url = self::get_vgp_url($game_id) . '/payment/direct?app_token=' . config('VGP_API_TOKEN') . '&is_web_recharge=1';
    //     $body['user_token'] = $user_token;
    //     $body['item_id'] = $item_id;
    //     $body['server_id'] = $server_id;
    //     $body['character_id'] = $character_id;
    //     $body['partner_token'] = $partner_token;

    //     return static::request($url, $body);
    // }

    public static function get_payment_token_retrieval($game_id, $url, $vgpid, $server_id, $role_id, $item_id, $ticket, $dpt_id)
    {
        $key = sprintf("_%s", $game_id);
        $games = collect(config('games', []));
        $game = $games[$key];
        $method = $game['payment_token_retrieval']['method'];
        $params = $game['payment_token_retrieval']['params'];
        if ($method == "GET") {
            $param = array(
                $params['vgpid'] => $vgpid,
                $params['server_id'] => $server_id,
                $params['role_id'] => $role_id,
                $params['item_id'] => $item_id,
                $params['timestamp'] => time(),
                $params['ticket'] => $ticket,
            );
            if (isset($params['dpt_id'])) {
                $param[$params['dpt_id']] = $dpt_id;
            } else {
                $param['dpt_id'] = $dpt_id;
            }
            $api = $url . '?' . http_build_query($param);
            return static::request_external($api);
        } else {
            $body['game_id'] = $game_id;
            $body[$params['vgpid']] = $vgpid;
            $body[$params['server_id']] = $server_id;
            $body[$params['role_id']] = $role_id;
            $body[$params['item_id']] = $item_id;
            $body[$params['timestamp']] = time();
            $body[$params['ticket']] = $ticket;
            $body['dpt_id'] = $dpt_id;
            return static::request_external($url, $body);
        }
    }
}
