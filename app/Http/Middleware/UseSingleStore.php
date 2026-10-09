<?php

namespace App\Http\Middleware;

use App\Models\Branch;
use Closure;
use Illuminate\Http\Request;

class UseSingleStore
{
    public function handle(Request $request, Closure $next)
    {
        $store = Branch::first();
        // Keep relational IDs for transactions, but never trust a client branch selection.
        $request->merge(['cabang' => $store?->id, 'perusahaan_cabang_id' => $store?->id]);
        if ($request->isMethod('GET')) {
            $request->query->set('cabang', $store?->id);
        }
        return $next($request);
    }
}
