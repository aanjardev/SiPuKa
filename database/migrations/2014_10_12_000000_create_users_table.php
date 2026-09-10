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

        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 50);
            $table->string('nik', 20)->unique();
            $table->string('jabatan', 50);
            $table->integer('gaji')->nullable();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->string('nomor_telepon', 15);
            $table->string('alamat', 100)->nullable();
            $table->timestamps();
        });

        Schema::create('users', function (Blueprint $table) {
            $table->foreignId('id')->constrained('karyawan')->onDelete('cascade');
            $table->string('name')->nullable(); // Menambahkan kolom name
            $table->string('password');
            $table->string('email')->unique()->nullable();
            $table->string('role');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('karyawan');
    }
};
