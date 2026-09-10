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

        Schema::create('jam_operasional_cabang', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perusahaan_cabang_id')->constrained('perusahaan_cabang')->onDelete('cascade');

            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu']);

            $table->boolean('is_buka')->default(true);

            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();

            $table->text('catatan')->nullable();
            
            $table->timestamps();

            $table->unique(['perusahaan_cabang_id', 'hari'], 'unique_hari_per_cabang');
        });

        Schema::table('perusahaan_cabang', function (Blueprint $table) {

            if (!Schema::hasColumn('perusahaan_cabang', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('nomor_telepon');
            }

            if (!Schema::hasColumn('perusahaan_cabang', 'email')) {
                $table->string('email', 100)->nullable()->after('link_maps');
            }

            if (!Schema::hasColumn('perusahaan_cabang', 'deskripsi')) {
                $table->text('deskripsi')->nullable()->after('email');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perusahaan_cabang', function (Blueprint $table) {
            $columnsToDrop = ['is_active', 'email', 'deskripsi'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('perusahaan_cabang', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        
        Schema::dropIfExists('jam_operasional_cabang');
    }
};
