<?php

namespace Vgplay\Recharges\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Http\Request;
use MongoDB\BSON\UTCDateTime;
use Vgplay\Recharges\Models\Recharge;
use Vgplay\Recharges\Models\Wallet\WalletLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Vgplay\Recharges\Services\RechargeService;
use Vgplay\Recharges\Services\RoleService;
// use Vgplay\Items\Services\ItemService;
use Illuminate\Support\Facades\Session;
use Vgplay\ApiHelpers\Helpers\ApiHelper;
// use Vgplay\Payments\Services\PaymentService;
use Vgplay\Recharges\Models\TopDailyRechargeTotal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Vgplay\Exceptions\Exceptions\Recharges\RechargeNotFoundException;
use Vgplay\Exceptions\Exceptions\Caches\CacheConnectionFailedException;

class RechargeController extends Controller
{
    // public function __construct(protected RechargeService $gameService, protected ItemService $itemService, protected PaymentService $paymentService) {}
    public function __construct(protected RechargeService $gameService) {}

    public function search(Request $request): JsonResponse
    {
        $keyword = (string) $request->input('keyword', '');

        if ($keyword === '') {
            return response()->json([]);
        }

        $games = $this->gameService->search($keyword);

        return response()->json(
            collect($games)->map(fn($game) => [
                'label' => $game['name'],
                'value' => $game['name'],
                'icon'  => $game['icon'] ?? null,
                'data'  => $game,
            ])->values()
        );
    }

    public function show(string $alias, Request $request)
    {
        try {
            $game = $this->gameService->findByAlias($alias);
            // $items = $this->itemService->getAllItemByRechargeId($game['game_id'], true);
            // $user = Auth::user()?->api_data['user'];

            $recharge = false;

            // if ($user) {
            //     $user_id = $user['id'];
            //     $game_id = $game['game_id'];
            //     $token = $user['user_token'];

            //     $cacheKey = "daily_recharge_{$user_id}_{$game_id}_" . now()->format('Y-m-d');

            //     $balance = ApiHelper::balance($token);

            //     session(['balance' => $balance]);

            //     $flag = self::getUserExtra("recharge_{$game_id}");
            //     if ($flag === true) {
            //         $recharge = true;
            //     } else {
            //         $dataRecharge = Cache::get($cacheKey);

            //         if (!$dataRecharge) {
            //             $cursor = DB::connection('mongodb_statistic')
            //                 ->getMongoDB()
            //                 ->selectCollection('top_daily_recharge_total')
            //                 ->find([
            //                     'game_id' => $game_id,
            //                     'vgp_id'  => $user_id,
            //                 ]);

            //             foreach ($cursor as $doc) {
            //                 $dataRecharge = $doc;
            //                 Cache::put($cacheKey, $dataRecharge, now()->addDay());
            //             }
            //         }

            //         $required = $game['settings']['required_vxu'] ?? 0;

            //         $rechargeTotal = (int) data_get($dataRecharge, 'total', 0);

            //         if ($rechargeTotal > $required) {
            //             self::setUserExtra("recharge_{$game_id}", true);
            //         } else {
            //             self::setUserExtra("recharge_{$game_id}", false); // nếu cần reset khi không đủ điều kiện
            //         }
            //         // if ($dataRecharge['total'] > $required) {
            //         //     self::setUserExtra("recharge_{$game_id}", true);
            //         // }
            //     }
            // }

            $selectedRole = session('selected_role', []);

            $props = [
                // 'selectedRecharge' => $game,
                // 'packages' => $items,
                // 'userData' => Auth::user()?->api_data['user'],
                // 'server' => $selectedRole['server_id'] ?? null,
                // 'serverName' => $selectedRole['server_name'] ?? null,
                'game' => $game ?? null,
                'role' => $selectedRole ?? null,
                // 'roleName' => $selectedRole['role_name'] ?? null,
                // 'recharge' => $recharge,
                'breadcrumbs' => [
                    ['label' => 'Trang chủ', 'url' => '/'],
                    ['label' => $game['name'], 'url' => "/{$alias}"],
                ],
                // 'open_role_form' => Session::pull('open_role_form', false),
                // 'balance' => $balance
            ];

            return Inertia::render('RechargePage', $props);
        } catch (RechargeNotFoundException $e) {
            return redirect('/')
                ->with('error', 'Không tìm thấy game với alias: ' . $alias);
        } catch (CacheConnectionFailedException $e) {
            return redirect('/')
                ->with('error', 'Lỗi kết nối cache: ' . $e->getMessage());
        }
    }

