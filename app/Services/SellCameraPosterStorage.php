<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class SellCameraPosterStorage
{
    public function __construct(private OriginalImageStorage $images) {}

    public function upload(UploadedFile $file): string
    {
        return $this->images->upload($file, 'catalog/sell-camera');
    }
}
