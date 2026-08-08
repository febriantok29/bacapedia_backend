# Bacapedia, Sistem Manajemen Perpustakaan Digital

Backend API + Web UI untuk sistem manajemen perpustakaan digital "Bacapedia"

## Tech Stack

- PHP >= 8.3
- Laravel 13
- MariaDB/MySQL
- JWT Authentication (firebase/php-jwt)
- DomPDF (barryvdh/laravel-dompdf)
- Tailwind CSS (CDN)

## Instalasi

```bash
git clone https://github.com/febriantok29/bacapedia_backend.git
cd bacapedia_backend

composer install

cp .env.example .env
php artisan key:generate
```

Generate JWT Secret:
```bash
php -r "echo bin2hex(random_bytes(32));"
```
Copy hasilnya ke `.env` field `JWT_SECRET=`.

## Konfigurasi Database

Edit `.env`:
```env
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bacapedia_backend
DB_USERNAME=root
DB_PASSWORD=
```

Buat database `bacapedia_backend`, lalu jalankan:
```bash
php artisan migrate --seed
```

## Menjalankan Server

```bash
php artisan serve
```

- Web UI: `http://localhost:8000`
- API: `http://localhost:8000/api/v1/`

## Akun Testing (Seeder)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@bacapedia.com | password123 |
| Petugas | rina@bacapedia.com | password123 |
| Anggota | budi@gmail.com | password123 |

## Artisan Commands

```bash
php artisan migrate:fresh --seed    # Reset database + seed
php artisan route:list              # Lihat semua route
php artisan serve                   # Jalankan server
php artisan cache:clear             # Clear cache
php artisan config:clear            # Clear config cache
```

## Halaman Web UI

| URL | Auth | Keterangan |
|-----|------|-----------|
| `/login` | - | Login |
| `/register` | - | Daftar anggota baru |
| `/` | Login | Dashboard statistik |
| `/books` | Login | Katalog buku (Admin/Petugas bisa CRUD) |
| `/categories` | Login | Kategori buku (Admin/Petugas bisa CRUD) |
| `/borrows` | Login | Peminjaman (multi-borrow, return, filter) |
| `/borrows/{id}` | Login | Detail peminjaman + riwayat status |
| `/users` | Admin | Manajemen user |

## Daftar Endpoint API

### Autentikasi

| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|-----------|
| POST | `/api/v1/auth/register` | - | Registrasi anggota baru |
| POST | `/api/v1/auth/login` | - | Login |
| POST | `/api/v1/auth/refresh` | - | Refresh token |
| GET | `/api/v1/auth/me` | JWT | Profil user yang login |
| POST | `/api/v1/auth/logout` | JWT | Logout |

### Kategori

| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|-----------|
| GET | `/api/v1/categories` | JWT | List kategori |
| POST | `/api/v1/categories` | JWT (Admin) | Buat kategori |
| GET | `/api/v1/categories/{id}` | JWT | Detail kategori |
| PUT | `/api/v1/categories/{id}` | JWT (Admin) | Update kategori |
| DELETE | `/api/v1/categories/{id}` | JWT (Admin) | Hapus kategori |

### Buku

| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|-----------|
| GET | `/api/v1/books` | JWT | List buku (filter: search, category_id, published_year) |
| POST | `/api/v1/books` | JWT (Admin) | Buat buku (auto generate book_code) |
| GET | `/api/v1/books/{id}` | JWT | Detail buku |
| PUT | `/api/v1/books/{id}` | JWT (Admin) | Update buku |
| DELETE | `/api/v1/books/{id}` | JWT (Admin) | Hapus buku |

### Peminjaman

| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|-----------|
| GET | `/api/v1/borrows` | JWT | Riwayat peminjaman (filter: status, user_id, is_overdue) |
| GET | `/api/v1/borrows/summary` | JWT | Statistik peminjaman |
| POST | `/api/v1/borrows` | JWT | Pinjam buku |
| GET | `/api/v1/borrows/{id}` | JWT | Detail peminjaman + history |
| PUT | `/api/v1/borrows/{id}` | JWT (Admin) | Update data peminjaman |
| POST | `/api/v1/borrows/{id}/return` | JWT (Admin/Petugas) | Proses pengembalian |

