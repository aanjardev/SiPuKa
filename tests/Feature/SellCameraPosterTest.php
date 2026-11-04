<?php

namespace Tests\Feature;

use App\Models\CatalogSettings;
use App\Models\SellCameraPoster;
use App\Models\User;
use App\Services\SellCameraPosterStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class SellCameraPosterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('public');
        Storage::fake('r2');
    }

    private function admin(): User
    {
        $id = DB::table('karyawan')->insertGetId(['nama_lengkap' => 'Poster Admin', 'nik' => 'POSTER-ADMIN', 'jabatan' => 'Manager', 'tanggal_masuk' => '2026-01-01', 'status' => 'aktif', 'nomor_telepon' => '081234567890']);

        return User::factory()->create(['id' => $id, 'role' => 'manager', 'status' => 'active']);
    }

    private function poster(array $attributes = []): SellCameraPoster
    {
        $path = 'posters/'.Str::uuid().'.png';
        $image = UploadedFile::fake()->image('poster.png', 100, 160);
        Storage::disk('public')->put($path, file_get_contents($image->getPathname()));

        return SellCameraPoster::create(array_merge(['image_path' => 'public/'.$path, 'alt_text' => null, 'position' => SellCameraPoster::count() + 1, 'batch_token' => (string) Str::uuid(), 'upload_index' => 0], $attributes));
    }

    private function payload(array $overrides = []): array
    {
        return array_merge(['submission_token' => (string) Str::uuid(), 'images' => [UploadedFile::fake()->image('poster.png', 100, 160)]], $overrides);
    }

    public function test_guests_cannot_manage_posters(): void
    {
        $poster = $this->poster();
        foreach ([['get', 'index'], ['get', 'edit'], ['post', 'store'], ['put', 'update'], ['delete', 'destroy'], ['patch', 'move']] as [$method, $action]) {
            $url = route('admin.sell-camera-posters.'.$action, in_array($action, ['index', 'store']) ? [] : $poster);
            $this->{$method}($url)->assertRedirect(route('login'));
        }
    }

    public function test_empty_public_page_has_whatsapp_and_contact_fallback(): void
    {
        $setting = CatalogSettings::firstOrCreate(['singleton_key' => true], ['nama_website' => 'PusatKamera']);
        $setting->update(['nomor_telepon' => '082345672034']);
        $this->get('/jual-kamera')->assertOk()->assertSee('Panduan bergambar akan segera tersedia')->assertSee('https://wa.me/6282345672034?text=', false)->assertSee('Tawarkan Kamera via WhatsApp');
        $setting->update(['nomor_telepon' => null]);
        $this->app->forgetScopedInstances();
        $this->get('/jual-kamera')->assertOk()->assertSee('Hubungi Tim Kami')->assertDontSee('class="sell-camera-poster"', false);
        $this->get('/sitemap.xml')->assertOk()->assertSee('/jual-kamera');
        $this->get('/')->assertOk()->assertSee(route('sell-camera'));
    }

    public function test_batch_upload_preserves_originals_and_prevents_duplicate_submit(): void
    {
        $this->actingAs($this->admin());
        $file = UploadedFile::fake()->image('original.png', 300, 600);
        $original = file_get_contents($file->getPathname());
        $payload = $this->payload(['images' => [$file, UploadedFile::fake()->image('second.jpg')], 'alt_texts' => ['Alur di toko', null]]);
        $this->post(route('admin.sell-camera-posters.store'), $payload)->assertSessionHasNoErrors()->assertRedirect(route('admin.sell-camera-posters.index'));
        $this->assertDatabaseCount('sell_camera_posters', 2);
        $first = SellCameraPoster::orderBy('position')->firstOrFail();
        $this->assertSame('Alur di toko', $first->alt_text);
        $this->assertStringEndsWith('.png', $first->image_path);
        $this->assertSame($original, Storage::disk('r2')->get($first->image_path));
        $this->assertSame([], Storage::disk('local')->allFiles('temp/original-images'));
        $this->post(route('admin.sell-camera-posters.store'), $this->payload(['submission_token' => $payload['submission_token']]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('sell_camera_posters', 2);
        $this->assertCount(2, Storage::disk('r2')->allFiles());
        $this->get($first->original_url)->assertOk()->assertHeader('Content-Type', 'image/png');
        $this->get($first->preview_url)->assertOk()->assertHeader('Content-Type', 'image/webp');
        $this->get(route('admin.sell-camera-posters.index'))->assertOk()->assertSee('Naik')->assertSee('Turun');
    }

    public function test_public_posters_are_ordered_lazy_loaded_and_zoomable(): void
    {
        $second = $this->poster(['alt_text' => 'Poster kedua', 'position' => 2]);
        $first = $this->poster(['alt_text' => 'Poster pertama', 'position' => 1]);
        $this->get('/jual-kamera')->assertOk()->assertSeeInOrder([$first->preview_url, $second->preview_url])->assertSee('loading="eager"', false)->assertSee('loading="lazy"', false)->assertSee('data-original="'.e($first->original_url).'"', false)->assertSee('id="poster-dialog"', false);
    }

    public function test_invalid_uploads_and_oversized_batches_are_rejected(): void
    {
        $this->actingAs($this->admin());
        foreach ([
            [],
            [UploadedFile::fake()->create('poster.svg', 10, 'image/svg+xml')],
            [UploadedFile::fake()->image('large.png')->size(2049)],
            [UploadedFile::fake()->image('tall.png', 1, 5001)],
            array_map(fn () => UploadedFile::fake()->image('poster.png'), range(1, 6)),
            array_map(fn () => UploadedFile::fake()->image('poster.png')->size(1800), range(1, 4)),
        ] as $files) {
            $this->post(route('admin.sell-camera-posters.store'), $this->payload(['images' => $files]))->assertSessionHasErrors();
        }
        $this->post(route('admin.sell-camera-posters.store'), $this->payload(['alt_texts' => [str_repeat('x', 256)]]))->assertSessionHasErrors('alt_texts.0');
        $this->assertDatabaseCount('sell_camera_posters', 0);
        $this->assertSame([], Storage::disk('r2')->allFiles());
    }

    public function test_partial_batch_failure_cleans_new_files_and_preserves_existing_posters(): void
    {
        $old = $this->poster();
        Storage::disk('public')->put('posters/new.png', 'new');
        $calls = 0;
        $this->mock(SellCameraPosterStorage::class)->shouldReceive('upload')->twice()->andReturnUsing(function () use (&$calls) {
            if ($calls++ === 0) {
                return 'public/posters/new.png';
            }
            throw new \RuntimeException('Storage unavailable');
        });
        $this->actingAs($this->admin())->post(route('admin.sell-camera-posters.store'), $this->payload(['images' => [UploadedFile::fake()->image('one.png'), UploadedFile::fake()->image('two.png')]]))->assertSessionHasErrors('images');
        $this->assertDatabaseCount('sell_camera_posters', 1);
        Storage::disk('public')->assertMissing('posters/new.png');
        Storage::disk('public')->assertExists(substr($old->image_path, 7));
    }

    public function test_replacement_failure_keeps_old_image_and_text(): void
    {
        $poster = $this->poster(['alt_text' => 'Tetap']);
        $this->mock(SellCameraPosterStorage::class)->shouldReceive('upload')->once()->andThrow(new \RuntimeException('Storage unavailable'));
        $this->actingAs($this->admin())->put(route('admin.sell-camera-posters.update', $poster), ['image' => UploadedFile::fake()->image('new.png'), 'alt_text' => 'Berubah'])->assertSessionHasErrors('image');
        $this->assertSame('Tetap', $poster->fresh()->alt_text);
        Storage::disk('public')->assertExists(substr($poster->image_path, 7));
    }

    public function test_admin_can_edit_replace_and_delete_a_poster(): void
    {
        $poster = $this->poster();
        $old = $poster->image_path;
        $this->actingAs($this->admin());
        $this->get(route('admin.sell-camera-posters.edit', $poster))->assertOk();
        $this->put(route('admin.sell-camera-posters.update', $poster), ['alt_text' => 'Alur luar kota'])->assertSessionHasNoErrors();
        $this->assertSame($old, $poster->fresh()->image_path);
        $this->put(route('admin.sell-camera-posters.update', $poster), ['image' => UploadedFile::fake()->image('new.png'), 'alt_text' => 'Baru'])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing(substr($old, 7));
        $path = $poster->fresh()->image_path;
        Storage::disk('r2')->assertExists($path);
        $this->delete(route('admin.sell-camera-posters.destroy', $poster))->assertRedirect(route('admin.sell-camera-posters.index'));
        $this->assertDatabaseCount('sell_camera_posters', 0);
        Storage::disk('r2')->assertMissing($path);
    }

    public function test_reordering_is_bounded_and_a_repeated_move_is_not_applied_twice(): void
    {
        $first = $this->poster();
        $second = $this->poster();
        $last = $this->poster();
        $this->actingAs($this->admin());
        $this->patch(route('admin.sell-camera-posters.move', $first), ['direction' => 'up', 'expected_position' => 1])->assertSessionHasNoErrors();
        $this->patch(route('admin.sell-camera-posters.move', $last), ['direction' => 'down', 'expected_position' => 3])->assertSessionHasNoErrors();
        $payload = ['direction' => 'up', 'expected_position' => 2];
        $this->patch(route('admin.sell-camera-posters.move', $second), $payload)->assertSessionHasNoErrors();
        $this->patch(route('admin.sell-camera-posters.move', $second), $payload)->assertSessionHasNoErrors();
        $this->assertSame([$second->id, $first->id, $last->id], SellCameraPoster::orderBy('position')->pluck('id')->all());
        $this->patch(route('admin.sell-camera-posters.move', $second), ['direction' => 'invalid', 'expected_position' => 1])->assertSessionHasErrors('direction');
    }

    public function test_database_failure_rolls_back_the_whole_batch_and_cleans_uploaded_files(): void
    {
        $this->actingAs($this->admin());
        $count = 0;
        SellCameraPoster::creating(function () use (&$count) {
            if (++$count === 2) {
                throw new \RuntimeException('Database write failed');
            }
        });
        $this->withoutExceptionHandling();
        try {
            $this->post(route('admin.sell-camera-posters.store'), $this->payload(['images' => [UploadedFile::fake()->image('one.png'), UploadedFile::fake()->image('two.png')]]));
            $this->fail('Expected a database failure.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Database write failed', $exception->getMessage());
        }
        $this->assertDatabaseCount('sell_camera_posters', 0);
        $this->assertSame([], Storage::disk('r2')->allFiles());
    }

    public function test_original_storage_falls_back_to_local_when_r2_fails(): void
    {
        $local = Storage::disk('local');
        $public = Storage::disk('public');
        $remote = \Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        $remote->shouldReceive('putFileAs')->once()->andReturn(false);
        Storage::shouldReceive('disk')->with('local')->andReturn($local);
        Storage::shouldReceive('disk')->with('public')->andReturn($public);
        Storage::shouldReceive('disk')->with('r2')->andReturn($remote);
        $file = UploadedFile::fake()->image('original.png');
        $original = file_get_contents($file->getPathname());
        $path = app(SellCameraPosterStorage::class)->upload($file);
        $this->assertStringStartsWith('public/catalog/sell-camera/', $path);
        $this->assertSame($original, $public->get(substr($path, 7)));
    }
}
