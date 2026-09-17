<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('perusahaan_cabang')) {
            return;
        }

        $storeIds = DB::table('perusahaan_cabang')->orderBy('id')->pluck('id');

        if ($storeIds->count() > 1) {
            $extraIds = $storeIds->slice(1)->values();
            $usedBySales = Schema::hasTable('penjualan')
                && DB::table('penjualan')->whereIn('perusahaan_cabang_id', $extraIds)->exists();
            $usedByPurchases = Schema::hasTable('pembelian')
                && DB::table('pembelian')->whereIn('perusahaan_cabang_id', $extraIds)->exists();

            if ($usedBySales || $usedByPurchases) {
                throw new \RuntimeException(
                    'Migrasi satu toko dihentikan: toko tambahan masih dipakai oleh transaksi. Pindahkan transaksi ke toko utama terlebih dahulu.'
                );
            }

            if (Schema::hasTable('jam_operasional_cabang')) {
                DB::table('jam_operasional_cabang')->whereIn('perusahaan_cabang_id', $extraIds)->delete();
            }

            DB::table('perusahaan_cabang')->whereIn('id', $extraIds)->delete();
        }

        if (! Schema::hasColumn('perusahaan_cabang', 'singleton_key')) {
            Schema::table('perusahaan_cabang', function (Blueprint $table) {
                $table->boolean('singleton_key')
                    ->default(true)
                    ->unique('perusahaan_cabang_singleton_unique')
                    ->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('perusahaan_cabang', 'singleton_key')) {
            Schema::table('perusahaan_cabang', function (Blueprint $table) {
                $table->dropUnique('perusahaan_cabang_singleton_unique');
                $table->dropColumn('singleton_key');
            });
        }
    }
};
