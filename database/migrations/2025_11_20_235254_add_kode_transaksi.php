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

        Schema::table('pembelian', function (Blueprint $table) {

            $table->string('kode_transaksi', 20)->unique()->after('id');
        });

        Schema::table('penjualan', function (Blueprint $table) {
            $table->string('kode_transaksi', 20)->unique()->after('id');
        });

        Schema::table('customer', function (Blueprint $table) {
            $table->string('kode_customer', 10)->unique()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('pembelian', function (Blueprint $table) {
            $table->dropColumn('kode_transaksi');
        });

        Schema::table('penjualan', function (Blueprint $table) {
            $table->dropColumn('kode_transaksi');
        });

        Schema::table('customer', function (Blueprint $table) {
            $table->dropColumn('kode_customer');
        });
    }
};
