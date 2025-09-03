<?php

namespace Vgplay\Auth\Console\Commands;

use Illuminate\Console\Command;

class InstallVgpAuthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Khi chạy lệnh: php artisan mit-admin:install
     *
     * @var string
     */
    protected $signature = 'vgp-auth:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cài đặt gói VGP Auth: publish config, views, migrations và seeders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Bắt đầu cài đặt gói Vgp Auth ...');

        // Chạy migrate
        $this->info('▶ Làm mới migrations của gói Vgp Auth...');
        $this->call('migrate:refresh', ['--path' => 'vendor/vgplay/auth/src/database/migrations']);

        $this->info('Gói Vgp Auth đã được cài đặt thành công!');
    }
}
