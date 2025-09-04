<?php

namespace Vgplay\Recharge\Http\Controllers;

use Illuminate\Http\Request;
use Vgplay\Recharge\Models\Item;
use Vgplay\Games\Traits\FindGame;
use App\Http\Controllers\Controller;
use Vgplay\Recharge\Models\GamePaymentMethod;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PaymentController extends Controller
{
    use FindGame;

    /**
     * Danh sách phương thức nạp hợp lệ cho (game, item).
     * Không tìm thấy game/item -> JSON 404.
     */
    public function methods(Request $request, string $game, int $item)
    {
        try {
            $game = $this->findGame($game);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Không tìm thấy nội dung.'], 404);
        }

        $item = Item::query()
            ->select(['id', 'unit', 'vxu_amount', 'type', 'name'])
            ->whereHas(
                'games',
                fn($q) => $q
                    ->where('games.game_id', $game->game_id)
                    ->where('game_item.is_active', true)
            )
            ->where('is_active', true)
            ->find($item);

        if (! $item) {
            return response()->json(['message' => 'Không tìm thấy nội dung.'], 404);
        }

        $configs = GamePaymentMethod::query()
            ->with('method')
            ->where('game_id', $game->game_id)
            ->where('status', true)
            ->get();

        $methods = [];
        foreach ($configs as $cfg) {
            $pm = $cfg->method;
            if (! $pm || ! $pm->is_active) continue;

            $vnd = (int) round(((int)$item->vxu_amount) * (float) $cfg->exchange_rate);
            if ($cfg->max_amount > 0) {
                if ($vnd < (int)$cfg->min_amount || $vnd > (int)$cfg->max_amount) {
                    continue;
                }
            }

            $methods[] = [
                'id'             => (int) $pm->id,
                'alias'          => $pm->alias,
                'name'           => $pm->name,
                'image'          => $pm->image,
                'description'    => $pm->description,
                'promotion_rate' => (float) $pm->promotion_rate,
                'vnd'            => $vnd,
            ];
        }

        usort($methods, fn($a, $b) => $a['id'] <=> $b['id']);

        return response()->json([
            'game' => [
                'game_id' => $game->game_id,
                'alias'   => $game->alias,
                'name'    => $game->name,
            ],
            'item' => [
                'id'          => $item->id,
                'name'        => $item->name,
                'vxu_amount' => (int) $item->vxu_amount,
                'unit'        => $item->unit,
            ],
            'methods' => $methods,
        ]);
    }
}
