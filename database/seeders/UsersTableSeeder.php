<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    const ADMIN_ID = '01914e07-a001-7000-8000-000000000001';

    public function run(): void
    {
        DB::table('s_users')->insert([
            [
                'id' => self::ADMIN_ID,
                'user_code' => 'USR-00001',
                'name' => 'Super Admin',
                'email' => 'admin@bacapedia.com',
                'password' => Hash::make('password123'),
                'role' => 'Admin',
                'created_at' => now(),
                'created_by' => self::ADMIN_ID,
            ],
            [
                'id' => '01914e07-a001-7000-8000-000000000002',
                'user_code' => 'USR-00002',
                'name' => 'Rina Petugas',
                'email' => 'rina@bacapedia.com',
                'password' => Hash::make('password123'),
                'role' => 'Petugas',
                'created_at' => now(),
                'created_by' => self::ADMIN_ID,
            ],
            [
                'id' => '01914e07-a001-7000-8000-000000000003',
                'user_code' => 'USR-00003',
                'name' => 'Budi Anggota',
                'email' => 'budi@gmail.com',
                'password' => Hash::make('password123'),
                'role' => 'Anggota',
                'created_at' => now(),
                'created_by' => self::ADMIN_ID,
            ],
        ]);
    }
}
