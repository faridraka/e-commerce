<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectSellerFromStorefront
{
    /**
     * Penjual yang sudah login tidak boleh membuka etalase publik
     * (beranda, katalog, detail produk). Alihkan ke dasbor penjual.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'seller' && $user->hasCompletedOnboarding()) {
            return redirect()->route('seller.dashboard');
        }

        return $next($request);
    }
}
