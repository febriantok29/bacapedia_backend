# Bacapedia — Sistem Manajemen Perpustakaan Digital

Backend API untuk sistem manajemen perpustakaan digital "Bacapedia" — Perpustakaan Kota Sejahtera.

## Tech Stack

- PHP >= 8.3
- Laravel 13
- MariaDB/MySQL
- JWT Authentication (firebase/php-jwt)
- DomPDF (barryvdh/laravel-dompdf)

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

## Setup Database

Buat database `bacapedia_backend` di MySQL/MariaDB, lalu:
```bash
php artisan migrate --seed
```
atau
```bash
php artisan migrate:fresh --seed
```

Data seeder:
| Role | Email | Password |
|------|-------|----------|
| Admin | admin@bacapedia.com | password123 |
| Petugas | rina@bacapedia.com | password123 |
| Anggota | budi@gmail.com | password123 |

## Menjalankan Server

```bash
php artisan serve
```

Server berjalan di `http://localhost:8000`

## Artisan Commands

```bash
php artisan migrate:fresh --seed    # Reset database + seed
php artisan route:list              # Lihat semua route
php artisan serve                   # Jalankan server
php artisan cache:clear             # Clear cache
php artisan config:clear            # Clear config cache
```

## Struktur Project

```
app/
├── Http/
│   ├── Controllers/Api/    → AuthController, BookController, BorrowController,
│   │                         CategoryController, UserController
│   ├── Middleware/          → JwtMiddleware
│   └── Responses/          → ApiResponse (centralized)
├── Models/                 → User, Book, Category, Borrow, BorrowHistory,
│                             Config, ErrorLog, PasswordReset
├── Services/               → JwtService, BorrowService, ConfigService
└── Support/                → ApiErrorCodes, ApiMessages

database/
├── migrations/             → 8 migration files
└── seeders/                → Users, Categories, Books, Config, Borrows

routes/
├── api.php                 → Semua API routes (prefix /api/v1)
└── web.php                 → Root endpoint
```

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
| GET | `/api/v1/borrows` | JWT | Riwayat peminjaman (filter: status, user_id) |
| POST | `/api/v1/borrows` | JWT | Pinjam buku |
| GET | `/api/v1/borrows/{id}` | JWT | Detail peminjaman + history |
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

## Aturan Bisnis

1. Anggota hanya dapat meminjam buku jika stok > 0
2. Maksimal peminjaman aktif per anggota: 3 buku (dari s_config)
3. Batas waktu peminjaman: 7 hari (dari s_config)
4. Denda keterlambatan: Rp2.000/hari (dari s_config)
5. Stok otomatis berkurang saat dipinjam, bertambah saat dikembalikan
6. Hanya Admin yang dapat menghapus data buku atau kategori
7. Password wajib terenkripsi (bcrypt)

## Format Response

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
| Admin | Mengelola buku, kategori, anggota; melihat semua peminjaman |
| Petugas | Memproses peminjaman dan pengembalian |
| Anggota | Melihat katalog, meminjam buku, melihat riwayat sendiri |
