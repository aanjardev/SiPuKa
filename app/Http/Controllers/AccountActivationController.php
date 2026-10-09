<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountActivationController extends Controller
{
    public function showActivationForm()
    {
        return view('admin.activation');
    }

    public function verifyEmail(Request $request)
    {
        $request->session()->forget(['activation_email', 'activation_token', 'verification_code']);
        $request->validate(['email' => 'required|email']);
        $user = User::where('email', $request->email)->first();

        if (!$user || $user->status !== 'pending') {
            return back()->withInput()->withErrors([
                'email' => 'Aktivasi hanya tersedia untuk email terdaftar dengan status pending.'
            ]);
        }

        $token = Str::random(64);
        $user->update([
            'activation_token' => $token,
            'token_expiry' => now()->addMinutes(30),
        ]);
        $request->session()->put(['activation_email' => $user->email, 'activation_token' => $token]);

        return redirect()->route('activation.setup-password', ['token' => $token]);
    }

    private function pendingUser(Request $request, string $token): ?User
    {
        $sessionToken = $request->session()->get('activation_token');
        if (!is_string($sessionToken) || !hash_equals($sessionToken, $token)) {
            return null;
        }

        return User::where('email', $request->session()->get('activation_email'))
            ->where('activation_token', $token)
            ->where('status', 'pending')
            ->where('token_expiry', '>', now())
            ->first();
    }

    public function showPasswordSetupForm(Request $request, string $token)
    {
        $user = $this->pendingUser($request, $token);
        if (!$user) {
            return redirect()->route('activation.form')->withErrors([
                'email' => 'Sesi aktivasi tidak valid atau kedaluwarsa. Silakan masukkan email kembali.'
            ]);
        }
        return view('admin.setup-password', compact('user'));
    }

    public function setupPassword(Request $request, string $token)
    {
        $user = $this->pendingUser($request, $token);
        if (!$user) {
            return redirect()->route('activation.form')->withErrors([
                'email' => 'Sesi aktivasi tidak valid atau kedaluwarsa. Silakan masukkan email kembali.'
            ]);
        }
        $request->validate(['password' => 'required|string|min:6|confirmed']);
        $updated = User::where('id', $user->id)
            ->where('email', $request->session()->get('activation_email'))
            ->where('status', 'pending')
            ->where('activation_token', $token)
            ->where('token_expiry', '>', now())
            ->update([
                'password' => Hash::make($request->password),
                'status' => 'active',
                'activation_token' => null,
                'token_expiry' => null,
                'email_verified_at' => null,
            ]);
        if (!$updated) {
            return redirect()->route('activation.form')->withErrors(['email' => 'Silakan mulai ulang aktivasi.']);
        }
        $request->session()->forget(['activation_email', 'activation_token', 'verification_code']);
        auth()->login($user->fresh());
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Akun berhasil diaktifkan! Selamat datang di sistem.');
    }
}
