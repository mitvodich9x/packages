<?php

namespace Vgplay\Recharge\Database\Seeders;

use Vgplay\Recharge\Models\ItemDetail;
use Illuminate\Database\Seeder;
use Vgplay\Recharge\Models\Item;

class ItemDetailSeeder extends Seeder
{
    public function run(): void
    {
        $map = [
            'VXU_100' => [
                ['name' => 'Thưởng Vxu', 'quantity' => 100, 'description' => 'Nhận 100 Vxu'],
            ],
            'VXU_200' => [
                ['name' => 'Thưởng Vxu', 'quantity' => 200, 'description' => 'Nhận 200 Vxu'],
            ],
            'VXU_300' => [
                ['name' => 'Thưởng Vxu', 'quantity' => 300, 'description' => 'Nhận 300 Vxu'],
            ],
            'VXU_1000' => [
                ['name' => 'Thưởng Vxu', 'quantity' => 1000, 'description' => 'Nhận 1000 Vxu (mở khóa)'],
            ],
            'KNB_60' => [
                ['name' => 'Thưởng KNB', 'quantity' => 60, 'description' => 'Nhận 60 KNB'],
            ],
            'KNB_300' => [
                ['name' => 'Thưởng KNB', 'quantity' => 300, 'description' => 'Nhận 300 KNB'],
            ],
        ];

        foreach ($map as $code => $details) {
            $item = Item::where('code', $code)->first();
            if (!$item) continue;

            foreach ($details as $i => $d) {
                ItemDetail::updateOrCreate(
                    ['item_id' => $item->id, 'name' => $d['name']],
                    [
                        'image' => $d['image'] ?? null,
                        'description' => $d['description'] ?? null,
                        'quantity' => $d['quantity'] ?? 1,
                        'sort' => $i + 1,
                    ]
                );
            }
        }
    }
}
