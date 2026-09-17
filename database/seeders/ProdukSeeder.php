<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\PreventsProductionSeeding;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Produk;
use App\Models\GambarProduk;

class ProdukSeeder extends Seeder
{
    use PreventsProductionSeeding;

    private array $imageOffsets = [];

    private const IMAGE_PATHS = [
        1 => ['public/product/1/54fde50442ea4f6d92d77fc57c19f8910b3a1486.webp', 'public/product/1/df1e6a44877cca6e3b39bd0aee57afd6d1170f6a.webp', 'public/product/1/3f6e333927d4300b5934069eb7c709088f6a7911.webp', 'public/product/1/032f29598729550833c97ff77bd91753c770d208.webp'],
        2 => ['public/product/2/ac99be8f7032c73c007e1ad32197be1189b833d6.webp', 'public/product/2/5a46b6a34bb4908bb80f9e37b81ab4e376b73ed8.webp', 'public/product/2/483af934a66c1a6e33955b2d9628c5582cb736b7.webp', 'public/product/2/2d3cacaa679cc0e3637c9f2151b5a28233cfc8f0.webp', 'public/product/2/01e3549cd8e92f0a3e2479bea8e8b3e571783a6a.webp', 'public/product/2/a7024c3a20cd0814325901113ab70ecd28143f46.webp'],
        3 => ['public/product/3/373e7b3fc55f4c1d12bda8e3802720756331ea71.webp', 'public/product/3/6d2b44bf6233c0a3708323639753f1e65f065d57.webp', 'public/product/3/5295ba0596ba3b2467eb48b3b797f7dc5f02b1ed.webp', 'public/product/3/599b172fbb4ab7dc96ef30120b5dff00b316a2d7.webp', 'public/product/3/3559b6a546e2e8b53888e93334ec423bd1378713.webp', 'public/product/3/7f605f76a110a053f7323d1c345f6b14844230a7.webp'],
        4 => ['public/product/4/ac4461e315a1089c9d2ad1c9c7a9be82090c9e17.webp', 'public/product/4/f9861ea21fffcf3c96bba81aef184667daf30206.webp', 'public/product/4/ba6c035a21cf4a41fe96a9f983198d67027fa911.webp'],
        5 => ['public/product/5/36bec06b404dfdfd3537f9fc1482c312add3389b.webp', 'public/product/5/40ba7ddbb5a0cd5be6613fa6e73997cb827476f2.webp', 'public/product/5/799b0f83cc5dce292c8f0055e184a52d1c0939d1.webp', 'public/product/5/e072e3a23a22d796605f48cfd8d08f589d6da7f6.webp', 'public/product/5/8cdd1e52e79142d15a169c8784ff8d75a791068f.webp', 'public/product/5/eb370e925e85f18a5be7352a899b1db177be6f46.webp'],
        6 => ['public/product/6/8c71b02b29227127a9269cf9d92e0941988a7a5a.webp', 'public/product/6/4d569eaa81db4745e2726db62b78db0e4cc8fd1a.webp', 'public/product/6/911857cbc7e44768f7acc7065053b3dffec71ef0.webp', 'public/product/6/9f0ab7385e68100fa5d472f244b441db0caa6885.webp', 'public/product/6/c978d0bac6d33dd474b6acab2d06cd1b874dd093.webp'],
        7 => ['public/product/7/3b4ffb1af75361bbee27b0feb9fd1c5e6fcfd0e1.webp'],
        8 => ['public/product/8/50fa2f6f0a5291fa7c685ed48fa8baec000b1569.webp'],
        9 => ['public/product/9/a3f04d03ac6aed2079800dbca970c854d8343731.webp'],
        10 => ['public/product/10/24598d7287f515cdd3e86d019ff09eeccfe623df.webp'],
        11 => ['public/product/11/fc6a9845e6795c28f56c2fd8e7d9a2443e574d50.webp'],
        12 => ['public/product/12/cff7aac194df09fc73164ada55f85feea7526669.webp'],
        13 => ['public/product/13/51f8cdcbc2feb7636c8eb6a2393d1d419e68e1b8.webp'],
        14 => ['public/product/14/6b6576ae8022e766b7972ead26e602ff40fa8df4.webp'],
        15 => ['public/product/15/fe07649f4f9c1c9434c39c17e66400d54e7efe2c.webp'],
        16 => ['public/product/16/1055318a4aab67f26bf33351f74ba345a084ad9f.webp', 'public/product/16/7e8ecf6d09b7e33f4eb66c1040ba0c0bc09a6f2b.webp', 'public/product/16/1334182914a1e4737987b85b818fa55a30d3243c.webp', 'public/product/16/941d2edb45084993a5ef7f9e25ba4b1cdd76622a.webp'],
        17 => ['public/product/17/034f41187a15e691964794f22df1cf4167229dbe.webp', 'public/product/17/504e39d3e5619a8964c22a973923f71d5da77335.webp', 'public/product/17/ffd52ceacdf967422d8e29fe069d9db8a2333ab7.webp', 'public/product/17/12c85b2898feb527fd8827281b9ef7e3ff164600.webp', 'public/product/17/f0d13c4c336999968c58c4f5674f1f0eb268cffa.webp', 'public/product/17/6f1a0fffd92e41c27e0bcffc8e9585e227693e36.webp'],
        18 => ['public/product/18/a4a61a4f9e99ee943bde56a1f432314cc918d632.webp', 'public/product/18/c020656e20b050e52722bef8d2f8904abb05db1a.webp', 'public/product/18/f9823ea1eba23d0505496675348dd18f8eddab43.webp', 'public/product/18/89e69c5fe8b088d224964840cd7bfd7b72341ea4.webp', 'public/product/18/31d9dd32c4e411e697041c90225080787517c0a5.webp'],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->guardAgainstProduction();


        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        GambarProduk::truncate();
        Produk::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        $localDir = '';

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
        $offset = $this->imageOffsets[$productId] ?? 0;
        $path = self::IMAGE_PATHS[$productId][$offset] ?? null;

        if (!$path) {
            throw new \RuntimeException("Snapshot gambar produk {$productId} tidak lengkap ({$relativePath}).");
        }

        $this->imageOffsets[$productId] = $offset + 1;

        return $path;
    }
}
