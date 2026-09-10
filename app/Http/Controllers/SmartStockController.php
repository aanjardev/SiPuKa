<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\ProductStockForecast;
use App\Models\User;
use App\Services\SmartStockCacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SmartStockController extends Controller
{
    public function __construct(private SmartStockCacheService $cacheService)
    {
    }

    /**
     * Display the smart stock analysis page.
     */
    public function index(Request $request)
    {
        $thresholdInput = $request->input('threshold');
        if ($thresholdInput === null || $thresholdInput === '') {
            $threshold = 3;
        } else {
            $thresholdParsed = filter_var($thresholdInput, FILTER_VALIDATE_INT);
            $threshold = $thresholdParsed === false ? 3 : (int) $thresholdParsed;
        }
        $threshold = max(1, min(30, $threshold));
        $sortBy = $request->input('sort', 'days_left'); // 'days_left', 'stock', 'name'
        $filter = $request->input('filter', 'all'); // 'all', 'critical', 'warning', 'safe'

        $this->cacheService->refreshAllIfStale();

        $query = ProductStockForecast::query()
            ->join('produk', 'product_stock_forecasts.product_id', '=', 'produk.id')
            ->select('product_stock_forecasts.*')
            ->with([
                'product:id,nama_produk,kode_sku,harga_jual,stok_produk,id_kategori',
                'product.kategori:id,nama_kategori',
            ]);

        if ($filter === 'critical') {
            $query->where('predicted_days_left', '<=', 1)->where('predicted_days_left', '<', 999);
        } elseif ($filter === 'warning') {
            $query->whereBetween('predicted_days_left', [2, max($threshold, 2)])->where('predicted_days_left', '<', 999);
        } elseif ($filter === 'safe') {
            $query->where('predicted_days_left', '>', $threshold)->where('predicted_days_left', '<', 999);
        } elseif ($filter === 'infinite') {
            $query->where('predicted_days_left', '>=', 999);
        }

        switch ($sortBy) {
            case 'stock':
                $query->orderByDesc('product_stock_forecasts.current_stock');
                break;
            case 'name':
                $query->orderBy('produk.nama_produk', 'asc');
                break;
            case 'days_left':
            default:
                $query->orderBy('product_stock_forecasts.predicted_days_left', 'asc');
                break;
        }

        $forecasts = $query->paginate(15);

        $analysisData = $forecasts->getCollection()->transform(function ($item) use ($threshold) {
            $status = 'safe';
            if ($item->predicted_days_left <= $threshold && $item->predicted_days_left < 999) {
                $status = $item->predicted_days_left <= 1 ? 'critical' : 'warning';
            } elseif ($item->predicted_days_left >= 999) {
                $status = 'infinite';
            }

            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product->nama_produk ?? 'Unknown',
                'current_stock' => (int) $item->current_stock,
                'predicted_days_left' => (int) $item->predicted_days_left,
                'average_daily_usage' => (float) $item->average_daily_usage,
                'status' => $status,
                'sku' => $item->product->kode_sku ?? '-',
                'harga_jual' => (int) ($item->product->harga_jual ?? 0),
                'kategori' => $item->product->kategori->nama_kategori ?? null,
            ];
        });

        $statsQuery = ProductStockForecast::query();
        $stats = [
            'total_products' => (clone $statsQuery)->count(),
            'critical' => (clone $statsQuery)->where('predicted_days_left', '<=', 1)->where('predicted_days_left', '<', 999)->count(),
            'warning' => (clone $statsQuery)->whereBetween('predicted_days_left', [2, max($threshold, 2)])->where('predicted_days_left', '<', 999)->count(),
            'safe' => (clone $statsQuery)->where('predicted_days_left', '>', $threshold)->where('predicted_days_left', '<', 999)->count(),
            'infinite' => (clone $statsQuery)->where('predicted_days_left', '>=', 999)->count(),
        ];

        return view('admin.smart-stock.index', [
            'analysisData' => $analysisData,
            'forecasts' => $forecasts,
            'stats' => $stats,
            'threshold' => $threshold,
            'sortBy' => $sortBy,
            'filter' => $filter,
            'pagination' => $forecasts->appends($request->query()),
        ]);
    }

    /**
     * Get stock prediction for a specific product (AJAX endpoint).
     */
    public function getProductPrediction(Request $request, $productId)
    {
        try {
            $forecast = ProductStockForecast::where('product_id', $productId)->first();
            if (!$forecast) {

                $this->cacheService->refreshOne($productId);
                $forecast = ProductStockForecast::where('product_id', $productId)->firstOrFail();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'product_id' => $forecast->product_id,
                    'product_name' => optional($forecast->product)->nama_produk ?? 'Unknown',
                    'current_stock' => (int) $forecast->current_stock,
                    'predicted_days_left' => (int) $forecast->predicted_days_left,
                    'average_daily_usage' => (float) $forecast->average_daily_usage,
                    'status' => $forecast->predicted_days_left <= 3
                        ? ($forecast->predicted_days_left <= 1 ? 'critical' : 'warning')
                        : ($forecast->predicted_days_left >= 999 ? 'infinite' : 'safe'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all low stock notifications for the current user.
     */
    public function getNotifications()
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $notifications = $user->notifications()
            ->where('type', 'App\Notifications\SmartLowStockAlert')
            ->whereNull('read_at')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data;
                return [
                    'id' => $notification->id,
                    'product_id' => $data['product_id'] ?? null,
                    'product_name' => $data['product_name'] ?? 'Unknown',
                    'current_stock' => $data['current_stock'] ?? 0,
                    'predicted_days_left' => $data['predicted_days_left'] ?? 0,
                    'message' => $data['message'] ?? '',
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $notifications,
            'count' => $notifications->count(),
        ]);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(Request $request, $notificationId)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated',
            ], 401);
        }

        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->markAsRead();
            return response()->json([
                'success' => true,
                'message' => 'Notification marked as read',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Notification not found',
        ], 404);
    }
}
