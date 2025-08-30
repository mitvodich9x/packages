<?php

namespace Vgplay\Admins\Database\Seeders;

use Illuminate\Database\Seeder;
use Vgplay\Admins\Models\Admin;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Tạo các tài khoản admin mẫu
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@vgplay.vn'],
            [
                'name'              => 'Admin',
                'email_verified_at' => now(),
                'password'          => bcrypt('vgp123123'),
                'remember_token'    => \Illuminate\Support\Str::random(10),
            ]
        );

        $dev = Admin::firstOrCreate(
            ['email' => 'developer@vgplay.vn'],
            [
                'name'              => 'Dev',
                'email_verified_at' => now(),
                'password'          => bcrypt('dev123123'),
                'remember_token'    => \Illuminate\Support\Str::random(10),
            ]
        );

        $admin->assignRole('Admin');
        $dev->assignRole('Dev');
    }
}
