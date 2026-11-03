<?php

namespace Tests\Feature;

use App\Helpers\StoredImage;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ImagePerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');
        Storage::fake('r2');
    }

    public function test_remote_image_is_downloaded_once_and_supports_browser_revalidation(): void
    {
        $image = UploadedFile::fake()->image('logo.png', 120, 60);
        $disk = \Mockery::mock(FilesystemAdapter::class);
        $disk->shouldReceive('readStream')->once()->with('partners/logo.png')->andReturn(fopen($image->getPathname(), 'rb'));
        $this->app['filesystem']->set('r2', $disk);
        $url = StoredImage::url('partners/logo.png');
        $first = $this->get($url)->assertOk()->assertHeader('Content-Type', 'image/png');
        $this->get($url)->assertOk();
        $this->withHeaders(['If-None-Match' => $first->headers->get('ETag')])->get($url)->assertStatus(304);
    }

    public function test_thumbnails_preserve_proportions_and_do_not_change_original(): void
    {
        $image = UploadedFile::fake()->image('large.jpg', 1600, 800);
        $original = file_get_contents($image->getPathname());
        Storage::disk('public')->put('products/large.jpg', $original);
        $response = $this->get(StoredImage::url('public/products/large.jpg', 320))->assertOk()->assertHeader('Content-Type', 'image/webp');
        $info = getimagesize($response->baseResponse->getFile()->getPathname());
        $this->assertSame(320, $info[0]);
        $this->assertSame(160, $info[1]);
        $this->assertSame($original, Storage::disk('public')->get('products/large.jpg'));
        StoredImage::delete('public/products/large.jpg');
        Storage::disk('public')->assertMissing('products/large.jpg');
        $this->assertDirectoryDoesNotExist(Storage::disk('local')->path('image-cache/'.hash('sha256', 'public/products/large.jpg')));
    }

    public function test_image_endpoint_keeps_signature_and_path_validation(): void
    {
        $this->get('/media/image?path=partners/logo.png')->assertForbidden();
        foreach ([['path' => '../.env'], ['path' => 'private.php'], ['path' => 'logo.png', 'width' => '99999'], ['path' => 'logo.png', 'width' => ['320']]] as $parameters) {
            $url = URL::signedRoute('stored-image', $parameters, null, false);
            $this->get($url)->assertNotFound();
        }
        $this->get(StoredImage::url('missing.png'))->assertNotFound();
    }

    public function test_cache_pruning_removes_only_expired_cache_files(): void
    {
        $directory = 'image-cache/'.hash('sha256', 'old.png');
        Storage::disk('local')->put($directory.'/source.png', 'old');
        touch(Storage::disk('local')->path($directory.'/source.png'), time() - 8 * 86400);
        Storage::disk('local')->put($directory.'/fresh.webp', 'fresh');
        Storage::disk('local')->put('uploads/original.png', 'original');
        $this->artisan('images:prune-cache')->assertSuccessful();
        Storage::disk('local')->assertMissing($directory.'/source.png');
        Storage::disk('local')->assertExists($directory.'/fresh.webp');
        Storage::disk('local')->assertExists('uploads/original.png');
    }
}
