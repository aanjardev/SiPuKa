# Panduan Admin PusatKamera.id

## Persiapan aplikasi

- Jalankan `php artisan migrate` untuk menambah kategori harga manual, konten katalog, dan perlindungan pengiriman ulang. Jangan gunakan `migrate:fresh` pada database yang akan dipakai toko.
- Jalankan `npm run build` untuk menyiapkan aset. Pada produksi, jangan sertakan `public/hot`; file itu hanya dipakai saat Vite development berjalan.
- Pastikan penyimpanan R2 berfungsi. Cadangan gambar lokal memakai disk public dan memerlukan `php artisan storage:link`.
- Bila queue memakai database, jalankan worker sesuai konfigurasi aplikasi agar pemrosesan foto selesai.
- Pengguna awal memakai role manager. Seeder akun baru menggunakan `admin@pusatkamera.id` dan password dari `SEED_ADMIN_PASSWORD`; akun yang sudah ada tidak otomatis diganti.

## Urutan pengisian sebelum serah terima

1. **Data Toko:** isi nama toko fisik, alamat, telepon, peta, dan jadwal tujuh hari. Sistem hanya memakai satu toko. Transaksi menggunakan ID toko ini secara otomatis.
2. **Kategori Kamera:** isi jenis kamera/produk yang dipakai toko. Kategori ini wajib dipilih saat membuat produk dan sebelum kelulusan QC.
3. **Kategori Harga:** periksa nama, slug, rentang, serta status aktif agar sama dengan permintaan client. Klik Ubah Urutan, geser pegangan pada daftar kategori harga, lalu klik Simpan Urutan. Tombol Batal mengembalikan urutan sebelumnya; kategori baru ditempatkan paling bawah. Rentang adalah informasi kategori; sistem tidak memasukkan produk secara otomatis berdasarkan harga jual.
4. **Produk:** isi SKU unik, kategori kamera, kategori harga jika diperlukan, harga, stok, kondisi, grade, deskripsi, dan foto. Pilih foto utama lalu cek hasil melalui Lihat di Katalog.
5. **Pengaturan Web Katalog:** isi identitas, WhatsApp konsultasi, media sosial, teks hero, Tentang Kami, tiga layanan, galeri pelanggan, dan SEO. Alamat/peta/jam buka tetap berada di Data Toko.
6. Periksa beranda, filter katalog, detail produk, Tentang Kami, dan Kontak sebelum mengisi data transaksi nyata.

## Aturan kategori dan publikasi

- Kategori kamera wajib. Kategori harga opsional dan dipilih manual; produk tanpa pilihan tetap ada di katalog umum tetapi tidak masuk filter kategori harga.
- Contoh: produk harga Rp9 juta yang dipilihkan kategori 1 Jutaan tetap masuk kategori 1 Jutaan. Admin bertanggung jawab atas pilihan yang sesuai.
- Kategori harga nonaktif tidak ditampilkan pada filter atau pilihan baru. Produk lama boleh mempertahankan kategorinya saat diedit. Kategori yang masih digunakan produk/item QC tidak dapat dihapus.
- Stok nol berarti Stok Habis dan tetap bisa dilihat publik. Sembunyikan berarti tidak tampil di katalog. Arsip berarti keluar dari katalog dan daftar produk aktif.
- Produk baru tanpa foto disimpan tersembunyi. Unggah foto dan periksa pengaturan visibilitas sebelum publikasi.
- Penghapusan foto terakhir akan menyembunyikan produk. Menghapus satu dari beberapa foto tetap mempertahankan visibilitas dan memilih foto utama pengganti jika diperlukan.

## Operasional harian

1. Buat pembelian dan masukkan item. Simpan draft atau finalisasi sebagai Deal.
2. Proses item pada Quality Control. Item pembelian hanya bisa lolos QC setelah pembeliannya Deal.
3. Saat Lolos QC, kategori kamera, SKU, jumlah, harga jual, deskripsi, kondisi dan grade harus lengkap. Kategori harga tetap opsional. Produk baru dari QC perlu dilengkapi foto sebelum ditampilkan.
4. Kelulusan QC mencatat stok sekali. Pengiriman ulang dan pemrosesan ulang item yang sudah selesai tidak menambah stok; koreksi produk melalui Data Produk.
5. Pilih produk dan jumlah untuk penjualan. Sistem menolak jumlah melebihi stok. Pengiriman ulang formulir yang sama tidak membuat penjualan kedua. Edit penjualan menyesuaikan stok, dan penghapusan penjualan mengembalikan stoknya.
6. Cetak nota dan ekspor laporan PDF/Excel dari halaman transaksi. Tidak ada pemilihan cabang atau Smart Stock.

## Pemeriksaan serah terima

- Isi data nyata setelah identitas toko, kategori dan pengaturan website disetujui client.
- Jangan menghapus kategori yang digunakan transaksi/produk; gunakan nonaktif untuk kategori harga yang tidak ingin ditampilkan.
- Uji satu pembelian Deal, satu item QC, satu produk dengan foto, dan satu penjualan; cocokkan stok, nilai, nota dan laporan.
- Jalankan `php artisan test --testsuite=Feature`. Pengujian menggunakan `db_sipk_testing`, bukan database aplikasi.
- Data produk lama dibiarkan tanpa kategori harga; pilih kategori secara manual saat melengkapi data nyata.
