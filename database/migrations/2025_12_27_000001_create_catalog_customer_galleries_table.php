<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalog_customer_galleries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('catalog_setting_id')->nullable();
            $table->string('image_path');
            $table->string('caption')->nullable();
            $table->timestamps();

            $table->foreign('catalog_setting_id')
                ->references('id')
                ->on('catalog_settings')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_customer_galleries');
    }
};
