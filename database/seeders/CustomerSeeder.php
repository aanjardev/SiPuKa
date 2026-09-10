<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        DB::table('customer')->insert([
            [
                'kode_customer' => 'CS0001',
                'nama' => 'Parel Alpatir',
                'identitas' => 'KTP123456789',
                'no_telp' => '081234567890',
                'alamat' => 'Jl. Miaw No. 12',
                'jenis_kelamin' => 'L',
                'referensi' => 'Teman kerja',
                'keterangan' => 'Pelanggan rutin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [   
                'kode_customer' => 'CS0002',
                'nama' => 'Aini Aan',
                'identitas' => 'KTP987654321',
                'no_telp' => '085612341234',
                'alamat' => 'Jl. Rawrrrr No. 45',
                'jenis_kelamin' => 'P',
                'referensi' => 'Media sosial',
                'keterangan' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_customer' => 'CS0003',
                'nama' => 'Bambang Wicaksono',
                'identitas' => null,
                'no_telp' => '082345678901',
                'alamat' => null,
                'jenis_kelamin' => 'L',
                'referensi' => null,
                'keterangan' => 'Datang tanpa referensi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
