<?php

namespace App\Support\Enums;

enum UserRole: string
{
    case ADMIN = 'Admin';
    case OFFICER = 'Petugas';
    case MEMBER = 'Anggota';
}
