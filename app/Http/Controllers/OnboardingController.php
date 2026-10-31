<?php

namespace App\Http\Controllers;

use App\Models\Store;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
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
     * Halaman pendaftaran toko: isi nama toko, sistem menerbitkan kode aktivasi.
     */
    public function showVerification(): RedirectResponse|View
    {
        $store = auth()->user()->store;

        // Sudah punya toko pending/menunggu verifikasi: tampilkan halaman kode.
        if ($store) {
            return view('onboarding.verification', ['store' => $store]);
        }

        return view('onboarding.register-store');
    }

    /**
     * Daftarkan toko baru untuk user ini, terbitkan kode aktivasi unik.
     */
    public function registerStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:100'],
        ], [
            'name.required' => 'Nama toko wajib diisi.',
            'name.min' => 'Nama toko minimal 3 karakter.',
        ]);

        $user = $request->user();

        if ($user->store) {
            return redirect()->route('onboarding.verification');
        }

        do {
            $code = 'LKL-' . random_int(1000, 9999) . '-' . random_int(1000, 9999);
        } while (Store::where('activation_code', $code)->exists());

        $slugBase = Str::slug($validated['name']) ?: 'toko';
        $slug = $slugBase;
        $counter = 2;
        while (Store::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $counter++;
        }

        Store::create([
            'user_id' => $user->id,
            'name' => trim($validated['name']),
            'slug' => $slug,
            'activation_code' => $code,
        ]);

        return redirect()->route('onboarding.verification')
            ->with('status', "Toko {$validated['name']} didaftarkan. Masukkan kode aktivasi {$code} untuk membuka Dasbor Penjual.");
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

        $store = $request->user()->store;

        if (! $store || $store->activation_code !== strtoupper($validated['activation_code'])) {
            return back()->withErrors([
                'activation_code' => 'Kode tidak valid. Coba lagi atau periksa kembali format kode aktivasi toko Anda.',
            ])->onlyInput('activation_code');
        }

        DB::transaction(function () use ($store, $request) {
            $store->claimBy($request->user());
            $request->user()->forceFill(['onboarding_completed_at' => now()])->save();
        });

        return redirect()->route('seller.dashboard')->with('status', 'Verifikasi berhasil! Selamat datang di Dasbor Penjual Lokalku.');
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
