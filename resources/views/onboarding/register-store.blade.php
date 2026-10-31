@extends('layouts.auth')

@section('title', 'Daftarkan Toko - Onboarding Lokalku')

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
                    <span class="font-caption text-caption font-semibold">Langkah 1 dari 2</span>
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
                <div class="w-14 h-14 rounded-full bg-blue-subtle text-primary flex items-center justify-center shadow-inner transition-colors">
                    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">storefront</span>
                </div>
                <div class="flex flex-col gap-space-xs mt-1">
                    <h2 class="font-headline-md text-headline-md text-on-surface font-bold">Daftarkan Toko Anda</h2>
                    <p class="font-body-md text-body-md text-ink-muted max-w-[380px] leading-relaxed">
                        Isi nama toko Anda. Sistem akan menerbitkan kode aktivasi unik yang dipakai untuk membuka Dasbor Penjual.
                    </p>
                </div>
            </div>

            <!-- Form Section -->
            <form class="flex flex-col gap-space-md" method="POST" action="{{ route('onboarding.store') }}">
                @csrf
                <div class="flex flex-col gap-1.5">
                    <div class="flex items-center justify-between">
                        <label class="font-body-md text-body-md font-medium text-on-surface flex items-center gap-space-xs" for="store-name">
                            Nama Toko
                        </label>
                        <span class="px-2 py-0.5 rounded-full bg-error-container text-secondary font-caption text-[11px] font-semibold tracking-wide">
                            Wajib
                        </span>
                    </div>
                    <input autocomplete="off" class="w-full h-12 px-space-md font-body-md text-body-md font-semibold text-on-surface bg-surface-container-lowest rounded-lg shadow-sm focus:outline-none transition-all placeholder:text-outline-variant @error('name') !shadow-[inset_0_0_0_2px_#ba1a1a] @enderror" id="store-name" name="name" placeholder="Contoh: Kopi Gayo Aceh" type="text" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="flex items-center gap-space-xs mt-1 text-secondary">
                            <span class="material-symbols-outlined text-[16px]">error</span>
                            <span class="font-caption text-caption font-medium">{{ $message }}</span>
                        </div>
                    @else
                        <div class="flex items-center gap-space-xs mt-1 text-ink-muted">
                            <span class="material-symbols-outlined text-[14px]">lock</span>
                            <span class="font-caption text-caption">Nama toko tampil di etalase dan struk kasir.</span>
                        </div>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col gap-space-sm pt-2">
                    <button class="w-full h-11 rounded-full hover:bg-blue-hover active:scale-[0.99] text-on-primary font-headline-sm text-headline-sm font-bold flex items-center justify-center gap-space-xs transition-all shadow-sm bg-primary" type="submit">
                        <span>Daftarkan &amp; Terima Kode</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                    <button class="w-full h-10 rounded-full bg-transparent hover:bg-surface-container text-primary font-body-md text-body-md font-semibold flex items-center justify-center transition-colors text-center" type="submit" form="skip-form">
                        Lanjutkan sebagai Pembeli
                    </button>
                </div>
            </form>

            <form id="skip-form" method="POST" action="{{ route('onboarding.verification.skip') }}" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</main>
@endsection
