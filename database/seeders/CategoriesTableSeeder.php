<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriesTableSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = UsersTableSeeder::ADMIN_ID;

        DB::table('m_categories')->insert([
            [
                'id' => '01914e07-b001-7000-8000-000000000001',
                'name' => 'Fiksi',
                'created_at' => now(),
                'created_by' => $adminId,
            ],
            [
                'id' => '01914e07-b001-7000-8000-000000000002',
                'name' => 'Non-Fiksi',
                'created_at' => now(),
                'created_by' => $adminId,
            ],
            [
                'id' => '01914e07-b001-7000-8000-000000000003',
                'name' => 'Sains & Teknologi',
                'created_at' => now(),
                'created_by' => $adminId,
            ],
        ]);
    }
}
