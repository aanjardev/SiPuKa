<?php

namespace App\Services;

use App\Models\ProductStockForecast;
use App\Models\Produk;
use Illuminate\Support\Carbon;
use App\Services\StockForecastingService;

class SmartStockCacheService
{
    public function __construct(private StockForecastingService $forecastingService)
    {
    }

    /**
     * Recompute forecast untuk semua produk aktif jika data sudah kedaluwarsa.
     */
    public function refreshAllIfStale(int $maxAgeMinutes = 10): void
    {
        $lastUpdated = ProductStockForecast::max('updated_at');
        if (!$lastUpdated || Carbon::parse($lastUpdated)->lt(now()->subMinutes($maxAgeMinutes))) {
            $this->refreshAll();
        }
    }

    /**
     * Recompute forecast untuk semua produk dengan stok > 0.
     */
    public function refreshAll(): void
    {
        Produk::query()
            ->select(['id', 'nama_produk', 'kode_sku', 'harga_jual', 'stok_produk'])
            ->where('stok_produk', '>', 0)
            ->orderBy('id')
            ->chunk(200, function ($products) {
                $payload = [];

                foreach ($products as $product) {
                    $currentStock = (int) $product->stok_produk;
                    $averageDailyUsage = $this->forecastingService->calculateDailyUsage($product->id);
                    $predictedDaysLeft = $this->forecastingService->predictStockDepletion(
                        $product->id,
                        $currentStock
                    );

                    $payload[] = [
                        'product_id' => $product->id,
                        'current_stock' => $currentStock,
                        'average_daily_usage' => $averageDailyUsage,
                        'predicted_days_left' => $predictedDaysLeft,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ];
                }

                if (!empty($payload)) {
                    ProductStockForecast::upsert(
                        $payload,
                        ['product_id'],
                        ['current_stock', 'average_daily_usage', 'predicted_days_left', 'updated_at']
                    );
                }
            });
    }

    /**
     * Recompute untuk satu produk (mis. dipanggil dari event update stok).
     */
    public function refreshOne(int $productId, ?int $currentStock = null): void
    {
        $product = Produk::find($productId);
        if (!$product || $product->stok_produk <= 0) {
            ProductStockForecast::where('product_id', $productId)->delete();
            return;
        }

        $currentStock = $currentStock ?? (int) $product->stok_produk;
        $averageDailyUsage = $this->forecastingService->calculateDailyUsage($product->id);
        $predictedDaysLeft = $this->forecastingService->predictStockDepletion($product->id, $currentStock);

        ProductStockForecast::updateOrCreate(
            ['product_id' => $productId],
            [
                'current_stock' => $currentStock,
                'average_daily_usage' => $averageDailyUsage,
                'predicted_days_left' => $predictedDaysLeft,
            ]
        );
    }
}
