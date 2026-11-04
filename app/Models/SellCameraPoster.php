<?php

namespace App\Models;

use App\Helpers\StoredImage;
use Illuminate\Database\Eloquent\Model;

class SellCameraPoster extends Model
{
    protected $fillable = ['image_path', 'alt_text', 'position', 'batch_token', 'upload_index'];

    protected $casts = ['position' => 'integer', 'upload_index' => 'integer'];

    public function getPreviewUrlAttribute(): ?string
    {
        return StoredImage::url($this->image_path, 1280);
    }

    public function getOriginalUrlAttribute(): ?string
    {
        return StoredImage::url($this->image_path);
    }
}
