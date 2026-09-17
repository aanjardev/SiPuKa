<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('catalog_settings', 'singleton_key')) {
            Schema::table('catalog_settings', function (Blueprint $table) {
                $table->boolean('singleton_key')->default(true)->unique()->after('id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('catalog_settings', 'singleton_key')) {
            Schema::table('catalog_settings', function (Blueprint $table) {
                $table->dropUnique(['singleton_key']);
                $table->dropColumn('singleton_key');
            });
        }
    }
};
