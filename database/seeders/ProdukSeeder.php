<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\File;
use App\Models\Produk;
use App\Models\GambarProduk;
use App\Helpers\ImageUpload;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        GambarProduk::truncate();
        Produk::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $localDir = base_path('public/productIMG');
        if (!is_dir($localDir)) {
            throw new \RuntimeException("Seeder image directory not found: {$localDir}");
        }

        $produk = Produk::create([
            'kode_sku' => '1274OBOOPL',
            'id_kategori' => 4,
            'nama_produk' => 'Handycam Panasonic SDR S26 Second Kondisi Baik',
            'harga_jual' => 900000,
            'stok_produk' => 1,
            'status' => 'Second',
            'grade' => 'Unggulan',
            'deskripsi_produk' => 'KONDISI :
- Layar Flip 180°
- Flash Baik
- Tutup USB Baik
- Tombol Baik
- Body Paintlos
- Jamur Tipis / Debu Micro
(Tidak Berpengaruh pada Fungsi dan Hasil)
- AF/MF Baik
- LCD Baik dan Bersih
- Fungsional Normal

KELENGKAPAN:
- Kamera
- Lensa
- Baterai
- Charger
- Nota Pembelian Dinoyo Kamera'
        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/11.png', 'is_main' => true],
            ['path_gambar' => 'gambar_produk/12.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/13.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/14.jpg', 'is_main' => false],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => '1267OHOOVT',
            'id_kategori' => 1,
            'nama_produk' => 'Nikon D5200 + kit 18 55mm Kondisi Baik',
            'harga_jual' => 2450000,
            'stok_produk' => 1,
            'status' => 'Second',
            'grade' => 'Standar',
            'deskripsi_produk' => 'TYPE  : Nikon D5200 + kit 18 55mm

HARGA : 2.450.000

KONDISI :
● Layar Flip
● Flash Baik
● Tutup USB Baik
● Tombol Baik
● Body Paintlos
● Jamur Tipis / Debu Micro
(Tidak Berpengaruh pada Fungsi dan Hasil)
● AF/MF Baik
● LCD Vignet
● Fungsional Normal

KELENGKAPAN:
● Kamera
● Lensa
● Baterai
● Charger
● Nota Pembelian Dinoyo Kamera

Kode Barang : #1267_DK

Jual beli kamera malang raya
Kalian juga bisa jual kamera anda di toko kami, langsung aja ke Dinoyo Kamera

Barang siap pakai, silahkan di order

Untuk memaksimalkan pengiriman :
● Luar pulau jawa Wajib pakai JNE
● Dalam pulau Jawa wajib pakai JNT'

        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/21.png', 'is_main' => true],
            ['path_gambar' => 'gambar_produk/22.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/23.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/24.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/25.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/26.png', 'is_main' => false],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => '1271AEOOWS',
            'id_kategori' => 2,
            'nama_produk' => 'Fujifilm X a10 + kit 16 50mm (Pink) Kondisi Baik',
            'harga_jual' => 3300000,
            'stok_produk' => 1,
            'status' => 'Second',
            'grade' => 'Unggulan',
            'deskripsi_produk' => 'TYPE  : Fujifilm X a10 + kit 16 50mm (Pink)

HARGA : 3.300.000

KONDISI :
● Wifi Berfungsi
● Layar Flip 180°
● Flash Baik
● Tutup USB Baik
● Tombol Baik
● Body Paintlos
● Jamur Tipis / Debu Micro
(Tidak Berpengaruh pada Fungsi dan Hasil)
● AF/MF Baik
● LCD ada whitespot tipis
● Fungsional Normal

KELENGKAPAN:
● Kamera
● Lensa
● Baterai
● Charger
● Nota Pembelian Dinoyo Kamera

Kode Barang : #1271_DK

Jual beli kamera malang raya
Kalian juga bisa jual kamera anda di toko kami, langsung aja ke Dinoyo Kamera

Barang siap pakai, silahkan di order

Untuk memaksimalkan pengiriman :
● Luar pulau jawa Wajib pakai JNE
● Dalam pulau Jawa wajib pakai JNT'

        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/31.png', 'is_main' => true],
            ['path_gambar' => 'gambar_produk/32.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/33.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/34.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/35.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/36.png', 'is_main' => false],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }














































        $produk = Produk::create([
            'kode_sku' => '1065',
            'id_kategori' => 5,
            'nama_produk' => 'Instax mini Li Play Brown Second Kondisi Baik',
            'harga_jual' => 1900000,
            'stok_produk' => 1,
            'status' => 'Second',
            'grade' => 'Unggulan',
            'deskripsi_produk' => 'TYPE  : Instax mini Li Play Brown Second

HARGA : 1.900.000

KONDISI :
● Tutup USB Baik
● Tombol Baik
● Body Paintlos
● Jamur Tipis / Debu Micro
(Tidak Berpengaruh pada Fungsi dan Hasil)
● AF/MF Baik
● LCD Baik dan Bersih
● Fungsional Normal

KELENGKAPAN:
● Kamera
● Baterai
● Nota Pembelian Dinoyo Kamera

Kode Barang : #1065_DK

Jual beli kamera malang raya
Kalian juga bisa jual kamera anda di toko kami, langsung aja ke Dinoyo Kamera

Barang siap pakai, silahkan di order

Untuk memaksimalkan pengiriman :
● Luar pulau jawa Wajib pakai JNE
● Dalam pulau Jawa wajib pakai JNT'

        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/51.png', 'is_main' => true],
            ['path_gambar' => 'gambar_produk/52.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/53.jpg', 'is_main' => false],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => '1225OEOOPL',
            'id_kategori' => 3,
            'nama_produk' => 'Canon Ixus 185 Kondisi Baik',
            'harga_jual' => 2350000,
            'stok_produk' => 1,
            'status' => 'Second',
            'grade' => 'Unggulan',
            'deskripsi_produk' => 'TYPE : Canon Ixus 185

HARGA : 2.350.000

KONDISI :
● Flash Baik
● Tutup USB Baik
● Tombol Baik
● Body Paintlos
● Jamur Tipis / Debu Micro
(Tidak Berpengaruh pada Fungsi dan Hasil)
● AF Baik
● LCD Baik dan Bersih
● Fungsional Normal

KELENGKAPAN:
● Kamera
● Baterai
● Charger
● Nota Pembelian Dinoyo Kamera

Kode Barang : #1225_DK

Jual beli kamera malang raya
Kalian juga bisa jual kamera anda di toko kami, langsung aja ke Dinoyo Kamera

Barang siap pakai, silahkan di order

Untuk memaksimalkan pengiriman :
● Luar pulau jawa Wajib pakai JNE
● Dalam pulau Jawa wajib pakai JNT'

        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/61.png', 'is_main' => true],
            ['path_gambar' => 'gambar_produk/62.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/63.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/64.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/65.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/66.png', 'is_main' => false],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => '1052COOOPL',
            'id_kategori' => 6,
            'nama_produk' => 'Insta 360 One RS Twin (4k + 360) Kondisi Baik',
            'harga_jual' => 5250000,
            'stok_produk' => 1,
            'status' => 'Second',
            'grade' => 'Unggulan',
            'deskripsi_produk' => 'TYPE : Insta 360 One RS Twin double lensa (4k dan  360)
HARGA: 5.250.000


Fitur Utama :
Kamera Aksi Modular & Sistem Kamera 360
Video 5.7K30, Foto 18MP dengan Lensa 360
Lensa 4K: Video 4K60, Foto 48MP
Kapasitas Baterai 21% Lebih Banyak dari SATU R
3 Mikrofon untuk Audio Lebih Baik, Zoom Instan
HDR Aktif, Stabilisasi FlowState

Kondisi Second Baik

Body Fisik Baik

Tombol Baik

Touch Screen Baik

LCD Baik

WiFi Baik

Audio Baik

Fungsional Normal Baik

Jamur Tipis atau Debu Micro (Tidak Berpengaruh Pada Hasil)

KELENGKAPAN:

● Box
● Kamera 4k dan 360
● Baterai
● Nota Pembelian Dinoyo Kamera

Kode Barang : #1052_DK

Jual beli kamera malang raya
Kalian juga bisa jual kamera anda di toko kami, langsung aja ke Dinoyo Kamera

Barang siap pakai, silahkan di order

Untuk memaksimalkan pengiriman :
● Luar pulau jawa Wajib pakai JNE
● Dalam pulau Jawa wajib pakai JNT'

        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/71.png', 'is_main' => true],
            ['path_gambar' => 'gambar_produk/72.png', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/73.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/74.png', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/75.jpg', 'is_main' => false],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => 'ACC01',
            'id_kategori' => 8,
            'nama_produk' => 'Baterai Kamera Canon LP-E6',
            'harga_jual' => 250000,
            'stok_produk' => 10,
            'status' => 'Baru',
            'deskripsi_produk' => 'Baterai Kamera Canon LP-E6

Kompatibel dengan Kamera Canon R / 6D / 6DII / 7D II / 7D / 70D / 80D'
        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/81.png', 'is_main' => true],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => 'ACC02',
            'id_kategori' => 9,
            'nama_produk' => 'Memory Card Sandisk 16GB Ultra SDHC',
            'harga_jual' => 150000,
            'stok_produk' => 10,
            'status' => 'Baru',
            'deskripsi_produk' => 'Memory Card Sandisk 16GB Ultra SDHC'
        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/91.png', 'is_main' => true],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => 'ACC03',
            'id_kategori' => 10,
            'nama_produk' => 'Tali Strap Leher Kamera Motif Batik',
            'harga_jual' => 120000,
            'stok_produk' => 10,
            'status' => 'Baru',
            'deskripsi_produk' => '-'
        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/101.png', 'is_main' => true],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => 'ACC04',
            'id_kategori' => 10,
            'nama_produk' => 'Front/Rear Cap Lensa Canon Mirrorless, Tutup Body',
            'harga_jual' => 70000,
            'stok_produk' => 10,
            'status' => 'Baru',
            'deskripsi_produk' => '-'
        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/102.png', 'is_main' => true],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => 'ACC05',
            'id_kategori' => 9,
            'nama_produk' => 'Memory Card Sandisk 32GB Ultra SDHC',
            'harga_jual' => 250000,
            'stok_produk' => 10,
            'status' => 'Baru',
            'deskripsi_produk' => 'Memory Card Sandisk 32GB Ultra SDHC'
        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/92.png', 'is_main' => true],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => 'ACC06',
            'id_kategori' => 9,
            'nama_produk' => 'Memory Card Sandisk 64GB Ultra SDHC',
            'harga_jual' => 250000,
            'stok_produk' => 10,
            'status' => 'Baru',
            'deskripsi_produk' => 'Memory Card Sandisk 64GB Ultra SDHC'
        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/93.png', 'is_main' => true],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => 'ACC07',
            'id_kategori' => 8,
            'nama_produk' => 'Baterai Kamera Canon LP-E8',
            'harga_jual' => 250000,
            'stok_produk' => 10,
            'status' => 'Baru',
            'deskripsi_produk' => 'Baterai Kamera Canon LP-E8

Kompatibel dengan Kamera Canon R / 6D / 6DII / 7D II / 7D / 70D / 80D'
        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/82.png', 'is_main' => true],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => 'ACC08',
            'id_kategori' => 8,
            'nama_produk' => 'Baterai Kamera Canon LP-E10',
            'harga_jual' => 250000,
            'stok_produk' => 0,
            'status' => 'Baru',
            'deskripsi_produk' => 'Baterai Kamera Canon LP-E10

Kompatibel dengan Kamera Canon R / 6D / 6DII / 7D II / 7D / 70D / 80D'
        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/83.png', 'is_main' => true],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => '-',
            'id_kategori' => 8,
            'nama_produk' => 'Baterai Kamera Canon LP-E12',
            'harga_jual' => 250000,
            'stok_produk' => 10,
            'status' => 'Baru',
            'deskripsi_produk' => 'Baterai Kamera Canon LP-E12

Kompatibel dengan Kamera Canon R / 6D / 6DII / 7D II / 7D / 70D / 80D'
        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/84.png', 'is_main' => true],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => '1309OCOOJT',
            'id_kategori' => 7,
            'nama_produk' => 'Lensa 7artisan 50mm F1.8 Fuji Kondisi Baik',
            'harga_jual' => 750000,
            'stok_produk' => 0,
            'status' => 'Second',
            'deskripsi_produk' => 'KONDISI :
● Second Baik
● Tombol Baik
● Body Fisik Paint Lost
● MF baik
● Akurasi Baik
● Fungsional Normal Baik
● Jamur Tipis / Debu Micro (Tidak Berpengaruh Pada Hasil)



KELENGKAPAN :
● Lensa
● Tutup Depan
● Tutup Belakang
● Nota Pembelian Dinoyo Kamera

Kode Barang : #1309_DK'

        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/111.png', 'is_main' => true],
            ['path_gambar' => 'gambar_produk/112.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/113.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/114.png', 'is_main' => false],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => '1308AFEOPL',
            'id_kategori' => 1,
            'nama_produk' => 'Canon 1300d + kit 18 55mm Kondisi Baik',
            'harga_jual' => 3200000,
            'stok_produk' => 1,
            'status' => 'Second',
            'deskripsi_produk' => 'KONDISI :
● Wifi Berfungsi
● Flash Baik
● Tutup USB Baik
● Tombol Baik
● Body Paintlos
● Jamur Tipis / Debu Micro
(Tidak Berpengaruh pada Fungsi dan Hasil)
● AF/MF Baik
● LCD Baik dan Bersih
● Fungsional Normal

KELENGKAPAN:
● Kamera
● Lensa
● Baterai
● Charger
● Nota Pembelian Dinoyo Kamera'

        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/121.png', 'is_main' => true],
            ['path_gambar' => 'gambar_produk/122.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/123.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/124.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/125.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/126.png', 'is_main' => false],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);

            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }

        $produk = Produk::create([
            'kode_sku' => '1298COOOJT',
            'id_kategori' => 2,
            'nama_produk' => 'Sony a5100 + kit 16 50mm (Black) Second Kondisi Baik',
            'harga_jual' => 5250000,
            'stok_produk' => 1,
            'grade' => 'Unggulan',
            'status' => 'Second',
            'deskripsi_produk' => 'KONDISI :
● Toucscreen Baik
● Wifi Berfungsi
● Layar Flip 180°
● Flash Baik
● Tutup USB Baik
● Tombol Baik
● Body Paintlos
● Jamur Tipis / Debu Micro
(Tidak Berpengaruh pada Fungsi dan Hasil)
● AF/MF Baik
● LCD Baik dan Bersih
● Fungsional Normal

KELENGKAPAN:
● Kamera
● Lensa
● Baterai
● Charger
● Nota Pembelian Dinoyo Kamera

Kode Barang : #1298_DK'

        ]);

        $gambarList = [
            ['path_gambar' => 'gambar_produk/131.png', 'is_main' => true],
            ['path_gambar' => 'gambar_produk/132.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/133.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/134.jpg', 'is_main' => false],
            ['path_gambar' => 'gambar_produk/135.png', 'is_main' => false],
        ];

        foreach ($gambarList as $gambar) {
            $uploadedPath = $this->uploadSeederImage($produk->id, $gambar['path_gambar'], $localDir);
            GambarProduk::create([
                'id_produk' => $produk->id,
                'path_gambar' => $uploadedPath,
                'is_main' => $gambar['is_main'],
            ]);
        }
    }

    private function uploadSeederImage(int $productId, string $relativePath, string $localDir): string
    {
        $relative = ltrim($relativePath, DIRECTORY_SEPARATOR);
        $source = $localDir . DIRECTORY_SEPARATOR . $relative;

        if (!is_file($source)) {
            $basename = basename($relative);
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($localDir, \FilesystemIterator::SKIP_DOTS)
            );
            foreach ($iterator as $file) {
                if (strcasecmp($file->getFilename(), $basename) === 0) {
                    $source = $file->getPathname();
                    break;
                }
            }
        }

        if (!is_file($source)) {
            throw new \RuntimeException("Seeder image not found: {$source}");
        }

        $paths = ImageUpload::upload($source, "product/{$productId}");
        return $paths['path'];
    }
}
