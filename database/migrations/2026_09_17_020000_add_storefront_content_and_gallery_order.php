<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catalog_settings', function (Blueprint $table) {
            $table->string('hero_eyebrow')->nullable()->after('description');
            $table->string('hero_title')->nullable()->after('hero_eyebrow');
            $table->text('hero_description')->nullable()->after('hero_title');
            $table->string('seo_title')->nullable()->after('hero_description');
            $table->string('seo_description', 320)->nullable()->after('seo_title');
            $table->string('og_image_path')->nullable()->after('seo_description');
        });

        Schema::table('catalog_customer_galleries', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(0)->after('caption');
            $table->index(['catalog_setting_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('catalog_customer_galleries', function (Blueprint $table) {
            $table->dropIndex(['catalog_setting_id', 'sort_order']);
            $table->dropColumn('sort_order');
        });

        Schema::table('catalog_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_eyebrow', 'hero_title', 'hero_description',
                'seo_title', 'seo_description', 'og_image_path',
            ]);
        });
    }
};
