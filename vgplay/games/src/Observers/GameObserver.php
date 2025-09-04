<?php

namespace Vgplay\Games\Observers;

use Vgplay\Games\Models\Game;
use Vgplay\Games\Services\GameService;

class GameObserver
{
    public bool $afterCommit = true;

    public function saved(Game $game): void
    {
        app(GameService::class)->syncAll();
    }

    public function deleted(Game $game): void
    {
        app(GameService::class)->syncAll();
    }

    public function restored(Game $game): void
    {
        app(GameService::class)->syncAll();
    }
}
