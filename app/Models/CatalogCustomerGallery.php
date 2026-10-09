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
        return \App\Helpers\StoredImage::url($this->image_path);
    }
}
