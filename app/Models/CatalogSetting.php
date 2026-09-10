<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogSetting extends Model
{
    use HasFactory;

    protected $table = 'catalog_settings';

    protected $fillable = [
        'site_name',
        'logo_path',
        'banner_path',
        'brand_logo_1_path',
        'brand_logo_2_path',
        'brand_logo_3_path',
        'brand_logo_4_path',
        'social_facebook',
        'social_instagram',
        'social_whatsapp',
        'social_tiktok',
        'social_youtube',
        'contact_phone',
        'contact_email',
        'address_text',
    ];
}

