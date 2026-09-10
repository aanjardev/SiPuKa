<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('item_pembelian_draft')) {
            return;
        }

        // Drop FK if exists, then make column nullable, then re-add FK.
        try {
            DB::statement('ALTER TABLE item_pembelian_draft DROP FOREIGN KEY item_pembelian_draft_pembelian_id_foreign');
        } catch (\Throwable $e) {
            // ignore if FK not found
        }

        DB::statement('ALTER TABLE item_pembelian_draft MODIFY pembelian_id BIGINT UNSIGNED NULL');

        try {
            DB::statement('ALTER TABLE item_pembelian_draft ADD CONSTRAINT item_pembelian_draft_pembelian_id_foreign FOREIGN KEY (pembelian_id) REFERENCES pembelian(id) ON DELETE CASCADE');
        } catch (\Throwable $e) {
            // ignore if cannot re-add (already exists)
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('item_pembelian_draft')) {
            return;
        }

        try {
            DB::statement('ALTER TABLE item_pembelian_draft DROP FOREIGN KEY item_pembelian_draft_pembelian_id_foreign');
        } catch (\Throwable $e) {
            // ignore if FK not found
        }

        DB::statement('ALTER TABLE item_pembelian_draft MODIFY pembelian_id BIGINT UNSIGNED NOT NULL');

        try {
            DB::statement('ALTER TABLE item_pembelian_draft ADD CONSTRAINT item_pembelian_draft_pembelian_id_foreign FOREIGN KEY (pembelian_id) REFERENCES pembelian(id) ON DELETE CASCADE');
        } catch (\Throwable $e) {
            // ignore if cannot re-add (already exists)
        }
    }
};
