<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSeller
{
    /**
     * Hanya penjual dengan toko terverifikasi yang boleh masuk portal penjual.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        abort_unless($user && $user->role === 'seller', 403);

        if (! $user->store || ! $user->store->verified_at) {
            return redirect()->route('onboarding.verification');
        }

        return $next($request);
    }
}
