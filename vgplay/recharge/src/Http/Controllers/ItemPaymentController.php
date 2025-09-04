<?php

namespace Vgplay\Recharge\Http\Controllers;

use Illuminate\Http\Request;
use Vgplay\Recharge\Models\Item;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Vgplay\Recharge\Models\PaymentConfig;
use Vgplay\Recharge\Services\PaymentSelector;

class ItemPaymentController extends Controller
{

    // public function index(Request $request, int $game, int $item)
    // {
    //     $user = $request->user();
    //     if (!$user) {
    //         return response()->json(['message' => 'Unauthenticated.'], 401);
    //     }

    //     $record = Item::query()->find($item);
    //     if (!$record) {
    //         return response()->json(['message' => 'Item not found.'], 404);
    //     }
    //     if ((int) $record->game_id !== (int) $game) {
    //         // Với thay đổi mới: mọi unit (kể cả vxu) đều phải thuộc đúng game
    //         return response()->json(['message' => 'Item does not belong to this game.'], 404);
    //     }

    //     $methods = PaymentSelector::methodsForItem($game, $record)
    //         ->map(function ($m) {
    //             return [
    //                 'id'          => (int) $m->id,
    //                 'alias'       => $m->alias,
    //                 'name'        => $m->name,
    //                 'image'       => $m->image,
    //                 'price_point' => (int) $m->price_point,  // Vxu
    //                 'promotion'   => (float) $m->promotion,  // hệ số, vd 1.05
    //                 'discount'    => (float) $m->discount,   // %
    //                 'amount_vnd'  => (float) $m->amount_vnd, // VND = Vxu*100*promotion
    //             ];
    //         })
    //         ->values();

    //     return response()->json([
    //         'game_id' => (int) $game,
    //         'item'    => [
    //             'id'    => (int) $record->id,
    //             'name'  => $record->name,
    //             'unit'  => $record->unit,
    //             'price' => (int) $record->price,
    //         ],
    //         'methods' => $methods,
    //     ]);
    // }

    public function index(Request $request, int $game, int $itemId)
    {
        // dd($game, $itemId);
        $item = Item::findOrFail($itemId);
        if ($item->game_id !== $game) abort(404);

        $methods = PaymentSelector::methodsForItem($game, $item);

        return response()->json([
            'game_id' => $game,
            'item'    => ['id' => $item->id, 'name' => $item->name, 'unit' => $item->unit, 'price' => (int)$item->price],
            'methods' => $methods,
        ]);
    }

    public function methods(Request $request, int $game, int $item)
    {

        // 1) Lấy item & kiểm tra thuộc đúng game
        $record = Item::query()
            ->select(['id', 'game_id', 'unit', 'price', 'name'])
            ->findOrFail($item);

        if ((int) $record->game_id !== (int) $game) {
            return response()->json(['message' => 'Item does not belong to this game.'], 404);
        }

        $price = (int) $record->price;

        $configs = PaymentConfig::query()->where('price', $price)->get();
       
        // // 2) Lấy phương thức: mệnh giá đúng (payment_configs.price = item.price)
        // //    và đang bật cho game (game_payment.is_active = 1)
        // $q = DB::table('payment_configs as pc')
        //     ->join('game_payment as gp', function ($j) use ($game) {
        //         $j->on('gp.payment_id', '=', 'pc.payment_id')
        //             ->where('gp.game_id', '=', $game)
        //             ->where('gp.is_active', '=', 1);
        //     })
        //     ->join('payments as p', 'p.id', '=', 'pc.payment_id')
        //     ->where('pc.is_active', 1)
        //     ->where('p.is_active', 1)
        //     ->where('pc.price', $price);

        // // Nếu gói là Vxu thì ẩn chính payment alias 'vxu'
        // if (strtolower($record->unit) === 'vxu') {
        //     $x = $q->where('p.alias', '!=', 'vxu');
        // }

        // $methods = $q->orderByRaw('COALESCE(gp.sort_order, p.sort) asc')
        //     ->select([
        //         'p.id',
        //         'p.alias',
        //         'p.name',
        //         'p.image',
        //         'p.description',
        //         'pc.price as price_point',
        //         DB::raw('COALESCE(gp.promotion, pc.promotion, p.base_promotion) as promotion'),
        //         DB::raw('COALESCE(gp.discount, p.base_discount) as discount'),
        //         DB::raw('CAST(ROUND(pc.price * 100 * COALESCE(gp.promotion, pc.promotion, p.base_promotion), 0) AS UNSIGNED) as amount_vnd'),
        //     ])
        //     ->get();

        // return response()->json([
        //     'game_id' => (int) $game,
        //     'item' => [
        //         'id'    => (int) $record->id,
        //         'name'  => $record->name,
        //         'unit'  => $record->unit,
        //         'price' => $price,
        //     ],
        //     'methods' => $methods,
        // ]);
    }
}
