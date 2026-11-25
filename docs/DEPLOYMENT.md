# Deployment

## Sebelum deployment

1. Buat backup database penuh dan pastikan file backup dapat dibaca.
2. Atur `APP_ENV=production`, `APP_DEBUG=false`, serta seluruh kredensial melalui environment server.
3. Pastikan `ALLOW_PRODUCTION_SEEDING=false`.
4. Jalankan test terhadap database testing terpisah.

## Deploy aplikasi

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan optimize:clear
php artisan migrate --force
php artisan optimize
```

Jangan menjalankan `db:seed` atau `migrate:fresh` pada production. Seeder proyek ini berisi snapshot dan akan mengosongkan beberapa tabel master sebelum mengisinya kembali.

## Pemulihan

Jika migration gagal, hentikan deployment dan pulihkan backup database sebelum mengulang. Jangan menjalankan rollback otomatis bila migration berisi transformasi data tanpa terlebih dahulu memeriksa dampaknya.

## Catatan kondisi pembelian

Migration `2026_11_25_000000_expand_purchase_condition_notes` memperluas kolom
`item_pembelian_draft.kondisi_lain_lain` menjadi TEXT. Jalankan migration sebelum
memakai kode pembelian yang menerima catatan hingga 5.000 karakter, lalu gunakan
aset hasil `npm run build`. Data lama tetap tersimpan. Rollback ditolak bila masih
ada catatan melebihi 255 karakter agar data tidak terpotong.

## Perubahan grade A/B/C

Migration `2026_11_03_000000_convert_product_grades_to_abc` mengubah grade pada
`produk` dan `item_pembelian_draft`: Unggulan menjadi A, Standar menjadi B,
dan Minus menjadi C. Nilai default menjadi B; grade QC yang NULL tetap NULL.
Migration memperluas enum sebelum mengonversi data, lalu membatasi enum ke A/B/C.
Rollback memetakan A/B/C kembali ke nilai lama.

Backup database sebelum menjalankan migration. Hentikan sementara penulisan produk
serta QC selama deployment agar kode dan enum database tidak berbeda versi.
Jalankan `php artisan migrate --force` bersama rilis kode ini sebelum membuka
kembali akses penulisan. Jangan jalankan seeder untuk melakukan konversi grade.

## Section partner beranda

Jalankan migration `2026_11_03_010000_create_partners_table` bersama rilis fitur
partner. Kelola data melalui menu admin **Partner** (`/admin/partners`). Logo
mengikuti alur ImageUpload (R2 dengan fallback lokal), maksimal 2 MB, format
PNG/JPG/WebP. Partner aktif ditampilkan menurut urutan penambahan (ID).

Saat tabel partner kosong, beranda menampilkan tiga contoh partner fiktif dari
aset lokal; tidak ada seeder yang dijalankan. Contoh otomatis hilang setelah
partner pertama ditambahkan. Jika semua partner disembunyikan, section juga
tersembunyi. Backup database dan penyimpanan gambar sebelum pemulihan data.

Migration `2026_11_03_020000_add_submission_token_to_partners_table` diperlukan
untuk pengaman submit ganda partner. Logo partner ditayangkan lewat endpoint
gambar bertanda tangan aplikasi agar tidak bergantung pada domain publik R2.


## Panduan Jual Kamera

Migration `2026_11_04_000000_create_sell_camera_posters_table` membuat tabel
poster khusus tanpa mengisi data contoh. Halaman publik berada di `/jual-kamera`,
dan pengelolaannya di `/admin/sell-camera-posters` melalui menu **Panduan Jual Kamera**.
Backup database sebelum menerapkan migration sesuai prosedur di atas.

Admin dapat mengunggah maksimal 5 poster per batch, 2 MB per gambar dan total
6 MB. Format PNG/JPG/WebP, dimensi maksimal 5000 x 5000 piksel. Urutan diatur
melalui tombol Naik/Turun; upload baru selalu ditambahkan setelah poster terakhir.
Gambar asli disimpan melalui R2 dengan fallback lokal. Pratinjau memakai cache
gambar 1280 piksel; pembesaran membuka gambar asli. Tidak memerlukan storage symlink.

Pastikan batas upload PHP/web server minimal 2 MB per file dan 8 MB per request.
Jika server memakai batas lebih kecil, sesuaikan konfigurasi server agar konsisten.
Upload batch memakai identitas unik dan transaksi; kegagalan batch membersihkan
file baru. Penggantian gambar menghapus file lama setelah data baru berhasil disimpan.
Nomor WhatsApp menggunakan pengaturan nomor konsultasi website, dengan fallback
ke halaman kontak jika belum diatur.

Pemeriksaan fitur:

```bash
php artisan test tests/Feature/SellCameraPosterTest.php
node --test tests/js/sell-camera.test.cjs
```


## Testimoni Pelanggan

Migration `2026_11_04_010000_create_testimonials_table` membuat tabel testimoni
terpisah tanpa data contoh. Halaman publik `/testimoni` menampilkan 12 ulasan aktif
per halaman, terbaru ditambahkan lebih dahulu. Menu admin **Testimoni** tersedia
di `/admin/testimonials` dengan akses autentikasi seperti pengelolaan konten lainnya.

Admin mengisi nama pelanggan (120 karakter), ulasan (2000), nama sumber (80), dan
tautan HTTP/HTTPS (2048). Rating 1-5, tanggal ulasan, dan screenshot opsional.
Tanggal ulasan tidak boleh di masa depan. Testimoni aktif secara default dan
bisa disembunyikan melalui form edit. Website tidak menghitung rating keseluruhan
toko dari ulasan pilihan dan tidak mengambil data otomatis melalui API.

Screenshot PNG/JPG/WebP maksimal 2 MB dan 5000 x 5000 piksel. Gambar asli disimpan
melalui OriginalImageStorage (R2 dengan fallback lokal), yang juga dipakai poster
panduan jual kamera. Pratinjau 640 piksel memakai cache gambar aplikasi, sedangkan
dialog pembesaran membuka file asli. Menghapus dan mengganti screenshot sekaligus
ditolak agar maksud perubahan jelas; gambar lama baru dibersihkan setelah database
berhasil disimpan. Token unik mencegah submit tambah testimoni yang sama membuat
duplikat. Backup database sebelum menerapkan migration sesuai prosedur di atas.

Pemeriksaan:

```bash
php artisan test tests/Feature/TestimonialTest.php
node --test tests/js/testimonial-form.test.cjs
```
