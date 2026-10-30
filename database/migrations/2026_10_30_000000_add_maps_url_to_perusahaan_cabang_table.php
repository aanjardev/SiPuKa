<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('perusahaan_cabang', function (Blueprint $table) {
            $table->text('maps_url')->nullable()->after('maps_embed_url');
        });
    }

    public function down(): void
    {
        Schema::table('perusahaan_cabang', function (Blueprint $table) {
            $table->dropColumn('maps_url');
        });
    }
};
