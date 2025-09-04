<?php

namespace Vgplay\Recharge\Observers;

use Vgplay\Recharge\Models\Item;
use Vgplay\Recharge\Services\ItemService;

class ItemObserver
{
    public bool $afterCommit = true;

    public function saved(Item $item): void
    {
        // Invalidate theo tất cả game đang gắn item này
        $svc = app(ItemService::class);
        $item->loadMissing('games:games.game_id'); // chỉ lấy game_id
        foreach ($item->games as $game) {
            $svc->forgetByGame((int)$game->game_id, (int)$item->id);
        }
    }

    public function deleted(Item $item): void
    {
        $svc = app(ItemService::class);
        $item->loadMissing('games:games.game_id');
        foreach ($item->games as $game) {
            $svc->forgetByGame((int)$game->game_id, (int)$item->id);
        }
    }

    public function restored(Item $item): void
    {
        $this->saved($item);
    }
}
