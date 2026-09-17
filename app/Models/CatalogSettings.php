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
        'singleton_key',
        'logo_path',
        'nomor_telepon',
        'description',
        'hero_eyebrow',
        'hero_title',
        'hero_description',
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
        if (!$this->logo_path) {
            return null;
        }

        $base = rtrim(config('filesystems.disks.r2.url', ''), '/');

        if ($base) {
            return $base . '/' . ltrim($this->logo_path, '/');
        }

        return Storage::disk('r2')->url($this->logo_path);
    }
    protected $appends = ['logo_url'];

    public function getOgImageUrlAttribute(): ?string
    {
        if (!$this->og_image_path) {
            return $this->logo_url;
        }

        $base = rtrim(config('filesystems.disks.r2.url', ''), '/');

        return $base
            ? $base . '/' . ltrim($this->og_image_path, '/')
            : Storage::disk('r2')->url($this->og_image_path);
    }

    protected $casts = [
        'singleton_key' => 'boolean',
    ];

}
