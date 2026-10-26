# AGENTS.md

## Proyek

- Aplikasi inventaris, transaksi, dan katalog toko kamera berbasis Laravel 10.
- Backend menggunakan PHP ^8.1, Laravel, Eloquent, Blade, dan PHPUnit.
- Frontend menggunakan Blade, Bootstrap 5, JavaScript, serta Vite.
- Bahasa UI dan domain utama adalah Bahasa Indonesia. Pertahankan istilah domain yang sudah dipakai seperti produk, penjualan, pembelian, cabang, karyawan, dan kategori.

## Struktur penting

- `app/Http/Controllers`: controller HTTP.
- `app/Http/Requests`: validasi request.
- `app/Models`: model Eloquent.
- `app/Services`: logika aplikasi yang dipakai lintas controller.
- `app/Jobs`: pekerjaan antrean.
- `routes/web.php` dan `routes/api.php`: route.
- `resources/views`: template Blade.
- `resources/js` dan `public/js`: kode JavaScript.
- `database/migrations`, `database/seeders`, `database/factories`: skema dan data.
- `tests/Feature`, `tests/Unit`, `tests/Browser`: pengujian.
- `docs/DEPLOYMENT.md`: langkah deployment dan pemulihan.

## Cara bekerja

- Ikuti pola Laravel yang sudah ada; periksa implementasi terkait sebelum mengubah controller, model, view, route, atau migration.
- Untuk fitur baru, tempatkan validasi pada Form Request bila sesuai, dan pisahkan logika domain yang digunakan ulang ke service.
- Gunakan route bernama dan helper route di Blade bila pola di sekitarnya melakukannya.
- Terapkan middleware autentikasi/otorisasi yang sesuai untuk semua route admin. Jangan mengandalkan penyembunyian tombol di UI sebagai kontrol akses.
- Pertahankan kompatibilitas dengan skema dan konvensi basis data yang ada. Periksa relasi, indeks, foreign key, constraint, dan dampak data sebelum mengubah migration.
- Migration harus aman untuk data yang sudah ada dan memiliki langkah `down()` yang masuk akal. Jangan mengubah migration lama yang sudah dipakai; buat migration baru.
- Jangan menaruh rahasia atau kredensial di source code. Gunakan konfigurasi Laravel dan environment.
- Untuk upload gambar/aset, ikuti alur penyimpanan dan akses file yang sudah dipakai aplikasi; validasi tipe dan ukuran, serta tangani kegagalan penyimpanan.
- Pertahankan proteksi CSRF, validasi server, escaping Blade, dan pembatasan laju pada endpoint sensitif.
- Hindari perubahan format besar atau perubahan file yang tidak berhubungan dengan tugas.

## Verifikasi

Pilih pemeriksaan yang relevan dengan perubahan; jangan menjalankan semuanya tanpa kebutuhan.

- Suite PHP: `php artisan test`
- Satu file tes: `php artisan test tests/Feature/NamaTest.php`
- Format PHP: `vendor/bin/pint --test`
- Build frontend: `npm run build`

Tambahkan atau sesuaikan feature test untuk perilaku yang berubah, terutama akses admin, validasi, transaksi, katalog, dan alur penyimpanan gambar. Jangan menjalankan tes yang memakai database sebelum memastikan konfigurasi menunjuk ke database testing yang aman.

## Batas keselamatan data

- Jangan jalankan `migrate:fresh`, `db:wipe`, seeder, atau perintah destruktif terhadap database tanpa memastikan lingkungan dan targetnya.
- Jangan menjalankan seeding pada production. Seeder proyek dapat menghapus atau mengganti data master.
- Ikuti prosedur backup, deployment, dan pemulihan di `docs/DEPLOYMENT.md`.

