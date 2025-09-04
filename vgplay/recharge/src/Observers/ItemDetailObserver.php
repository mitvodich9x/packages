<?php

namespace Vgplay\Recharge\Observers;

use Vgplay\Recharge\Models\ItemDetail;
use Vgplay\Recharge\Services\ItemService;

class ItemDetailObserver
{
    public bool $afterCommit = true;

    public function saved(ItemDetail $detail): void
    {
        $svc = app(ItemService::class);
        $item = $detail->item()->with('games:games.game_id')->first();
        if (!$item) return;

        foreach ($item->games as $game) {
            $svc->forgetByGame((int)$game->game_id, (int)$item->id);
        }
    }

    public function deleted(ItemDetail $detail): void
    {
        $this->saved($detail);
    }

    public function restored(ItemDetail $detail): void
    {
        $this->saved($detail);
    }
}
