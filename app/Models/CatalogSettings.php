<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CatalogSettings extends Model
{
    use HasFactory;
    protected $table = 'catalog_settings';
    protected $fillable = [
        'nama_website',
        'logo_path',
        'nomor_telfon',
        'description',
        'facebook_link',
        'instagram_link',
        'tiktok_link',
        'youtube_link',
        'tokopedia_link',
        'shopee_link',
    ];
    public function getLogoUrlAttribute()
    {
        if (!$this->logo_path) {
            return null;
        }

        $base = rtrim(env('CDN_BASE_URL', 'https://sidika.qurrotul-ainii0266.workers.dev'), '/');

        if ($base) {
            return $base . '/' . ltrim($this->logo_path, '/');
        }

        return Storage::disk('r2')->url($this->logo_path);
    }
    protected $appends = ['logo_url'];

}
