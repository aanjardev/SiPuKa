<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Services\DashboardMetricsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardPerformanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_totals_keep_month_year_and_deal_filters(): void
    {
        $branch = Branch::create(['nama' => 'Toko Test', 'alamat' => 'Malang', 'nomor_telepon' => '081234567890', 'is_active' => true, 'singleton_key' => true]);
        $id = DB::table('karyawan')->insertGetId(['nama_lengkap' => 'Admin', 'nik' => 'PERF-TEST', 'jabatan' => 'Manager', 'tanggal_masuk' => '2025-01-01', 'status' => 'aktif', 'nomor_telepon' => '081234567890']);
        DB::table('users')->insert(['id' => $id, 'name' => 'Admin', 'email' => 'perf@example.test', 'password' => bcrypt('testing'), 'role' => 'manager', 'status' => 'active']);
        $customer = DB::table('customer')->insertGetId(['kode_customer' => 'PERF-TEST', 'nama' => 'Pelanggan', 'no_telp' => '081234567890', 'jenis_kelamin' => 'L']);
        foreach ([['2025-12-31 23:59:59', 500], ['2026-01-01 00:00:00', 1000], ['2026-12-31 23:59:59', 500], ['2027-01-01 00:00:00', 9000]] as $index => [$date, $amount]) {
            DB::table('penjualan')->insert(['kode_transaksi' => 'PERF-'.$index, 'customer_id' => $customer, 'perusahaan_cabang_id' => $branch->id, 'user_id' => $id, 'harga_total' => $amount, 'kas' => 'cash', 'tanggal' => substr($date, 0, 10), 'created_at' => $date, 'updated_at' => $date]);
        }
        foreach (['deal', 'draft'] as $status) {
            DB::table('pembelian')->insert(['kode_transaksi' => 'PERF-'.$status, 'customer_id' => $customer, 'perusahaan_cabang_id' => $branch->id, 'user_id' => $id, 'status_pembelian' => $status, 'created_at' => '2026-01-15 12:00:00', 'updated_at' => '2026-01-15 12:00:00']);
        }
        $service = app(DashboardMetricsService::class);
        $month = $service->getMetrics(2026, 1, $branch->id);
        $this->assertSame(1000, $month['totalPendapatan']);
        $this->assertSame(2, $month['totalTransaksi']);
        $this->assertSame([1, 1], $month['dataTransaksiChart']);
        $this->assertSame(100.0, $month['growthPercentage']);
        $year = $service->getMetrics(2026, null, $branch->id);
        $this->assertSame(1500, $year['totalPendapatan']);
        $this->assertSame(3, $year['totalTransaksi']);
        $this->assertSame(200.0, $year['growthPercentage']);
        $this->assertSame(1000, $year['dataPendapatanChart'][0]);
        $this->assertSame(500, $year['dataPendapatanChart'][11]);
    }
}
