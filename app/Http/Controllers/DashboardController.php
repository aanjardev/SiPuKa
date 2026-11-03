<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Services\DashboardMetricsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardMetricsService $dashboardMetrics)
    {
    }

    public function index(Request $request)
    {
        $selectedYear = (int) $request->input('year', now()->year);

        $selectedMonth = $request->input('month');
        $selectedMonth = $selectedMonth ? (int) $selectedMonth : null;

        $branch = Branch::first();
        $selectedBranch = $branch?->id;
        $selectedBranchName = $branch?->nama ?? 'Toko';

        $labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $metrics = $this->dashboardMetrics->getMetrics($selectedYear, $selectedMonth, $selectedBranch);

        $stock = \App\Models\Produk::where('is_archived', false)
            ->selectRaw('COALESCE(SUM(stok_produk > 0), 0) as available, COALESCE(SUM(stok_produk <= 0), 0) as empty_count, COALESCE(SUM(is_visible = 0), 0) as hidden')
            ->first();

        return view('admin.index', [
            'stockSummary' => [
                'available' => (int) $stock->available,
                'empty' => (int) $stock->empty_count,
                'hidden' => (int) $stock->hidden,
            ],
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'labelBulan' => $labelBulan,
            'selectedBranch' => $selectedBranch,
            'selectedBranchName' => $selectedBranchName,
            'totalPendapatan' => $metrics['totalPendapatan'],
            'totalHPP' => $metrics['totalHPP'],
            'totalLabaBersih' => $metrics['totalLabaBersih'],
            'totalTransaksi' => $metrics['totalTransaksi'],
            'growthPercentage' => $metrics['growthPercentage'],
            'dataPendapatanChart' => $metrics['dataPendapatanChart'],
            'dataHppChart' => $metrics['dataHppChart'],
            'dataTransaksiChart' => $metrics['dataTransaksiChart'],
            'topProducts' => $metrics['topProducts'],
            'recentSales' => $metrics['recentSales'],
            'recentPurchases' => $metrics['recentPurchases'],
            'dataCabang' => $metrics['dataCabang'],
            'namaCabangTerbaik' => $metrics['namaCabangTerbaik'],
            'bestBranchGrowthPercent' => $metrics['bestBranchGrowthPercent'],
            'bestBranchGrowthPeriod' => $metrics['bestBranchGrowthPeriod'],
            'showBestBranch' => false,
        ]);
    }}
