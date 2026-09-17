<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\PreventsProductionSeeding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriSeeder extends Seeder
{
    use PreventsProductionSeeding;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->guardAgainstProduction();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('kategori')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $categories = [
            ['id' => 1, 'nama_kategori' => 'Kamera DSLR', 'path_gambar' => 'public/category/1/9032677b8fc9e5b6a8922f096f25f519faade9dc.webp'],
            ['id' => 2, 'nama_kategori' => 'Kamera Mirrorless', 'path_gambar' => 'public/category/2/460d4a64d1c974b6e04521e0a087497daf06ad25.webp'],
            ['id' => 3, 'nama_kategori' => 'Kamera Digital', 'path_gambar' => 'public/category/3/b5c77bbf6aeeaaeefc84b464af3a786e3bd433dd.webp'],
            ['id' => 4, 'nama_kategori' => 'Handycam', 'path_gambar' => 'public/category/4/cac24c40e0db993ecae4c9599ba67b5ed35de9b6.webp'],
            ['id' => 5, 'nama_kategori' => 'Kamera Instan', 'path_gambar' => 'public/category/5/c86ae69498d8b8dd24da5f33e70a10007238fd4e.webp'],
            ['id' => 6, 'nama_kategori' => 'Kamera Lain', 'path_gambar' => 'public/category/6/2cb05f3bc7f32d62c2ee288345b68194eb5afc40.webp'],
            ['id' => 7, 'nama_kategori' => 'Lensa', 'path_gambar' => 'public/category/7/baa8c7690a62f95f649213b4278f11c7bf162d07.webp'],
            ['id' => 8, 'nama_kategori' => 'Baterai/Charger', 'path_gambar' => 'public/category/8/068dabdb87d27aec08c17edd4301f08d2a40d34e.webp'],
            ['id' => 9, 'nama_kategori' => 'Kartu Memori', 'path_gambar' => 'public/category/9/1423382c8efa2a12749a0c53d216bd88afa0a518.webp'],
            ['id' => 10, 'nama_kategori' => 'Aksesoris Lain', 'path_gambar' => 'public/category/10/8c6752304571581475b6e3f84fe813808eec0104.webp'],
        ];

        $payload = [];
        foreach ($categories as $category) {
            $payload[] = [
                'id' => $category['id'],
                'nama_kategori' => $category['nama_kategori'],
                'path_gambar' => $category['path_gambar'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('kategori')->insert($payload);
    }
}
