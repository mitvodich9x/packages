<?php

namespace Vgplay\Recharge\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Vgplay\Recharge\Models\Payment;
use Vgplay\Recharge\Models\PaymentConfig;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            ['id' => 1, 'name' => 'Thanh toán Vxu', 'alias' => 'vxu', 'image' => 'https://img.vgplay.vn/vgplay/images/1244220eca5a5de261835cc810401544.webp', 'description' => 'Thanh toán bằng Vxu', 'sort' => 1],
            ['id' => 2, 'name' => 'ATM/Visa Việt Nam', 'alias' => 'atm', 'image' => 'https://img.vgplay.vn/vgplay/images/2eaad86494c2adda4a13fabe5b5eb322.webp', 'description' => 'Thanh toán tiện dụng với thẻ ATM/Ngân hàng điện tử (iBanking)...', 'sort' => 2],
            ['id' => 3, 'name' => 'Visa Quốc tế', 'alias' => 'visa', 'image' => 'https://img.vgplay.vn/vgplay/images/fc63fa50a48ae2519254eaa9fd498f98.webp', 'description' => 'Thanh toán tiện dụng với thẻ Thanh toán quốc tế MasterCard/Visa...', 'sort' => 3],
            ['id' => 4, 'name' => 'Ứng Dụng Momo', 'alias' => 'momo', 'image' => 'https://img.vgplay.vn/vgplay/images/40439198aec27d81d592468c419fbcd6.webp', 'description' => 'Thanh toán sử dụng tài khoản Momo', 'sort' => 4],
            ['id' => 5, 'name' => 'Zalo Pay', 'alias' => 'zalo', 'image' => 'https://img.vgplay.vn/vgplay/images/32208e96788ab5f3e595bdbc44e2f04c.webp', 'description' => 'Thanh toán sử dụng tài khoản ZaloPay', 'sort' => 5],
            ['id' => 6, 'name' => 'Apple Pay', 'alias' => 'apple', 'image' => 'https://img.vgplay.vn/vgplay/images/dc49bca6c939fd2f1f5b834fe99a4379.webp', 'description' => 'Thanh toán bằng Apple Pay', 'sort' => 6],
            ['id' => 7, 'name' => 'Google Pay', 'alias' => 'google', 'image' => 'https://img.vgplay.vn/vgplay/images/2be26c8e869daee9da860506c686b8ae.webp', 'description' => 'Thanh toán bằng Google Pay', 'sort' => 7],
            ['id' => 8, 'name' => 'QR Ngân Hàng', 'alias' => 'qr', 'image' => 'https://img.vgplay.vn/vgplay/images/345d8628736ace5d025c7549fa0e3179.webp', 'description' => 'Thanh toán bằng QR Ngân Hàng', 'sort' => 8],
            ['id' => 9, 'name' => 'Samsung Pay', 'alias' => 'samsung', 'image' => 'https://img.vgplay.vn/vgplay/images/b8857e19f7f96224680b3911909140a6.webp', 'description' => 'Thanh toán bằng Samsung Pay', 'sort' => 9],
            ['id' => 10, 'name' => 'Thẻ VGP', 'alias' => 'vgp', 'image' => 'https://img.vgplay.vn/vgplay/images/1244220eca5a5de261835cc810401544.webp', 'description' => 'Thanh toán bằng Thẻ VGP', 'sort' => 10],
        ];

        // hệ số promotion mặc định theo alias (khớp bảng ảnh)
        $PROMO = [
            'momo' => 1.10,
            'zalo' => 1.05,
            // còn lại 1.00
        ];

        // danh sách mệnh giá Vxu dùng chung
        $VXU_PRICES = [200, 500, 1000, 2000, 5000, 10000, 20000, 50000, 100000, 200000, 500000];

        // giới hạn mệnh giá theo cổng
        $MAX_VXU_BY_ALIAS = [
            'atm'     => 500000,
            'visa'    => 500000,
            'qr'      => 500000,
            'vgp'     => 500000, // tuỳ bạn
            'momo'    => 10000,
            'zalo'    => 10000,
            'apple'   => 10000,
            'google'  => 10000,
            'samsung' => 10000,
            'vxu'     => 0, // không dùng để mua Vxu
        ];

        DB::transaction(function () use ($methods, $PROMO, $VXU_PRICES, $MAX_VXU_BY_ALIAS) {
            foreach ($methods as $m) {
                /** @var \Vgplay\Recharge\Models\Payment $pay */
                $pay = Payment::query()->updateOrCreate(
                    ['id' => $m['id']],
                    [
                        'alias' => $m['alias'],
                        'name' => $m['name'],
                        'image' => $m['image'],
                        'description' => $m['description'] ?? null,
                        'sort' => $m['sort'] ?? 0,
                        'base_discount' => 0,
                        'base_promotion' => $PROMO[$m['alias']] ?? 1.00,
                        'is_active' => true,
                    ]
                );

                // Tạo mệnh giá
                $max = $MAX_VXU_BY_ALIAS[$m['alias']] ?? 0;
                foreach ($VXU_PRICES as $price) {
                    $active = $max > 0 && $price <= $max;
                    if ($m['alias'] === 'vxu') {
                        $active = false;
                    } // không mua Vxu bằng Vxu

                    PaymentConfig::query()->updateOrCreate(
                        ['payment_id' => $pay->id, 'price' => $price],
                        ['promotion' => null, 'is_active' => $active]
                    );
                }
            }
        });
    }
}
