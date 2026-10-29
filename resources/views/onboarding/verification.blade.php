@extends('layouts.auth')

@section('title', 'Verifikasi Penjual - Onboarding Lokalku')

@section('header')
    <header class="fixed top-0 left-0 right-0 h-16 bg-surface-card/90 backdrop-blur-md shadow-[0_1px_8px_rgba(25,25,25,0.04)] z-50">
        <div class="h-16 max-w-7xl mx-auto px-margin flex items-center justify-between">
            <div class="flex items-center gap-space-md">
                <a aria-label="Lokalku Home" class="flex items-center gap-space-xs select-none" href="{{ route('home') }}">
                    <span class="font-headline-md text-headline-md tracking-tight text-primary">Lokal</span>
                    <span class="font-headline-md text-headline-md tracking-tight text-accent-yellow">k</span>
                    <span class="font-headline-md text-headline-md tracking-tight text-red-graphic">u</span>
                </a>
                <span class="hidden sm:inline-block w-px h-4 bg-outline-variant"></span>
                <span class="hidden sm:inline-block font-caption text-caption text-ink-muted uppercase tracking-wider">Onboarding UMKM</span>
            </div>
            <div class="flex items-center gap-space-md">
                <div class="flex items-center gap-space-xs px-space-md py-space-xs rounded-full bg-blue-subtle text-primary border border-outline-variant/30">
                    <span class="material-symbols-outlined text-[16px]">flag</span>
                    <span class="font-caption text-caption font-semibold">Langkah 1 dari 1</span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-space-xs font-caption text-caption text-ink-muted hover:text-on-surface transition-colors">
                        <span class="material-symbols-outlined text-[16px]">logout</span>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>
@endsection

