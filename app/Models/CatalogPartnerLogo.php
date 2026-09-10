<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CatalogPartnerLogo extends Model
{
    use HasFactory;
    protected $table = 'catalog_partner_logos';
    protected $fillable = [
        'logo_path',
        'catalog_setting_id'
    ];

    protected $appends = ['url'];

    public function getUrlAttribute()
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
}
