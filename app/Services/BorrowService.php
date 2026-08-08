<?php

namespace App\Services;

use App\Models\Book;
use App\Models\Borrow;
use App\Models\BorrowHistory;
use App\Models\User;
use App\Support\ApiErrorCodes;
use App\Support\ApiMessages;
use App\Support\Enums\BorrowStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BorrowService
{
    public function borrow(User $user, string $bookId, string $performedBy): array
    {
        $book = Book::find($bookId);

        if (!$book) {
            return $this->fail(ApiErrorCodes::NOT_FOUND, ApiMessages::BOOK_NOT_FOUND, 404);
        }

        if ($book->stock <= 0) {
            return $this->fail(ApiErrorCodes::UNPROCESSABLE, ApiMessages::STOCK_EMPTY, 422);
        }

        $maxBorrows = ConfigService::getInt('max_active_borrows', 3);
        $activeBorrows = Borrow::where('user_id', $user->id)
            ->where('status', BorrowStatus::ACTIVE->value)
            ->count();

        if ($activeBorrows >= $maxBorrows) {
            $message = ApiMessages::BORROW_LIMIT_REACHED . " ({$maxBorrows} buku)";
            return $this->fail(ApiErrorCodes::CONFLICT, $message, 409);
        }

        $alreadyBorrowed = Borrow::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->where('status', BorrowStatus::ACTIVE->value)
            ->exists();

        if ($alreadyBorrowed) {
            return $this->fail(ApiErrorCodes::CONFLICT, ApiMessages::ALREADY_BORROWED, 409);
        }

        $borrowDays = ConfigService::getInt('borrow_duration_days', 7);
        $borrowDate = Carbon::today();
        $dueDate = $borrowDate->copy()->addDays($borrowDays);

        $borrow = DB::transaction(function () use ($user, $bookId, $book, $borrowDate, $dueDate, $performedBy) {
            $borrow = Borrow::create([
                'user_id' => $user->id,
                'book_id' => $bookId,
                'borrow_date' => $borrowDate,
                'due_date' => $dueDate,
                'status' => BorrowStatus::ACTIVE->value,
                'penalty' => 0,
            ]);

            $borrow->created_by = $performedBy;
            $borrow->save();

            $book->decrement('stock');

            BorrowHistory::create([
                'borrow_id' => $borrow->id,
                'status' => BorrowStatus::ACTIVE->value,
                'remarks' => 'Peminjaman baru',
                'created_at' => now(),
                'created_by' => $performedBy,
            ]);

            return $borrow;
        });

        $borrow->load(['book:id,book_code,title,author', 'user:id,user_code,name']);

        return ['success' => true, 'data' => $borrow];
    }

    public function returnBook(string $borrowId, string $performedBy): array
    {
        $borrow = Borrow::with(['book', 'user'])->find($borrowId);

        if (!$borrow) {
            return $this->fail(ApiErrorCodes::NOT_FOUND, ApiMessages::BORROW_NOT_FOUND, 404);
        }

        if (in_array($borrow->status, [BorrowStatus::RETURNED->value, BorrowStatus::OVERDUE->value])) {
            return $this->fail(ApiErrorCodes::CONFLICT, ApiMessages::ALREADY_RETURNED, 409);
        }

        $returnDate = Carbon::today();
        $dueDate = Carbon::parse($borrow->due_date);
        $penalty = 0;
        $status = BorrowStatus::RETURNED->value;

        if ($returnDate->greaterThan($dueDate)) {
            $daysLate = $dueDate->diffInDays($returnDate);
            $penaltyPerDay = ConfigService::getInt('penalty_per_day', 2000);
            $penalty = $daysLate * $penaltyPerDay;
            $status = BorrowStatus::OVERDUE->value;
        }

        DB::transaction(function () use ($borrow, $returnDate, $penalty, $status, $performedBy) {
            $borrow->return_date = $returnDate;
            $borrow->penalty = $penalty;
            $borrow->status = $status;
            $borrow->updated_by = $performedBy;
            $borrow->save();

            $borrow->book->increment('stock');

            $remarks = $status === BorrowStatus::OVERDUE->value
                ? 'Dikembalikan terlambat, denda Rp' . number_format($penalty, 0, ',', '.')
                : 'Dikembalikan tepat waktu';

            BorrowHistory::create([
                'borrow_id' => $borrow->id,
                'status' => $status,
                'remarks' => $remarks,
                'created_at' => now(),
                'created_by' => $performedBy,
            ]);
        });

        $borrow->refresh();
        $borrow->load(['book:id,book_code,title,author', 'user:id,user_code,name']);

        return ['success' => true, 'data' => $borrow];
    }

    private function fail(string $code, string $message, int $status): array
    {
        return ['success' => false, 'code' => $code, 'message' => $message, 'status' => $status];
    }
}
