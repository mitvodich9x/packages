<?php

namespace Vgplay\Recharges\Console\Commands;

use Illuminate\Console\Command;
use Vgplay\Recharges\Services\RechargeService;

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
    public function handle(RechargeService $service)
    {
        $this->info('Bắt đầu cài đặt gói Vgp Recharge ...');

        // Chạy migrate
        $this->info('▶ Làm mới migrations của gói MitAdmin...');
        $this->call('migrate:refresh', ['--path' => 'vendor/vgplay/recharges/src/database/migrations']);

        // Chạy Seeder
        $this->info('▶ Chạy Seeder...');
        $this->call('db:seed', ['--class' => 'Vgplay\Recharges\Database\Seeders\RechargeDatabaseSeeder']);

        try {
            $service->syncAll();
            $this->info('✅ Đồng bộ recharges thành công!');
            $this->info('🎉 Gói VGP recharge đã được cài đặt thành công!');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('❌ Lỗi khi sync recharges: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
