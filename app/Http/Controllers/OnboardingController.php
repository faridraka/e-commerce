<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Menampilkan halaman pemilihan peran akun.
     */
    public function showRole(): RedirectResponse|View
    {
        if (auth()->user()->hasCompletedOnboarding()) {
            return redirect()->route('home');
        }

        return view('onboarding.role');
    }

    /**
     * Menyimpan peran yang dipilih pengguna.
     */
    public function storeRole(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'in:' . User::ROLE_BUYER . ',' . User::ROLE_SELLER],
        ]);

        $user = $request->user();
        $user->forceFill(['role' => $validated['role']])->save();

        if ($validated['role'] === User::ROLE_SELLER) {
            return redirect()->route('onboarding.verification');
        }

        $user->forceFill(['onboarding_completed_at' => now()])->save();

        return redirect()->route('home');
    }

    /**
     * Menampilkan halaman verifikasi penjual (kode aktivasi toko).
     */
    public function showVerification(): View
    {
        return view('onboarding.verification');
    }

    /**
     * Verifikasi kode aktivasi toko, lalu klaim toko untuk user ini.
     */
    public function verify(Request $request): RedirectResponse
    {
        // Normalisasi kode agar cocok dengan validasi (LKL-XXXX-XXXX).
        $request->merge([
            'activation_code' => strtoupper(trim((string) $request->input('activation_code'))),
        ]);

        $validated = $request->validate([
            'activation_code' => [
                'required',
                'string',
                'regex:/^LKL-\d{4}-\d{4}$/',
            ],
        ], [
            'activation_code.required' => 'Kode aktivasi toko wajib diisi.',
            'activation_code.regex' => 'Kode tidak valid. Coba lagi atau periksa kembali format kode aktivasi toko Anda.',
        ]);

        $store = Store::where('activation_code', strtoupper($validated['activation_code']))->first();

        if (! $store) {
            return back()->withErrors([
                'activation_code' => 'Kode tidak valid. Coba lagi atau periksa kembali format kode aktivasi toko Anda.',
            ])->onlyInput('activation_code');
        }

        if ($store->user_id && $store->user_id !== $request->user()->id) {
            return back()->withErrors([
                'activation_code' => 'Kode aktivasi ini sudah digunakan toko lain. Hubungi CS Lokalku jika Anda merasa ini keliru.',
            ])->onlyInput('activation_code');
        }

        DB::transaction(function () use ($store, $request) {
            $store->claimBy($request->user());
            $request->user()->forceFill(['onboarding_completed_at' => now()])->save();
        });

        return redirect()->route('home')->with('status', 'Verifikasi berhasil! Selamat datang di Dasbor Penjual Lokalku.');
    }

    /**
     * Lewati verifikasi penjual dan lanjutkan sebagai pembeli.
     */
    public function skip(Request $request): RedirectResponse
    {
        $user = $request->user();

        DB::transaction(function () use ($user) {
            $user->forceFill([
                'role' => User::ROLE_BUYER,
                'onboarding_completed_at' => now(),
            ])->save();
        });

        return redirect()->route('home');
    }
}
