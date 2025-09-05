<?php

namespace Vgplay\Recharge\Database\Seeders;

use Vgplay\Games\Models\Game;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentMethodSeeder extends Seeder
{
    public function run(): void
    {
        // === DỮ LIỆU CẤU HÌNH GỐC ===
        $methods = [
            ['id' => 1,  'name' => 'Thanh toán Vxu',        'alias' => 'vxu',     'image' => 'https://img.vgplay.vn/vgplay/images/1244220eca5a5de261835cc810401544.webp', 'description' => 'Thanh toán bằng Vxu'],
            ['id' => 2,  'name' => 'ATM/Visa Việt Nam',     'alias' => 'atm',     'image' => 'https://img.vgplay.vn/vgplay/images/2eaad86494c2adda4a13fabe5b5eb322.webp', 'description' => 'Thanh toán tiện dụng với thẻ ATM/Ngân hàng điện tử (iBanking)...'],
            ['id' => 3,  'name' => 'Visa Quốc tế',          'alias' => 'visa',    'image' => 'https://img.vgplay.vn/vgplay/images/fc63fa50a48ae2519254eaa9fd498f98.webp', 'description' => 'Thanh toán tiện dụng với thẻ Thanh toán quốc tế MasterCard/Visa...'],
            ['id' => 4,  'name' => 'Ứng Dụng Momo',         'alias' => 'momo',    'image' => 'https://img.vgplay.vn/vgplay/images/40439198aec27d81d592468c419fbcd6.webp', 'description' => 'Thanh toán sử dụng tài khoản Momo'],
            ['id' => 5,  'name' => 'Zalo Pay',              'alias' => 'zalo',    'image' => 'https://img.vgplay.vn/vgplay/images/32208e96788ab5f3e595bdbc44e2f04c.webp', 'description' => 'Thanh toán sử dụng tài khoản ZaloPay'],
            ['id' => 6,  'name' => 'Apple Pay',             'alias' => 'apple',   'image' => 'https://img.vgplay.vn/vgplay/images/dc49bca6c939fd2f1f5b834fe99a4379.webp', 'description' => 'Thanh toán bằng Apple Pay'],
            ['id' => 7,  'name' => 'Google Pay',            'alias' => 'google',  'image' => 'https://img.vgplay.vn/vgplay/images/2be26c8e869daee9da860506c686b8ae.webp', 'description' => 'Thanh toán bằng Google Pay'],
            ['id' => 8,  'name' => 'QR Ngân Hàng',          'alias' => 'qr',      'image' => 'https://img.vgplay.vn/vgplay/images/345d8628736ace5d025c7549fa0e3179.webp', 'description' => 'Thanh toán bằng QR Ngân Hàng'],
            ['id' => 9,  'name' => 'Samsung Pay',           'alias' => 'samsung', 'image' => 'https://img.vgplay.vn/vgplay/images/b8857e19f7f96224680b3911909140a6.webp', 'description' => 'Thanh toán bằng Samsung Pay'],
            ['id' => 10, 'name' => 'Thẻ VGP',               'alias' => 'vgp',     'image' => 'https://img.vgplay.vn/vgplay/images/1244220eca5a5de261835cc810401544.webp', 'description' => 'Thanh toán bằng Thẻ VGP'],
        ];

        $PROMO = [
            'momo' => 1.10,
            'zalo' => 1.05,
        ];

        $PROMO_DISPLAY_BY_GAME = [
            '*' => 0,   // mặc định cho các game khác
        ];

        $MAX_PRICE_BY_ALIAS = [
            'atm'     => 20000000, // hoặc lớn hơn
            'visa'    => 20000000,
            'qr'      => 20000000,
            'vgp'     => 500000,
            'momo'    => 1000000,
            'zalo'    => 1000000,
            'apple'   => 500000,
            'google'  => 500000,
            'samsung' => 500000,
            'vxu'     => 0, // không dùng để mua Vxu
        ];

        $now = now();

        // =============== B1) UPSERT payment_methods ==================
        $this->command?->info('Seeding payment_methods (upsert)...');

        // chuẩn hoá payload upsert
        $pmRows = array_map(function ($m) use ($PROMO, $now) {
            return [
                'id'              => (int) $m['id'],
                'alias'           => $m['alias'],
                'name'            => $m['name'],
                'image'           => $m['image'] ?? null,
                'description'     => $m['description'] ?? null,
                'promotion_rate'  => (float)($PROMO[$m['alias']] ?? 1.00),
                'is_active'       => true,
                'sort'            => (int) $m['id'],
                'created_at'      => $now,
                'updated_at'      => $now,
            ];
        }, $methods);

        // upsert theo PK 'id'
        DB::table('payment_methods')->upsert(
            $pmRows,
            ['id'], // uniqueBy
            ['alias', 'name', 'image', 'description', 'promotion_rate', 'is_active', 'sort', 'updated_at'] // update columns
        );

        // =============== B2) Xây configs cho tất cả games ==================
        $games = Game::query()->get(['game_id']);
        if ($games->isEmpty()) {
            $this->command?->warn('No games found. Skipping game_payment_method.');
            return;
        }

        $this->command?->info('Seeding game_payment_method (upsert)...');

        $methodIdByAlias = collect($methods)->keyBy('alias')->map(fn($m) => (int)$m['id']);
        $allowedAliases  = collect($MAX_PRICE_BY_ALIAS)->filter(fn($max) => $max > 0)->keys(); // bỏ vxu

        $rows = [];
        foreach ($games as $g) {
            $promotion = $PROMO_DISPLAY_BY_GAME[$g->game_id] ?? $PROMO_DISPLAY_BY_GAME['*'];

            foreach ($allowedAliases as $alias) {
                $pmId = $methodIdByAlias[$alias] ?? null;
                if (!$pmId) continue;

                $rows[] = [
                    'game_id'           => (int) $g->game_id,
                    'payment_method_id' => (int) $pmId,
                    'exchange_rate'     => 100.00,     // 1 unit = 100 VND
                    'min_amount'        => 10_000,
                    'max_amount'        => (int) $MAX_PRICE_BY_ALIAS[$alias],
                    'promotion'         => (int) $promotion,
                    'status'            => true,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }
        }

        // upsert theo unique (game_id, payment_method_id)
        // Chia chunk để tránh payload lớn
        foreach (array_chunk($rows, 1000) as $chunk) {
            DB::table('game_payment_method')->upsert(
                $chunk,
                ['game_id', 'payment_method_id'], // uniqueBy
                ['exchange_rate', 'min_amount', 'max_amount', 'promotion', 'status', 'updated_at'] // columns update
            );
        }

        // =============== B3) DỌN DỮ LIỆU CŨ (không còn trong cấu hình) ==================
        $this->command?->info('Cleaning stale rows in game_payment_method...');

        // xoá những row có payment_method_id không nằm trong danh sách methods hiện tại
        $allMethodIds = collect($methods)->pluck('id')->map(fn($v) => (int)$v)->all();
        DB::table('game_payment_method')->whereNotIn('payment_method_id', $allMethodIds)->delete();

        // xoá những row của games không còn (hiếm khi xảy ra)
        $allGameIds = $games->pluck('game_id')->map(fn($v) => (int)$v)->all();
        DB::table('game_payment_method')->whereNotIn('game_id', $allGameIds)->delete();

        // đảm bảo alias bị tắt (MAX=0) không còn config (vd: vxu)
        $disabledMethodIds = collect($MAX_PRICE_BY_ALIAS)
            ->filter(fn($max) => (int)$max === 0)
            ->keys()
            ->map(fn($alias) => $methodIdByAlias[$alias] ?? null)
            ->filter()
            ->map(fn($v) => (int)$v)
            ->values()
            ->all();

        if (!empty($disabledMethodIds)) {
            DB::table('game_payment_method')->whereIn('payment_method_id', $disabledMethodIds)->delete();
        }

        $this->command?->info('PaymentMethodSeeder done.');
    }
}
