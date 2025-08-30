<?php

namespace Vgplay\Admins\Database\Seeders;

use Illuminate\Database\Seeder;

class AdminDatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionRoleSeeder::class);
        $this->call(AdminSeeder::class);
    }
}
