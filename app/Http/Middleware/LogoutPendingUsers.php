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
        if (Auth::check() && Auth::user()->status === 'pending') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('warning', 'Email Anda telah diganti. Mohon cek email baru untuk aktivasi ulang, lalu login kembali.');
        }

        return $next($request);
    }
}
