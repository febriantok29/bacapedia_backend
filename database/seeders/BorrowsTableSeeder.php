<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BorrowsTableSeeder extends Seeder
{
    const LATE_BORROW_ID = '01914e07-d001-7000-8000-000000000001';

    public function run(): void
    {
        $anggotaId = '01914e07-a001-7000-8000-000000000003';
        $bookId = '01914e07-c001-7000-8000-000000000002';
        $adminId = UsersTableSeeder::ADMIN_ID;

        $borrowDate = Carbon::today()->subDays(10);
        $dueDate = $borrowDate->copy()->addDays(7);

        DB::table('t_borrows')->insert([
            'id' => self::LATE_BORROW_ID,
            'user_id' => $anggotaId,
            'book_id' => $bookId,
            'borrow_date' => $borrowDate->toDateString(),
            'due_date' => $dueDate->toDateString(),
            'return_date' => null,
            'status' => 'Aktif',
            'penalty' => 0,
            'created_at' => $borrowDate,
            'created_by' => $adminId,
        ]);

        DB::table('h_borrows')->insert([
            'id' => Str::uuid7()->toString(),
            'borrow_id' => self::LATE_BORROW_ID,
            'status' => 'Aktif',
            'remarks' => 'Peminjaman baru',
            'created_at' => $borrowDate,
            'created_by' => $adminId,
        ]);

        DB::table('m_books')->where('id', $bookId)->decrement('stock');
    }
}
