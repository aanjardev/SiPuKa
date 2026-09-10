<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('perusahaan_cabang', function (Blueprint $table) {

            $redundantColumns = [
                'jam_buka', // Jam buka lama (single)
                'jam_tutup', // Jam tutup lama (single)
                'buka_senin', 'buka_selasa', 'buka_rabu', 'buka_kamis', 'buka_jumat', 'buka_sabtu', 'buka_minggu',
                'jam_buka_senin', 'jam_tutup_senin',
                'jam_buka_selasa', 'jam_tutup_selasa',
                'jam_buka_rabu', 'jam_tutup_rabu',
                'jam_buka_kamis', 'jam_tutup_kamis',
                'jam_buka_jumat', 'jam_tutup_jumat',
                'jam_buka_sabtu', 'jam_tutup_sabtu',
                'jam_buka_minggu', 'jam_tutup_minggu',
                'status_operasional', 'catatan_operasional'
            ];

            foreach ($redundantColumns as $column) {
                if (Schema::hasColumn('perusahaan_cabang', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perusahaan_cabang', function (Blueprint $table) {

            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();

            $table->boolean('buka_senin')->default(true);
            $table->boolean('buka_selasa')->default(true);
            $table->boolean('buka_rabu')->default(true);
            $table->boolean('buka_kamis')->default(true);
            $table->boolean('buka_jumat')->default(true);
            $table->boolean('buka_sabtu')->default(true);
            $table->boolean('buka_minggu')->default(false);

            $table->time('jam_buka_senin')->nullable();
            $table->time('jam_tutup_senin')->nullable();
            $table->time('jam_buka_selasa')->nullable();
            $table->time('jam_tutup_selasa')->nullable();
            $table->time('jam_buka_rabu')->nullable();
            $table->time('jam_tutup_rabu')->nullable();
            $table->time('jam_buka_kamis')->nullable();
            $table->time('jam_tutup_kamis')->nullable();
            $table->time('jam_buka_jumat')->nullable();
            $table->time('jam_tutup_jumat')->nullable();
            $table->time('jam_buka_sabtu')->nullable();
            $table->time('jam_tutup_sabtu')->nullable();
            $table->time('jam_buka_minggu')->nullable();
            $table->time('jam_tutup_minggu')->nullable();

            $table->string('status_operasional')->default('buka');
            $table->text('catatan_operasional')->nullable();
        });
    }
};
