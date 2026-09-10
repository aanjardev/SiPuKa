<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Pastikan untuk meng-import Auth
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles  Role yang diizinkan (misal: 'manajer', 'operasional')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {

        if (!Auth::check()) {

            return redirect('/login'); // Sesuaikan dengan route login Anda
        }


        $userRole = Auth::user()->role;

        if ($userRole == 'manager') {
            return $next($request);
        }

        if (in_array($userRole, $roles)) {
            return $next($request);
        }



        return back()->with('error', 'Anda tidak memiliki hak akses untuk halaman ini.');
    }
}
