<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kategori_harga', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 80);
            $table->string('slug', 80)->unique();
            $table->unsignedBigInteger('harga_minimum')->default(0);
            $table->unsignedBigInteger('harga_maksimum')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('kategori_harga')->insert([
            ['nama'=>'500 Ribuan','slug'=>'500rb','harga_minimum'=>500000,'harga_maksimum'=>999999,'urutan'=>1,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['nama'=>'1 Jutaan','slug'=>'1jt','harga_minimum'=>1000000,'harga_maksimum'=>1999999,'urutan'=>2,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['nama'=>'2 Jutaan','slug'=>'2jt','harga_minimum'=>2000000,'harga_maksimum'=>2999999,'urutan'=>3,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['nama'=>'3 Jutaan','slug'=>'3jt','harga_minimum'=>3000000,'harga_maksimum'=>3999999,'urutan'=>4,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['nama'=>'4-5 Juta','slug'=>'4-5jt','harga_minimum'=>4000000,'harga_maksimum'=>5999999,'urutan'=>5,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['nama'=>'6-10 Juta','slug'=>'6-10jt','harga_minimum'=>6000000,'harga_maksimum'=>10999999,'urutan'=>6,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
            ['nama'=>'Di Atas 10 Juta','slug'=>'diatas-10jt','harga_minimum'=>11000000,'harga_maksimum'=>null,'urutan'=>7,'is_active'=>true,'created_at'=>$now,'updated_at'=>$now],
        ]);
    }

    public function down(): void { Schema::dropIfExists('kategori_harga'); }
};
