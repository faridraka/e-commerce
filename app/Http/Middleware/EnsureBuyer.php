<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBuyer
{
    /**
     * Hanya pembeli yang boleh masuk area pembeli.
     * Penjual dialihkan ke dasbor portal penjual.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'seller') {
            return redirect()->route('seller.dashboard');
        }

        abort_unless($user && $user->role === 'buyer', 403);

        return $next($request);
    }
}
