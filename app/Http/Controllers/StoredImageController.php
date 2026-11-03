<?php

namespace App\Http\Controllers;

use App\Services\ImageResponseService;
use Illuminate\Http\Request;

class StoredImageController extends Controller
{
    public function show(Request $request, ImageResponseService $images)
    {
        $path = $request->query('path');
        $width = $request->query('width', '0');
        abort_unless(is_string($path) && is_scalar($width) && in_array((string) $width, ['0', '320', '640', '1280'], true), 404);

        return $images->respond($request, $path, (int) $width);
    }
}
