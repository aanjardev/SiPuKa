<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Menggabungkan Seeder Admin, Kategori, dan Produk.
     */
    public function run(): void
    {
        if (app()->environment('production') && !config('seeders.allow_destructive_in_production')) {
            throw new \RuntimeException(
                'Seeder diblokir di production. Set ALLOW_PRODUCTION_SEEDING=true hanya untuk eksekusi yang disengaja.'
            );
        }

        if (!config('seeders.admin_password') || !config('seeders.legacy_admin_password')) {
            throw new \RuntimeException(
                'SEED_ADMIN_PASSWORD dan SEED_LEGACY_ADMIN_PASSWORD wajib diatur sebelum menjalankan seeder.'
            );
        }

        $this->call([
            KaryawanSeeder::class,    // Run first to create manager employee
            AdminSeeder::class,       // Then create admin user
            KategoriSeeder::class,
            ProdukSeeder::class,
            BranchSeeder::class,
            CatalogSeeder::class,
            CustomerSeeder::class,
        ]);





























































































































































    }
}
