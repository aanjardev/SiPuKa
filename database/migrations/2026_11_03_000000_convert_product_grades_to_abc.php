<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $this->convert(['Unggulan' => 'A', 'Standar' => 'B', 'Minus' => 'C'], 'B');
    }

    public function down(): void
    {
        $this->convert(['A' => 'Unggulan', 'B' => 'Standar', 'C' => 'Minus'], 'Standar');
    }

    private function convert(array $mapping, string $default): void
    {
        // Expand before updating so MySQL never truncates existing enum values.
        foreach (['produk' => false, 'item_pembelian_draft' => true] as $table => $nullable) {
            $null = $nullable ? 'NULL' : 'NOT NULL';
            DB::statement("ALTER TABLE `{$table}` MODIFY `grade` ENUM('Unggulan','Standar','Minus','A','B','C') {$null} DEFAULT '{$default}'");
            foreach ($mapping as $from => $to) {
                DB::table($table)->where('grade', $from)->update(['grade' => $to]);
            }
            $values = "'".implode("','", array_values($mapping))."'";
            DB::statement("ALTER TABLE `{$table}` MODIFY `grade` ENUM({$values}) {$null} DEFAULT '{$default}'");
        }
    }
};
