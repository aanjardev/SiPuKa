<?php
namespace Tests\Feature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
class AccountActivationTest extends TestCase
{
    use RefreshDatabase;

    public function test_pending_account_activates_without_email(): void
    {
        Mail::fake();
        $user = $this->createUser('manager');
        $user->update(['status' => 'pending', 'token_expiry' => now()->subDay()]);
        $this->post('/activation/verify-email', ['email' => $user->email]);
        $token = $user->fresh()->activation_token;
        $this->get('/activation/setup-password/'.$token)->assertOk();
        $this->post('/activation/setup-password/'.$token, [
            'password' => 'new-password', 'password_confirmation' => 'new-password',
        ])->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);
        $user->refresh();
        $this->assertSame('active', $user->status);
        $this->assertNull($user->email_verified_at);
        $this->assertNull($user->activation_token);
        $this->assertTrue(Hash::check('new-password', $user->password));
        Mail::assertNothingSent();
    }

    public function test_only_pending_registered_accounts_can_activate(): void
    {
        $user = $this->createUser('manager');
        foreach (['active', 'inactive'] as $status) {
            $user->update(['status' => $status]);
            $this->post('/activation/verify-email', ['email' => $user->email])->assertSessionHasErrors('email');
        }
        $this->post('/activation/verify-email', ['email' => 'missing@example.test'])->assertSessionHasErrors('email');
    }

    public function test_password_setup_requires_matching_session_and_unexpired_token(): void
    {
        $user = $this->createUser('manager');
        $user->update(['status' => 'pending', 'activation_token' => str_repeat('a', 64), 'token_expiry' => now()->addMinutes(30)]);
        $url = '/activation/setup-password/'.$user->activation_token;
        $this->post($url, ['password' => 'new-password', 'password_confirmation' => 'new-password'])->assertRedirect('/activation');
        $this->assertSame('pending', $user->fresh()->status);
        $this->post('/activation/verify-email', ['email' => $user->email]);
        $url = '/activation/setup-password/'.$user->fresh()->activation_token;
        $this->post($url, ['password' => 'new-password', 'password_confirmation' => 'wrong'])->assertSessionHasErrors('password');
        $user->update(['token_expiry' => now()->subMinute()]);
        $this->get($url)->assertRedirect('/activation');
        $this->post($url, ['password' => 'new-password', 'password_confirmation' => 'new-password'])->assertRedirect('/activation');
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
