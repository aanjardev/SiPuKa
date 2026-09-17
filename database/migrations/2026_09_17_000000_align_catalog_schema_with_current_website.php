<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel ini berasal dari fitur lama dan tidak lagi digunakan website katalog.
        Schema::dropIfExists('catalog_banners');
        Schema::dropIfExists('catalog_partner_logos');

        if (Schema::hasColumn('catalog_settings', 'nomor_telfon')
            && !Schema::hasColumn('catalog_settings', 'nomor_telepon')) {
            DB::statement('ALTER TABLE catalog_settings CHANGE nomor_telfon nomor_telepon VARCHAR(20) NULL');
        }

        Schema::table('catalog_settings', function (Blueprint $table) {
            $unusedColumns = array_filter(
                ['tokopedia_link', 'shopee_link'],
                fn (string $column) => Schema::hasColumn('catalog_settings', $column)
            );

            if ($unusedColumns !== []) {
                $table->dropColumn($unusedColumns);
            }
        });

        // Menjaga database yang sudah pernah menjalankan migration lama tetap kompatibel.
        if (Schema::hasColumn('perusahaan_cabang', 'link_maps')
            && !Schema::hasColumn('perusahaan_cabang', 'maps_embed_url')) {
            DB::statement('ALTER TABLE perusahaan_cabang CHANGE link_maps maps_embed_url TEXT NULL');
        }

        if (Schema::hasColumn('perusahaan_cabang', 'maps_embed_url')) {
            Schema::table('perusahaan_cabang', function (Blueprint $table) {
                $table->text('maps_embed_url')->nullable()->change();
            });

            DB::table('perusahaan_cabang')
                ->whereNotNull('maps_embed_url')
                ->orderBy('id')
                ->each(function (object $branch): void {
                    if (preg_match('#^https://(?:www\.)?(?:google\.[^/]+|maps\.google\.[^/]+)/maps/embed(?:[/?]|$)#i', $branch->maps_embed_url)) {
                        return;
                    }

                    parse_str((string) parse_url($branch->maps_embed_url, PHP_URL_QUERY), $queryParameters);
                    $location = $queryParameters['q'] ?? trim($branch->nama . ' ' . $branch->alamat);

                    DB::table('perusahaan_cabang')
                        ->where('id', $branch->id)
                        ->update([
                            'maps_embed_url' => 'https://www.google.com/maps/embed?q=' . rawurlencode($location),
                        ]);
                });
        }
    }

    public function down(): void
    {
        Schema::table('catalog_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('catalog_settings', 'tokopedia_link')) {
                $table->string('tokopedia_link')->nullable();
            }
            if (!Schema::hasColumn('catalog_settings', 'shopee_link')) {
                $table->string('shopee_link')->nullable();
            }
        });

        if (Schema::hasColumn('catalog_settings', 'nomor_telepon')
            && !Schema::hasColumn('catalog_settings', 'nomor_telfon')) {
            DB::statement('ALTER TABLE catalog_settings CHANGE nomor_telepon nomor_telfon VARCHAR(255) NULL');
        }

        if (Schema::hasColumn('perusahaan_cabang', 'maps_embed_url')
            && !Schema::hasColumn('perusahaan_cabang', 'link_maps')) {
            DB::statement('ALTER TABLE perusahaan_cabang CHANGE maps_embed_url link_maps VARCHAR(255) NULL');
        }

    }
};
