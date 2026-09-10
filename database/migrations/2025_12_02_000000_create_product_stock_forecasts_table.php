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
        Schema::create('product_stock_forecasts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->unique();
            $table->integer('current_stock')->default(0);
            $table->decimal('average_daily_usage', 10, 2)->default(0);
            $table->integer('predicted_days_left')->default(0);
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('produk')->onDelete('cascade');
            $table->index('predicted_days_left');
            $table->index('current_stock');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stock_forecasts');
    }
};
