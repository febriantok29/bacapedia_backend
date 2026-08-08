<?php

namespace App\Http\Controllers\Concerns;

trait NormalizesFilterValues
{
    protected function normalizeFilter(?string $value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        return array_map('trim', explode(',', $value));
    }
}
