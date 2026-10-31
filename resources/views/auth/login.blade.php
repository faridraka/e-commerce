@extends('layouts.auth')

@section('title', 'Masuk ke Akun - Lokalku')

@section('header')
    <header class="w-full bg-surface-container-lowest border-b border-border-default">
        <div class="max-w-7xl mx-auto px-margin-desktop h-16 flex items-center justify-between">
            <a class="flex items-center gap-space-sm group" href="{{ route('home') }}">
                <div class="w-8 h-8 rounded-lg bg-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-primary text-[20px]">storefront</span>
                </div>
                <span class="font-headline-md text-headline-md tracking-tight text-primary font-bold">Lokalku</span>
            </a>
            <a class="flex items-center gap-space-xs font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('home') }}">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </header>
@endsection

@section('content')
<main class="flex-1 w-full max-w-7xl mx-auto px-margin-desktop py-space-xl flex flex-col justify-center items-center">
    <div class="flex flex-col w-full">
        <div class="w-full max-w-7xl mx-auto py-4 sm:py-6">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-stretch">
                <!-- Left Column: Brand & Benefits Panel (5 cols) -->
                <div class="lg:col-span-5 bg-primary-container text-on-primary rounded-xl p-6 sm:p-8 flex flex-col justify-between relative overflow-hidden shadow-sm">
                    <div class="absolute -right-16 -top-16 w-56 h-56 rounded-full bg-white/5 blur-2xl pointer-events-none"></div>
                    <div class="absolute -left-12 -bottom-12 w-48 h-48 rounded-full bg-black/10 blur-xl pointer-events-none"></div>
                    <div class="relative z-10">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-white backdrop-blur-sm font-caption text-caption mb-5">
                            <span class="material-symbols-outlined text-[14px]">verified</span>
                            <span>Sentra Pasar Digital UMKM</span>
                        </div>
                        <h1 class="font-headline-lg text-headline-lg tracking-tight text-white mb-3">
                            Belanja Mudah, Berdayakan UMKM Lokal
                        </h1>
                        <p class="font-body-md text-body-md text-blue-subtle/90 mb-8 leading-relaxed">
                            Akses keranjang belanja, pantau status pengiriman pesanan, dan nikmati kemudahan transaksi manual terverifikasi.
                        </p>
                        <div class="space-y-5">
                            <div class="flex items-start gap-3.5">
                                <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center shrink-0 mt-0.5 text-white">
                                    <span class="material-symbols-outlined text-[20px]">qr_code_scanner</span>
                                </div>
                                <div>
                                    <h4 class="font-headline-sm text-headline-sm text-white font-semibold">Konfirmasi Bayar Manual</h4>
                                    <p class="font-body-md text-body-md text-blue-subtle/80 mt-0.5">Tandai pembayaran selesai, penjual langsung memproses pesanan.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3.5">
                                <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center shrink-0 mt-0.5 text-white">
                                    <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                                </div>
                                <div>
                                    <h4 class="font-headline-sm text-headline-sm text-white font-semibold">Lacak Pesanan Real-Time</h4>
                                    <p class="font-body-md text-body-md text-blue-subtle/80 mt-0.5">Pantau kurir dan pengiriman produk langsung dari pengrajin.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3.5">
                                <div class="w-9 h-9 rounded-lg bg-white/10 flex items-center justify-center shrink-0 mt-0.5 text-white">
                                    <span class="material-symbols-outlined text-[20px]">favorite_border</span>
                                </div>
                                <div>
                                    <h4 class="font-headline-sm text-headline-sm text-white font-semibold">Riwayat &amp; Favorit Tersimpan</h4>
                                    <p class="font-body-md text-body-md text-blue-subtle/80 mt-0.5">Simpan produk favorit dan riwayat belanja UMKM Anda.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative z-10 pt-8 mt-8 border-t border-white/15">
                        <div class="grid grid-cols-2 gap-3">
                            <div class="p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                                <p class="font-headline-md text-headline-md font-bold text-white leading-none">1.200+</p>
                                <p class="font-caption text-caption text-blue-subtle/80 mt-1">Produk Terkurasi</p>
                            </div>
                            <div class="p-3 bg-white/10 rounded-lg backdrop-blur-sm">
                                <p class="font-headline-md text-headline-md font-bold text-white leading-none">250+</p>
                                <p class="font-caption text-caption text-blue-subtle/80 mt-1">UMKM Terverifikasi</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Login Form Container (7 cols) -->
                <div class="lg:col-span-7 flex items-center justify-center">
                    <div class="w-full max-w-[440px] bg-surface-card rounded-xl border border-border-default p-6 sm:p-8 shadow-sm">
                        @if (session('status'))
                            <div class="mb-5 bg-blue-subtle rounded-lg p-3 flex items-start gap-2.5 border border-primary-container/20">
                                <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">check_circle</span>
                                <p class="font-caption text-caption text-primary leading-snug font-medium">
                                    {{ session('status') }}
                                </p>
                            </div>
                        @else
                            <div class="mb-5 bg-blue-subtle rounded-lg p-3 sm:py-3 sm:px-3.5 flex items-start gap-2.5 border border-primary-container/20" id="context-banner">
                                <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">shopping_bag</span>
                                <p class="font-caption text-caption text-primary leading-snug font-medium">
                                    Masuk dulu untuk menambahkan produk ke keranjang belanja Anda.
                                </p>
                            </div>
                        @endif

                        <!-- Error Alert -->
                        @error('email')
                            <div class="mb-5 bg-[#FDE8E8] border border-red-graphic rounded-lg p-3 flex items-start gap-2.5" id="error-alert">
                                <span class="material-symbols-outlined text-error text-[20px] shrink-0 mt-0.5">error</span>
                                <p class="font-caption text-caption text-error font-medium leading-snug">
                                    {{ $message }}
                                </p>
                            </div>
                        @enderror

                        <div class="mb-6">
                            <h2 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">
                                Masuk ke Lokalku
                            </h2>
                            <p class="font-body-md text-body-md text-ink-muted mt-1">
                                Belum punya akun? <a class="text-primary font-medium hover:underline" href="{{ route('register') }}">Daftar Akun Baru</a>
                            </p>
                        </div>

                        <form class="space-y-4" id="login-form" method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email Field -->
                            <div class="space-y-1.5">
                                <label class="block font-caption text-caption text-on-surface font-medium" for="input-email">Email</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-muted">
                                        <span class="material-symbols-outlined text-[18px]">mail</span>
                                    </span>
                                    <input autocomplete="email" class="w-full h-11 pl-10 pr-3.5 rounded-lg border bg-surface-card text-on-surface font-body-md text-body-md focus:outline-none focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all placeholder:text-ink-muted/60 @error('email') border-red-graphic bg-red-50/20 @else border-border-input @enderror" id="input-email" name="email" placeholder="budi.santoso@nusantara.id" type="email" value="{{ old('email') }}" required autofocus>
                                </div>
                            </div>

                            <!-- Password Field -->
                            <div class="space-y-1.5">
                                <label class="block font-caption text-caption text-on-surface font-medium" for="input-password">Kata Sandi</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-ink-muted">
                                        <span class="material-symbols-outlined text-[18px]">lock</span>
                                    </span>
                                    <input autocomplete="current-password" class="w-full h-11 pl-10 pr-11 rounded-lg border bg-surface-card text-on-surface font-body-md text-body-md focus:outline-none focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 transition-all placeholder:text-ink-muted/60 @error('email') border-red-graphic bg-red-50/20 @else border-border-input @enderror" id="input-password" name="password" placeholder="••••••••••••••••" type="password" required>
                                    <button aria-label="Tampilkan atau sembunyikan kata sandi" class="absolute inset-y-0 right-0 w-11 h-11 flex items-center justify-center text-ink-muted hover:text-on-surface transition-colors" id="toggle-password" onclick="togglePasswordVisibility()" type="button">
                                        <span class="material-symbols-outlined text-[20px]" id="eye-icon">visibility</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Utilities: Remember Me & Forgot Password -->
                            <div class="flex items-center justify-between pt-0.5">
                                <label class="flex items-center gap-2 cursor-pointer select-none">
                                    <input class="w-4 h-4 rounded border-border-input text-primary-container focus:ring-primary-container/30 cursor-pointer accent-primary-container" id="input-remember" name="remember" type="checkbox">
                                    <span class="font-body-md text-body-md text-on-surface">Ingat saya</span>
                                </label>
                                <a class="font-caption text-caption text-primary hover:text-blue-hover font-semibold transition-colors" href="#">
                                    Lupa kata sandi?
                                </a>
                            </div>

                            <!-- Submit CTA Button -->
                            <div class="pt-2">
                                <button class="w-full h-11 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-headline-sm text-headline-sm font-bold flex items-center justify-center transition-all shadow-sm active:scale-[0.99] disabled:opacity-75 disabled:cursor-not-allowed" id="btn-submit" type="submit">
                                    <span id="btn-text">Masuk</span>
                                    <span class="hidden items-center gap-2" id="btn-spinner">
                                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" fill="currentColor"></path>
                                        </svg>
                                        <span>Memproses…</span>
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
    function togglePasswordVisibility() {
        const input = document.getElementById('input-password');
        const icon = document.getElementById('eye-icon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerText = 'visibility_off';
        } else {
            input.type = 'password';
            icon.innerText = 'visibility';
        }
    }
</script>
@endpush
@endsection
