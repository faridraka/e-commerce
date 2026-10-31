@extends('layouts.auth')

@section('title', 'Onboarding - Pilih Peran Akun Lokalku')

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
        <div class="w-full max-w-3xl mx-auto px-margin flex flex-col items-center">

            <div class="text-center max-w-xl mb-space-xl">
                <div class="inline-flex items-center gap-space-xs px-space-md py-1 rounded-full bg-blue-subtle text-primary font-caption text-caption font-semibold mb-space-sm">
                    <span class="material-symbols-outlined text-[15px]">badge</span>
                    <span>Pilih Peran Pengguna</span>
                </div>
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight mb-space-xs">
                    Selamat datang, {{ explode(' ', trim(auth()->user()->name))[0] }}! Anda ingin menggunakan Lokalku sebagai apa?
                </h1>
                <p class="font-body-md text-body-md text-ink-muted">
                    Pilih peran utama akun Anda. Anda tetap dapat beralih atau menambahkan peran nanti melalui pengaturan profil.
                </p>
            </div>

            <form id="role-form" method="POST" action="{{ route('onboarding.role') }}" class="w-full flex flex-col items-center">
                @csrf
                <input type="hidden" name="role" id="role-input" value="{{ old('role', 'buyer') }}">

                <div aria-label="Pilihan Peran Pengguna" class="w-full grid grid-cols-1 md:grid-cols-2 gap-5 mb-space-xl" role="radiogroup">
                    <!-- Buyer Card -->
                    <div aria-checked="{{ old('role', 'buyer') === 'buyer' ? 'true' : 'false' }}" class="group relative rounded-xl p-6 transition-all duration-200 cursor-pointer shadow-sm flex flex-col justify-between select-none" id="role-card-buyer" role="radio" tabindex="0">
                        <div class="absolute top-6 right-6">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors" id="radio-buyer">
                                <span class="material-symbols-outlined text-[16px] text-surface-card font-bold">check</span>
                            </div>
                        </div>
                        <div>
                            <div class="w-14 h-14 rounded-xl bg-blue-subtle flex items-center justify-center text-primary mb-space-lg transition-transform group-hover:scale-105">
                                <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">shopping_bag</span>
                            </div>
                            <div class="flex items-center gap-space-sm mb-space-xs">
                                <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold tracking-tight">Pembeli</h2>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-caption text-caption bg-surface-container text-ink-muted">
                                    Pribadi &amp; Keluarga
                                </span>
                            </div>
                            <p class="font-body-md text-body-md text-ink-muted mb-space-lg">
                                Jelajahi ragam produk nusantara, belanja dengan aman, dan lacak pesanan secara akurat.
                            </p>
                            <div class="space-y-space-sm">
                                <div class="flex items-start gap-space-sm">
                                    <span class="material-symbols-outlined text-success-text text-[18px] shrink-0 mt-0.5">check_circle</span>
                                    <span class="font-body-md text-body-md text-on-surface">Belanja langsung dari perajin lokal</span>
                                </div>
                                <div class="flex items-start gap-space-sm">
                                    <span class="material-symbols-outlined text-success-text text-[18px] shrink-0 mt-0.5">check_circle</span>
                                    <span class="font-body-md text-body-md text-on-surface">Konfirmasi pembayaran manual dengan sekali klik</span>
                                </div>
                                <div class="flex items-start gap-space-sm">
                                    <span class="material-symbols-outlined text-success-text text-[18px] shrink-0 mt-0.5">check_circle</span>
                                    <span class="font-body-md text-body-md text-on-surface">Lacak kurir pengiriman secara real-time</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-space-xl pt-space-md flex items-center gap-space-xs text-primary font-caption text-caption font-semibold opacity-90">
                            <span>Akses instan tanpa verifikasi toko</span>
                            <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                        </div>
                    </div>

                    <!-- Seller Card -->
                    <div aria-checked="{{ old('role', 'buyer') === 'seller' ? 'true' : 'false' }}" class="group relative bg-surface-card rounded-xl p-6 transition-all duration-200 cursor-pointer shadow-sm flex flex-col justify-between select-none" id="role-card-seller" role="radio" tabindex="0">
                        <div class="absolute top-6 right-6">
                            <div class="w-6 h-6 rounded-full flex items-center justify-center transition-colors" id="radio-seller">
                                <span class="material-symbols-outlined text-[16px] text-surface-card font-bold">check</span>
                            </div>
                        </div>
                        <div>
                            <div class="w-14 h-14 rounded-xl bg-blue-subtle flex items-center justify-center text-primary mb-space-lg transition-transform group-hover:scale-105">
                                <span class="material-symbols-outlined text-[28px]" style="font-variation-settings: 'FILL' 1;">storefront</span>
                            </div>
                            <div class="flex items-center gap-space-sm mb-space-xs">
                                <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold tracking-tight">Penjual (Admin)</h2>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full font-caption text-caption bg-surface-container text-ink-muted">
                                    Mitra Bisnis UMKM
                                </span>
                            </div>
                            <p class="font-body-md text-body-md text-ink-muted mb-space-md">
                                Kelola produk, transaksi pesanan, stok inventaris, dan kasir POS toko fisik Anda.
                            </p>
                            <div class="flex items-center gap-space-xs px-space-md py-1.5 rounded-lg bg-surface-container-low mb-space-lg">
                                <span class="material-symbols-outlined text-accent-yellow text-[16px]">lock</span>
                                <span class="font-caption text-caption text-ink-muted">Daftarkan toko lalu verifikasi kode otomatis</span>
                            </div>
                            <div class="space-y-space-sm">
                                <div class="flex items-start gap-space-sm">
                                    <span class="material-symbols-outlined text-success-text text-[18px] shrink-0 mt-0.5">check_circle</span>
                                    <span class="font-body-md text-body-md text-on-surface">Buka etalase digital &amp; katalog produk</span>
                                </div>
                                <div class="flex items-start gap-space-sm">
                                    <span class="material-symbols-outlined text-success-text text-[18px] shrink-0 mt-0.5">check_circle</span>
                                    <span class="font-body-md text-body-md text-on-surface">Akses aplikasi Kasir POS toko terintegrasi</span>
                                </div>
                                <div class="flex items-start gap-space-sm">
                                    <span class="material-symbols-outlined text-success-text text-[18px] shrink-0 mt-0.5">check_circle</span>
                                    <span class="font-body-md text-body-md text-on-surface">Laporan penjualan &amp; manajemen inventaris</span>
                                </div>
                            </div>
                        </div>
                        <div class="mt-space-xl pt-space-md flex items-center justify-between text-ink-muted font-caption text-caption font-medium transition-colors" id="seller-code-prompt">
                            <span>Isi nama toko di langkah berikutnya</span>
                            <span class="material-symbols-outlined text-[16px]">info</span>
                        </div>
                    </div>
                </div>

                @error('role')
                    <p class="font-caption text-caption text-secondary flex items-center gap-1 mb-space-md">
                        <span class="material-symbols-outlined text-[16px]">error</span>
                        <span>{{ $message }}</span>
                    </p>
                @enderror

                <div class="w-full flex flex-col items-center gap-space-md">
                    <button class="w-full max-w-xs h-12 rounded-full font-headline-sm text-headline-sm font-semibold flex items-center justify-center gap-space-xs transition-all duration-200 bg-primary hover:bg-blue-hover text-on-primary cursor-pointer shadow-md" id="btn-submit" type="submit">
                        <span>Lanjutkan</span>
                        <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                    </button>
                    <a class="font-caption text-caption text-ink-muted hover:text-primary transition-colors flex items-center gap-1" href="#">
                        <span>Butuh bantuan menentukan pilihan?</span>
                        <span class="underline font-semibold">Hubungi Dukungan Lokalku</span>
                    </a>
                </div>
            </form>

            <div class="mt-space-xl pt-space-md flex items-center gap-space-xs font-caption text-caption text-ink-muted">
                <span class="material-symbols-outlined text-[15px] text-tertiary">lock</span>
                <span>Transkrip Data &amp; Akun Terlindungi • Lokalku UMKM Digital</span>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
    (function () {
        const cardBuyer = document.getElementById('role-card-buyer');
        const cardSeller = document.getElementById('role-card-seller');
        const radioBuyer = document.getElementById('radio-buyer');
        const radioSeller = document.getElementById('radio-seller');
        const roleInput = document.getElementById('role-input');

        const baseCard = 'group relative rounded-xl p-6 transition-all duration-200 cursor-pointer shadow-sm flex flex-col justify-between select-none';
        const selectedCard = 'group relative bg-blue-subtle/30 rounded-xl p-6 transition-all duration-200 cursor-pointer shadow-md flex flex-col justify-between select-none shadow-[0_0_0_2px_#0064d2]';
        const baseRadio = 'w-6 h-6 rounded-full flex items-center justify-center transition-colors bg-surface-container text-transparent';
        const selectedRadio = 'w-6 h-6 rounded-full flex items-center justify-center transition-colors bg-primary text-surface-card';

        function applyRole(role) {
            roleInput.value = role;

            cardBuyer.className = role === 'buyer' ? selectedCard : baseCard;
            cardSeller.className = role === 'seller' ? selectedCard + ' bg-surface-card' : baseCard + ' bg-surface-card';
            cardBuyer.setAttribute('aria-checked', role === 'buyer' ? 'true' : 'false');
            cardSeller.setAttribute('aria-checked', role === 'seller' ? 'true' : 'false');

            radioBuyer.className = role === 'buyer' ? selectedRadio : baseRadio;
            radioSeller.className = role === 'seller' ? selectedRadio : baseRadio;
        }

        cardBuyer.addEventListener('click', () => applyRole('buyer'));
        cardSeller.addEventListener('click', () => applyRole('seller'));

        [cardBuyer, cardSeller].forEach((card) => {
            card.addEventListener('keydown', (e) => {
                if (e.key === ' ' || e.key === 'Enter') {
                    e.preventDefault();
                    applyRole(card === cardBuyer ? 'buyer' : 'seller');
                }
            });
        });

        applyRole(roleInput.value || 'buyer');
    })();
</script>
@endpush
@endsection
