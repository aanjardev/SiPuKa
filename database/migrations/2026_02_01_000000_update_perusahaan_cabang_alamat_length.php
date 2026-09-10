<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Update alamat column length from 100 to 200 characters
     */
    public function up(): void
    {
        Schema::table('perusahaan_cabang', function (Blueprint $table) {
            $table->string('alamat', 200)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perusahaan_cabang', function (Blueprint $table) {
            $table->string('alamat', 100)->change();
        });
    }
};
