<?php

namespace App\Support\Enums;

enum BorrowStatus: string
{
    case ACTIVE = 'Aktif';
    case RETURNED = 'Dikembalikan';
    case OVERDUE = 'Terlambat';
}
