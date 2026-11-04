<?php

namespace App\Http\Controllers;

use App\Models\SellCameraPoster;
use App\Services\StorefrontData;

class SellCameraController extends Controller
{
    public function __invoke(StorefrontData $storefront)
    {
        $posters = SellCameraPoster::orderBy('position')->orderBy('id')->get();
        $phone = preg_replace('/\D+/', '', $storefront->settings()?->nomor_telepon ?? '');
        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62'.$phone;
        }
        $whatsAppUrl = $phone ? 'https://wa.me/'.$phone.'?text='.rawurlencode('Halo PusatKamera, saya ingin menjual kamera. Bisa dibantu prosesnya?') : null;

        return view('sell-camera', compact('posters', 'whatsAppUrl'));
    }
}
