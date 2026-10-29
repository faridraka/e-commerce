<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOnboardingCompleted
{
    /**
     * Pastikan pengguna telah menyelesaikan onboarding sebelum masuk ke aplikasi utama.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Tamu tetap bisa melihat beranda publik.
        if (! $user) {
            return $next($request);
        }

        if (! $user->hasCompletedOnboarding()) {
            return redirect()->route(
                $user->role === 'seller' ? 'onboarding.verification' : 'onboarding.role'
            );
        }

        return $next($request);
    }
}
