<?php

namespace Vgplay\Recharge\Services;

use Illuminate\Support\Str;
use Vgplay\Recharge\Models\Item;
use Illuminate\Support\Facades\DB;
use Vgplay\Recharge\Models\Payment;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Collection;

class PaymentSelector
{
    public static function methodsForItem(int $gameId, int|Item $item, bool $useCache = true): Collection
    {
        // 1) Chuẩn hoá item
        if (is_int($item)) {
            /** @var Item $item */
            $item = Item::query()
                ->select(['id', 'game_id', 'unit', 'unit_alias', 'price'])
                ->findOrFail($item);
        }

        if ((int) $item->game_id !== (int) $gameId) {
            abort(404, 'Item does not belong to this game.');
        }

        $unitAlias = $item->unit_alias ?? (method_exists(Item::class, 'toAlias')
            ? Item::toAlias($item->unit)
            : strtolower($item->unit));

        $price = (int) $item->price;

        // 2) Cache theo game+item+price
        $cacheKey = "pay_methods:g{$gameId}:i{$item->id}:p{$price}";
        if ($useCache && ($cached = Cache::get($cacheKey))) {
            return collect($cached)
                ->when($unitAlias === 'vxu', fn($c) => $c->reject(fn($m) => $m['alias'] === 'vxu'))
                ->values();
        }

        // 3) Truy vấn: chỉ trả payment có mệnh giá khớp và đang active trên game
        $rows = Payment::query()
            ->select([
                'payments.id',
                'payments.alias',
                'payments.name',
                'payments.description',
                DB::raw('COALESCE(gp.image, payments.image) as image'),
                DB::raw('payments.sort as base_sort'),
                DB::raw('gp.sort_order as pivot_sort'),
                DB::raw('pc.price as price_point'),
                DB::raw('COALESCE(gp.promotion, pc.promotion, payments.base_promotion) as promotion'),
                DB::raw('COALESCE(gp.discount, payments.base_discount) as discount'),
                DB::raw('CAST(ROUND(pc.price * 100 * COALESCE(gp.promotion, pc.promotion, payments.base_promotion), 0) AS UNSIGNED) as amount_vnd'),
            ])
            // payment phải được bật cho game
            ->join('game_payment as gp', function ($j) use ($gameId) {
                $j->on('gp.payment_id', '=', 'payments.id')
                    ->where('gp.game_id', '=', $gameId)
                    ->where('gp.is_active', '=', 1);
            })
            // payment phải có mệnh giá đúng = price và đang active
            ->join('payment_configs as pc', function ($j) use ($price) {
                $j->on('pc.payment_id', '=', 'payments.id')
                    ->where('pc.is_active', '=', 1)
                    ->where('pc.price', '=', $price);
            })
            ->where('payments.is_active', true)
            // Nếu gói là vxu thì ẩn payment alias 'vxu'
            ->when($unitAlias === 'vxu', fn($q) => $q->where('payments.alias', '!=', 'vxu'))
            ->orderByRaw('COALESCE(gp.sort_order, payments.sort) asc')
            ->orderBy('payments.id')
            ->get();

        $methods = $rows->map(function ($m) {
            dd($m);
            return [
                'id'          => (int) $m->id,
                'alias'       => (string) $m->alias,
                'name'        => (string) $m->name,
                'image'       => $m->image,
                'description' => $m->description,
                'price_point' => (int) $m->price_point,   // Vxu
                'promotion'   => (float) $m->promotion,   // hệ số
                'discount'    => (float) $m->discount,    // %
                'amount_vnd'  => (int) $m->amount_vnd,    // VND
                'sort'        => (int) ($m->pivot_sort ?? $m->base_sort ?? 0),
            ];
        })->values();

        if ($useCache) {
            Cache::put($cacheKey, $methods->toArray(), now()->addMinutes(5));
        }

        return $methods;
    }

    public static function methodsForPrice(int $gameId, int $price, ?string $unit = null, bool $useCache = true): Collection
    {
        $unitAlias = null;
        if ($unit !== null) {
            if (method_exists(Item::class, 'toAlias')) {
                $unitAlias = Item::toAlias($unit);
            } else {
                $unitAlias = Str::of($unit)->squish()->lower()->ascii()->replaceMatches('/[^a-z0-9]+/', '')->value();
            }
        }

        $cacheKey = "pay_methods_by_price:g{$gameId}:p{$price}";
        if ($useCache && ($cached = Cache::get($cacheKey))) {
            $col = collect($cached);
            return ($unitAlias === 'vxu')
                ? $col->reject(fn($m) => $m['alias'] === 'vxu')->values()
                : $col;
        }

        $rows = Payment::query()
            ->select([
                'payments.id',
                'payments.alias',
                'payments.name',
                'payments.description',
                DB::raw('COALESCE(gp.image, payments.image) as image'),
                DB::raw('payments.sort as base_sort'),
                DB::raw('gp.sort_order as pivot_sort'),
                DB::raw('pc.price as price_point'),
                DB::raw('COALESCE(gp.promotion, pc.promotion, payments.base_promotion) as promotion'),
                DB::raw('COALESCE(gp.discount, payments.base_discount) as discount'),
                DB::raw('CAST(ROUND(pc.price * 100 * COALESCE(gp.promotion, pc.promotion, payments.base_promotion), 0) AS UNSIGNED) as amount_vnd'),
            ])
            ->join('game_payment as gp', function ($j) use ($gameId) {
                $j->on('gp.payment_id', '=', 'payments.id')
                    ->where('gp.game_id', '=', $gameId)
                    ->where('gp.is_active', '=', 1);
            })
            ->join('payment_configs as pc', function ($j) use ($price) {
                $j->on('pc.payment_id', '=', 'payments.id')
                    ->where('pc.is_active', '=', 1)
                    ->where('pc.price', '=', (int) $price);
            })
            ->where('payments.is_active', true)
            ->when($unitAlias === 'vxu', fn($q) => $q->where('payments.alias', '!=', 'vxu'))
            ->orderByRaw('COALESCE(gp.sort_order, payments.sort) asc')
            ->orderBy('payments.id')
            ->get();

        $methods = $rows->map(function ($m) {
            return [
                'id'          => (int) $m->id,
                'alias'       => (string) $m->alias,
                'name'        => (string) $m->name,
                'image'       => $m->image,
                'description' => $m->description,
                'price_point' => (int) $m->price_point, // Vxu
                'promotion'   => (float) $m->promotion,
                'discount'    => (float) $m->discount,
                'amount_vnd'  => (int) $m->amount_vnd,
                'sort'        => (int) ($m->pivot_sort ?? $m->base_sort ?? 0),
            ];
        });

        if ($useCache) Cache::put($cacheKey, $methods->toArray(), now()->addMinutes(5));

        return $methods->values();
    }
}
