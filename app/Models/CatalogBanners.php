<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CatalogBanners extends Model
{
    use HasFactory;
    protected $table = 'catalog_banners';
    protected $fillable = [
        'banner_path',
        'catalog_setting_id'
    ];

    protected $appends = ['banner_url'];

    public function getBannerUrlAttribute()
    {
        if (!$this->banner_path) {
            return null;
        }

        $base = rtrim(env('CDN_BASE_URL', 'https://sidika.qurrotul-ainii0266.workers.dev'), '/');

        if ($base) {
            return $base . '/' . ltrim($this->banner_path, '/');
        }

        return Storage::disk('r2')->url($this->banner_path);
    }
}
