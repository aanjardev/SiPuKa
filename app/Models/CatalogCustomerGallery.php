<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CatalogCustomerGallery extends Model
{
    use HasFactory;

    protected $table = 'catalog_customer_galleries';
    protected $fillable = [
        'catalog_setting_id',
        'image_path',
        'caption',
    ];

    protected $appends = ['url'];

    public function getUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        $base = rtrim(env('CDN_BASE_URL', 'https://sidika.qurrotul-ainii0266.workers.dev'), '/');

        if ($base) {
            return $base . '/' . ltrim($this->image_path, '/');
        }

        return Storage::disk('r2')->url($this->image_path);
    }
}
