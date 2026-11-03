<?php

namespace Tests\Feature;

use App\Helpers\ImageUpload;
use App\Models\Partner;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PartnerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_manage_partners(): void
    {
        $this->get(route('admin.partners.index'))->assertRedirect(route('login'));
        $this->post(route('admin.partners.store'))->assertRedirect(route('login'));
        $partner = $this->partner();
        $this->put(route('admin.partners.update', $partner))->assertRedirect(route('login'));
        $this->delete(route('admin.partners.destroy', $partner))->assertRedirect(route('login'));
    }

    private function partner(array $attributes = []): Partner
    {
        return Partner::create(array_merge(['nama' => 'Partner Test', 'logo_path' => 'public/partners/test.png', 'urutan' => 0, 'is_active' => true], $attributes));
    }

    private function admin(): User
    {
        $id = \Illuminate\Support\Facades\DB::table('karyawan')->insertGetId([
            'nama_lengkap' => 'Partner Admin', 'nik' => 'PARTNER-ADMIN', 'jabatan' => 'Manager',
            'tanggal_masuk' => '2026-01-01', 'status' => 'aktif', 'nomor_telepon' => '081234567890',
            'created_at' => now(), 'updated_at' => now(),
        ]);

        return User::factory()->create(['id' => $id, 'role' => 'manager', 'status' => 'active']);
    }

    public function test_home_shows_demo_then_only_active_partners_in_order(): void
    {
        $this->get('/')->assertOk()->assertSee('partner fiktif');
        $this->partner(['nama' => 'Partner Kedua', 'urutan' => 2]);
        $this->partner(['nama' => 'Partner Pertama', 'urutan' => 1]);
        $hidden = $this->partner(['nama' => 'Partner Tersembunyi', 'is_active' => false]);
        $this->get('/')->assertOk()->assertSeeInOrder(['Partner Kedua', 'Partner Pertama'])->assertDontSee($hidden->nama)->assertDontSee('partner fiktif');
        Partner::query()->update(['is_active' => false]);
        $this->get('/')->assertOk()->assertDontSee('PARTNER KAMI')->assertDontSee('partner fiktif');
    }

    public function test_admin_can_upload_edit_replace_and_delete_partner(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('partners/first.webp', 'first');
        Storage::disk('public')->put('partners/second.webp', 'second');
        $this->mock(ImageUpload::class)->shouldReceive('upload')->twice()->andReturn(
            ['path' => 'public/partners/first.webp'], ['path' => 'public/partners/second.webp']
        );
        $this->actingAs($this->admin());
        $this->get(route('admin.partners.index'))->assertOk();
        $this->get(route('admin.partners.create'))->assertOk();
        $this->post(route('admin.partners.store'), ['submission_token' => (string) \Illuminate\Support\Str::uuid(), 'nama' => 'Partner Baru', 'logo' => UploadedFile::fake()->image('logo.png'), 'urutan' => 3, 'is_active' => 1])->assertSessionHasNoErrors()->assertRedirect(route('admin.partners.index'));
        $partner = Partner::firstOrFail();
        $this->get(route('admin.partners.edit', $partner))->assertOk();
        $this->put(route('admin.partners.update', $partner), ['nama' => 'Partner Edit', 'urutan' => 1, 'is_active' => 0])->assertSessionHasNoErrors();
        $this->assertSame('public/partners/first.webp', $partner->fresh()->logo_path);
        Storage::disk('public')->assertExists('partners/first.webp');
        $this->put(route('admin.partners.update', $partner), ['nama' => 'Partner Edit', 'logo' => UploadedFile::fake()->image('new.png'), 'urutan' => 1, 'is_active' => 1])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing('partners/first.webp');
        $this->assertSame('public/partners/second.webp', $partner->fresh()->logo_path);
        $this->delete(route('admin.partners.destroy', $partner))->assertRedirect(route('admin.partners.index'));
        $this->assertDatabaseMissing('partners', ['id' => $partner->id]);
        Storage::disk('public')->assertMissing('partners/second.webp');
    }

    public function test_invalid_uploads_and_missing_names_are_rejected(): void
    {
        $this->actingAs($this->admin());
        $this->post(route('admin.partners.store'), ['submission_token' => (string) \Illuminate\Support\Str::uuid(), 'urutan' => -1, 'is_active' => 1])->assertSessionHasErrors(['nama', 'logo']);
        foreach ([UploadedFile::fake()->create('logo.svg', 10, 'image/svg+xml'), UploadedFile::fake()->image('large.png')->size(2049)] as $logo) {
            $this->post(route('admin.partners.store'), ['submission_token' => (string) \Illuminate\Support\Str::uuid(), 'nama' => 'Invalid', 'logo' => $logo, 'urutan' => 0, 'is_active' => 1])->assertSessionHasErrors('logo');
        }
        $this->assertDatabaseCount('partners', 0);
    }

    public function test_upload_failure_does_not_replace_existing_partner(): void
    {
        $partner = $this->partner();
        $this->mock(ImageUpload::class)->shouldReceive('upload')->once()->andThrow(new \RuntimeException('Storage unavailable'));
        $this->actingAs($this->admin())->put(route('admin.partners.update', $partner), ['nama' => 'Changed', 'logo' => UploadedFile::fake()->image('logo.png'), 'urutan' => 0, 'is_active' => 1])->assertSessionHasErrors('logo');
        $this->assertSame('Partner Test', $partner->fresh()->nama);
        $this->assertSame('public/partners/test.png', $partner->fresh()->logo_path);
    }

    public function test_repeated_submission_does_not_create_a_second_partner(): void
    {
        $token = (string) \Illuminate\Support\Str::uuid();
        $this->partner(['submission_token' => $token]);
        $this->mock(ImageUpload::class)->shouldNotReceive('upload');
        $this->actingAs($this->admin())->post(route('admin.partners.store'), [
            'submission_token' => $token, 'nama' => 'Duplicate',
            'logo' => UploadedFile::fake()->image('logo.png'), 'is_active' => 1,
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.partners.index'));
        $this->assertDatabaseCount('partners', 1);
        $this->assertSame('Partner Test', Partner::firstOrFail()->nama);
    }
}
