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
        Schema::create('catalog_settings', function (Blueprint $table) {
        $table->id();
        $table->string('nama_website')->nullable();
        $table->string('nomor_telfon')->nullable();
        $table->text('description')->nullable();
        $table->string('logo_path')->nullable();
        $table->string('facebook_link')->nullable();
        $table->string('youtube_link')->nullable();
        $table->string('instagram_link')->nullable();
        $table->string('tiktok_link')->nullable();
        $table->string('tokopedia_link')->nullable();
        $table->string('shopee_link')->nullable();
        $table->timestamps();
        });

    Schema::create('catalog_banners', function (Blueprint $table) {
        $table->id();
        $table->foreignId('catalog_setting_id')->constrained()->onDelete('cascade'); // relasi ke catalog_settings
        $table->string('banner_path')->nullable();
        $table->timestamps();
        });

    Schema::create('catalog_partner_logos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('catalog_setting_id')->constrained()->onDelete('cascade'); // relasi ke catalog_settings
        $table->string('logo_path')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_settings');
        Schema::dropIfExists('catalog_banners');
        Schema::dropIfExists('catalog_partner_logos');
    }
};
