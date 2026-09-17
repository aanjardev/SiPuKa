<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\PreventsProductionSeeding;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use App\Models\Branch;
use App\Models\JamOperasionalCabang;
use Illuminate\Support\Facades\DB;

class BranchSeeder extends Seeder
{
    use PreventsProductionSeeding;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->guardAgainstProduction();

        Schema::disableForeignKeyConstraints();

        DB::table('perusahaan_cabang')->truncate();
        DB::table('jam_operasional_cabang')->truncate();

        Schema::enableForeignKeyConstraints();

        $branch1 = Branch::create([
            'singleton_key' => true,
            'nama' => 'PusatKamera.id',
            'alamat' => 'JL MERTOJOYO E1a, Merjosari, Kec. Lowokwaru, Kota Malang, Jawa Timur 65144',
            'nomor_telepon' => '082345672034',
            'maps_embed_url' => 'https://www.google.com/maps/embed?q=Pusat%20Kamera%20Malang%20JL%20MERTOJOYO%20E1a%2C%20Merjosari%2C%20Kec.%20Lowokwaru%2C%20Kota%20Malang%2C%20Jawa%20Timur%2065144',
            'is_active' => true,
            'email' => 'pusatkamera@gmail.com',
            'deskripsi' => 'Toko kamera di Malang dengan koleksi kamera dan perlengkapan fotografi.'
        ]);

        $this->createJamOperasional($branch1->id, [
            'Senin' => ['buka' => true, 'jam_buka' => '10:00', 'jam_tutup' => '19:30'],
            'Selasa' => ['buka' => true, 'jam_buka' => '10:00', 'jam_tutup' => '19:30'],
            'Rabu' => ['buka' => true, 'jam_buka' => '10:00', 'jam_tutup' => '19:30'],
            'Kamis' => ['buka' => true, 'jam_buka' => '10:00', 'jam_tutup' => '19:30'],
            'Jumat' => ['buka' => true, 'jam_buka' => '10:00', 'jam_tutup' => '19:30'],
            'Sabtu' => ['buka' => true, 'jam_buka' => '10:00', 'jam_tutup' => '19:30'],
            'Minggu' => ['buka' => false, 'jam_buka' => '10:00', 'jam_tutup' => '19:30', 'catatan' => '']
        ]);

    }

    private function createJamOperasional($branchId, $jadwal)
    {
        foreach ($jadwal as $hari => $data) {
            JamOperasionalCabang::create([
                'perusahaan_cabang_id' => $branchId,
                'hari' => $hari,
                'is_buka' => $data['buka'],
                'jam_buka' => $data['jam_buka'],
                'jam_tutup' => $data['jam_tutup'],
                'catatan' => $data['catatan'] ?? null
            ]);
        }
    }
}
