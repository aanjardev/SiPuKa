<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\PreventsProductionSeeding;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class KaryawanSeeder extends Seeder
{
    use PreventsProductionSeeding;

    public function run(): void
    {
        $this->guardAgainstProduction();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('users')->truncate();
        DB::table('karyawan')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('karyawan')->insert([
            [
                'nama_lengkap' => 'Ahmad Fauzi',
                'nik' => '1234567890123456',
                'jabatan' => 'Teknisi',
                'gaji' => 5000000,
                'tanggal_masuk' => '2024-01-15',
                'tanggal_keluar' => null,
                'status' => 'aktif',
                'nomor_telepon' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 10'
            ],
            [
                'nama_lengkap' => 'Siti Aminah',
                'nik' => '1234567890123457',
                'jabatan' => 'Manager',
                'gaji' => 10000000,
                'tanggal_masuk' => '2023-03-01',
                'tanggal_keluar' => null,
                'status' => 'aktif',
                'nomor_telepon' => '081298765432',
                'alamat' => 'Jl. Sudirman No. 20'
            ],
            [
                'nama_lengkap' => 'Budi Santoso',
                'nik' => '1234567890123458',
                'jabatan' => 'Staff Administrasi',
                'gaji' => 4000000,
                'tanggal_masuk' => '2022-06-10',
                'tanggal_keluar' => '2024-06-10',
                'status' => 'non-aktif',
                'nomor_telepon' => '081212345678',
                'alamat' => 'Jl. Diponegoro No. 15'
            ],
            [
                'nama_lengkap' => 'Aan',
                'nik' => '1234567890023458',
                'jabatan' => 'Staff Administrasi',
                'gaji' => 4000000,
                'tanggal_masuk' => '2022-06-10',
                'tanggal_keluar' => '2024-06-10',
                'status' => 'aktif',
                'nomor_telepon' => '081212345678',
                'alamat' => 'Jl. Diponegoro No. 15'
            ],
            [
                'nama_lengkap' => 'Anjar',
                'nik' => '1534567890123458',
                'jabatan' => 'Staff Administrasi',
                'gaji' => 4000000,
                'tanggal_masuk' => '2022-06-10',
                'tanggal_keluar' => '2024-06-10',
                'status' => 'aktif',
                'nomor_telepon' => '081212345678',
                'alamat' => 'Jl. Diponegoro No. 15'
            ],
            [
                'nama_lengkap' => 'Setyawati',
                'nik' => '1234567890183458',
                'jabatan' => 'Staff Administrasi',
                'gaji' => 4000000,
                'tanggal_masuk' => '2022-06-10',
                'tanggal_keluar' => '2024-06-10',
                'status' => 'aktif',
                'nomor_telepon' => '081212345678',
                'alamat' => 'Jl. Diponegoro No. 15'
            ],
        ]);
        DB::table('users')->insert([
            'id' => 1,
            'name' => 'Ahmad Fauzi',
            'password' => Hash::make(config('seeders.legacy_admin_password')),
            'email' => 'admin@gmail.com',
            'role' => 'manager',
            'status' => 'pending',
        ]);
    }
}
