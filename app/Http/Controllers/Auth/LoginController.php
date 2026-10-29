<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Menampilkan halaman Masuk ke Akun.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Memproses percobaan login lalu mengarahkan sesuai status onboarding.
     */
    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi salah. Silakan periksa kembali.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended($this->redirectPath());
    }

    /**
     * Mengakhiri sesi pengguna.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }

    /**
     * Tujuan setelah login tergantung kelengkapan onboarding.
     */
    private function redirectPath(): string
    {
        $user = Auth::user();

        if (! $user->hasCompletedOnboarding()) {
            return $user->role === 'seller'
                ? route('onboarding.verification')
                : route('onboarding.role');
        }

        return route('home');
    }
}
