<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {

        if (!$request->email || !$request->password) {
            return back()->withErrors([
                'email' => 'Email dan password harus diisi.',
            ])->onlyInput('email');
        }

        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user) {

            if ($user->status === 'pending') {
                return back()->withErrors([
                    'email' => 'Akun Anda belum diaktivasi. Silakan <a href="' . route('activation.form') . '">aktivasi</a> akun terlebih dahulu.',
                ])->onlyInput('email');
            }

            if ($user->employee && $user->employee->status === 'non-aktif') {

                $user->update(['status' => 'inactive']);
                
                return back()->withErrors([
                    'email' => 'Anda sudah tidak memiliki akses untuk login. Hubungi manager untuk informasi lebih lanjut.',
                ])->onlyInput('email');
            }

            if ($user->status === 'inactive') {
                return back()->withErrors([
                    'email' => 'Anda sudah tidak memiliki akses untuk login. Hubungi manager untuk informasi lebih lanjut.',
                ])->onlyInput('email');
            }
        }

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            return back()->withErrors([
                'email' => 'Format email tidak valid.',
            ])->onlyInput('email');
        }

        if (!$user) {
            return back()->withErrors([
                'email' => 'Email atau Password salah.',
            ])->onlyInput('email');
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau Password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
