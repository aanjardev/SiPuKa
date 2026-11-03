<?php

namespace Tests\Feature;

use App\Helpers\ImageUpload;
use App\Helpers\StoredImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PartnerLogoUploadTest extends TestCase
{
    public function test_partner_logo_is_encoded_stored_and_served_as_an_image(): void
    {
        Storage::fake('r2');
        Storage::fake('local');
        config(['filesystems.disks.r2.url' => null]);
        $upload = ImageUpload::upload(UploadedFile::fake()->image('partner.png', 160, 90), 'catalog/partners/test');
        Storage::disk('r2')->assertExists($upload['path']);
        $info = getimagesizefromstring(Storage::disk('r2')->get($upload['path']));
        $this->assertSame('image/webp', $info['mime']);
        config(['filesystems.disks.r2.url' => 'https://unavailable.invalid']);
        $partner = new \App\Models\Partner(['logo_path' => $upload['path']]);
        $this->assertStringNotContainsString('unavailable.invalid', $partner->logo_url);
        $this->get($partner->logo_url)->assertOk()->assertHeader('Content-Type', 'image/webp');
        StoredImage::delete($upload['path']);
        Storage::disk('r2')->assertMissing($upload['path']);
    }
}
