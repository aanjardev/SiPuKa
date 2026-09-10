<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Branch;
use App\Models\JamOperasionalCabang;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        DB::table('perusahaan_cabang')->truncate();
        DB::table('jam_operasional_cabang')->truncate();

        Schema::enableForeignKeyConstraints();

        $branch1 = Branch::create([
            'nama' => 'Dinoyo Kamera 1',
            'alamat' => 'Jl. MT Haryono No. 123, Dinoyo, Malang',
            'nomor_telepon' => '081234567891',
            'link_maps' => 'https://maps.google.com/?q=-7.966620,112.632632',
            'is_active' => true,
            'email' => 'dinoyo1@sidika.com',
            'deskripsi' => 'Cabang utama dengan koleksi kamera terlengkap'
        ]);

        $this->createJamOperasional($branch1->id, [
            'Senin' => ['buka' => true, 'jam_buka' => '08:30', 'jam_tutup' => '17:00'],
            'Selasa' => ['buka' => true, 'jam_buka' => '08:30', 'jam_tutup' => '17:00'],
            'Rabu' => ['buka' => true, 'jam_buka' => '08:30', 'jam_tutup' => '17:00'],
            'Kamis' => ['buka' => true, 'jam_buka' => '08:30', 'jam_tutup' => '17:00'],
            'Jumat' => ['buka' => true, 'jam_buka' => '08:30', 'jam_tutup' => '17:00'],
            'Sabtu' => ['buka' => true, 'jam_buka' => '09:00', 'jam_tutup' => '15:00'],
            'Minggu' => ['buka' => false, 'jam_buka' => null, 'jam_tutup' => null, 'catatan' => 'Tutup hari Minggu']
        ]);

        $branch2 = Branch::create([
            'nama' => 'Dinoyo Kamera 2',
            'alamat' => 'Jl. Gajayana No. 45, Dinoyo, Malang',
            'nomor_telepon' => '081234567892',
            'link_maps' => 'https://maps.google.com/?q=-7.970000,112.630000',
            'is_active' => true,
            'email' => 'dinoyo2@sidika.com',
            'deskripsi' => 'Cabang dengan fokus kamera second berkualitas'
        ]);

        $this->createJamOperasional($branch2->id, [
            'Senin' => ['buka' => true, 'jam_buka' => '08:00', 'jam_tutup' => '17:00'],
            'Selasa' => ['buka' => true, 'jam_buka' => '08:00', 'jam_tutup' => '17:00'],
            'Rabu' => ['buka' => true, 'jam_buka' => '08:00', 'jam_tutup' => '17:00'],
            'Kamis' => ['buka' => true, 'jam_buka' => '08:00', 'jam_tutup' => '17:00'],
            'Jumat' => ['buka' => true, 'jam_buka' => '08:00', 'jam_tutup' => '17:00'],
            'Sabtu' => ['buka' => true, 'jam_buka' => '08:00', 'jam_tutup' => '14:00'],
            'Minggu' => ['buka' => false, 'jam_buka' => null, 'jam_tutup' => null, 'catatan' => 'Tutup hari Minggu']
        ]);

        $branch3 = Branch::create([
            'nama' => 'Dinoyo Kamera 3',
            'alamat' => 'Jl. Raya Tlogomas No. 67, Malang',
            'nomor_telepon' => '081234567893',
            'link_maps' => 'https://maps.google.com/?q=-7.955000,112.640000',
            'is_active' => true,
            'email' => 'dinoyo3@sidika.com',
            'deskripsi' => 'Cabang dengan layanan servis kamera'
        ]);

        $this->createJamOperasional($branch3->id, [
            'Senin' => ['buka' => true, 'jam_buka' => '08:30', 'jam_tutup' => '17:30'],
            'Selasa' => ['buka' => true, 'jam_buka' => '08:30', 'jam_tutup' => '17:30'],
            'Rabu' => ['buka' => true, 'jam_buka' => '08:30', 'jam_tutup' => '17:30'],
            'Kamis' => ['buka' => true, 'jam_buka' => '08:30', 'jam_tutup' => '17:30'],
            'Jumat' => ['buka' => true, 'jam_buka' => '08:30', 'jam_tutup' => '17:30'],
            'Sabtu' => ['buka' => true, 'jam_buka' => '09:00', 'jam_tutup' => '16:00'],
            'Minggu' => ['buka' => true, 'jam_buka' => '10:00', 'jam_tutup' => '14:00', 'catatan' => 'Jam khusus hari Minggu']
        ]);
    }

    private function createJamOperasional($branchId, $jadwal)
    {
        foreach ($jadwal as $hari => $data) {
            JamOperasionalCabang::create([
                'perusahaan_cabang_id' => $branchId,
                'hari' => $hari,
                'is_buka' => $data['buka'],
                'jam_buka' => $data['buka'] ? $data['jam_buka'] : null,
                'jam_tutup' => $data['buka'] ? $data['jam_tutup'] : null,
                'catatan' => $data['catatan'] ?? null
            ]);
        }
    }
}
