<?php

namespace Vgplay\Recharge\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Vgplay\Recharge\Models\Item;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Vgplay\Recharge\Services\ItemCatalog;

class ItemController extends Controller
{
    public function index(Request $request, int $game)
    {
        $unit = $request->query('unit', 'vxu');
        $unitAlias = Item::toAlias($request->query('unit', 'vxu'));
        $items = ItemCatalog::visibleItemsForUser($request->user()->id ?? null, $game, $unitAlias)
            ->map(function ($item) {
                return [
                    'id'            => $item->id,
                    'game_id'       => $item->game_id,
                    'unit'          => $item->unit,
                    'unit_alias'    => $item->unit_alias,
                    'code'          => $item->code,
                    'name'          => $item->name,
                    'image'         => $item->image,
                    'price'         => (int) $item->price,
                    'quantity'      => (int) $item->quantity,
                    'tier'          => (int) $item->tier,
                    'is_one_time'   => (bool) $item->is_one_time,
                    'check_purchase' => (bool) $item->check_purchase,
                    'rules'         => $item->rules ?? [],
                ];
            })
            ->values();

        return response()->json([
            'game_id' => $game,
            'unit'    => $unit,
            'unit_alias' => $unitAlias,
            'items'   => $items,
        ]);
    }

    public function unit(Request $request, int $game)
    {
        $onlyActive = !filter_var($request->query('active', '1'), FILTER_VALIDATE_BOOLEAN) ? false : true;

        $units = ItemCatalog::unitsForGame($game, $onlyActive);

        return response()->json([
            'game_id' => $game,
            'active'  => $onlyActive,
            'units'   => $units,
        ]);
    }
}