@section('content')
<main class="w-full pt-16 flex-1 flex flex-col items-center justify-center p-margin">
    <div class="flex flex-col w-full items-center justify-center py-space-xl">
        <div class="w-full max-w-[480px] bg-surface-card rounded-xl p-8 shadow-[0_2px_12px_rgba(25,25,25,0.06)] flex flex-col gap-space-lg relative overflow-hidden transition-all duration-300">
            <!-- Card Header / Icon -->
            <div class="flex flex-col items-center text-center gap-space-sm">
                <div class="w-14 h-14 rounded-full @error('activation_code') bg-error-container text-secondary @else bg-blue-subtle text-primary @enderror flex items-center justify-center shadow-inner transition-colors" id="icon-badge">
                    <span class="material-symbols-outlined text-[28px]" id="badge-icon-symbol" style="font-variation-settings: 'FILL' 1;">@error('activation_code') gpp_bad @else verified_user @enderror</span>
                </div>
                <div class="flex flex-col gap-space-xs mt-1">
                    <h2 class="font-headline-md text-headline-md text-on-surface font-bold" id="card-headline">Verifikasi Akun Penjual</h2>
                    <p class="font-body-md text-body-md text-ink-muted max-w-[380px] leading-relaxed">
                        Masukkan kode aktivasi toko yang diberikan pemilik toko atau administrator untuk membuka modul kasir POS dan inventaris.
                    </p>
                </div>
            </div>

            <!-- Informative Context Note -->
            <div class="bg-surface-container-low rounded-lg p-space-md flex gap-space-sm items-start transition-all" id="hint-box">
                <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">info</span>
                <p class="font-body-md text-body-md text-on-surface-variant text-[13px] leading-snug">
                    Format kode aktivasi berformat 12 digit (<span class="font-code-mono font-medium text-on-surface">LKL-8821-4902</span>) yang tercantum pada surat kemitraan atau mesin POS fisik Lokalku.
                </p>
            </div>

            <!-- Form Section -->
            <form class="flex flex-col gap-space-md" id="verify-form" method="POST" action="{{ route('onboarding.verification') }}">
                @csrf
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between">
                        <label class="font-body-md text-body-md font-medium text-on-surface flex items-center gap-space-xs" for="activation-code">
                            Kode Aktivasi Toko
                        </label>
                        <span class="px-2 py-0.5 rounded-full bg-error-container text-secondary font-caption text-[11px] font-semibold tracking-wide">
                            Wajib
                        </span>
                    </div>
                    <div class="relative flex items-center">
                        <input autocomplete="off" class="w-full h-12 px-space-md font-code-mono text-code-mono font-semibold text-on-surface uppercase tracking-wider bg-surface-container-lowest rounded-lg shadow-sm focus:outline-none transition-all placeholder:text-outline-variant @error('activation_code') bg-error-container/20 !shadow-[inset_0_0_0_2px_#ba1a1a] @enderror" id="activation-code" maxlength="13" name="activation_code" placeholder="LKL-XXXX-XXXX" spellcheck="false" type="text" value="{{ old('activation_code') }}" required>
                        <div class="absolute right-3 flex items-center pointer-events-none text-ink-muted" id="input-trailing-icon">
                            @error('activation_code')
                                <span class="material-symbols-outlined text-[20px] text-secondary">cancel</span>
                            @else
                                <span class="material-symbols-outlined text-[20px] text-primary">check</span>
                            @enderror
                        </div>
                    </div>
                    @error('activation_code')
                        <div class="flex items-center gap-space-xs mt-1 text-secondary" id="error-feedback">
                            <span class="material-symbols-outlined text-[16px]">error</span>
                            <span class="font-caption text-caption font-medium">{{ $message }}</span>
                        </div>
                    @else
                        <div class="flex items-center gap-space-xs mt-1 text-ink-muted" id="standard-feedback">
                            <span class="material-symbols-outlined text-[14px]">lock</span>
                            <span class="font-caption text-caption">Satu kode otorisasi per satu perangkat operasional kasir.</span>
                        </div>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-space-sm pt-2">
                    <button class="w-full h-11 rounded-full hover:bg-blue-hover active:scale-[0.99] text-on-primary font-headline-sm text-headline-sm font-bold flex items-center justify-center gap-space-xs transition-all shadow-sm @error('activation_code') bg-secondary @else bg-primary @enderror" id="btn-submit" type="submit">
                        <span>@error('activation_code') Ulangi Verifikasi @else Verifikasi &amp; Buka Dasbor Penjual @enderror</span>
                        <span class="material-symbols-outlined text-[18px]">@error('activation_code') refresh @else arrow_forward @enderror</span>
                    </button>
                    <button class="w-full h-10 rounded-full bg-transparent hover:bg-surface-container text-primary font-body-md text-body-md font-semibold flex items-center justify-center transition-colors text-center" type="submit" form="skip-form">
                        Lanjutkan sebagai Pembeli
                    </button>
                    <p class="font-caption text-caption text-ink-muted text-center -mt-1">
                        Bisa diverifikasi nanti kapan saja lewat menu Pengaturan Toko
                    </p>
                </div>
            </form>

            <form id="skip-form" method="POST" action="{{ route('onboarding.verification.skip') }}" class="hidden">
                @csrf
            </form>

            <!-- Divider -->
            <div class="h-px w-full bg-surface-container"></div>

            <!-- Security & Support Sub-Panel -->
            <div class="flex flex-col items-center gap-space-sm text-center">
                <div class="flex items-center gap-space-xs text-ink-muted font-caption text-caption">
                    <span class="material-symbols-outlined text-[15px] text-tertiary">shield</span>
                    <span>Enkripsi Verifikasi 256-bit • Kemitraan UMKM Terdaftar</span>
                </div>
                <a class="font-caption text-caption text-primary hover:text-blue-hover font-medium underline underline-offset-2 transition-colors" href="#">
                    Belum punya kode aktivasi? Hubungi Pemilik Toko / CS Lokalku
                </a>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
    // Auto-format activation code with hyphens
    document.getElementById('activation-code').addEventListener('input', function (e) {
        let val = e.target.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        if (val.startsWith('LKL')) {
            let rest = val.substring(3);
            if (rest.length > 4) {
                val = 'LKL-' + rest.substring(0, 4) + '-' + rest.substring(4, 8);
            } else if (rest.length > 0) {
                val = 'LKL-' + rest;
            }
        }
        e.target.value = val;
    });
</script>
@endpush
@endsection
