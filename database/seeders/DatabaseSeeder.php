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
