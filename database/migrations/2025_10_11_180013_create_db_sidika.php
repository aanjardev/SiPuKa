<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * VERSI HYBRID: Menggabungkan tabel admin baru dengan struktur tabel customer lama.
     */
    public function up(): void
    {




        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['manager', 'operasional'])->default('operasional')->after('password');
            }
        });


        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 50);
            $table->timestamps();
        });

        Schema::create('perusahaan_cabang', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->string('alamat', 200);
            $table->time('jam_buka')->nullable();
            $table->time('jam_tutup')->nullable();
            $table->string('nomor_telepon', 20);
            $table->timestamps();
        });

        Schema::create('customer', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 50);
            $table->string('identitas', 20)->nullable();
            $table->string('no_telp', 20);
            $table->string('alamat', 100)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('referensi', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });






        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('kode_sku', 20)->unique();



            $table->unsignedBigInteger('id_kategori'); // Pakai 'id_kategori' BUKAN 'kategori_id'
            $table->string('nama_produk', 200);
            $table->integer('harga_jual')->nullable();
            $table->integer('stok_produk')->nullable(); // INI YANG PENTING
            $table->text('deskripsi_produk')->nullable(); // INI YANG PENTING
            $table->enum('status', ['Second', 'Baru']); // INI YANG PENTING
            $table->enum('grade', ['Unggulan', 'Standar', 'Minus'])->default('Standar'); // INI YANG PENTING

            $table->integer('harga_beli')->nullable();
            $table->integer('harga_servis')->nullable();

            $table->timestamps();
            $table->foreign('id_kategori')->references('id')->on('kategori')->onDelete('cascade');
        });


        Schema::create('gambar_produk', function (Blueprint $table) { // Nama tabel lama
            $table->id();
            $table->unsignedBigInteger('id_produk'); // Nama kolom lama
            $table->string('path_gambar');
            $table->boolean('is_main')->default(false); // Logika 'is_main' lama
            $table->timestamps();

            $table->foreign('id_produk')->references('id')->on('produk')->onDelete('cascade'); // Foreign key lama
        });




        Schema::create('pembelian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer')->onDelete('restrict');
            $table->foreignId('perusahaan_cabang_id')->constrained('perusahaan_cabang')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');


            $table->string('keterangan', 200)->nullable();
            $table->integer('harga_tawaran_customer')->nullable();
            $table->integer('harga_tawaran_toko')->nullable();
            $table->integer('harga_deal')->nullable();

            $table->enum('status_pembelian', ['draft', 'deal', 'tidak_deal'])->default('draft');

            $table->timestamps();
        });

        Schema::create('item_pembelian_draft', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pembelian_id')->constrained('pembelian')->onDelete('cascade');
            $table->string('kode_sku', 20)->nullable();

            $table->string('nama_item', 200); // Misal: "Canon 60D Body Only"
            $table->foreignId('kategori_id')->constrained('kategori')->onDelete('restrict');
            $table->string('serial_number', 50)->nullable();
            $table->string('serial_lens', 50)->nullable();
            $table->string('kondisi_fisik', 100)->nullable();
            $table->string('kondisi_baut', 50)->nullable();
            $table->string('kondisi_tutup_usb', 50)->nullable();
            $table->string('kondisi_grip', 50)->nullable();
            $table->string('kondisi_jamur_lensa', 100)->nullable();
            $table->string('kondisi_view_finder', 50)->nullable();
            $table->string('kondisi_mounting', 50)->nullable();
            $table->string('kondisi_slot_memori', 50)->nullable();
            $table->string('kondisi_jamur_sensor', 100)->nullable();
            $table->string('kondisi_lcd', 100)->nullable();
            $table->string('kondisi_tombol', 50)->nullable();
            $table->string('kondisi_zoom_lensa', 50)->nullable();
            $table->string('kondisi_af_lensa', 50)->nullable();
            $table->string('kondisi_diafragma_lensa', 50)->nullable();
            $table->string('kondisi_kalibrasi_fokus', 50)->nullable();
            $table->string('kondisi_flash', 100)->nullable();
            $table->string('kondisi_sound_mic', 50)->nullable();
            $table->string('kondisi_lain_lain', 255)->nullable();

            $table->text('kelengkapan')->nullable(); // Misal: "Box, Baterai, Charger"
            $table->integer('qty')->default(1);

            $table->integer('harga_jual')->nullable();
            $table->integer('harga_beli')->nullable();
            $table->integer('harga_servis')->nullable();
            $table->enum('grade', ['Unggulan', 'Standar', 'Minus'])->default('Standar')->nullable();
            $table->enum('status', ['Second', 'Baru'])->default('Second');
            $table->text('deskripsi_produk')->nullable();

            $table->enum('status_qc', ['menunggu_qc', 'lolos_qc', 'gagal_qc', 'diarsipkan'])->default('menunggu_qc');
            $table->text('catatan_qc')->nullable(); // Catatan dari tim QC
            $table->timestamps();
        });

        Schema::create('penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customer')->onDelete('restrict');
            $table->foreignId('perusahaan_cabang_id')->constrained('perusahaan_cabang')->onDelete('restrict');
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->integer('harga_total');
            $table->double('diskon')->nullable();
            $table->enum('kas', ['transfer', 'cash']);
            $table->string('keterangan', 200)->nullable();
            $table->date('tanggal');
            $table->timestamps();
        });

        Schema::create('detail_penjualan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penjualan_id')->constrained('penjualan')->onDelete('cascade');
            $table->foreignId('produk_id')->constrained('produk')->onDelete('restrict');

            $table->integer('qty');
            $table->integer('harga_jual_satuan');
            $table->double('harga_depresiasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualan');
        Schema::dropIfExists('penjualan');
        Schema::dropIfExists('item_pembelian_draft');
        Schema::dropIfExists('pembelian');
        Schema::dropIfExists('gambar_produk');
        Schema::dropIfExists('produk');

        Schema::dropIfExists('customer');
        Schema::dropIfExists('perusahaan_cabang');
        Schema::dropIfExists('kategori');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
    }
};
