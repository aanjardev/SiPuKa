<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Helpers\ImageUpload;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $localDir = base_path('public/mainIMG');

        if (!is_dir($localDir)) {
            throw new \RuntimeException("Folder mainIMG tidak ditemukan: {$localDir}");
        }

        $catalogId = DB::table('catalog_settings')->insertGetId([
            'nama_website'    => 'Pusat Kamera Malang',
            'nomor_telfon'    => '082345670014',
            'description'     => 'Toko kamera terpercaya di Malang dan Pasuruan. Menyediakan berbagai kebutuhan fotografi dan videografi dengan kualitas terjamin.',
            'logo_path'       => null,
            'facebook_link'   => 'https://www.facebook.com/jualkameramalang',
            'youtube_link'    => 'https://www.youtube.com/@DINOYOKAMERA_OFFICIAL',
            'instagram_link'  => 'https://www.instagram.com/dinoyokamera/',
            'tiktok_link'     => 'https://www.tiktok.com/@dinoyokamera',
            'tokopedia_link'  => 'https://www.tokopedia.com/dinoyo-kamera',
            'shopee_link'     => 'https://shopee.co.id/dinoyokamera',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        $logoPath = $this->uploadLocalToR2('logopk.png', $localDir, 'catalog/logo');

        DB::table('catalog_settings')->where('id', $catalogId)->update([
            'logo_path' => $logoPath,
        ]);

        $bannerFiles = [
            'sample1.png',
            'sample2.png',
            'sample3.png',
        ];

        foreach ($bannerFiles as $file) {
            $uploaded = $this->uploadLocalToR2($file, $localDir, 'catalog/banners');

            DB::table('catalog_banners')->insert([
                'catalog_setting_id' => $catalogId,
                'banner_path'        => $uploaded,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }

        $partnerUrls = [];

        foreach ($partnerUrls as $url) {
            $uploaded = $this->uploadUrlToR2($url, 'catalog/partners');

            DB::table('catalog_partner_logos')->insert([
                'catalog_setting_id' => $catalogId,
                'logo_path'          => $uploaded,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }

    private function uploadLocalToR2(string $filename, string $localDir, string $prefix): string
    {
        $source = $localDir . DIRECTORY_SEPARATOR . $filename;

        if (!is_file($source)) {
            throw new \RuntimeException("Seeder image not found: {$source}");
        }

        $paths = ImageUpload::upload($source, $prefix);
        return $paths['path'];
    }

    private function uploadUrlToR2(string $url, string $prefix): string
    {
        $temp = tempnam(sys_get_temp_dir(), 'img_');
        file_put_contents($temp, file_get_contents($url));

        $paths = ImageUpload::upload($temp, $prefix);
        $path = $paths['path'];

        @unlink($temp);

        return $path;
    }
}
