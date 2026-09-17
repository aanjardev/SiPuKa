<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutPendingUsers
{
    /**
     * Log out users whose status is pending (misalnya setelah email diganti).
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('warning', 'Akun Anda belum aktif atau sudah dinonaktifkan. Silakan hubungi manager.');
        }

        return $next($request);
    }
}
