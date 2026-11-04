<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sell_camera_posters', function (Blueprint $table) {
            $table->id();
            $table->string('image_path');
            $table->string('alt_text')->nullable();
            $table->unsignedInteger('position');
            $table->uuid('batch_token');
            $table->unsignedTinyInteger('upload_index');
            $table->timestamps();
            $table->unique(['batch_token', 'upload_index']);
            $table->index(['position', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sell_camera_posters');
    }
};
