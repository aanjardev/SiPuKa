<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name', 120);
            $table->text('review_text');
            $table->string('source_name', 80);
            $table->string('source_url', 2048);
            $table->unsignedTinyInteger('rating')->nullable();
            $table->date('reviewed_at')->nullable();
            $table->string('screenshot_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->uuid('submission_token')->unique();
            $table->timestamps();
            $table->index(['is_active', 'created_at', 'id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