    // public function payment(Request $request)
    // {
    //     $user = Auth::user()?->api_data['user'];
    //     $items = array_filter($this->itemService->getAllItems(true), function ($item) {
    //         return $item['unit'] === 'Vxu';
    //     });

    //     $alias = $request->segment(1);
    //     $breadcrumbs = [
    //         ['label' => 'Trang chủ', 'url' => '/'],
    //     ];

    //     $game = null;
    //     $recharge = false;

    //     if (strtolower($alias) !== 'vxu') {
    //         $game = Recharge::where('alias', $alias)->first();

    //         if ($user) {
    //             $user_id = $user['id'];
    //             $game_id = $game->game_id;
    //             $cacheKey = "daily_recharge_{$user_id}_{$game_id}_" . now()->format('Y-m-d');
    //             $flag = self::getUserExtra("recharge_{$game_id}");
    //             if ($flag === true) {
    //                 $recharge = true;
    //             } else {
    //                 $dataRecharge = Cache::get($cacheKey);

    //                 if (!$dataRecharge) {
    //                     $cursor = DB::connection('mongodb_statistic')
    //                         ->getMongoDB()
    //                         ->selectCollection('top_daily_recharge_total')
    //                         ->find([
    //                             'game_id' => $game_id,
    //                             'vgp_id'  => $user_id,
    //                         ]);

    //                     foreach ($cursor as $doc) {
    //                         $dataRecharge = $doc;
    //                         Cache::put($cacheKey, $dataRecharge, now()->addDay());
    //                     }
    //                 }

    //                 $required = $game['settings']['required_vxu'] ?? 0;

    //                 $rechargeTotal = (int) data_get($dataRecharge, 'total', 0);

    //                 if ($rechargeTotal > $required) {
    //                     self::setUserExtra("recharge_{$game_id}", true);
    //                 } else {
    //                     self::setUserExtra("recharge_{$game_id}", false); // nếu cần reset khi không đủ điều kiện
    //                 }
    //                 // if ($dataRecharge['total'] > $required) {
    //                 //     self::setUserExtra("recharge_{$game_id}", true);
    //                 // }
    //             }
    //         }

    //         if ($game) {
    //             $breadcrumbs[] = ['label' => $game->name, 'url' => '/' . $alias];
    //         } else {
    //             $breadcrumbs[] = ['label' => strtoupper($alias), 'url' => '/'];
    //         }
    //     }

    //     $breadcrumbs[] = ['label' => 'Nạp Vxu', 'url' => '/' . $alias . '/payment'];

    //     return Inertia::render('PaymentPage', [
    //         'game' => $game,
    //         'packages' => $items,
    //         'recharge' => $recharge,
    //         'breadcrumbs' => $breadcrumbs,
    //     ]);
    // }

    // public function getMethodsByItem(string $alias, string $itemId)
    // {
    //     try {
    //         $item = $this->itemService->findById((int) $itemId);
    //         // $methods = $this->paymentService->getMethodsByItem($alias, $itemId);
    //         $methods = $this->paymentService->getMethodsByItem($itemId);

    //         // dd($methods);

    //         return response()->json([
    //             'item' => $item,
    //             'methods' => $methods,
    //         ]);
    //     } catch (\Exception $e) {
    //         return back()->with('error', 'Lỗi: ' . $e->getMessage());
    //     }
    // }

