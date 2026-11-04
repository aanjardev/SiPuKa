<?php

namespace App\Models;

use App\Helpers\StoredImage;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['customer_name', 'review_text', 'source_name', 'source_url', 'rating', 'reviewed_at', 'screenshot_path', 'is_active', 'submission_token'];

    protected $casts = ['rating' => 'integer', 'reviewed_at' => 'date', 'is_active' => 'boolean'];

    public function getScreenshotPreviewUrlAttribute(): ?string
    {
        return StoredImage::url($this->screenshot_path, 640);
    }

    public function getScreenshotOriginalUrlAttribute(): ?string
    {
        return StoredImage::url($this->screenshot_path);
    }
}
