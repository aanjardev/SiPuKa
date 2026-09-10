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

        $allBranches = Branch::orderBy('nama', 'asc')->get(['id', 'nama']);

        $selectedBranch = $request->input('branch_id');
        $selectedBranch = $selectedBranch === 'all' ? null : $selectedBranch;
        if (!empty($selectedBranch)) {
            $selectedBranch = (int) $selectedBranch;
            if (!$allBranches->pluck('id')->contains($selectedBranch)) {
                $selectedBranch = null;
            }
        } else {
            $selectedBranch = null;
        }

        $selectedBranchName = $selectedBranch
            ? optional($allBranches->firstWhere('id', $selectedBranch))->nama ?? 'Cabang'
            : 'Semua Cabang';

        $labelBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

        $metrics = $this->dashboardMetrics->getMetrics($selectedYear, $selectedMonth, $selectedBranch);

        return view('admin.index', [
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'labelBulan' => $labelBulan,
            'selectedBranch' => $selectedBranch,
            'allBranches' => $allBranches,
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
            'showBestBranch' => $metrics['showBestBranch'],
        ]);
    }}