### Manajemen User (Admin Only)

| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|-----------|
| GET | `/api/v1/users` | JWT (Admin) | List user |
| POST | `/api/v1/users` | JWT (Admin) | Buat user (semua role) |
| GET | `/api/v1/users/{id}` | JWT (Admin) | Detail user |
| PUT | `/api/v1/users/{id}` | JWT (Admin) | Update user |
| DELETE | `/api/v1/users/{id}` | JWT (Admin) | Hapus user |
| POST | `/api/v1/users/{id}/reset-password` | JWT (Admin) | Reset password |

### Konfigurasi (Admin Only)

| Method | Endpoint | Auth | Keterangan |
|--------|----------|------|-----------|
| GET | `/api/v1/configs` | JWT (Admin) | List config |
| POST | `/api/v1/configs` | JWT (Admin) | Buat config |
| GET | `/api/v1/configs/{id}` | JWT (Admin) | Detail config |
| PUT | `/api/v1/configs/{id}` | JWT (Admin) | Update config |
| DELETE | `/api/v1/configs/{id}` | JWT (Admin) | Hapus config |

## Aturan Bisnis

1. Anggota hanya dapat meminjam buku jika stok > 0
2. Maksimal peminjaman aktif per anggota: 3 buku (dari s_config)
3. Batas waktu peminjaman: 7 hari (dari s_config)
4. Denda keterlambatan: Rp2.000/hari (dari s_config)
5. Stok otomatis berkurang saat dipinjam, bertambah saat dikembalikan
6. Hanya Admin yang dapat menghapus data buku atau kategori
7. Password wajib terenkripsi (bcrypt)

## Struktur Database

| Tabel | Tipe | Keterangan |
|-------|------|-----------|
| `s_users` | System | Data pengguna (Admin, Petugas, Anggota) |
| `m_categories` | Master | Kategori buku |
| `m_books` | Master | Data buku |
| `t_borrows` | Transaksi | Data peminjaman |
| `h_borrows` | History | Log perubahan status peminjaman |
| `s_config` | System | Konfigurasi dinamis (batas pinjam, denda, durasi) |
| `s_error_logs` | System | Log error internal |
| `s_password_resets` | System | Token reset password |

## Struktur Project

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/            → AuthController, BookController, BorrowController,
│   │   │                     CategoryController, ConfigController, UserController
│   │   ├── Concerns/       → NormalizesFilterValues trait
│   │   └── WebController   → Blade panel controller
│   ├── Middleware/          → JwtMiddleware, PanelAuth
│   └── Responses/          → ApiResponse (centralized)
├── Models/                 → User, Book, Category, Borrow, BorrowHistory,
│                             Config, ErrorLog, PasswordReset
├── Services/               → JwtService, BorrowService, ConfigService
└── Support/
    ├── Enums/              → UserRole, BorrowStatus
    ├── ApiErrorCodes.php
    └── ApiMessages.php

resources/views/
├── components/             → layout, guest-layout, card, table, alert,
│                             badge, button, input, sidebar-link, stat-card
└── pages/                  → login, register, dashboard, books,
                              categories, users, borrows, borrow-detail

database/
├── migrations/             → 8 migration files
└── seeders/                → Users, Categories, Books, Config, Borrows
```

## Format Response API

### Success
```json
{
  "success": true,
  "message": "Data berhasil diambil",
  "data": {}
}
```

### Error
```json
{
  "success": false,
  "error_code": "VALIDATION_ERROR",
  "message": "Data yang dikirim tidak valid",
  "errors": {}
}
```

## Role & Hak Akses

| Role | Hak Akses |
|------|-----------|
| Admin | Mengelola user, melihat semua data, konfigurasi sistem |
| Petugas | Mengelola buku, kategori, memproses peminjaman dan pengembalian |
| Anggota | Melihat katalog, meminjam buku, melihat riwayat sendiri |
