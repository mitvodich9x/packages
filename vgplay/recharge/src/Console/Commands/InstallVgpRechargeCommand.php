<?php

namespace Vgplay\Recharge\Console\Commands;

use Illuminate\Console\Command;
use Vgplay\Recharge\Services\Rechargeervice;

class InstallVgpRechargeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Khi chạy lệnh: php artisan mit-admin:install
     *
     * @var string
     */
    protected $signature = 'vgp-recharge:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cài đặt gói VGP Recharge: publish config, views, migrations và seeders';

    /**
     * Execute the console command.
     */
    // public function handle(Rechargeervice $service)
    public function handle()
    {
        $this->info('Bắt đầu cài đặt gói Vgp Recharge ...');

        // Chạy migrate
        $this->info('▶ Làm mới migrations của gói Vgp Recharge...');
        $this->call('migrate:refresh', ['--path' => 'vendor/vgplay/recharge/src/database/migrations']);

        // Chạy Seeder
        $this->info('▶ Chạy Seeder...');
        $this->call('db:seed', ['--class' => 'Vgplay\Recharge\Database\Seeders\RechargeDatabaseSeeder']);

        // try {
        //     $service->syncAll();
        //     $this->info('✅ Đồng bộ Recharge thành công!');
        //     $this->info('🎉 Gói VGP Recharge đã được cài đặt thành công!');
        //     return self::SUCCESS;
        // } catch (\Throwable $e) {
        //     $this->error('❌ Lỗi khi sync Recharge: ' . $e->getMessage());
        //     return self::FAILURE;
        // }
    }
}
