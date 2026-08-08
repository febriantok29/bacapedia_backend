<?php

namespace App\Services;

use App\Models\Config;
use Carbon\Carbon;

class ConfigService
{
    public static function get(string $key, ?string $default = null): ?string
    {
        $config = Config::where('key', $key)
            ->whereNull('deleted_at')
            ->first();

        return $config?->value ?? $default;
    }

    public static function getActive(string $key, ?string $default = null): ?string
    {
        $today = Carbon::today();

        $config = Config::where('key', $key)
            ->whereNull('deleted_at')
            ->where(function ($query) use ($today) {
                $query->where(function ($q) use ($today) {
                    $q->whereNull('active_start_date')
                      ->whereNull('active_end_date');
                })->orWhere(function ($q) use ($today) {
                    $q->where('active_start_date', '<=', $today)
                      ->where(function ($inner) use ($today) {
                          $inner->whereNull('active_end_date')
                                ->orWhere('active_end_date', '>=', $today);
                      });
                });
            })
            ->orderByDesc('active_start_date')
            ->first();

        return $config?->value ?? $default;
    }

    public static function getInt(string $key, int $default = 0): int
    {
        $value = self::getActive($key);

        return $value !== null ? (int) $value : $default;
    }

    public static function getFloat(string $key, float $default = 0.0): float
    {
        $value = self::getActive($key);

        return $value !== null ? (float) $value : $default;
    }
}
