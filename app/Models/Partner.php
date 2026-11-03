<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $fillable = ['nama', 'logo_path', 'urutan', 'is_active', 'submission_token'];

    protected $casts = ['is_active' => 'boolean', 'urutan' => 'integer'];

    public function getLogoUrlAttribute(): ?string
    {
        return url(\Illuminate\Support\Facades\URL::signedRoute('stored-image', ['path' => $this->logo_path], null, false));
    }
}
