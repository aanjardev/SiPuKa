<?php

namespace Tests\Feature;

use App\Models\Testimonial;
use App\Models\User;
use App\Services\OriginalImageStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class TestimonialTest extends TestCase
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
        $id = DB::table('karyawan')->insertGetId(['nama_lengkap' => 'Testimoni Admin', 'nik' => 'TESTIMONIAL-ADMIN', 'jabatan' => 'Manager', 'tanggal_masuk' => '2026-01-01', 'status' => 'aktif', 'nomor_telepon' => '081234567890']);

        return User::factory()->create(['id' => $id, 'role' => 'manager', 'status' => 'active']);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge(['customer_name' => 'Pelanggan Test', 'review_text' => 'Kamera sesuai kebutuhan.', 'source_name' => 'Google Maps', 'source_url' => 'https://www.google.com/maps/place/example', 'is_active' => 1, 'submission_token' => (string) Str::uuid()], $overrides);
    }

    private function record(array $overrides = []): Testimonial
    {
        return Testimonial::create($this->payload($overrides));
    }

    public function test_guests_cannot_manage_testimonials(): void
    {
        $testimonial = $this->record();
        foreach ([['get', 'index'], ['get', 'create'], ['get', 'edit'], ['post', 'store'], ['put', 'update'], ['delete', 'destroy']] as [$method, $action]) {
            $this->{$method}(route('admin.testimonials.'.$action, in_array($action, ['index', 'create', 'store']) ? [] : $testimonial))->assertRedirect(route('login'));
        }
    }

    public function test_empty_page_navigation_and_sitemap(): void
    {
        $this->get('/testimoni')->assertOk()->assertSee('CERITA PELANGGAN')->assertSee('Ulasan pelanggan akan segera hadir.')->assertDontSee('class="testimonial-entry"', false);
        $this->get('/')->assertOk()->assertSee(route('testimonials'));
        $this->get('/sitemap.xml')->assertOk()->assertSee('/testimoni');
    }

    public function test_admin_can_create_text_only_and_duplicate_submit_is_ignored(): void
    {
        $this->actingAs($this->admin());
        $this->get(route('admin.testimonials.index'))->assertOk();
        $this->get(route('admin.testimonials.create'))->assertOk()->assertSee('Tampilkan di website');
        $payload = $this->payload();
        $this->post(route('admin.testimonials.store'), $payload)->assertSessionHasNoErrors()->assertRedirect(route('admin.testimonials.index'));
        $this->post(route('admin.testimonials.store'), $payload)->assertSessionHasNoErrors();
        $this->assertDatabaseCount('testimonials', 1);
        $testimonial = Testimonial::firstOrFail();
        $this->assertNull($testimonial->rating);
        $this->assertNull($testimonial->reviewed_at);
        $this->assertNull($testimonial->screenshot_path);
        $this->get('/testimoni')->assertOk()->assertSee('Kamera sesuai kebutuhan.')->assertSee('Lihat ulasan asli')->assertDontSee('class="testimonial-rating"', false)->assertDontSee('class="testimonial-screenshot"', false);
    }

    public function test_public_page_shows_only_active_latest_entries_and_paginates_twelve(): void
    {
        $this->record(['customer_name' => 'Hidden Customer', 'is_active' => false]);
        foreach (range(1, 13) as $index) {
            $this->record(['customer_name' => 'Customer '.$index, 'created_at' => now()->subMinutes(14 - $index)]);
        }
        $response = $this->get('/testimoni')->assertOk()->assertSeeInOrder(['Customer 13', 'Customer 12'])->assertDontSee('Hidden Customer')->assertDontSee('Customer 1</h2>', false);
        $this->assertSame(12, substr_count($response->getContent(), '<article class="testimonial-entry">'));
        $this->get('/testimoni?page=2')->assertOk()->assertSee('Customer 1')->assertDontSee('Hidden Customer');
    }

    public function test_content_is_escaped_and_rating_date_and_original_link_are_optional_or_correct(): void
    {
        $this->record(['customer_name' => '<script>alert(1)</script>', 'review_text' => "<img src=x onerror=alert(1)>\nBaris kedua", 'rating' => 4, 'reviewed_at' => '2026-01-15', 'source_name' => '<b>Marketplace</b>', 'source_url' => 'https://example.com/product?review=1&order=2']);
        $this->get('/testimoni')->assertOk()->assertSee('<script>alert(1)</script>')->assertDontSee('<script>alert(1)</script>', false)->assertSee('<img src=x onerror=alert(1)>')->assertDontSee('<img src=x onerror=alert(1)>', false)->assertSee('Rating 4 dari 5')->assertSee('datetime="2026-01-15"', false)->assertSee('rel="noopener noreferrer"', false)->assertSee('https://example.com/product?review=1&order=2')->assertSee('<b>Marketplace</b>');
    }

    public function test_validation_rejects_missing_long_unsafe_and_future_values(): void
    {
        $this->actingAs($this->admin());
        $this->post(route('admin.testimonials.store'), [])->assertSessionHasErrors(['customer_name', 'review_text', 'source_name', 'source_url', 'is_active', 'submission_token']);
        foreach ([
            ['customer_name', str_repeat('a', 121)], ['review_text', str_repeat('a', 2001)], ['source_name', str_repeat('a', 81)],
            ['source_url', 'javascript:alert(1)'], ['source_url', 'ftp://example.com/review'], ['source_url', 'https://example.com/'.str_repeat('a', 2048)],
            ['rating', 0], ['rating', 6], ['rating', 2.5], ['reviewed_at', now()->addDay()->toDateString()], ['reviewed_at', 'not-a-date'],
            ['screenshot', UploadedFile::fake()->create('screenshot.svg', 5, 'image/svg+xml')],
            ['screenshot', UploadedFile::fake()->image('large.png')->size(2049)],
            ['screenshot', UploadedFile::fake()->image('tall.png', 1, 5001)],
        ] as [$field, $value]) {
            $this->post(route('admin.testimonials.store'), $this->payload([$field => $value]))->assertSessionHasErrors($field);
        }
        $this->assertDatabaseCount('testimonials', 0);
    }

    public function test_upload_preserves_original_bytes_and_zoom_uses_original_with_light_preview(): void
    {
        $file = UploadedFile::fake()->image('screenshot.png', 800, 1200);
        $original = file_get_contents($file->getPathname());
        $this->actingAs($this->admin())->post(route('admin.testimonials.store'), $this->payload(['screenshot' => $file, 'rating' => 5]))->assertSessionHasNoErrors();
        $testimonial = Testimonial::firstOrFail();
        $this->assertSame($original, Storage::disk('r2')->get($testimonial->screenshot_path));
        $this->get($testimonial->screenshot_original_url)->assertOk()->assertHeader('Content-Type', 'image/png');
        $this->get($testimonial->screenshot_preview_url)->assertOk()->assertHeader('Content-Type', 'image/webp');
        $this->get('/testimoni')->assertOk()->assertSee('data-original="'.e($testimonial->screenshot_original_url).'"', false)->assertSee($testimonial->screenshot_preview_url)->assertSee('loading="lazy"', false)->assertSee('method="dialog"', false);
        $this->assertSame([], Storage::disk('local')->allFiles('temp/original-images'));
        $this->get(route('admin.testimonials.index'))->assertOk();
    }

    public function test_admin_can_edit_hide_replace_remove_screenshot_and_delete(): void
    {
        $this->actingAs($this->admin());
        $this->post(route('admin.testimonials.store'), $this->payload(['screenshot' => UploadedFile::fake()->image('first.png')]))->assertSessionHasNoErrors();
        $testimonial = Testimonial::firstOrFail();
        $oldPath = $testimonial->screenshot_path;
        $this->get(route('admin.testimonials.edit', $testimonial))->assertOk()->assertSee('Hapus screenshot tersimpan');
        $data = $this->payload(['customer_name' => 'Hidden Reviewer', 'is_active' => 0]);
        unset($data['submission_token']);
        $this->put(route('admin.testimonials.update', $testimonial), $data)->assertSessionHasNoErrors();
        $this->assertSame($oldPath, $testimonial->fresh()->screenshot_path);
        $this->get('/testimoni')->assertOk()->assertDontSee('Hidden Reviewer');
        $data['is_active'] = 1;
        $data['screenshot'] = UploadedFile::fake()->image('replacement.png');
        $this->put(route('admin.testimonials.update', $testimonial), $data)->assertSessionHasNoErrors();
        Storage::disk('r2')->assertMissing($oldPath);
        $replacement = $testimonial->fresh()->screenshot_path;
        Storage::disk('r2')->assertExists($replacement);
        unset($data['screenshot']);
        $data['remove_screenshot'] = 1;
        $this->put(route('admin.testimonials.update', $testimonial), $data)->assertSessionHasNoErrors();
        $this->assertNull($testimonial->fresh()->screenshot_path);
        Storage::disk('r2')->assertMissing($replacement);
        $this->get('/testimoni')->assertOk()->assertSee('Hidden Reviewer');
        $this->delete(route('admin.testimonials.destroy', $testimonial))->assertRedirect(route('admin.testimonials.index'));
        $this->assertDatabaseCount('testimonials', 0);
    }

    public function test_failed_upload_keeps_old_image_and_text(): void
    {
        $testimonial = $this->record(['screenshot_path' => 'public/reviews/old.png']);
        Storage::disk('public')->put('reviews/old.png', 'old');
        $this->mock(OriginalImageStorage::class)->shouldReceive('upload')->once()->andThrow(new \RuntimeException('Storage unavailable'));
        $data = $this->payload(['customer_name' => 'Changed', 'screenshot' => UploadedFile::fake()->image('new.png')]);
        unset($data['submission_token']);
        $this->actingAs($this->admin())->put(route('admin.testimonials.update', $testimonial), $data)->assertSessionHasErrors('screenshot');
        $this->assertSame('Pelanggan Test', $testimonial->fresh()->customer_name);
        $this->assertSame('public/reviews/old.png', $testimonial->fresh()->screenshot_path);
        Storage::disk('public')->assertExists('reviews/old.png');
    }

    public function test_database_failure_cleans_new_image_and_keeps_old_image(): void
    {
        $testimonial = $this->record(['screenshot_path' => 'public/reviews/old.png']);
        Storage::disk('public')->put('reviews/old.png', 'old');
        Storage::disk('public')->put('reviews/new.png', 'new');
        $this->mock(OriginalImageStorage::class)->shouldReceive('upload')->once()->andReturn('public/reviews/new.png');
        Testimonial::updating(function () {
            throw new \RuntimeException('Database failed');
        });
        $this->actingAs($this->admin());
        $this->withoutExceptionHandling();
        $data = $this->payload(['screenshot' => UploadedFile::fake()->image('new.png')]);
        unset($data['submission_token']);
        try {
            $this->put(route('admin.testimonials.update', $testimonial), $data);
            $this->fail('Expected database failure.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Database failed', $exception->getMessage());
        }
        $this->assertSame('public/reviews/old.png', $testimonial->fresh()->screenshot_path);
        Storage::disk('public')->assertExists('reviews/old.png');
        Storage::disk('public')->assertMissing('reviews/new.png');
    }

    public function test_delete_also_cleans_screenshot_and_conflicting_replacement_is_rejected(): void
    {
        $testimonial = $this->record(['screenshot_path' => 'public/reviews/old.png']);
        Storage::disk('public')->put('reviews/old.png', 'old');
        $this->actingAs($this->admin());
        $data = $this->payload(['screenshot' => UploadedFile::fake()->image('new.png'), 'remove_screenshot' => 1]);
        unset($data['submission_token']);
        $this->put(route('admin.testimonials.update', $testimonial), $data)->assertSessionHasErrors('screenshot');
        Storage::disk('public')->assertExists('reviews/old.png');
        $this->delete(route('admin.testimonials.destroy', $testimonial))->assertRedirect(route('admin.testimonials.index'));
        Storage::disk('public')->assertMissing('reviews/old.png');
    }
}
