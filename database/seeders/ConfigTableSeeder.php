<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConfigTableSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = UsersTableSeeder::ADMIN_ID;

        DB::table('s_config')->insert([
            [
                'id' => Str::uuid7()->toString(),
                'key' => 'max_active_borrows',
                'value' => '3',
                'active_start_date' => '2026-01-01',
                'active_end_date' => null,
                'created_at' => now(),
                'created_by' => $adminId,
            ],
            [
                'id' => Str::uuid7()->toString(),
                'key' => 'penalty_per_day',
                'value' => '2000',
                'active_start_date' => '2026-01-01',
                'active_end_date' => null,
                'created_at' => now(),
                'created_by' => $adminId,
            ],
            [
                'id' => Str::uuid7()->toString(),
                'key' => 'borrow_duration_days',
                'value' => '7',
                'active_start_date' => '2026-01-01',
                'active_end_date' => null,
                'created_at' => now(),
                'created_by' => $adminId,
            ],
        ]);
    }
}
