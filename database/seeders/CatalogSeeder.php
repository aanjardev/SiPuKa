<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\PreventsProductionSeeding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeeder extends Seeder
{
    use PreventsProductionSeeding;

    public function run(): void
    {
        $this->guardAgainstProduction();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('catalog_settings')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('catalog_settings')->insert([
            'id'              => 1,
            'singleton_key'   => true,
            'nama_website'    => 'PusatKamera.id',
            'nomor_telepon'   => '082345672034',
            'description'     => 'Toko kamera terpercaya untuk kebutuhan fotografi dan videografi dengan kualitas terjamin.',
            'hero_eyebrow'    => 'JAGONYA KAMERA PEMULA & LEMBAGA',
            'hero_title'      => 'PusatKamera.id',
            'hero_description' => 'Pilihan kamera terjangkau untuk pelajar, mahasiswa, ekstrakurikuler fotografi, dan lembaga pendidikan.',
            'seo_title'       => 'PusatKamera.id — Jagonya Kamera Pemula & Lembaga',
            'seo_description' => 'Katalog kamera terjangkau untuk pelajar, mahasiswa, sekolah, dan lembaga pendidikan di Malang.',
            'logo_path'       => null,
            'facebook_link'   => null,
            'youtube_link'    => null,
            'instagram_link'  => null,
            'tiktok_link'     => null,
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

    }
}
