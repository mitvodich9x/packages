<?php

namespace Vgplay\Settings\Services;

use Vgplay\Settings\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SettingService
{
    protected function cacheKey(string $key): string
    {
        return "setting:{$key}";
    }

    /**
     * Decode JSON string nếu có, ngược lại trả về nguyên giá trị.
     */
    protected function decodeValue(mixed $value): mixed
    {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return $value;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        try {
            return Cache::remember($this->cacheKey($key), 3600, function () use ($key, $default) {
                $setting = Setting::where('key', $key)->first();
                return $setting ? $this->decodeValue($setting->value) : $default;
            });
        } catch (\Throwable $e) {
            Log::error('Cache get failed', [
                'key'   => $key,
                'error' => $e->getMessage(),
            ]);
            return $default;
        }
    }

    public function set(string $key, mixed $value): void
    {
        // Lưu DB luôn ở dạng string
        $dbValue = is_string($value) ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);

        Setting::updateOrCreate(['key' => $key], ['value' => $dbValue]);

        // Cache lưu bản decode để xài cho tiện
        Cache::put($this->cacheKey($key), $this->decodeValue($dbValue), 3600);
    }

    public function forget(string $key): void
    {
        Cache::forget($this->cacheKey($key));
    }

    public function getByGroup(string $group): array
    {
        try {
            return Cache::remember("settings:group:{$group}", 3600, function () use ($group) {
                return Setting::where('group', $group)
                    ->get()
                    ->mapWithKeys(fn($s) => [$s->key => $this->decodeValue($s->value)])
                    ->toArray();
            });
        } catch (\Throwable $e) {
            Log::error('Cache getByGroup failed', [
                'group' => $group,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    public function getByType(string $type): array
    {
        try {
            return Cache::remember("settings:type:{$type}", 3600, function () use ($type) {
                return Setting::where('type', $type)
                    ->get()
                    ->mapWithKeys(fn($s) => [$s->key => $this->decodeValue($s->value)])
                    ->toArray();
            });
        } catch (\Throwable $e) {
            Log::error('Cache getByType failed', [
                'type'  => $type,
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    public function getAll(): array
    {
        try {
            return Cache::remember("settings:all", 3600, function () {
                return Setting::all()
                    ->mapWithKeys(fn($s) => [$s->key => $this->decodeValue($s->value)])
                    ->toArray();
            });
        } catch (\Throwable $e) {
            Log::error('Cache getAll failed', [
                'error' => $e->getMessage(),
            ]);
            return [];
        }
    }

    public function forgetAll(): void
    {
        Cache::forget("settings:all");

        $groups = Setting::select('group')->distinct()->pluck('group');
        foreach ($groups as $group) {
            Cache::forget("settings:group:{$group}");
        }

        $types = Setting::select('type')->distinct()->pluck('type');
        foreach ($types as $type) {
            Cache::forget("settings:type:{$type}");
        }

        $keys = Setting::pluck('key');
        foreach ($keys as $key) {
            Cache::forget($this->cacheKey($key));
        }
    }

    public function syncAll(): void
    {
        $this->forgetAll();

        $all = Setting::all();

        // cache từng key
        foreach ($all as $setting) {
            Cache::put($this->cacheKey($setting->key), $this->decodeValue($setting->value), 3600);
        }

        // cache all
        Cache::put(
            'settings:all',
            $all->mapWithKeys(fn($s) => [$s->key => $this->decodeValue($s->value)])->toArray(),
            3600
        );

        // cache theo group
        foreach ($all->groupBy('group') as $group => $settings) {
            Cache::put(
                "settings:group:{$group}",
                $settings->mapWithKeys(fn($s) => [$s->key => $this->decodeValue($s->value)])->toArray(),
                3600
            );
        }

        // cache theo type
        foreach ($all->groupBy('type') as $type => $settings) {
            Cache::put(
                "settings:type:{$type}",
                $settings->mapWithKeys(fn($s) => [$s->key => $this->decodeValue($s->value)])->toArray(),
                3600
            );
        }
    }
}
