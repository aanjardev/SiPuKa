<?php

namespace App\Services;

use App\Models\DetailPenjualan;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class StockForecastingService
{
    /**
     * Calculate Average Daily Sales (ADS) based on sales history.
     *
     * @param int $productId
     * @param int $days Number of days to look back (default: 30)
     * @return float Average daily sales quantity
     */
    public function calculateDailyUsage(int $productId, int $days = 30): float
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $totalQty = DetailPenjualan::join('penjualan', 'detail_penjualan.penjualan_id', '=', 'penjualan.id')
            ->where('detail_penjualan.produk_id', $productId)
            ->whereBetween('penjualan.created_at', [$startDate, $endDate])
            ->sum('detail_penjualan.qty');

        $averageDailyUsage = $totalQty / $days;

        return max(0, round($averageDailyUsage, 2));
    }

    /**
     * Predict how many days until stock is depleted.
     *
     * @param int $productId
     * @param int|null $currentStock Optional current stock. If null, will fetch from database.
     * @return int Estimated days until stock is empty (999 if ADS is 0 or stock is infinite)
     */
    public function predictStockDepletion(int $productId, ?int $currentStock = null): int
    {

        if ($currentStock === null) {
            $product = Produk::find($productId);
            if (!$product) {
                return 999; // Product not found, return safe value
            }
            $currentStock = (int) $product->stok_produk;
        }

        if ($currentStock <= 0) {
            return 0;
        }

        $averageDailyUsage = $this->calculateDailyUsage($productId);

        if ($averageDailyUsage <= 0) {
            return 999;
        }

        $daysUntilDepletion = (int) floor($currentStock / $averageDailyUsage);

        return max(0, $daysUntilDepletion);
    }
}

