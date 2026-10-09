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
        'about_description',
        'service_buy_title',
        'service_buy_description',
        'service_sell_title',
        'service_sell_description',
        'service_repair_title',
        'service_repair_description',
        'singleton_key',
        'logo_path',
        'nomor_telepon',
        'description',
        'hero_eyebrow',
        'hero_title',
        'hero_description',
        'hero_image_path',
        'seo_title',
        'seo_description',
        'og_image_path',
        'facebook_link',
        'instagram_link',
        'tiktok_link',
        'youtube_link',
    ];
    public function getLogoUrlAttribute()
    {
        return \App\Helpers\StoredImage::url($this->logo_path);
    }
    protected $appends = ['logo_url'];

    public function getOgImageUrlAttribute(): ?string
    {
        return \App\Helpers\StoredImage::url($this->og_image_path) ?? $this->logo_url;
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        return \App\Helpers\StoredImage::url($this->hero_image_path);
    }

    protected $casts = [
        'singleton_key' => 'boolean',
    ];

}
