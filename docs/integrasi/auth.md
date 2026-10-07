# 🔐 Autentikasi & produksi

Autentikasi memakai [Laravel Fortify](https://laravel.com/docs/fortify) (headless: Fortify menyediakan route dan logika, view Blade disediakan starter kit ini). Fitur yang aktif ada di `config/fortify.php`: login/logout, atur ulang kata sandi, serta pembaruan profil dan kata sandi dari halaman pengaturan. Registrasi, verifikasi email, dua langkah, dan passkey **dinonaktifkan**.

## 🔑 Login dan view

View didaftarkan di `app/Providers/FortifyServiceProvider.php`:

| View | Route | Berkas |
| --- | --- | --- |
| Masuk | `login` / `login.store` | `resources/views/auth/login.blade.php` |
| Lupa kata sandi | `password.request` / `password.email` | `resources/views/auth/forgot-password.blade.php` |
| Atur ulang kata sandi | `password.reset` / `password.update` | `resources/views/auth/reset-password.blade.php` |

Ketiganya memakai `<x-auth.shell>` (`resources/views/components/auth/shell.blade.php`). Login dibatasi 5 percobaan per menit per email+IP. Setelah login, pengguna diarahkan ke `/dashboard` (`home` di `config/fortify.php`). Tombol **Keluar** ada di sidebar dan mengirim `POST /logout`.

Pengaturan akun (`/settings`) mengirim `PUT user/profile-information` dan `PUT user/password`. Pesan galat tampil per bag (`updateProfileInformation`, `updatePassword`).

Untuk membuat pengguna pertama: `php artisan db:seed` (membuat `test@example.com`, kata sandi `password`) atau `php artisan tinker`. Ganti sebelum produksi.

Email atur ulang kata sandi memakai driver `MAIL_MAILER` di `.env` (default `log`, lihat `storage/logs`).

## 🛡️ Route admin

Dashboard, analitik, pengaturan, katalog komponen, dan contoh entri berada di dalam `Route::middleware('auth')` pada `routes/web.php`. Route CRUD baru yang privat juga harus masuk ke grup ini. `/demo/404` dan `/testing` tetap publik; hapus atau lindungi `/testing` sebelum produksi.

Lindungi juga setiap operasi tulis dengan pemeriksaan izin (Policy/Gate) sesuai aturan proyek.

## 🚢 Saat deploy

Siapkan `.env` produksi dan database aplikasi, jalankan migrasi sesuai skema baru, lalu bangun aset Laravel dengan `npm run build`. Dokumentasi dibangun terpisah lewat `npm run docs:build` menjadi berkas statis di `docs/.vitepress/dist`; Anda bisa menerbitkannya sebagai situs dokumentasi statis tanpa mengarahkan request docs ke Laravel. Gunakan `ADMIN_SHOWCASE=false` jika katalog contoh tidak ingin ditampilkan pada aplikasi produksi, dan periksa tautan menuju showcase sebelum menonaktifkannya.
