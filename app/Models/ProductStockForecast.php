<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductStockForecast extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'current_stock',
        'average_daily_usage',
        'predicted_days_left',
    ];

    public function product()
    {
        return $this->belongsTo(Produk::class, 'product_id', 'id');
    }
}
