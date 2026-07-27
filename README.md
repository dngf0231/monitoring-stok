# Stock ATK

Stock ATK adalah aplikasi inventaris ATK berbasis Laravel 12 untuk mengelola stok barang, transaksi barang masuk, pengajuan barang keluar, pengguna, role akses dinamis, dan API integrasi.

Repository: [danajoo01/monitoring-stok](https://github.com/danajoo01/monitoring-stok)

## Fitur

- Dashboard monitoring stok.
- CRUD data barang.
- Barang masuk dengan penambahan stok otomatis.
- Barang keluar dengan alur pengajuan, approve, dan reject.
- Manajemen pengguna dengan status akun `active` / `inactive`.
- Registrasi user baru dengan status awal `inactive`, wajib diaktifkan admin melalui web panel.
- Manajemen role akses dengan permission checklist dinamis.
- Permission role berlaku untuk akses web panel dan route API yang membutuhkan otorisasi.
- Tabel utama memakai DataTables server-side via CDN.
- API Bearer Token untuk login, register, barang, barang masuk, dan barang keluar.
- Dokumentasi API tersedia di halaman web.
- Koleksi Postman siap import.
- Log Activity untuk melihat aktivitas CRUD, auth, approval, dan penggunaan API.

## Kebutuhan Sistem

- PHP 8.2 atau lebih baru.
- Composer.
- MySQL/MariaDB.
- Web server lokal seperti Laragon, atau server bawaan Laravel.

Project ini memakai CDN untuk Tailwind, Bootstrap, Font Awesome, SweetAlert, jQuery, dan DataTables. Tidak perlu `npm install` atau build asset frontend untuk menjalankan aplikasi.

## Instalasi

1. Install dependency PHP.

```bash
composer install
```

2. Buat file `.env`.

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

4. Buat database MySQL sesuai `.env`.

Default pada `.env.example`:

```text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mystock
DB_USERNAME=root
DB_PASSWORD=
```

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

## Akun Demo

Seeder membuat akun demo berikut:

```text
Admin
Email: admin@example.com
Password: password

User
Email: user@example.com
Password: password
```

Akun demo juga ditampilkan di halaman login untuk kebutuhan demo.

## Alur Akun

- Registrasi dari halaman `/register` membuat akun baru dengan role `user`.
- Status akun baru adalah `inactive`.
- Akun `inactive` tidak bisa login.
- Admin harus membuka menu `Pengguna`, lalu mengubah status akun menjadi `active`.
- Admin juga bisa menonaktifkan akun existing melalui menu yang sama.

## Data Seeder

Seeder membuat:

- Role `admin` dengan semua permission.
- Role `user` dengan akses dasar.
- Akun demo admin dan user.
- Contoh data barang ATK.
- Contoh transaksi barang masuk.
- Contoh transaksi barang keluar.

Seeder utama ada di:

```text
database/seeders/DatabaseSeeder.php
```

## Role Akses

Permission role disimpan pada kolom JSON:

```text
roles.permissions
```

Daftar permission didefinisikan di:

```text
app/Models/Role.php
```

melalui konstanta:

```php
Role::AVAILABLE_PERMISSIONS
```

Untuk menambah permission baru:

1. Tambahkan key permission di `Role::AVAILABLE_PERMISSIONS`.
2. Tambahkan Gate atau pengecekan akses pada fitur terkait.
3. Update seeder jika permission default role perlu ikut berubah.

Permission pada menu Manajemen Role Akses juga dipakai oleh endpoint API. Contoh: role yang tidak memiliki `barang_masuk.create` tidak bisa membuat barang masuk lewat web ataupun `POST /api/barang-masuk`.

## Log Activity

Menu `Log Activity` menampilkan riwayat aktivitas project dari web panel dan API.

Aktivitas yang dicatat meliputi:

- Login, logout, dan register.
- CRUD data barang.
- Pencatatan barang masuk.
- Pengajuan, approve, dan reject barang keluar.
- CRUD pengguna dan perubahan status akun.
- CRUD role akses.
- Aktivitas endpoint API dengan channel `api`.

## API

Dokumentasi API tersedia di:

```text
http://atk-test.test/api-docs
```

Dokumentasi Postman online:

```text
https://documenter.getpostman.com/view/18717448/2sBY4Qu1PD
```

Base URL API:

```text
http://atk-test.test/api
```

API menggunakan Bearer Token. Token didapat dari endpoint login.

### Auth

```text
POST /api/register
POST /api/login
GET  /api/me
POST /api/logout
```

Catatan:

- Register API membuat akun `inactive`.
- Akun hasil register API harus diaktifkan admin lewat web panel.
- Login API hanya berhasil untuk akun `active`.

### Data Barang

```text
GET    /api/barang
POST   /api/barang
GET    /api/barang/{id}
PUT    /api/barang/{id}
DELETE /api/barang/{id}
```

### Barang Masuk

```text
GET  /api/barang-masuk
POST /api/barang-masuk
```

Saat barang masuk dibuat, stok barang otomatis bertambah.

### Barang Keluar

```text
GET  /api/barang-keluar
POST /api/barang-keluar
POST /api/barang-keluar/{id}/approve
POST /api/barang-keluar/{id}/reject
```

Alur barang keluar:

- User membuat pengajuan barang keluar.
- Status awal pengajuan adalah `pending`.
- Admin atau role dengan permission terkait bisa approve/reject.
- Stok hanya dipotong saat pengajuan di-approve.

## Postman

File koleksi Postman tersedia di root project:

```text
postman_collection.json
```

Cara pakai:

1. Buka Postman.
2. Import `postman_collection.json`.
3. Jalankan request `Login Admin` atau `Login User`.
4. Token otomatis disimpan ke variable collection `token`.
5. Jalankan endpoint lain yang membutuhkan Bearer Token.

Default variable:

```text
base_url = http://{url}/api
```

Dokumentasi online Postman juga bisa dibuka melalui:

```text
https://documenter.getpostman.com/view/18717448/2sBY4Qu1PD
```

## Command Berguna

```bash
php artisan migrate --seed
php artisan migrate:fresh --seed
php artisan optimize:clear
php artisan route:list
php artisan route:list --path=api
php artisan view:clear
```

## Deploy Apache/Cloud Server

Rekomendasi terbaik untuk Laravel adalah mengarahkan document root domain ke folder:

```text
public
```

Jika hosting tidak menyediakan pengaturan document root ke `public`, project ini sudah menyediakan `.htaccess` di root untuk meneruskan request ke `public/index.php`. File `public/.htaccess` tetap dipakai untuk rewrite route Laravel dan menjaga header `Authorization` agar API Bearer Token berjalan.

## Testing

```bash
php artisan test
```

Created by Danang Fathurrohman
