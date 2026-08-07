<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BooksTableSeeder extends Seeder
{
    public function run(): void
    {
        $adminId = UsersTableSeeder::ADMIN_ID;

        DB::table('m_books')->insert([
            [
                'id' => '01914e07-c001-7000-8000-000000000001',
                'book_code' => 'BK-2026-00001',
                'category_id' => '01914e07-b001-7000-8000-000000000001',
                'title' => 'Laskar Pelangi',
                'author' => 'Andrea Hirata',
                'publisher' => 'Bentang Pustaka',
                'published_year' => 2005,
                'stock' => 5,
                'created_at' => now(),
                'created_by' => $adminId,
            ],
            [
                'id' => '01914e07-c001-7000-8000-000000000002',
                'book_code' => 'BK-2026-00002',
                'category_id' => '01914e07-b001-7000-8000-000000000002',
                'title' => 'Sapiens: Riwayat Singkat Umat Manusia',
                'author' => 'Yuval Noah Harari',
                'publisher' => 'Gramedia Pustaka Utama',
                'published_year' => 2014,
                'stock' => 3,
                'created_at' => now(),
                'created_by' => $adminId,
            ],
            [
                'id' => '01914e07-c001-7000-8000-000000000003',
                'book_code' => 'BK-2026-00003',
                'category_id' => '01914e07-b001-7000-8000-000000000003',
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'publisher' => 'Prentice Hall',
                'published_year' => 2008,
                'stock' => 2,
                'created_at' => now(),
                'created_by' => $adminId,
            ],
        ]);
    }
}
