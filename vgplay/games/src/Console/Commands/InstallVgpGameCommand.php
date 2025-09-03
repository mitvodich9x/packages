<?php

namespace Vgplay\Games\Console\Commands;

use Illuminate\Console\Command;
use Vgplay\Games\Services\GameService;

class InstallVgpGameCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Khi chạy lệnh: php artisan mit-admin:install
     *
     * @var string
     */
    protected $signature = 'vgp-game:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cài đặt gói VGP Game: publish config, views, migrations và seeders';

    /**
     * Execute the console command.
     */
    public function handle(GameService $service)
    {
        $this->info('Bắt đầu cài đặt gói Vgp Game ...');

        // Chạy migrate
        $this->info('▶ Làm mới migrations của gói MitAdmin...');
        $this->call('migrate:refresh', ['--path' => 'vendor/vgplay/games/src/database/migrations']);

        // Chạy Seeder
        $this->info('▶ Chạy Seeder...');
        $this->call('db:seed', ['--class' => 'Vgplay\Games\Database\Seeders\GameDatabaseSeeder']);

        try {
            $service->syncAll();
            $this->info('✅ Đồng bộ games thành công!');
            $this->info('🎉 Gói VGP game đã được cài đặt thành công!');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('❌ Lỗi khi sync games: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
