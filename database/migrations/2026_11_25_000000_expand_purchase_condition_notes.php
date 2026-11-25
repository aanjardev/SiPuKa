<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE item_pembelian_draft MODIFY kondisi_lain_lain TEXT NULL');
    }

    public function down(): void
    {
        if (DB::table('item_pembelian_draft')->whereRaw('CHAR_LENGTH(kondisi_lain_lain) > 255')->exists()) {
            throw new RuntimeException('Rollback dibatalkan: catatan kondisi melebihi 255 karakter. Cadangkan dan sesuaikan catatan sebelum rollback.');
        }

        DB::statement('ALTER TABLE item_pembelian_draft MODIFY kondisi_lain_lain VARCHAR(255) NULL');
    }
};
