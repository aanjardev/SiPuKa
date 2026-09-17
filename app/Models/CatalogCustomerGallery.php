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
        'sort_order',
    ];

    protected $appends = ['url'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function getUrlAttribute()
    {
        if (!$this->image_path) {
            return null;
        }

        $base = rtrim(config('filesystems.disks.r2.url', ''), '/');

        if ($base) {
            return $base . '/' . ltrim($this->image_path, '/');
        }

        return Storage::disk('r2')->url($this->image_path);
    }
}
