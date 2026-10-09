<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\DetailPenjualan;
use App\Models\GambarProduk;
use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardMetricsService
{
    /**
    * Hitung dan cache metrik dashboard berdasarkan kombinasi filter.
    *
    * @return array{
    *   totalPendapatan:int,
    *   totalHPP:int,
    *   totalLabaBersih:int,
    *   totalTransaksi:int,
    *   growthPercentage:float,
    *   dataPendapatanChart:array<int,int>,
    *   dataHppChart:array<int,int>,
    *   dataTransaksiChart:array<int,int>,
    *   topProducts:Collection,
    *   recentSales:Collection,
    *   recentPurchases:Collection,
    *   dataCabang:Collection,
    *   namaCabangTerbaik:string,
    *   bestBranchGrowthPercent:float,
    *   bestBranchGrowthPeriod:array{from:array{year:int,month:int},to:array{year:int,month:int}},
    *   showBestBranch:bool,
    * }
    */
    public function getMetrics(int $year, ?int $month, ?int $branchId): array
    {
        $penjualanBase = Penjualan::query()->whereYear('created_at', $year);
        if ($month) {
            $penjualanBase->whereMonth('created_at', $month);
        }
        if ($branchId) {
            $penjualanBase->where('perusahaan_cabang_id', $branchId);
        }

        $detailBase = DetailPenjualan::query()
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->leftJoin('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->whereYear('penjualan.created_at', $year);

        if ($month) {
            $detailBase->whereMonth('penjualan.created_at', $month);
        }
        if ($branchId) {
            $detailBase->where('penjualan.perusahaan_cabang_id', $branchId);
        }

        $totalPendapatan = (int) (clone $penjualanBase)->sum('harga_total');

        $totalHPP = (int) (clone $detailBase)
            ->selectRaw('SUM(detail_penjualan.qty * (COALESCE(produk.harga_beli, 0) + COALESCE(produk.harga_servis, 0))) as total')
            ->value('total') ?? 0;
        $totalLabaBersih = $totalPendapatan - $totalHPP;

        $totalPenjualan = (clone $penjualanBase)->count();
        $pembelianBase = Pembelian::query()->whereYear('created_at', $year);
        if ($month) {
            $pembelianBase->whereMonth('created_at', $month);
        }
        if ($branchId) {
            $pembelianBase->where('perusahaan_cabang_id', $branchId);
        }

        $pembelianBase->where('status_pembelian', 'deal');
        $totalPembelian = (clone $pembelianBase)->count();
        $totalTransaksi = $totalPenjualan + $totalPembelian;

        $growthPercentage = $this->calculateGrowth($year, $month, $branchId);


        [$dataPendapatanChart, $dataHppChart] = $this->buildAreaChartData($year, $branchId);
        $dataTransaksiChart = $this->buildDonutChartData($year, $month, $branchId);

        $topProducts = $this->topProducts($year, $branchId);
        $recentSales = $this->recentSales($year, $branchId);
        $recentPurchases = $this->recentPurchases($year, $branchId);

        $dataCabang = collect();
        $namaCabangTerbaik = '';
        $bestBranchGrowthPercent = 0;
        $bestBranchGrowthPeriod = [];
        $showBestBranch = false;

        return [
            'totalPendapatan' => $totalPendapatan,
            'totalHPP' => $totalHPP,
            'totalLabaBersih' => $totalLabaBersih,
            'totalTransaksi' => $totalTransaksi,
            'growthPercentage' => round($growthPercentage, 2),
            'dataPendapatanChart' => $dataPendapatanChart,
            'dataHppChart' => $dataHppChart,
            'dataTransaksiChart' => $dataTransaksiChart,
            'topProducts' => $topProducts,
            'recentSales' => $recentSales,
            'recentPurchases' => $recentPurchases,
            'dataCabang' => $dataCabang,
            'namaCabangTerbaik' => $namaCabangTerbaik,
            'bestBranchGrowthPercent' => round($bestBranchGrowthPercent, 2),
            'bestBranchGrowthPeriod' => $bestBranchGrowthPeriod,
            'showBestBranch' => $showBestBranch,
        ];
    }

    private function calculateGrowth(int $year, ?int $month, ?int $branchId): float
    {
        if ($month) {
            $current = Penjualan::query()
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month);
            $previousMonth = $month - 1;
            $previousYear = $year;
            if ($previousMonth < 1) {
                $previousMonth = 12;
                $previousYear = $year - 1;
            }
            $previous = Penjualan::query()
                ->whereYear('created_at', $previousYear)
                ->whereMonth('created_at', $previousMonth);

            if ($branchId) {
                $current->where('perusahaan_cabang_id', $branchId);
                $previous->where('perusahaan_cabang_id', $branchId);
            }

            $currentValue = (int) $current->sum('harga_total');
            $previousValue = (int) $previous->sum('harga_total');

            if ($previousValue > 0) {
                return (($currentValue - $previousValue) / $previousValue) * 100;
            }

            return $currentValue > 0 ? 100.0 : 0.0;
        }

        $current = Penjualan::query()->whereYear('created_at', $year);
        $previous = Penjualan::query()->whereYear('created_at', $year - 1);
        if ($branchId) {
            $current->where('perusahaan_cabang_id', $branchId);
            $previous->where('perusahaan_cabang_id', $branchId);
        }

        $currentValue = (int) $current->sum('harga_total');
        $previousValue = (int) $previous->sum('harga_total');

        if ($previousValue > 0) {
            return (($currentValue - $previousValue) / $previousValue) * 100;
        }

        return $currentValue > 0 ? 100.0 : 0.0;
    }

    /**
     * Build monthly area chart data (pendapatan & HPP) for the full year.
     *
     * @return array{0:array<int,int>,1:array<int,int>}
     */
    private function buildAreaChartData(int $year, ?int $branchId): array
    {
        $pendapatanBulananQuery = Penjualan::query()
            ->selectRaw('MONTH(created_at) as bulan, SUM(harga_total) as total')
            ->whereYear('created_at', $year);

        if ($branchId) {
            $pendapatanBulananQuery->where('perusahaan_cabang_id', $branchId);
        }

        $pendapatanBulanan = $pendapatanBulananQuery
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $hppBulananQuery = DetailPenjualan::query()
            ->selectRaw('MONTH(penjualan.created_at) as bulan, SUM(detail_penjualan.qty * COALESCE(produk.harga_beli, 0)) as total')
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->leftJoin('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->whereYear('penjualan.created_at', $year);

        if ($branchId) {
            $hppBulananQuery->where('penjualan.perusahaan_cabang_id', $branchId);
        }

        $hppBulanan = $hppBulananQuery
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $dataPendapatanChart = [];
        $dataHppChart = [];
        foreach (range(1, 12) as $bulan) {
            $dataPendapatanChart[] = (int) ($pendapatanBulanan[$bulan] ?? 0);
            $dataHppChart[] = (int) ($hppBulanan[$bulan] ?? 0);
        }

        return [$dataPendapatanChart, $dataHppChart];
    }

    /**
     * Build donut chart data untuk total transaksi (Penjualan vs Pembelian).
     * Mengikuti filter tahun, bulan (jika ada), dan cabang.
     */
    private function buildDonutChartData(int $year, ?int $month, ?int $branchId): array
    {
        $countPenjualan = Penjualan::query()->whereYear('created_at', $year);
        $countPembelian = Pembelian::query()->whereYear('created_at', $year);

        if ($month) {
            $countPenjualan->whereMonth('created_at', $month);
            $countPembelian->whereMonth('created_at', $month);
        }

        if ($branchId) {
            $countPenjualan->where('perusahaan_cabang_id', $branchId);
            $countPembelian->where('perusahaan_cabang_id', $branchId);
        }

        $countPembelian->where('status_pembelian', 'deal');

        return [
            (int) $countPenjualan->count(),
            (int) $countPembelian->count(),
        ];
    }

    private function topProducts(int $year, ?int $branchId): Collection
    {
        $mainImageSubquery = GambarProduk::query()
            ->select('path_gambar')
            ->whereColumn('id_produk', 'produk.id')
            ->orderByDesc('is_main')
            ->orderBy('id')
            ->limit(1);

        $query = DetailPenjualan::query()
            ->select([
                'produk.id',
                'produk.nama_produk',
                'produk.harga_jual',
                'produk.kode_sku',
                DB::raw('SUM(detail_penjualan.qty) as total_qty'),
            ])
            ->join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->join('produk', 'detail_penjualan.produk_id', '=', 'produk.id')
            ->whereYear('penjualan.created_at', $year)
            ->groupBy('produk.id', 'produk.nama_produk', 'produk.harga_jual', 'produk.kode_sku')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->addSelect(['main_image_path' => $mainImageSubquery]);

        if ($branchId) {
            $query->where('penjualan.perusahaan_cabang_id', $branchId);
        }

        return $query->get()->map(function ($item) {
            $gambar = $item->main_image_path
                ? new GambarProduk(['path_gambar' => $item->main_image_path])
                : null;

            return [
                'id' => $item->id,
                'nama_produk' => $item->nama_produk,
                'kode_sku' => $item->kode_sku ?? 'N/A',
                'harga_jual' => (int) $item->harga_jual,
                'total_qty' => (int) $item->total_qty,
                'gambar' => $gambar,
            ];
        });
    }

    private function recentSales(int $year, ?int $branchId): Collection
    {
        return Penjualan::query()
            ->with(['customer', 'branch'])
            ->whereYear('created_at', $year)
            ->when($branchId, fn ($q) => $q->where('perusahaan_cabang_id', $branchId))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($penjualan) {
                return [
                    'id' => $penjualan->id,
                    'kode_transaksi' => $penjualan->kode_transaksi ?? 'N/A',
                    'customer' => $penjualan->customer->nama ?? 'Guest',
                    'total' => (int) $penjualan->harga_total,
                    'status' => $penjualan->kas ?? 'cash',
                    'waktu' => $penjualan->created_at->format('d M Y, H:i'),
                    'cabang' => $penjualan->branch->nama ?? 'N/A',
                ];
            });
    }

    private function recentPurchases(int $year, ?int $branchId): Collection
    {
        return Pembelian::query()
            ->with(['customer', 'perusahaan_cabang', 'item_pembelian_draft'])
            ->whereYear('created_at', $year)
            ->when($branchId, fn ($q) => $q->where('perusahaan_cabang_id', $branchId))
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($pembelian) {
                return [
                    'id' => $pembelian->id,
                    'kode_transaksi' => $pembelian->kode_transaksi ?? '#' . $pembelian->id,
                    'customer' => $pembelian->customer->nama ?? 'N/A',
                    'harga_deal' => (int) ($pembelian->harga_deal ?? 0),
                    'status' => $pembelian->status_pembelian ?? 'draft',
                    'waktu' => $pembelian->created_at->format('d M Y, H:i'),
                    'cabang' => $pembelian->perusahaan_cabang->nama ?? 'N/A',
                    'items' => $pembelian->item_pembelian_draft->map(function ($item) {
                        return [
                            'nama_produk' => $item->nama_item ?? 'N/A',
                            'qty' => (int) ($item->qty ?? 0),
                        ];
                    })->toArray(),
                ];
            });
    }

    /**
     * @return array{0:Collection,1:string,2:float,3:array{from:array{year:int,month:int},to:array{year:int,month:int}},4:bool}
     */
}
