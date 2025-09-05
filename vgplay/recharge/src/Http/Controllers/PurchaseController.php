<?php

namespace Vgplay\Recharge\Http\Controllers;

use Illuminate\Http\Request;
use Vgplay\Games\Traits\FindGame;
use App\Http\Controllers\Controller;
use Vgplay\Recharge\Models\PurchaseHistory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PurchaseController extends Controller
{
    use FindGame;

    /**
     * Lịch sử đã thanh toán (JSON).
     * Không tìm thấy game -> JSON 404.
     */
    public function history(Request $request, string $game, int $vgpId)
    {
        try {
            $game = $this->findGame($game);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Không tìm thấy nội dung.'], 404);
        }

        $rows = PurchaseHistory::query()
            ->where('vgp_id', $vgpId)
            ->where('game_id', $game->game_id)
            ->where('status', 'paid')
            ->orderByDesc('id')
            ->limit(100)
            ->get([
                'id',
                'item_id',
                'payment_method_id',
                'quantity',
                'vxu_amount',
                'price_vnd',
                'status',
                'created_at',
            ]);

        return response()->json(['items' => $rows]);
    }
}
