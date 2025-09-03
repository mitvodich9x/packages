<?php

namespace Vgplay\Settings\Database\Seeders;

use Illuminate\Database\Seeder;
use Vgplay\Settings\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $generalSettings = collect([
            [
                'key' => 'site_content_footer',
                'display_name' => 'Footer',
                'group' => 'site',
                'type' => 'General',
                'order' => 1,
                'value' => json_encode([
                    'logo'          => '',
                    'company'       => 'Công ty cổ phần VGP',
                    'hotline'       => '1900 636 521',
                    'email'         => 'hotro@vgpjsc.vn',
                    'content_author' => 'Hoàng Thái Sơn',
                    'license'       => 'Giấy phép G1 số: 318/GP-PTTH&TTDT cấp ngày 15/7/2025',
                    'address'       => 'Tầng 4 tòa nhà Greenpark Tower, 33 Dương Đình Nghệ, Yên Hòa, Hà Nội.'
                ], JSON_UNESCAPED_UNICODE),
            ],
            [
                'key' => 'site_cms_only_allow_ip_in_white_list',
                'display_name' => 'Hạn chế IP truy cập trang quản trị',
                'group' => 'site',
                'type' => 'Access Control',
                'order' => 2,
                'value' => json_encode(true),
            ],
            [
                'key' => 'site_user_only_allow_ip_in_white_list',
                'display_name' => 'Hạn chế IP truy cập website người dùng',
                'group' => 'site',
                'type' => 'Access Control',
                'order' => 3,
                'value' => json_encode(true),
            ],
            [
                'key' => 'site_white_list_ip',
                'display_name' => 'Danh sách IP được phép truy cập',
                'group' => 'site',
                'type' => 'Access Control',
                'order' => 4,
                'value' => json_encode([
                    '127.0.0.1',
                    '1.52.48.3',
                    '14.248.83.83',
                    '14.248.83.92',
                    '118.70.117.48'
                ], JSON_UNESCAPED_UNICODE),
            ],
            [
                'key' => 'site_white_list_url_path',
                'display_name' => 'Danh sách URL Path không bị hạn chế',
                'group' => 'site',
                'type' => 'Access Control',
                'order' => 5,
                'value' => json_encode(['dieu-khoan'], JSON_UNESCAPED_UNICODE),
            ],
            [
                'key' => 'site_white_list_user',
                'display_name' => 'Danh sách User được phép buff',
                'group' => 'site',
                'type' => 'Access Control',
                'order' => 6,
                'value' => json_encode([
                    '6272444',
                    '6924016',
                    '17343268'
                ], JSON_UNESCAPED_UNICODE),
            ],
        ]);

        $paymentSettings = collect([
            [
                'key' => 'payment_ip_limit_by_game',
                'display_name' => 'Hạn chế IP theo game và page',
                'group' => 'payment',
                'type' => 'Payment',
                'order' => 7,
                'value' => json_encode([
                    ['game_id' => 37, 'page' => 'items', 'access' => true],
                    ['game_id' => 42, 'page' => 'items', 'access' => false],
                ], JSON_UNESCAPED_UNICODE),
            ],
            [
                'key' => 'payment_site_sliders',
                'display_name' => 'Hạn chế IP theo game và page',
                'group' => 'payment',
                'type' => 'Payment',
                'order' => 8,
                'value' => json_encode([
                    ['https://img.vgplay.vn/vgplay/images/698a5c9a35fe55c88c9d518d6fa3fb64.webp'],  
                ], JSON_UNESCAPED_UNICODE),
            ],
        ]);

        $settings = $generalSettings->merge($paymentSettings);

        Setting::withoutEvents(function () use ($settings) {
            foreach ($settings as $setting) {
                Setting::updateOrCreate(
                    ['key' => $setting['key']],
                    collect($setting)->except('key')->toArray()
                );
            }
        });

        $this->command->info('✅ SettingSeeder đã chạy xong (1 dạng JSON duy nhất)!');
    }
}
