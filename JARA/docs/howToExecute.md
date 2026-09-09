# How to Execute JARA Locally

Panduan ini mengasumsikan source code Laravel sudah tersedia dan MySQL berjalan lokal. Schema tidak boleh dibuat manual melalui phpMyAdmin; phpMyAdmin hanya boleh dipakai untuk membuat database kosong bila diperlukan.

## 1. Verifikasi Tools

```bash
php -v
composer --version
mysql --version
```

Versi PHP harus memenuhi constraint pada `composer.json`.

## 2. Ambil Source dan Install Dependency

Masuk ke folder repository, kemudian:

```bash
composer install
```

Jika project final benar-benar memakai Vite:

```bash
npm install
```

## 3. Buat Environment File

Linux/macOS/Git Bash:

```bash
cp .env.example .env
```

Windows PowerShell:

```powershell
Copy-Item .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

## 4. Siapkan MySQL

Buat satu database kosong bernama `jara`. Contoh melalui MySQL client:

```sql
CREATE DATABASE jara CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Atur `.env` sesuai akun lokal:

```dotenv
APP_NAME=JARA
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jara
DB_USERNAME=root
DB_PASSWORD=
```

Jangan commit `.env`. Jika MySQL memakai password/port berbeda, ubah hanya nilai lokal tersebut.

## 5. Bangun Schema dan Data Minimum

Untuk instalasi pertama:

```bash
php artisan migrate --seed
```

Untuk reset database development secara penuh (menghapus seluruh data lokal dalam database yang dikonfigurasi):

```bash
php artisan migrate:fresh --seed
```

Pastikan `.env` menunjuk ke database development yang benar sebelum menjalankan `migrate:fresh`.

Admin development hasil seed:

- Email: `admin@jara.test`
- Password: `password`

## 6. Jalankan Aplikasi

```bash
php artisan serve
```

Buka `http://127.0.0.1:8000`.

Jika Vite digunakan, biarkan server Laravel berjalan dan pada terminal kedua jalankan:

```bash
npm run dev
```

## 7. Quick Smoke Test

1. Login sebagai admin; pastikan daftar user dapat dibuka.
2. Buat satu akun regular dari admin atau register sebagai guest.
3. Login regular; buat project dan pastikan progress `Not Started`.
4. Buat task; ubah status menjadi `In Progress`, lalu `Done`; pastikan progress berubah.
5. Register akun kedua; dari project akun pertama, tambah email akun kedua sebagai collaborator.
6. Login akun kedua; pastikan project terlihat dan dapat dikelola.
7. Gunakan akun ketiga/non-member untuk membuka URL project; pastikan mendapat 403.

## 8. Run Tests

Jika feature tests tersedia:

```bash
php artisan test
```

Sebelum submission, gunakan checklist lengkap pada `design.md`.

## 9. Common Problems

- **`Access denied for user`:** periksa `DB_USERNAME`, `DB_PASSWORD`, host, dan port di `.env`.
- **`Unknown database 'jara'`:** buat database kosong dahulu; tabel tetap dibuat oleh migration.
- **Perubahan `.env` tidak terbaca:** jalankan `php artisan config:clear`.
- **`No application encryption key`:** jalankan `php artisan key:generate`.
- **Table tidak ditemukan:** jalankan `php artisan migrate`; untuk database development yang boleh dihapus, gunakan `migrate:fresh --seed`.
- **CSS/asset tidak termuat:** hanya bila Vite digunakan, jalankan `npm install` dan `npm run dev`.
- **403:** pastikan akun merupakan member project atau role admin untuk area `/admin/users`; admin tidak otomatis dapat membuka project orang lain.
- **404 nested task:** pastikan task memang berada dalam project pada URL; scoped binding sengaja menolak kombinasi project-task yang salah.

## 10. Reproducibility Check

Pada database development kosong, instalasi dianggap reproducible bila rangkaian berikut berhasil tanpa perubahan tabel manual:

```bash
composer install
php artisan key:generate
php artisan migrate:fresh --seed
php artisan test
php artisan serve
```
