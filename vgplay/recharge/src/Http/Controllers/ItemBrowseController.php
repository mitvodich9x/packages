<?php

namespace Vgplay\Recharge\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Vgplay\Recharge\Services\ItemCatalog;

class ItemBrowseController extends Controller
{
    public function index(Request $request, int $game)
    {
        dd(Auth::check());
        $user = Auth::user();
        dd(1);
        $userId = $request->user()->id;
        $unit   = $request->query('unit', 'vxu');

        return response()->json(
            ItemCatalog::visibleItemsForUser($userId, $game, $unit)
        );
    }
}
