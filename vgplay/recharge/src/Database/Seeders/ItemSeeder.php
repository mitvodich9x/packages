<?php

namespace Vgplay\Recharge\Database\Seeders;

use Vgplay\Games\Models\Game;
use Illuminate\Database\Seeder;
use Vgplay\Recharge\Models\Item;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $packages = [
            ['vxu_amount' => 200, 'name' => 'Gói 200 Vxu', 'code' => null, 'unit' => 'Vxu', 'type' => 'vxu', 'image' => 'https://img.vgplay.vn/vgplay/recharge/images/200.webp'],
            ['vxu_amount' => 500, 'name' => 'Gói 500 Vxu', 'code' => null, 'unit' => 'Vxu', 'type' => 'vxu', 'image' => 'https://img.vgplay.vn/vgplay/recharge/images/500vxu.webp'],
            ['vxu_amount' => 1000, 'name' => 'Gói 1000 Vxu', 'code' => null, 'unit' => 'Vxu', 'type' => 'vxu', 'image' => 'https://img.vgplay.vn/vgplay/recharge/images/1k.webp'],
            ['vxu_amount' => 2000, 'name' => 'Gói 2000 Vxu', 'code' => null, 'unit' => 'Vxu', 'type' => 'vxu', 'image' => 'https://img.vgplay.vn/vgplay/recharge/images/2k.webp'],
            ['vxu_amount' => 5000, 'name' => 'Gói 5000 Vxu', 'code' => null, 'unit' => 'Vxu', 'type' => 'vxu', 'image' => 'https://img.vgplay.vn/vgplay/recharge/images/5k.webp'],
            ['vxu_amount' => 10000, 'name' => 'Gói 10000 Vxu', 'code' => null, 'unit' => 'Vxu', 'type' => 'vxu', 'image' => 'https://img.vgplay.vn/vgplay/recharge/images/10k.webp'],
            ['vxu_amount' => 20000, 'name' => 'Gói 20000 Vxu', 'code' => null, 'unit' => 'Vxu', 'type' => 'vxu', 'image' => 'https://img.vgplay.vn/vgplay/recharge/images/20k.webp'],
            ['vxu_amount' => 50000, 'name' => 'Gói 50000 Vxu', 'code' => null, 'unit' => 'Vxu', 'type' => 'vxu', 'image' => 'https://img.vgplay.vn/vgplay/recharge/images/50k.webp'],
            ['vxu_amount' => 100000, 'name' => 'Gói 100000 Vxu', 'code' => null, 'unit' => 'Vxu', 'type' => 'vxu', 'image' => 'https://img.vgplay.vn/vgplay/recharge/images/100k.webp'],
            ['vxu_amount' => 200000, 'name' => 'Gói 200000 Vxu', 'code' => null, 'unit' => 'Vxu', 'type' => 'vxu', 'image' => 'https://img.vgplay.vn/vgplay/recharge/images/200k.webp'],
            ['vxu_amount' => 200, 'name' => 'Gói 100 KNB', 'code' => 'g88.gold.20000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/Gói-200-Vxu_1.webp'],
            ['vxu_amount' => 500, 'name' => 'Gói 250 KNB', 'code' => 'g88.gold.50000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/Gói-500-Vxu_2.webp'],
            ['vxu_amount' => 1000, 'name' => 'Gói 500 KNB', 'code' => 'g88.gold.100000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/Gói-1000-Vxu_3.webp'],
            ['vxu_amount' => 2000, 'name' => 'Gói 1000 KNB', 'code' => 'g88.gold.200000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/Gói-2000-Vxu_4.webp'],
            ['vxu_amount' => 5000, 'name' => 'Gói 2500 KNB', 'code' => 'g88.gold.500000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/Gói-5000-Vxu_5.webp'],
            ['vxu_amount' => 10000, 'name' => 'Gói 5000 KNB', 'code' => 'g88.gold.1000000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/Gói-10000-Vxu_6.webp'],
            ['vxu_amount' => 20000, 'name' => 'Gói 10000 KNB', 'code' => 'g88.gold.2000000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/Gói-20000-Vxu_7.webp'],
            ['vxu_amount' => 50000, 'name' => 'Gói 25000 KNB', 'code' => 'g88.gold.5000000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/Gói-50000-Vxu_8.webp'],
            ['vxu_amount' => 100000, 'name' => 'Gói 50000 KNB', 'code' => 'g88.gold.10000000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/Gói-100000-Vxu_9.webp'],
            ['vxu_amount' => 200, 'name' => 'Gói 60 Hồn Ngọc', 'code' => 'gold.mongkiem.tieudao.20000', 'unit' => 'Hồn Ngọc', 'type' => 'honngoc', 'image' => 'https://img.vgplay.vn/vgplay/images/772592b1af682f56b9daec6187eafb7a.webp'],
            ['vxu_amount' => 500, 'name' => 'Gói 150 Hồn Ngọc', 'code' => 'gold.mongkiem.tieudao.50000', 'unit' => 'Hồn Ngọc', 'type' => 'honngoc', 'image' => 'https://img.vgplay.vn/vgplay/images/56e752f244a9155c1dc396b50fcfa30d.webp'],
            ['vxu_amount' => 1000, 'name' => 'Gói 300 Hồn Ngọc', 'code' => 'gold.mongkiem.tieudao.100000', 'unit' => 'Hồn Ngọc', 'type' => 'honngoc', 'image' => 'https://img.vgplay.vn/vgplay/images/114cab9f218e60e67dceb2b9ff937229.webp'],
            ['vxu_amount' => 2000, 'name' => 'Gói 600 Hồn Ngọc', 'code' => 'gold.mongkiem.tieudao.200000', 'unit' => 'Hồn Ngọc', 'type' => 'honngoc', 'image' => 'https://img.vgplay.vn/vgplay/images/9d2315c9310ea155ab08825794a1a4ae.webp'],
            ['vxu_amount' => 5000, 'name' => 'Gói 1500 Hồn Ngọc', 'code' => 'gold.mongkiem.tieudao.500000', 'unit' => 'Hồn Ngọc', 'type' => 'honngoc', 'image' => 'https://img.vgplay.vn/vgplay/images/d6b1eec57c994649204d770e63745e61.webp'],
            ['vxu_amount' => 10000, 'name' => 'Gói 3000 Hồn Ngọc', 'code' => 'gold.mongkiem.tieudao.1000000', 'unit' => 'Hồn Ngọc', 'type' => 'honngoc', 'image' => 'https://img.vgplay.vn/vgplay/images/fd67549dc0932500aab90e74b8439aba.webp'],
            ['vxu_amount' => 20000, 'name' => 'Gói 6000 Hồn Ngọc', 'code' => 'gold.mongkiem.tieudao.2000000', 'unit' => 'Hồn Ngọc', 'type' => 'honngoc', 'image' => 'https://img.vgplay.vn/vgplay/images/1c48b81d4aaa1df04454ec9eb918d820.webp'],
            ['vxu_amount' => 50000, 'name' => 'Gói 15000 Hồn Ngọc', 'code' => 'gold.mongkiem.tieudao.5000000', 'unit' => 'Hồn Ngọc', 'type' => 'honngoc', 'image' => 'https://img.vgplay.vn/vgplay/images/da10581378946c077768593fed669469.webp'],
            ['vxu_amount' => 100000, 'name' => 'Gói 30000 Hồn Ngọc', 'code' => 'gold.mongkiem.tieudao.10000000', 'unit' => 'Hồn Ngọc', 'type' => 'honngoc', 'image' => 'https://img.vgplay.vn/vgplay/images/9b6d94d0203688b4436693d79dd9e8d9.webp'],
            ['vxu_amount' => 200, 'name' => 'Gói 200 KNB', 'code' => 'gold.langvan.20000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/b46a995470b04736763fcade1e67ebf2.webp'],
            ['vxu_amount' => 500, 'name' => 'Gói 500 KNB', 'code' => 'gold.langvan.50000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/8f61eba02392028540c727bfe5f9d776.webp'],
            ['vxu_amount' => 1000, 'name' => 'Gói 1000 KNB', 'code' => 'gold.langvan.100000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/d52cfdc465265bf17fddad382de0d8be.webp'],
            ['vxu_amount' => 2000, 'name' => 'Gói 2000 KNB', 'code' => 'gold.langvan.200000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/dc5d30de764ffcfa69582884c2085f7c.webp'],
            ['vxu_amount' => 5000, 'name' => 'Gói 5000 KNB', 'code' => 'gold.langvan.500000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/482c16ed6338285a98949362f002b8c2.webp'],
            ['vxu_amount' => 10000, 'name' => 'Gói 10000 KNB', 'code' => 'gold.langvan.1000000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/155bde82f80a194cc3260ae7b1ced717.webp'],
            ['vxu_amount' => 20000, 'name' => 'Gói 20000 KNB', 'code' => 'langvan.item1.2000000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/edf42c55329681987cbaa575b17aa8c8.webp'],
            ['vxu_amount' => 50000, 'name' => 'Gói 50000 KNB', 'code' => 'langvan.item1.5000000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/39edf29bbdfc5fe878f9a1478fa036cf.webp'],
            ['vxu_amount' => 100000, 'name' => 'Gói 100000 KNB', 'code' => 'langvan.item1.10000000', 'unit' => 'KNB', 'type' => 'knb', 'image' => 'https://img.vgplay.vn/vgplay/images/9a5c942f96992c9a5948e6f59d57b334.webp'],
            ['vxu_amount' => 170, 'name' => 'Gói 85 Sò', 'code' => 'gold.longhon.kynguyen.17000', 'unit' => 'Sò', 'type' => 'so', 'image' => 'https://img.vgplay.vn/vgplay/images/caaee59547b2fa883d1d866a2a9aa2d7.webp'],
            ['vxu_amount' => 500, 'name' => 'Gói 250 Sò', 'code' => 'gold.longhon.kynguyen.50000', 'unit' => 'Sò', 'type' => 'so', 'image' => 'https://img.vgplay.vn/vgplay/images/92bcae783f1d9d9d6b09fce4e85b53aa.webp'],
            ['vxu_amount' => 1000, 'name' => 'Gói 500 Sò', 'code' => 'gold.longhon.kynguyen.100000', 'unit' => 'Sò', 'type' => 'so', 'image' => 'https://img.vgplay.vn/vgplay/images/a1cb95ef9744fc7f60480e77d8ad4b3f.webp'],
            ['vxu_amount' => 2000, 'name' => 'Gói 1000 Sò', 'code' => 'gold.longhon.kynguyen.200000', 'unit' => 'Sò', 'type' => 'so', 'image' => 'https://img.vgplay.vn/vgplay/images/8de9c3d4c0d8d68bd3afa545b5cc10bb.webp'],
            ['vxu_amount' => 5000, 'name' => 'Gói 2500 Sò', 'code' => 'gold.longhon.kynguyen.500000', 'unit' => 'Sò', 'type' => 'so', 'image' => 'https://img.vgplay.vn/vgplay/images/1326a38bb3b0830414e09fbc75f20ad3.webp'],
            ['vxu_amount' => 10000, 'name' => 'Gói 5000 Sò', 'code' => 'gold.longhon.kynguyen.1000000', 'unit' => 'Sò', 'type' => 'so', 'image' => 'https://img.vgplay.vn/vgplay/images/041965194409da14434ccf6d67d7cfd2.webp'],
            ['vxu_amount' => 20000, 'name' => 'Gói 10000 Sò', 'code' => 'gold.longhon.2000000', 'unit' => 'Sò', 'type' => 'so', 'image' => 'https://img.vgplay.vn/vgplay/images/820fa0be5950f6d7d4099afaa25ac99e.webp'],
            ['vxu_amount' => 50000, 'name' => 'Gói 25000 Sò', 'code' => 'gold.longhon.5000000', 'unit' => 'Sò', 'type' => 'so', 'image' => 'https://img.vgplay.vn/vgplay/images/db40c2863d2e5044e159786744c0e950.webp'],
            ['vxu_amount' => 100000, 'name' => 'Gói 50000 Sò', 'code' => 'gold.longhon.10000000', 'unit' => 'Sò', 'type' => 'so', 'image' => 'https://img.vgplay.vn/vgplay/images/ecfe09dc0040a9f8adb77fecca545ba2.webp'],
        ];
        $itemsForAllGames = [];
        $itemsForGame88 = [];
        $itemsForGame72 = [];
        $itemsForGame79 = [];
        $itemsForGame85 = [];

        foreach ($packages as $pkg) {
            $type = strtolower(trim($pkg['type']));        // vxu | knb | honngoc | so
            $unitName = trim($pkg['unit']);               // 'Vxu' | 'KNB' | 'Hồn Ngọc' | 'Sò'
            $name = trim($pkg['name']);
            $img = $pkg['image'] ?? null;
            $priceUnits = (int) ($pkg['vxu_amount'] ?? 0);
            // vxu_amount
            // if ($type === 'vxu') {
            //     $priceUnits = (int) ($pkg['vxu_amount'] ?? 0);
            // } else {
            //     // Parse số từ "Gói 100 X"
            //     $priceUnits = 0;
            //     // if (preg_match('/Gói\s+([\d\.]+)/u', $name, $m)) {
            //     //     $priceUnits = (int) str_replace('.', '', $m[1]);
            //     // }
            //     if ($priceUnits <= 0) {
            //         $priceUnits = (int) ($pkg['vxu_amount'] ?? 0);
            //     }
            // }

            $code = $pkg['code'];

            // ===== Defaults theo loại =====
            // Vxu: không tier, không limit, mua nhiều
            $defaults = [
                'limit_per_user'         => 0,
                'allow_multiple_per_order' => true,
                'tier'                   => 0,
                'requires_min_tier'      => 0,
            ];

            // So & HonNgoc: nếu không override, mặc định 1 lần & không mua nhiều & áp tier cơ bản
            if (in_array($type, ['so', 'honngoc'], true)) {
                $defaults = [
                    'limit_per_user'         => 1,
                    'allow_multiple_per_order' => false,
                    'tier'                   => 1,
                    'requires_min_tier'      => 0,
                ];
            }

            // ===== Override nếu gói có truyền lên =====
            $limitPerUser = array_key_exists('limit_per_user', $pkg) ? (int)$pkg['limit_per_user'] : $defaults['limit_per_user'];
            $allowMultiple = array_key_exists('allow_multiple_per_order', $pkg) ? (bool)$pkg['allow_multiple_per_order'] : $defaults['allow_multiple_per_order'];
            $tier = array_key_exists('tier', $pkg) ? (int)$pkg['tier'] : $defaults['tier'];
            $requiresMinTier = array_key_exists('requires_min_tier', $pkg) ? (int)$pkg['requires_min_tier'] : $defaults['requires_min_tier'];

            // Vxu luôn ép không tier (kể cả override nhầm)
            if ($type === 'vxu') {
                $tier = 0;
                $requiresMinTier = 0;
            }

            $type = strtolower($pkg['type']);
            $vxu  = (int) ($pkg['vxu_amount'] ?? 0);
            $code = $pkg['code'] ?? null;

            // Lookup key: Vxu dùng (type, vxu_amount); còn lại dùng code
            $lookup = $code
                ? ['code' => $code]
                : (($type === 'vxu' && $vxu > 0)
                    ? ['type' => 'vxu', 'vxu_amount' => $vxu]
                    : ['name' => $pkg['name']]); // fallback (hiếm khi dùng)

            $item = Item::updateOrCreate(
                $lookup,
                [
                    'type' => $type,
                    'name' => $name,
                    'code' => $code,
                    'image' => $img,
                    'unit' => $unitName,            // nội bộ: vxu/knb/honngoc/so
                    'description' => $name,
                    'vxu_amount' => $priceUnits,
                    'discount_percent' => 0,

                    'limit_per_user' => $limitPerUser,
                    'allow_multiple_per_order' => $allowMultiple,
                    'tier' => $tier,
                    'requires_min_tier' => $requiresMinTier,

                    'is_active' => true,
                    'sort' => $priceUnits,
                ]
            );

            // Phân bổ game
            if ($type === 'vxu') {
                $itemsForAllGames[] = $item->id;
            }
            $codeForCheck = strtolower($code);
            if (str_contains($codeForCheck, 'g88'))      $itemsForGame88[] = $item->id;
            if (str_contains($codeForCheck, 'mongkiem')) $itemsForGame72[] = $item->id;
            if (str_contains($codeForCheck, 'langvan'))  $itemsForGame79[] = $item->id;
            if (str_contains($codeForCheck, 'longhon'))  $itemsForGame85[] = $item->id;
        }

        // Gắn item vào các game
        $games = Game::all();
        foreach ($games as $game) {
            if (!empty($itemsForAllGames)) {
                $game->items()->syncWithoutDetaching($itemsForAllGames);
            }
        }
        if ($game88 = Game::where('game_id', 88)->first()) $game88->items()->syncWithoutDetaching($itemsForGame88);
        if ($game72 = Game::where('game_id', 72)->first()) $game72->items()->syncWithoutDetaching($itemsForGame72);
        if ($game79 = Game::where('game_id', 79)->first()) $game79->items()->syncWithoutDetaching($itemsForGame79);
        if ($game85 = Game::where('game_id', 85)->first()) $game85->items()->syncWithoutDetaching($itemsForGame85);
    }
}