    public function get_role(Request $request)
    {
        // dd(Auth::user()?->extras['info']['user_token']);
        $user = Auth::user()?->extras['info'];
        $gameId = $request->input('game');

        if (!$user || !$gameId) {
            return response()->json([
                'error' => true,
                'message' => 'Thiếu thông tin người dùng hoặc game.',
            ], 422);
        }

        try {
            $vgp_id = $user['id'];
            $game = app(RechargeService::class)->findById((int) $gameId);

            if (!$game || empty($game['apis']['api_config'])) {
                return response()->json([
                    'error' => true,
                    'message' => 'Không tìm thấy cấu hình API cho game.',
                ], 404);
            }

            $rolesData = app(RoleService::class)->getRolesData($game, $vgp_id);

            session(['roleData' => $rolesData]);

            return response()->json([
                'error' => false,
                'message' => 'Lấy thông tin nhân vật thành công.',
                'data' => $rolesData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Lỗi khi lấy dữ liệu nhân vật: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function get_characters_by_server(Request $request)
    {
        $user = Auth::user()?->api_data['user'];
        $gameId = $request->input('game');
        $serverId = $request->input('server');

        if (!$user || !$gameId || !$serverId) {
            return response()->json([
                'error' => true,
                'message' => 'Thiếu thông tin người dùng, game hoặc server.',
            ], 422);
        }

        try {
            $vgp_id = $user['id'];
            $game = app(RechargeService::class)->findById((int) $gameId);

            if (!$game || empty($game['apis']['api_config'])) {
                return response()->json([
                    'error' => true,
                    'message' => 'Không tìm thấy cấu hình API cho game.',
                ], 404);
            }

            $characters = app(RoleService::class)->getCharactersByServer($game, $vgp_id, $serverId);

            return response()->json([
                'error' => false,
                'message' => 'Lấy danh sách nhân vật theo server thành công.',
                'data' => $characters,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'Lỗi khi lấy dữ liệu nhân vật: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function selectRole(Request $request)
    {
        $request->validate([
            'alias' => 'required|string',
            'role_id' => 'required|integer',
            'server_id' => 'required|integer',
        ]);

        session()->put('selected_role', [
            'role_id' => $request->role_id,
            'server_id' => $request->server_id,
            'server_name' => $request->server_name,
            'role_name' => $request->role_name,
        ]);

        return redirect("/{$request->alias}");
    }

    public function getBalance(Request $request)
    {
        if (Auth::check()) {
            try {
                $token = $request->bearerToken();
                $response = ApiHelper::balance($token);

                session(['balance' => $response]);

                return Inertia::render('Home', [
                    'balance' => $response,
                    'message' => 'Lấy thông tin ví thành công.',
                ]);
            } catch (\Exception $e) {
                return Inertia::render('Home', [
                    'balance' => null,
                    'error' => true,
                    'message' => 'Lỗi lấy thông tin ví: ' . $e->getMessage(),
                ]);
            }
        } else {
            return Inertia::render('Home', [
                'balance' => null,
                'error' => true,
                'message' => 'Đăng nhập lấy thông tin ví',
            ]);
        }
    }

    public function getHistory(Request $request)
    {
        $user = Auth::user()?->api_data['user'];
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId   = (int) $user['id'];
        $gameId   = $request->query('game_id');

        $option   = $request->query('option', 'cash');
        $dateFrom = $request->query('date_start');
        $dateTo   = $request->query('date_end');

        $dateStart = $dateFrom
            ? Carbon::createFromFormat('d/m/Y', $dateFrom)
            : Carbon::now()->subDays(60);
        $dateEnd = $dateTo
            ? Carbon::createFromFormat('d/m/Y', $dateTo)
            : Carbon::now();

        $historyData = [];

        try {
            if ($option === 'cash') {
                // Nạp
                $startUtc = $dateStart->copy()->startOfDay()->toDateTime();
                $endUtc   = $dateEnd->copy()->endOfDay()->toDateTime();

                $cursor = DB::connection('mongodb_vgplay')
                    ->getMongoDB()
                    ->selectCollection('vip_coin_log')
                    ->find(
                        [
                            'game_id'    => $gameId,
                            'vgp_id'     => $userId,
                            'created_at' => ['$gte' => $startUtc, '$lte' => $endUtc],
                            'cash'       => ['$gt' => 0],
                        ],
                        ['sort' => ['_id' => -1]]
                    );

                foreach ($cursor as $doc) {
                    $historyData[] = (array) $doc;
                    // dd($historyData);
                }
            } elseif ($option === 'withdraw') {
                if ($dateFrom || $dateTo) {
                    $startTime = $dateStart->copy()->startOfDay()->timestamp;
                    $endTime   = $dateEnd->copy()->endOfDay()->timestamp;
                } else {
                    $startTime = strtotime(date('Y-m-d 00:00')) - 60 * 24 * 60 * 60;
                    $endTime   = time();
                }

                $key = md5(sprintf(
                    '%s%s%s%s%s%s',
                    __CLASS__,
                    __METHOD__,
                    $startTime,
                    $endTime,
                    $userId,
                    $request->query('page', 0)
                ));

                $items = Cache::remember($key, 300, function () use ($startTime, $endTime, $userId) {
                    return WalletLog::query()
                        ->where('wallet_log_uid', $userId)
                        ->where('wallet_log_status', 0)
                        ->where('wallet_log_amount', '<>', 0)
                        ->whereBetween('create_time', [$startTime, $endTime])
                        ->orderBy('wallet_log_id', 'desc')
                        ->simplePaginate(10);
                });

                $historyData = $items->items();
            }

            return response()->json([
                'code'    => 200,
                'history' => $historyData,
                'filters' => [
                    'game_id'    => $gameId,
                    'option'     => $option,
                    'date_start' => $dateStart->format('d/m/Y'),
                    'date_end'   => $dateEnd->format('d/m/Y'),
                ],
                'user_id' => $userId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'code'  => 500,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // public function purchasePayment(Request $request, $alias)
    // {
    //     try {
    //         $token = $request->bearerToken();
    //         $ip = $request->getClientIp();

    //         $params = [
    //             'user_token' => $token,
    //             'game_id' => $request->input('game_id'),
    //             'amount' => $request->input('amount'),
    //             'username' => $request->input('username'),
    //             'character_id' => $request->input('character_id'),
    //             'server_id' => $request->input('server_id'),
    //             'item_id'             => $request->get('item_id'),
    //             'vgp_id'              => $request->get('vgp_id'),
    //             'dpt_id'              => $request->get('dpt_id'),
    //             'timestamp'           => time(),
    //             'payment_token'       => $request->get('payment_token'),
    //             'md5'                 => null,
    //             'serial' => $request->input('serial'),
    //             'code' => $request->input('code'),
    //             'ip' => $ip,
    //         ];

    //         switch ($alias) {
    //             case 'atm':
    //                 $params['bank'] = 99030;
    //                 $endpoint = 'bank/deposit';
    //                 break;
    //             case 'qr':
    //                 $params['bank'] = 99999;
    //                 $endpoint = 'bank/deposit';
    //                 break;
    //             case 'visa':
    //                 $params['bank'] = 99031;
    //                 $endpoint = 'bank/deposit';
    //                 break;
    //             case 'momo':
    //                 $params['bank'] = 'momo';
    //                 $endpoint = 'wallet/charge';
    //                 break;
    //             case 'vgp':
    //                 $endpoint = 'card/vgp';
    //                 break;
    //             case 'zalo':
    //                 $endpoint = 'bank/zalopay-pay';
    //                 break;
    //             case 'google':
    //                 $endpoint = 'bank/google-pay';
    //                 break;
    //             case 'samsung':
    //                 $endpoint = 'bank/samsung-pay';
    //                 break;
    //             case 'apple':
    //                 $endpoint = 'bank/apple-pay';
    //                 break;
    //             default:
    //                 return response()->json([
    //                     'error' => true,
    //                     'message' => 'Loại thanh toán không hợp lệ.',
    //                 ], 400);
    //         }

    //         $response = ApiHelper::processPayment($endpoint, $params);

    //         return response()->json([
    //             'error' => false,
    //             'message' => 'Lấy dữ liệu thanh toán thành công.',
    //             'data' => $response,
    //         ]);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'error' => true,
    //             'message' => 'Lỗi khi xử lý thanh toán: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }

    // public function purchasePayment(Request $request, string $alias)
    // {
    //     $token = $request->bearerToken();

    //     if (empty($token)) {
    //         return response()->json([
    //             'error'   => true,
    //             'message' => 'Phiên đăng nhập đã hết hạn. Xin vui lòng đăng nhập lại.',
    //         ], 401);
    //     }

    //     $ip = $request->getClientIp();

    //     [$endpoint, $aliasExtras] = $this->mapAliasToEndpoint($alias);
    //     if (!$endpoint) {
    //         return response()->json([
    //             'error'   => true,
    //             'message' => 'Loại thanh toán không hợp lệ.',
    //         ], 400);
    //     }

    //     $params = [
    //         'user_token' => $token,
    //         'amount'     => $request->input('amount'),
    //         'serial'     => $request->input('serial'),
    //         'code'       => $request->input('code'),
    //         'ip'         => $ip,
    //     ] + $aliasExtras;

    //     $params += [
    //         'game_id'      => $request->input('game_id'),
    //         'vgp_id'       => $request->input('vgp_id'),
    //         'server_id'    => $request->input('server_id'),
    //         'character_id' => $request->input('character_id'),
    //         'item_id'      => $request->input('item_id'),
    //         'dpt_id'       => $request->input('dpt_id'),
    //         'timestamp'    => $request->input('timestamp') ?: time(),
    //         'payment_token' => $request->input('payment_token'),
    //         'partner_token' => $request->input('partner_token'),
    //         'md5'           => $request->input('md5'),
    //     ];

    //     try {
    //         $basicRules = [
    //             'user_token' => 'required',
    //         ];

    //         if (!empty($params['item_id'])) {
    //             $basicRules += [
    //                 'game_id'      => 'required|integer',
    //                 'vgp_id'       => 'required',
    //                 'item_id'      => 'required|string',
    //                 'server_id'      => 'required|string',
    //                 'character_id'      => 'required|string',
    //             ];
    //         }

    //         $validator = Validator::make($params, $basicRules);
    //         if ($validator->fails()) {
    //             return response()->json([
    //                 'error'   => true,
    //                 'message' => $validator->errors()->first(),
    //             ], 422);
    //         }

    //         if (!empty($params['item_id']) && !str_contains(strtolower($params['item_id']), 'vxu')) {
    //             $gameId   = (int) $params['game_id'];
    //             $serverId = $params['server_id'];
    //             $charId   = $params['character_id'];
    //             $itemId   = $params['item_id'];
    //             $vgpId    = $params['vgp_id'];
    //             $dptId    = $params['dpt_id'] ?? null;
    //             $partnerToken = $params['partner_token'];
    //             $timestamp = (int) $params['timestamp'];

    //             if ($gameId === 84) {
    //                 Log::error('Lỗi mua gói webgame ' . $gameId, [
    //                     'alias'        => $alias,
    //                     'endpoint'     => $endpoint,
    //                     'params'       => $params,
    //                 ]);
    //                 return response()->json([
    //                     'error'   => true,
    //                     'code'    => 840,
    //                     'message' => 'Thanh toán thất bại.  Xin vui lòng thanh toán lại',
    //                 ], 422);
    //             }

    //             $games = collect(config('games', []));
    //             $key   = sprintf('_%s', $gameId);
    //             $game  = $games[$key] ?? null;

    //             if (!$game || empty($game['payment_token_verify'])) {
    //                 Log::error('purchasePayment: thiếu payment_token_verify', [
    //                     'game_id' => $gameId,
    //                     'alias'   => $alias,
    //                 ]);
    //                 return response()->json([
    //                     'error'   => true,
    //                     'message' => 'Không tìm thấy thông tin game (payment_token_verify).',
    //                 ], 422);
    //             }

    //             $verify = $game['payment_token_verify'];

    //             $ticketMd5 = $params['md5'] ?? null;
    //             if (empty($ticketMd5)) {
    //                 if (!empty($dptId)) {
    //                     $ticketMd5 = md5(
    //                         $verify
    //                             . 'vgpid'     . $vgpId
    //                             . 'server_id' . $serverId
    //                             . 'role_id'   . $charId
    //                             . 'item_id'   . $itemId
    //                             . 'dpt_id'    . $dptId
    //                             . 'tstamp'    . $timestamp
    //                     );
    //                 } else {
    //                     $ticketMd5 = md5(
    //                         $verify
    //                             . 'vgpid'     . $vgpId
    //                             . 'server_id' . $serverId
    //                             . 'role_id'   . $charId
    //                             . 'item_id'   . $itemId
    //                             . 'tstamp'    . $timestamp
    //                     );
    //                 }
    //                 $params['md5'] = $ticketMd5;
    //             }

    //             $partnerToken = $params['partner_token'] ?? $params['payment_token'] ?? null;
    //             if (empty($partnerToken)) {
    //                 $tokenRes = ApiHelper::get_payment_token_retrieval(
    //                     $gameId,
    //                     $game['payment_token_retrieval']['url'] ?? null,
    //                     $vgpId,
    //                     $serverId,
    //                     $charId,
    //                     $itemId,
    //                     $ticketMd5,
    //                     $dptId
    //                 );

    //                 // dd($tokenRes);
    //                 if (!is_array($tokenRes) || empty($tokenRes['token'])) {
    //                     Log::error('purchasePayment: không lấy được partner_token', [
    //                         'alias'      => $alias,
    //                         'endpoint'   => $endpoint,
    //                         'params'     => $params,
    //                         'token_res'  => $tokenRes,
    //                     ]);

    //                     $msg = is_array($tokenRes) && !empty($tokenRes['code'])
    //                         ? $tokenRes['code']
    //                         : 'Lỗi lấy token đối tác';
    //                     return response()->json([
    //                         'error'   => true,
    //                         'message' => $msg,
    //                     ], 422);
    //                 }

    //                 $partnerToken = $tokenRes['token'];
    //                 $params['partner_token'] = $partnerToken;
    //             }

    //             $params += [
    //                 'ticket'    => $params['md5'],
    //                 'timestamp' => $timestamp,
    //             ];
    //         }

    //         // dd($params);
    //         $response = ApiHelper::processPayment($endpoint, $params);

    //         return response()->json([
    //             'error'   => (bool)($response['error'] ?? false),
    //             'message' => $response['message'] ?? 'Lấy dữ liệu thanh toán thành công.',
    //             'data'    => $response['data'] ?? $response,
    //         ]);
    //     } catch (\Throwable $e) {
    //         Log::error('purchasePayment exception', [
    //             'alias'  => $alias,
    //             'error'  => $e->getMessage(),
    //             'trace'  => $e->getTraceAsString(),
    //         ]);

    //         if (str_contains($e->getMessage(), 'Vxu không đủ')) {
    //             return response()->json([
    //                 'error'   => true,
    //                 'message' => $e->getMessage(),
    //             ], 422);
    //         }

    //         return response()->json([
    //             'error'   => true,
    //             'message' => 'Lỗi khi xử lý thanh toán: ' . $e->getMessage(),
    //         ], 500);
    //     }
    // }

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
