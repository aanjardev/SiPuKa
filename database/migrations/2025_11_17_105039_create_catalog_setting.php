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
        // Fallback untuk instalasi lama yang pernah menjalankan migration sebelumnya saat masih kosong.
        if (!Schema::hasTable('catalog_settings')) {
            Schema::create('catalog_settings', function (Blueprint $table) {
                $table->id();
                $table->boolean('singleton_key')->default(true)->unique();
                $table->string('nama_website');
                $table->string('nomor_telepon', 20)->nullable();
                $table->text('description')->nullable();
                $table->string('logo_path')->nullable();
                $table->string('facebook_link')->nullable();
                $table->string('youtube_link')->nullable();
                $table->string('instagram_link')->nullable();
                $table->string('tiktok_link')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op.
    }
};
