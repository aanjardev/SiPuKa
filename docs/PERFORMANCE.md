# Audit dan optimasi performa

Pengukuran dilakukan pada database lokal `db_sipk`, PHP 8.4.19, cache file,
dan queue database. Ini bukan hasil pengukuran hosting produksi atau browser
client. Waktu CLI dipengaruhi startup PHP dan OPcache CLI yang tidak aktif.

## Temuan dan perubahan

| Bagian | Sebelum | Sesudah |
| --- | --- | --- |
| Query beranda | 12 | 9 |
| Query katalog | 13 | 9 |
| Query dashboard | 24 | 18 |
| Gambar bawaan kamera | 846.871 byte | 35.192 byte (WebP) |
| Logo bawaan | 168.202 byte | 19.784 byte (WebP) |

Pengaturan dan data toko dipakai bersama hanya dalam satu request, sehingga
perubahan admin tidak tertahan cache lintas request. Dashboard memakai kembali
hasil agregasi dan filter rentang tanggal agar indeks tanggal dapat digunakan.

Gambar R2 ditayangkan melalui aplikasi secara default karena URL publik bucket
sebelumnya mengalami kegagalan sertifikat. Endpoint menyimpan salinan lokal selama
satu jam, menyediakan ETag/Last-Modified untuk respons 304, dan menghasilkan
thumbnail WebP 320/640/1280 piksel sesuai kebutuhan. Beranda, katalog, dan daftar
produk admin memakai thumbnail 640 piksel. Foto asli produk tetap disimpan.
Permintaan gambar tetap memakai tanda tangan URL dan validasi path.

Pada satu sampel logo 23.464 byte, pembacaan langsung R2 memakan 1.072,7 ms.
Pembacaan melalui service gambar: 279 ms untuk pengisian cache dan 0,3 ms pada
dua pembacaan cache berikutnya. Angka ini hanya proses service gambar, bukan
waktu bootstrap Laravel, jaringan pengguna, rendering browser, atau total halaman.

Upload logo partner dibatasi sisi terpanjang 640 piksel dan melewati pemeriksaan
HEAD yang tidak diperlukan karena folder upload sudah memakai UUID. Upload
melalui ImageUpload lainnya dibatasi 1920 piksel, dengan rasio gambar tetap.
Koneksi R2 memiliki batas koneksi 5 detik, request 20 detik, dan satu retry.

Bootstrap CDN yang duplikat dan Font Awesome kit yang duplikat di admin dihapus.
ApexCharts hanya dimuat di dashboard, AOS hanya di halaman Tentang Kami.
Stylesheet footer dan WhatsApp dimuat dari head. Gambar hero diberi prioritas
pemuatan, sedangkan gambar daftar menggunakan lazy loading dan decoding async.
Nomor pagination katalog juga dibatasi agar HTML tidak tumbuh mengikuti seluruh
jumlah halaman.

## Operasional

- Pastikan `storage/app` dapat ditulis. Cache berada di `storage/app/image-cache`.
- Scheduler menjalankan `images:prune-cache` setiap hari; perintah ini hanya
  menghapus file cache berumur lebih dari tujuh hari, bukan foto asli.
- Jalankan scheduler Laravel seperti konfigurasi server yang sudah ada; jangan
  menjalankan worker baru tanpa memperhatikan worker yang sudah aktif.
- Proses foto produk di lingkungan lokal tetap sinkron sesuai pola aplikasi.
  Di produksi, proses memakai antrean ketika queue bukan `sync`; worker harus aktif
  agar proses HTTP tidak menunggu seluruh pekerjaan gambar.
- Pada produksi gunakan build Vite, `APP_DEBUG=false`, OPcache, serta cache
  konfigurasi/view sesuai prosedur deployment. Pengaturan server client belum
  diubah dalam audit lokal ini.
- Jika CDN R2 sudah sehat dan terverifikasi, `R2_SERVE_VIA_APP=false` mengaktifkan
  kembali URL CDN untuk gambar asli. Thumbnail tetap melalui endpoint aplikasi.

## Verifikasi

Feature test mencakup cache R2 satu kali baca, respons 304, resize proporsional,
keutuhan foto asli, validasi signature/path, pembersihan cache, batas tahun/bulan
pada metrik dashboard, upload partner, dan regresi katalog/admin/transaksi.
Build frontend juga diperiksa. Uji waterfall jaringan, Core Web Vitals, beban
bersamaan, dan antrean pada hosting produksi masih diperlukan untuk menilai
keluhan client di lingkungan sebenarnya.

Tambahan sampel foto produk: sumber 213.434 byte menjadi thumbnail 45.304 byte;
pembuatan pertama 258,8 ms, lalu pembacaan cache 0,7 ms. Hasil ini bergantung
isi gambar dan tidak mewakili semua produk.

Aturan kompresi teks dan cache aset Vite ditambahkan ke `public/.htaccess`.
Aturan hanya aktif bila modul Apache terkait tersedia. Pengujian header lokal
belum menunjukkan gzip aktif; konfigurasi modul Apache produksi perlu diperiksa
pada server tujuan, tanpa mengubah konfigurasi global XAMPP dalam tugas ini.

Verifikasi akhir: 41 tes relevan lulus dengan total 409 assertion (40 tes dalam
satu run gabungan, ditambah tes pagination 100.000 halaman). Build Vite lulus.
