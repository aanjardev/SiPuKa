<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckExecutionTime
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Set time limit yang lebih aman (30 detik kurang dari default 60 detik)
        $safeTimeLimit = 30;

        $currentTime = microtime(true);
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : $currentTime;
        $elapsed = $currentTime - $startTime;

        if ($elapsed > $safeTimeLimit) {
            return response()->view('errors.timeout', [], 503);
        }
        
        // Set time limit untuk request ini
        set_time_limit($safeTimeLimit);
        
        $response = $next($request);
        
        return $response;
    }
}
