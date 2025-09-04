<?php

namespace Vgplay\Recharge\Database\Seeders;

use Illuminate\Database\Seeder;
use Vgplay\Recharge\Models\Item;
use Illuminate\Support\Facades\DB;

class VxuItemSeeder extends Seeder
{
    public function run(): void
    {
        $games = DB::table('games')->select('game_id')->get();
        if ($games->isEmpty()) return;

        $list = [200, 500, 1000, 2000, 5000, 10000, 20000, 50000, 100000, 200000, 500000];
        $now  = now();

        foreach ($games as $g) {
            $rows = [];
            foreach ($list as $vxu) {
                $rows[] = [
                    'game_id'        => (int) $g->game_id,
                    'unit'           => 'vxu',
                    'unit_alias'     => 'vxu',
                    'code'           => null,
                    'name'           => "Gói {$vxu} Vxu",
                    'image'          => null,
                    'price'          => $vxu,
                    'quantity'       => 1,
                    'rules'          => null,
                    'is_one_time'    => 0,
                    'check_purchase' => 0,
                    'tier'           => $vxu <= 1000 ? 1 : ($vxu <= 10000 ? 2 : 3),
                    'is_active'      => 1,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }

            DB::table('items')->upsert(
                $rows,
                ['game_id', 'unit', 'unit_alias', 'code'],
                ['name', 'image', 'price', 'quantity', 'rules', 'is_one_time', 'check_purchase', 'tier', 'is_active', 'updated_at']
            );
        }
    }
}
