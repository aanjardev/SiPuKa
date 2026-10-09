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

        $selectedBranch = Branch::first()?->id;
        $selectedBranchName = Branch::first()?->nama ?? 'Toko';

        $labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $metrics = $this->dashboardMetrics->getMetrics($selectedYear, $selectedMonth, $selectedBranch);

        return view('admin.index', [
            'stockSummary' => [
                'available' => \App\Models\Produk::where('is_archived', false)->where('stok_produk', '>', 0)->count(),
                'empty' => \App\Models\Produk::where('is_archived', false)->where('stok_produk', '<=', 0)->count(),
                'hidden' => \App\Models\Produk::where('is_archived', false)->where('is_visible', false)->count(),
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
