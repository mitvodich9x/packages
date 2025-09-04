<?php

namespace Vgplay\Recharge\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use MongoDB\BSON\UTCDateTime;
use Vgplay\Recharge\Models\Recharge;
use Vgplay\Recharge\Models\Wallet\WalletLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Vgplay\Recharge\Services\Rechargeervice;
use Vgplay\Recharge\Services\RoleService;
// use Vgplay\Items\Services\ItemService;
use Illuminate\Support\Facades\Session;
use Vgplay\ApiHelpers\Helpers\ApiHelper;
// use Vgplay\Payments\Services\PaymentService;
use Vgplay\Recharge\Models\TopDailyRechargeTotal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class RechargeController extends Controller
{
    public function purchasePayment(Request $request, string $alias)
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return response()->json([
                'error'   => true,
                'message' => 'Phiên đăng nhập đã hết hạn. Xin vui lòng đăng nhập lại.',
            ], 401);
        }

        $ip = $request->getClientIp();

        [$endpoint, $aliasExtras] = $this->mapAliasToEndpoint($alias);
        if (!$endpoint) {
            return response()->json([
                'error'   => true,
                'message' => 'Loại thanh toán không hợp lệ.',
            ], 400);
        }

        $params = [
            'user_token'    => $token,
            'amount'        => $request->input('amount', null),
            'serial'        => $request->input('serial', null),
            'code'          => $request->input('code', null),
            'ip'            => $ip,

            'game_id'       => $request->has('game_id') ? (int) $request->input('game_id') : 10,

            'vgp_id'        => $request->input('vgp_id', null),
            'server_id'     => $request->input('server_id', null),
            'character_id'  => $request->input('character_id', null),
            'item_id'       => $request->input('item_id', null),
            'dpt_id'        => $request->input('dpt_id', null),

            'timestamp'     => $request->has('timestamp') ? (int) $request->input('timestamp') : time(),

            'payment_token' => $request->input('payment_token', null),
            'partner_token' => $request->input('partner_token', null),
            'md5'
        ] + $aliasExtras;

        try {
            $validator = Validator::make($params, [
                'user_token' => 'required',
            ]);
            if ($validator->fails()) {
                return response()->json([
                    'error'   => true,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            if (!empty($params['item_id']) && !str_contains(strtolower($params['item_id']), 'vxu')) {
                $gameId   = $params['game_id'];
                $serverId = $params['server_id'];
                $charId   = $params['character_id'];
                $itemId   = $params['item_id'];
                $vgpId    = $params['vgp_id'];
                $dptId    = $params['dpt_id'];
                $timestamp = $params['timestamp'];

                $games = collect(config('games', []));
                $game  = $games[sprintf('_%s', $gameId)] ?? null;

                if (!$game || empty($game['payment_token_verify'])) {
                    Log::error('purchasePayment: thiếu payment_token_verify', [
                        'game_id' => $gameId,
                        'alias'   => $alias,
                    ]);
                    return response()->json([
                        'error'   => true,
                        'message' => 'Không tìm thấy thông tin game (payment_token_verify).',
                    ], 422);
                }

                if (empty($params['md5'])) {
                    $verify = $game['payment_token_verify'];
                    $params['md5'] = md5(
                        $verify
                            . 'vgpid'     . $vgpId
                            . 'server_id' . $serverId
                            . 'role_id'   . $charId
                            . 'item_id'   . $itemId
                            . ($dptId ? 'dpt_id' . $dptId : '')
                            . 'tstamp'    . $timestamp
                    );
                }

                if (empty($params['partner_token'])) {
                    $tokenRes = ApiHelper::get_payment_token_retrieval(
                        $gameId,
                        $game['payment_token_retrieval']['url'] ?? null,
                        $vgpId,
                        $serverId,
                        $charId,
                        $itemId,
                        $params['md5'],
                        $dptId
                    );

                    if (!is_array($tokenRes) || empty($tokenRes['token'])) {
                        Log::error('purchasePayment: không lấy được partner_token', [
                            'alias'     => $alias,
                            'endpoint'  => $endpoint,
                            'params'    => $params,
                            'token_res' => $tokenRes,
                        ]);

                        $msg = is_array($tokenRes) && !empty($tokenRes['code'])
                            ? $tokenRes['code']
                            : 'Lỗi lấy token đối tác';
                        return response()->json([
                            'error'   => true,
                            'message' => $msg,
                        ], 422);
                    }

                    $params['partner_token'] = $tokenRes['token'];
                }

                $params['ticket'] = $params['md5'];
            } else {
                $params['partner_token'] = null;
            }

            $response = ApiHelper::processPayment($endpoint, $params);

            return response()->json([
                'error'   => (bool)($response['error'] ?? false),
                'message' => $response['message'] ?? 'Lấy dữ liệu thanh toán thành công.',
                'data'    => $response['data'] ?? $response,
            ]);
        } catch (\Throwable $e) {
            Log::error('purchasePayment exception', [
                'alias'  => $alias,
                'error'  => $e->getMessage(),
                'trace'  => $e->getTraceAsString(),
            ]);

            if (str_contains($e->getMessage(), 'Vxu không đủ')) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return response()->json([
                'error'   => true,
                'message' => 'Lỗi khi xử lý thanh toán: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Map alias thanh toán sang endpoint & tham số bổ sung.
     *
     * @param  string $alias
     * @return array  [string|null $endpoint, array $extras]
     */
    private function mapAliasToEndpoint(string $alias): array
    {
        switch ($alias) {
            case 'atm':
                return ['bank/deposit', ['bank' => 99030]];
            case 'qr':
                return ['bank/deposit', ['bank' => 99999]];
            case 'visa':
                return ['bank/deposit', ['bank' => 99031]];
            case 'momo':
                return ['wallet/charge', ['bank' => 'momo']];
            case 'vgp':
                return ['card/vgp', []];
            case 'zalo':
                return ['bank/zalopay-pay', []];
            case 'google':
                return ['bank/google-pay', []];
            case 'samsung':
                return ['bank/samsung-pay', []];
            case 'apple':
                return ['bank/apple-pay', []];
            case 'vxu':
                return ['direct', []];
            default:
                return [null, []];
        }
    }

    protected function getUserExtra($key, $default = null)
    {
        $extra = json_decode(Auth::user()->extra ?? '{}', true);
        return $extra[$key] ?? $default;
    }

    protected function setUserExtra($key, $value)
    {
        $extra = json_decode(Auth::user()->extra ?? '{}', true);
        $extra[$key] = $value;
        Auth::user()->extra = json_encode($extra);
        Auth::user()->save();
    }
}
