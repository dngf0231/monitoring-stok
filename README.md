# Stock ATK

Stock ATK adalah aplikasi inventaris sederhana berbasis Laravel 12 untuk mengelola data barang, barang masuk, barang keluar, pengguna, dan role akses dinamis.

## Fitur Utama

- Dashboard stok ATK.
- CRUD data barang.
- Pencatatan barang masuk.
- Pengajuan barang keluar oleh user.
- Approve/reject barang keluar oleh role yang punya akses.
- Manajemen pengguna.
- Manajemen role akses dengan permission checklist dinamis.

## Kebutuhan Sistem

- PHP 8.2 atau lebih baru.
- Composer.
- MySQL/MariaDB.
- Web server lokal seperti Laragon, atau `php artisan serve`.

## Instalasi

1. Install dependency PHP.

```bash
composer install
```

2. Buat file environment.

```bash
cp .env.example .env
```

Untuk Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

3. Generate application key.

```bash
php artisan key:generate
```

4. Buat database MySQL, default pada `.env.example`:

```text
DB_DATABASE=mystock
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan nilai tersebut jika database lokal berbeda.

5. Jalankan migrasi dan seeder.

```bash
php artisan migrate --seed
```

6. Jalankan aplikasi.

Jika memakai Laragon, arahkan virtual host ke folder project dan buka:

```text
http://atk-test.test
```

Jika memakai server bawaan Laravel:

```bash
php artisan serve
```

Lalu buka URL yang muncul di terminal.

Catatan: template saat ini memakai CDN untuk Tailwind, Bootstrap, Font Awesome, dan SweetAlert, jadi tidak perlu `npm install` atau build asset frontend untuk menjalankan aplikasi.

## Akun Seeder

Seeder membuat akun awal berikut:

```text
Admin
Email: admin@example.com
Password: password

User
Email: user@example.com
Password: password
```

Segera ganti password jika aplikasi dipakai di luar lokal.

## Data Seeder

Seeder juga membuat:

- Role `admin` dengan semua permission.
- Role `user` dengan akses dasar.
- Contoh list barang ATK.
- Contoh transaksi barang masuk.
- Contoh transaksi barang keluar.

Seeder utama ada di `database/seeders/DatabaseSeeder.php`.

## Role Akses

Permission role disimpan pada kolom JSON `roles.permissions`. Daftar permission didefinisikan di `app/Models/Role.php` melalui konstanta `AVAILABLE_PERMISSIONS`.

Untuk menambah permission baru:

1. Tambahkan key permission di `Role::AVAILABLE_PERMISSIONS`.
2. Tambahkan Gate atau pengecekan akses yang sesuai jika fitur baru membutuhkan authorization.
3. Jalankan atau update seeder bila permission default perlu ikut berubah.

## Command Berguna

```bash
php artisan migrate:fresh --seed
php artisan optimize:clear
php artisan route:list
php artisan view:clear
```

## Testing

```bash
php artisan test
```

Catatan lokal: test bawaan Laravel memakai SQLite in-memory. Pastikan ekstensi `pdo_sqlite` aktif di PHP jika ingin menjalankan test tersebut.

