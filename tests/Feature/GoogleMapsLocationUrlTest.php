<?php

namespace Tests\Feature;

use App\Rules\GoogleMapsLocationUrl;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class GoogleMapsLocationUrlTest extends TestCase
{
    public function test_location_links_are_accepted_but_embed_and_unrelated_links_are_rejected(): void
    {
        foreach ([
            'https://maps.app.goo.gl/TestLokasi',
            'https://goo.gl/maps/TestLokasi',
            'https://www.google.com/maps/place/Pusat+Kamera/',
            'https://maps.google.com/?q=-7.9,112.6',
        ] as $url) {
            $this->assertTrue(Validator::make(['maps_url' => $url], [
                'maps_url' => ['url', new GoogleMapsLocationUrl],
            ])->passes(), $url);
        }

        foreach ([
            'https://www.google.com/maps/embed?pb=test',
            'https://www.google.com/maps?q=test&output=embed',
            'https://example.com/location',
            'https://www.google.com.evil.test/maps/place/test',
            'http://www.google.com/maps/place/test',
        ] as $url) {
            $this->assertTrue(Validator::make(['maps_url' => $url], [
                'maps_url' => ['url', new GoogleMapsLocationUrl],
            ])->fails(), $url);
        }
    }
}
