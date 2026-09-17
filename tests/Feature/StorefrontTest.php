<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_displays_the_single_store(): void
    {
        DB::table('perusahaan_cabang')->insert([
            'singleton_key' => true,
            'nama' => 'Pusat Kamera Malang',
            'alamat' => 'Jl. Mertojoyo E1a, Malang',
            'nomor_telepon' => '082345672034',
            'maps_embed_url' => 'https://www.google.com/maps/embed?q=Pusat+Kamera+Malang',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->get('/contact')
            ->assertOk()
            ->assertSee('Pusat Kamera Malang')
            ->assertDontSee('Pilih Cabang');
    }

    public function test_database_rejects_a_second_store(): void
    {
        $store = [
            'singleton_key' => true,
            'nama' => 'Pusat Kamera Malang',
            'alamat' => 'Jl. Mertojoyo E1a, Malang',
            'nomor_telepon' => '082345672034',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('perusahaan_cabang')->insert($store);

        $this->expectException(QueryException::class);
        DB::table('perusahaan_cabang')->insert(array_merge($store, [
            'nama' => 'Toko Kedua',
            'nomor_telepon' => '081234567890',
        ]));
    }

    public function test_sitemap_and_robots_are_available(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');
        $this->get('/robots.txt')->assertOk()->assertSee('/sitemap.xml');
    }

    public function test_catalog_uses_active_price_categories_from_database(): void
    {
        DB::table('kategori_harga')->where('slug', '500rb')->update(['nama' => 'Budget Pemula']);

        $this->get('/katalog')
            ->assertOk()
            ->assertSee('Budget Pemula');
    }
}
