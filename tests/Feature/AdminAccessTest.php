<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_login_form(): void
    {
        $this->get('/login')->assertOk()->assertViewIs('admin.login');
    }

    public function test_authenticated_manager_is_redirected_from_login_to_dashboard(): void
    {
        $this->actingAs($this->createUser('manager'))
            ->get('/login')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_authenticated_operational_user_is_redirected_from_login_to_dashboard(): void
    {
        $this->actingAs($this->createUser('operasional'))
            ->get('/login')
            ->assertRedirect(route('admin.dashboard'));
    }

    public function test_successful_login_defaults_to_dashboard(): void
    {
        $user = $this->createUser('manager');

        $this->post('/login', ['email' => $user->email, 'password' => 'testing-password'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_successful_login_preserves_intended_admin_destination(): void
    {
        $user = $this->createUser('manager');
        $this->get('/admin/employees')->assertRedirect(route('login'));

        $this->post('/login', ['email' => $user->email, 'password' => 'testing-password'])
            ->assertRedirect(url('/admin/employees'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_guest_is_redirected_to_login_from_admin(): void
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_operational_user_cannot_access_manager_routes(): void
    {
        $user = $this->createUser('operasional');

        $this->actingAs($user)
            ->get('/admin/employees')
            ->assertForbidden();
    }

    public function test_manager_can_access_manager_routes(): void
    {
        $user = $this->createUser('manager');

        $this->actingAs($user)
            ->get('/admin/employees')
            ->assertOk();
    }

    public function test_catalog_settings_rejects_wrong_social_media_domain(): void
    {
        $user = $this->createUser('manager');

        $this->actingAs($user)
            ->post('/admin/catalog-settings', [
                'site_name' => 'Katalog Test',
                'social_instagram' => 'https://example.com/bukan-instagram',
            ])
            ->assertSessionHasErrors('social_instagram');
    }

    private function createUser(string $role): User
    {
        $employeeId = DB::table('karyawan')->insertGetId([
            'nama_lengkap' => ucfirst($role) . ' Test',
            'nik' => $role === 'manager' ? 'TEST-MANAGER' : 'TEST-OPERASIONAL',
            'jabatan' => $role === 'manager' ? 'Manager' : 'Staff Administrasi',
            'tanggal_masuk' => '2026-01-01',
            'status' => 'aktif',
            'nomor_telepon' => '081234567890',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return User::create([
            'id' => $employeeId,
            'name' => ucfirst($role) . ' Test',
            'email' => $role . '@example.test',
            'password' => Hash::make('testing-password'),
            'role' => $role,
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }
}
