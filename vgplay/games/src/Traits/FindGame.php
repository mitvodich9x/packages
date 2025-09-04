<?php

namespace Vgplay\Games\Traits;

use Vgplay\Games\Models\Game;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait FindGame
{
    protected function findGame(string|int $gameParam): Game
    {
        if (is_numeric($gameParam)) {
            $game = Game::where('game_id', (int)$gameParam)->first();
        } else {
            $game = Game::where('alias', $gameParam)->first();
        }
        if (!$game) throw new ModelNotFoundException('Game not found');
        return $game;
    }
}
