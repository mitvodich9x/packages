<?php

namespace Vgplay\Settings\Console\Commands;

use Illuminate\Console\Command;
use Vgplay\Settings\Services\SettingService;

class InstallVgpSettingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Khi chạy lệnh: php artisan mit-admin:install
     *
     * @var string
     */
    protected $signature = 'vgp-setting:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cài đặt gói VGP Setting: publish config, views, migrations và seeders';

    /**
     * Execute the console command.
     */
    public function handle(SettingService $service)
    {
        $this->info('Bắt đầu cài đặt gói VGP Setting ...');

        // Chạy migrate
        $this->info('▶ Làm mới migrations của gói VGP Setting...');
        $this->call('migrate:refresh', ['--path' => 'vendor/vgplay/settings/src/database/migrations']);

        // Chạy Seeder
        $this->info('▶ Chạy Seeder...');
        $this->call('db:seed', ['--class' => 'Vgplay\Settings\Database\Seeders\SettingDatabaseSeeder']);

        // Sync cache
        $this->info('🔄 Đang đồng bộ lại cache settings...');
        try {
            $service->syncAll();
            $this->info('✅ Đồng bộ settings thành công!');
            $this->info('🎉 Gói VGP Setting đã được cài đặt thành công!');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('❌ Lỗi khi sync settings: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
