<?php

namespace App\Helpers;

use App\Models\CatalogSettings;

class Brand
{
    public static function name(): string
    {
        try {
            return CatalogSettings::first()?->nama_website ?: 'PusatKamera.id';
        } catch (\Throwable $exception) {
            return 'PusatKamera.id';
        }
    }
}
