<?php

namespace Vgplay\Admins\Console\Commands;

use Illuminate\Console\Command;

class InstallMitAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Khi chạy lệnh: php artisan mit-admin:install
     *
     * @var string
     */
    protected $signature = 'mit-admin:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cài đặt gói Mit Admin: publish config, views, migrations và seeders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu cài đặt gói Mit Admin...');

        // Publish cấu hình
        $this->info('Đang publish file cấu hình...');
        $this->call('vendor:publish', [
            '--tag' => 'admins-config',
            '--force' => true,
        ]);

        // Chạy migrate
        $this->info('▶ Làm mới migrations của gói MitAdmin...');
        $this->call('migrate:refresh', ['--path' => 'vendor/vgplay/admins/src/database/migrations']);

        // Chạy Seeder
        $this->info('▶ Chạy Seeder...');
        $this->call('db:seed', ['--class' => 'Vgplay\Admins\Database\Seeders\AdminDatabaseSeeder']);

        $this->info('Gói Mit Admin đã được cài đặt thành công!');
    }
}
