<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->index('kode_sku');
            $table->index('id_kategori');
        });

        Schema::table('penjualan', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('perusahaan_cabang_id');
        });

        Schema::table('detail_penjualan', function (Blueprint $table) {
            $table->index('produk_id');
        });

        Schema::table('pembelian', function (Blueprint $table) {
            $table->index('created_at');
            $table->index('perusahaan_cabang_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::table('produk', function (Blueprint $table) {
            $table->dropIndex(['kode_sku']);
            $table->dropIndex(['id_kategori']);
        });

        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['perusahaan_cabang_id']);
        });

        Schema::table('detail_penjualan', function (Blueprint $table) {
            $table->dropIndex(['produk_id']);
        });

        Schema::table('pembelian', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['perusahaan_cabang_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email']);
        });
    }
};
