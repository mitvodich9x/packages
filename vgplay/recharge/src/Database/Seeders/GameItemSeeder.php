<?php

namespace Vgplay\Recharge\Database\Seeders;

use Vgplay\Games\Models\Game;
use Vgplay\Recharge\Models\Item;
use Illuminate\Database\Seeder;

class GameItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = Item::where('type', 'vxu')->pluck('id')->toArray();
        $games = Game::all();

        foreach ($games as $game) {
            $game->items()->syncWithoutDetaching($items);
        }

        $items88 = Item::where('code', 'like', '%g88%')->pluck('id')->toArray();
        $items72 = Item::where('code', 'like', '%mongkiem%')->pluck('id')->toArray();
        $items79 = Item::where('code', 'like', '%langvan%')->pluck('id')->toArray();
        $items85 = Item::where('code', 'like', '%longhon%')->pluck('id')->toArray();

        $game88 = Game::where('game_id', 88)->first();
        if ($game88) {
            $game88->items()->syncWithoutDetaching($items88);
        }

        $game79 = Game::where('game_id', 79)->first();
        if ($game79) {
            $game79->items()->syncWithoutDetaching($items79);
        }
        $game72 = Game::where('game_id', 72)->first();
        if ($game72) {
            $game72->items()->syncWithoutDetaching($items72);
        }
        $game85 = Game::where('game_id', 85)->first();
        if ($game85) {
            $game85->items()->syncWithoutDetaching($items85);
        }
    }
}
