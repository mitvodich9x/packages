<?php

namespace Vgplay\Recharge\Database\Seeders;

use Illuminate\Database\Seeder;

class RechargeDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PaymentSeeder::class);
        $this->call(VxuItemSeeder::class);
    }
}
