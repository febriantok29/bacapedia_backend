<?php

namespace App\Support;

class ApiMessages
{
    const SUCCESS_GENERIC = 'Berhasil';
    const SUCCESS_DATA_RETRIEVED = 'Data berhasil diambil';
    const DATA_CREATED = 'Data berhasil dibuat';
    const DATA_UPDATED = 'Data berhasil diperbarui';
    const DATA_DELETED = 'Data berhasil dihapus';

    const VALIDATION_FAILED = 'Data yang dikirim tidak valid';
    const UNAUTHORIZED = 'Tidak terautentikasi';
    const FORBIDDEN = 'Anda tidak memiliki akses';
    const NOT_FOUND = 'Data tidak ditemukan';
    const INTERNAL_ERROR = 'Terjadi kesalahan pada server';
    const METHOD_NOT_ALLOWED = 'Method tidak diizinkan';
    const ENDPOINT_NOT_FOUND = 'Endpoint tidak ditemukan';

    const TOKEN_EXPIRED = 'Token sudah kedaluwarsa';
    const TOKEN_INVALID = 'Token tidak valid';
    const TOKEN_REFRESHED = 'Token berhasil diperbarui';

    const LOGIN_SUCCESS = 'Login berhasil';
    const LOGIN_FAILED = 'Email atau password salah';
    const REGISTER_SUCCESS = 'Registrasi berhasil';
    const LOGOUT_SUCCESS = 'Logout berhasil';
    const PASSWORD_RESET_SUCCESS = 'Password berhasil direset';

    const BORROW_SUCCESS = 'Peminjaman berhasil';
    const RETURN_SUCCESS = 'Pengembalian berhasil';
    const STOCK_EMPTY = 'Stok buku habis';
    const BORROW_LIMIT_REACHED = 'Batas maksimal peminjaman aktif telah tercapai';
    const ALREADY_BORROWED = 'Anda sudah meminjam buku ini dan belum dikembalikan';
    const ALREADY_RETURNED = 'Buku sudah dikembalikan sebelumnya';
    const RETURN_FORBIDDEN = 'Hanya Admin atau Petugas yang dapat memproses pengembalian';
    const CANNOT_DELETE_SELF = 'Tidak dapat menghapus akun sendiri';

    const CATEGORY_NOT_FOUND = 'Kategori tidak ditemukan';
    const BOOK_NOT_FOUND = 'Buku tidak ditemukan';
    const USER_NOT_FOUND = 'Pengguna tidak ditemukan';
    const BORROW_NOT_FOUND = 'Data peminjaman tidak ditemukan';
}
