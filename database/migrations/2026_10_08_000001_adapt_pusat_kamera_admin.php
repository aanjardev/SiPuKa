<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        foreach (['produk', 'item_pembelian_draft'] as $name) {
            Schema::table($name, function (Blueprint $table) {
                $table->foreignId('id_kategori_harga')->nullable()->constrained('kategori_harga')->restrictOnDelete();
            });
        }
        Schema::table('item_pembelian_draft', function (Blueprint $table) {
            $table->timestamp('stock_posted_at')->nullable();
            $table->uuid('submission_token')->nullable()->unique();
        });
        Schema::table('penjualan', fn (Blueprint $table) => $table->uuid('submission_token')->nullable()->unique());
        // Existing passed items have already contributed stock. Do not post them again.
        \Illuminate\Support\Facades\DB::table('item_pembelian_draft')->where('status_qc', 'lolos_qc')
            ->update(['stock_posted_at' => now()]);
        Schema::table('catalog_settings', function (Blueprint $table) {
            $table->text('about_description')->nullable();
            foreach (['buy', 'sell', 'repair'] as $service) {
                $table->string("service_{$service}_title", 100)->nullable();
                $table->text("service_{$service}_description")->nullable();
            }
        });
        \Illuminate\Support\Facades\DB::table('catalog_settings')->update(['about_description' => \Illuminate\Support\Facades\DB::raw('description')]);
    }

    public function down(): void
    {
        Schema::table('penjualan', fn (Blueprint $table) => $table->dropColumn('submission_token'));
        foreach (['produk', 'item_pembelian_draft'] as $name) {
            Schema::table($name, fn (Blueprint $table) => $table->dropConstrainedForeignId('id_kategori_harga'));
        }
        Schema::table('item_pembelian_draft', fn (Blueprint $table) => $table->dropColumn(['stock_posted_at', 'submission_token']));
        Schema::table('catalog_settings', fn (Blueprint $table) => $table->dropColumn([
            'about_description', 'service_buy_title', 'service_buy_description',
            'service_sell_title', 'service_sell_description', 'service_repair_title', 'service_repair_description',
        ]));
    }
};
