<?php

namespace Vgplay\Games\Database\Seeders;

use Illuminate\Database\Seeder;

class GameDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(GameSeeder::class);
    }
}
