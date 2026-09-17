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
