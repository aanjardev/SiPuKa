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
