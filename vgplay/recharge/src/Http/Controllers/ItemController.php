<?php

namespace Vgplay\Recharge\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Vgplay\Recharge\Models\Item;
use Vgplay\Games\Traits\FindGame;
use App\Http\Controllers\Controller;
use Vgplay\Recharge\Models\PurchaseHistory;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ItemController extends Controller
{
    use FindGame;

    /**
     * Trang Inertia: danh sách item theo game.
     * Nếu không tìm thấy game -> trả JSON 404.
     */
    public function index(Request $request, string $game)
    {
        try {
            $game = $this->findGame($game);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Không tìm thấy nội dung.'], 404);
        }

        $vgpId = $request->integer('vgp_id') ?: null;

        $items = $game->items()
            ->wherePivot('is_active', true)
            ->where('items.is_active', true)
            ->orderBy('items.type')
            ->orderBy('items.sort')
            ->get([
                'items.id',
                'items.type',
                'items.name',
                'items.code',
                'items.image',
                'items.unit',
                'items.description',
                'items.vxu_amount',
                'items.discount_percent',
                'items.limit_per_user',
                'items.allow_multiple_per_order',
                'items.tier',
                'items.requires_min_tier',
            ]);

        $locks = [];
        $boughtCount = [];

        // dd($items);
        if ($vgpId) {
            $paidItemIds = PurchaseHistory::query()
                ->where('vgp_id', $vgpId)
                ->where('game_id', $game->game_id)
                ->where('status', 'paid')
                ->pluck('item_id');

            $paidItems = Item::whereIn('id', $paidItemIds)->get(['id', 'type', 'tier']);

            $maxTierByType = [];
            foreach ($paidItems as $pi) {
                $maxTierByType[$pi->type] = max($maxTierByType[$pi->type] ?? 0, (int) $pi->tier);
            }

            $counts = PurchaseHistory::query()
                ->selectRaw('item_id, COUNT(*) as cnt')
                ->where('vgp_id', $vgpId)
                ->where('game_id', $game->game_id)
                ->where('status', 'paid')
                ->groupBy('item_id')
                ->pluck('cnt', 'item_id')
                ->toArray();

            foreach ($items as $it) {
                $isLocked = false;
                if ($it->type !== 'vxu') {
                    $maxTier = (int) ($maxTierByType[$it->type] ?? 0);
                    $isLocked = $it->requires_min_tier > 0 && ($maxTier < $it->requires_min_tier);
                }
                $locks[$it->id] = $isLocked;
                $boughtCount[$it->id] = (int) ($counts[$it->id] ?? 0);
            }
        }


        return Inertia::render('Pages/GamePage', [
            'game' => [
                'game_id' => $game->game_id,
                'alias'   => $game->alias,
                'name'    => $game->name,
            ],
            'items' => $items->map(fn($it) => [
                'id'                       => $it->id,
                'type'                     => $it->type,
                'name'                     => $it->name,
                'code'                     => $it->code,
                'image'                    => $it->image,
                'unit'                     => $it->unit,
                'description'              => $it->description,
                'vxu_amount'              => (int) $it->vxu_amount,
                'discount_percent'         => (float) $it->discount_percent,
                'limit_per_user'           => (int) $it->limit_per_user,
                'allow_multiple_per_order' => (bool) $it->allow_multiple_per_order,
                'tier'                     => (int) $it->tier,
                'requires_min_tier'        => (int) $it->requires_min_tier,
                'locked'                   => $vgpId ? (bool)($locks[$it->id] ?? false) : false,
                'bought_count'             => $vgpId ? (int)($boughtCount[$it->id] ?? 0) : 0,
            ]),
            'user' => [
                'vgp_id' => $vgpId,
            ],
        ]);
    }

    /**
     * Lấy chi tiết 1 gói (JSON). Không tìm thấy -> JSON 404.
     */
    public function show(Request $request, string $game, int $item)
    {
        try {
            $game = $this->findGame($game);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Không tìm thấy nội dung.'], 404);
        }

        $record = Item::query()
            ->whereHas(
                'games',
                fn($q) => $q
                    ->where('games.game_id', $game->game_id)
                    ->where('game_item.is_active', true)
            )
            ->where('items.is_active', true)
            ->with(['details' => fn($q) => $q->orderBy('sort')])
            ->find($item);

        if (! $record) {
            return response()->json(['message' => 'Không tìm thấy nội dung.'], 404);
        }

        return response()->json([
            'id'                => $record->id,
            'name'              => $record->name,
            'code'              => $record->code,
            'image'             => $record->image,
            'unit'              => $record->unit,
            'vxu_amount'       => (int) $record->vxu_amount,
            'tier'              => (int) $record->tier,
            'requires_min_tier' => (int) $record->requires_min_tier,
            'details'           => $record->details->map(fn($d) => [
                'name'        => $d->name,
                'image'       => $d->image,
                'description' => $d->description,
                'quantity'    => (int) $d->quantity,
            ])->all(),
        ]);
    }
}
