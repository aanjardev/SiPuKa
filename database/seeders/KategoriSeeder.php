<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\ImageUpload;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('kategori')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $localDir = base_path('public/mcategoryIMG');
        if (!is_dir($localDir)) {
            throw new \RuntimeException("Seeder image directory not found: {$localDir}");
        }

        $categories = [
            ['id' => 1, 'nama_kategori' => 'Kamera DSLR', 'file' => '1.png'],
            ['id' => 2, 'nama_kategori' => 'Kamera Mirrorless', 'file' => '2.png'],
            ['id' => 3, 'nama_kategori' => 'Kamera Digital', 'file' => '3.png'],
            ['id' => 4, 'nama_kategori' => 'Handycam', 'file' => '4.png'],
            ['id' => 5, 'nama_kategori' => 'Kamera Instan', 'file' => '5.png'],
            ['id' => 6, 'nama_kategori' => 'Kamera Lain', 'file' => '6.png'],
            ['id' => 7, 'nama_kategori' => 'Lensa', 'file' => '7.png'],
            ['id' => 8, 'nama_kategori' => 'Baterai/Charger', 'file' => '8.png'],
            ['id' => 9, 'nama_kategori' => 'Kartu Memori', 'file' => '9.png'],
            ['id' => 10, 'nama_kategori' => 'Aksesoris Lain', 'file' => '10.png'],
        ];

        $payload = [];
        foreach ($categories as $category) {
            $source = $localDir . DIRECTORY_SEPARATOR . $category['file'];
            if (!is_file($source)) {
                throw new \RuntimeException("Seeder image not found: {$source}");
            }

            $uploaded = ImageUpload::upload($source, "category/{$category['id']}");

            $payload[] = [
                'id' => $category['id'],
                'nama_kategori' => $category['nama_kategori'],
                'path_gambar' => $uploaded['path'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('kategori')->insert($payload);
    }
}
