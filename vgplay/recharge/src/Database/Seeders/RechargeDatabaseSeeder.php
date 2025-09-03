<?php

namespace Vgplay\Recharges\Database\Seeders;

use Illuminate\Database\Seeder;

class RechargeDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RechargeSeeder::class);
    }
}
