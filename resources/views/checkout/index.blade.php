@extends('layouts.app')

@section('title', 'Checkout Pengiriman & Tinjau Pesanan - Lokalku')

@section('content')
@php
    $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
    $totalQuantity = $cartItems->sum('quantity');
@endphp
<div class="w-full bg-canvas-background py-6 sm:py-8">
    <div class="max-w-[1280px] mx-auto px-4 sm:px-6">
        <!-- Breadcrumb & Header Halaman -->
        <header class="mb-8">
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-caption font-caption text-ink-muted mb-3">
                <a class="hover:text-primary transition-colors" href="{{ route('home') }}">Beranda</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <a class="hover:text-primary transition-colors" href="{{ route('cart.index') }}">Keranjang Belanja</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-on-surface font-bold">Checkout Pengiriman</span>
            </nav>
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 pb-6 border-b border-border-default">
                <div>
                    <h1 class="font-display text-display text-on-surface tracking-tight leading-tight">
                        Checkout &amp; Pengiriman
                    </h1>
                    <p class="font-body-md text-body-md text-ink-muted mt-1.5">
                        Periksa alamat penerima dan tinjau kembali rincian produk sebelum melanjutkan ke pembayaran terverifikasi.
                    </p>
                </div>
                <!-- Indikator Langkah Belanja -->
                <div class="flex items-center gap-2 sm:gap-3 bg-surface-card px-4 py-2.5 rounded-xl border border-border-default shadow-sm shrink-0">
                    <div class="flex items-center gap-1.5">
                        <span class="w-6 h-6 rounded-full bg-success-text/15 text-success-text font-bold text-caption flex items-center justify-center">
                            <span class="material-symbols-outlined text-[16px]">done</span>
                        </span>
                        <span class="text-caption font-caption font-bold text-on-surface hidden sm:inline">1. Keranjang</span>
                    </div>
                    <span class="material-symbols-outlined text-outline text-[16px]">chevron_right</span>
                    <div class="flex items-center gap-1.5">
                        <span class="w-6 h-6 rounded-full bg-primary-container text-on-primary font-bold text-caption flex items-center justify-center">2</span>
                        <span class="text-caption font-caption font-bold text-primary">2. Pengiriman &amp; Tinjau</span>
                    </div>
                    <span class="material-symbols-outlined text-outline text-[16px]">chevron_right</span>
                    <div class="flex items-center gap-1.5 opacity-60">
                        <span class="w-6 h-6 rounded-full bg-surface-container text-ink-muted font-bold text-caption flex items-center justify-center">3</span>
                        <span class="text-caption font-caption text-ink-muted hidden sm:inline">3. Pembayaran</span>
                    </div>
                </div>
            </div>
        </header>

        @if ($cartItems->isEmpty())
            <div class="bg-surface-card rounded-xl border border-border-default p-6 shadow-sm max-w-2xl mx-auto">
                <x-not-found title="Keranjang Belanja Kosong" message="Tidak ada produk untuk di-checkout. Tambahkan produk ke keranjang terlebih dahulu." actionUrl="{{ route('products.index') }}" actionLabel="Mulai Belanja" />
            </div>
        @else
            <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST">
                @csrf
                <input type="hidden" name="address_id" id="selected-address-id" value="{{ $defaultAddress?->id }}">
                <!-- Grid 8 Kolom (Kiri) + 4 Kolom (Kanan) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    <!-- Kolom Kiri: Alamat & Tinjau Produk -->
                    <div class="lg:col-span-8 flex flex-col gap-6">
                        <!-- KARTU LANGKAH 1: Alamat Pengiriman -->
                        <section class="bg-surface-card rounded-xl border border-border-default p-5 sm:p-6 shadow-sm transition-all" id="card-address-section">
                            <div class="flex items-center justify-between gap-4 pb-4 border-b border-border-default mb-5">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-primary-container text-on-primary font-bold flex items-center justify-center shrink-0">1</span>
                                    <div>
                                        <h2 class="font-headline-md text-headline-md text-on-surface">Alamat Pengiriman</h2>
                                        <p class="font-caption text-caption text-ink-muted">Tentukan tujuan pengiriman kurir lokal terdekat</p>
                                    </div>
                                </div>
                                <a href="{{ route('addresses.index') }}" class="inline-flex items-center gap-1 text-caption font-caption font-bold text-primary hover:text-blue-hover transition-colors px-3 py-1.5 rounded-full hover:bg-blue-subtle">
                                    <span class="material-symbols-outlined text-[18px]">add</span>
                                    Tambah Alamat Baru
                                </a>
                            </div>
                            @error('address_id')
                                <div class="mb-5 p-4 rounded-xl bg-secondary-fixed/50 border border-secondary text-secondary flex items-start gap-3" role="alert">
                                    <span class="material-symbols-outlined text-[20px] shrink-0 text-secondary">error</span>
                                    <div>
                                        <p class="font-body-md text-body-md font-bold text-secondary">Pilih alamat pengiriman terlebih dahulu</p>
                                        <p class="font-caption text-caption text-on-surface-variant">{{ $message }}</p>
                                    </div>
                                </div>
                            @enderror
                            @if ($addresses->isEmpty())
                                <!-- Panel info belum ada alamat -->
                                <div class="p-4 rounded-xl bg-blue-subtle/60 border border-primary-container/30 flex items-start gap-3">
                                    <span class="material-symbols-outlined text-[20px] shrink-0 text-primary">info</span>
                                    <div class="flex-1">
                                        <p class="font-body-md text-body-md font-bold text-on-surface">Belum ada alamat pengiriman</p>
                                        <p class="font-caption text-caption text-on-surface-variant">Tambahkan alamat dahulu untuk melanjutkan pesanan ini.</p>
                                        <a href="{{ route('addresses.index') }}" class="mt-2 inline-flex items-center gap-1 font-caption text-caption font-bold text-primary hover:text-blue-hover hover:underline">
                                            <span class="material-symbols-outlined text-[16px]">add_location_alt</span>
                                            Tambah Alamat
                                        </a>
                                    </div>
                                </div>
                            @else
                                <!-- Daftar Kartu Pilihan Alamat -->
                                <div class="flex flex-col gap-3.5" id="address-list-container">
                                    @foreach ($addresses as $address)
                                        @php $isSelected = (int) $address->id === (int) ($defaultAddress?->id); @endphp
                                        <label data-address-card data-address-id="{{ $address->id }}" class="relative cursor-pointer block rounded-xl p-4 sm:p-5 {{ $isSelected ? 'border-2 border-primary-container bg-[#F9FBFE] ring-2 ring-primary-container transition-all hover:shadow-sm' : 'border border-border-default bg-surface-card transition-all hover:border-primary-fixed-dim' }}">
                                            <div class="flex items-start justify-between gap-3">
                                                <div class="flex items-start gap-3">
                                                    <input type="radio" name="selected_shipping_address" value="{{ $address->id }}" data-address-radio @checked($isSelected) class="mt-1 w-4 h-4 text-primary-container focus:ring-primary-container">
                                                    <div>
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <span class="font-headline-sm text-headline-sm font-bold text-on-surface">{{ $address->recipient_name }}</span>
                                                            <span class="font-caption text-caption text-ink-muted">• {{ $address->phone }}</span>
                                                            @if ($address->is_default)
                                                                <span class="bg-blue-subtle text-primary font-caption text-caption px-2 py-0.5 rounded-full font-bold">Utama</span>
                                                            @endif
                                                        </div>
                                                        <p class="font-body-md text-body-md text-on-surface-variant mt-2 leading-relaxed">
                                                            {{ $address->address_detail }}, {{ $address->city }} {{ $address->postal_code }}
                                                        </p>
                                                        <div class="mt-3.5 flex flex-wrap items-center gap-4 text-caption font-caption">
                                                            <a href="{{ route('addresses.index') }}" class="text-primary font-bold hover:underline inline-flex items-center gap-1">
                                                                <span class="material-symbols-outlined text-[16px]">edit</span>
                                                                Ubah Alamat
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="material-symbols-outlined {{ $isSelected ? 'text-primary-container text-[24px]' : 'text-outline text-[24px] opacity-20' }}" data-check-icon>{{ $isSelected ? 'check_circle' : 'radio_button_unchecked' }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </section>
                        <!-- KARTU LANGKAH 2: Tinjau Pesanan Produk UMKM -->
                        <section class="bg-surface-card rounded-xl border border-border-default p-5 sm:p-6 shadow-sm">
                            <div class="flex items-center justify-between gap-4 pb-4 border-b border-border-default mb-5">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-full bg-primary-container text-on-primary font-bold flex items-center justify-center shrink-0">2</span>
                                    <div>
                                        <h2 class="font-headline-md text-headline-md text-on-surface">Tinjau Pesanan</h2>
                                        <p class="font-caption text-caption text-ink-muted">Pastikan pesanan dari merchant mitra sudah sesuai</p>
                                    </div>
                                </div>
                                <div class="hidden sm:flex items-center gap-1.5 px-3 py-1 bg-surface-container-low rounded-lg text-caption font-caption text-on-surface-variant border border-border-default">
                                    <span class="material-symbols-outlined text-[16px] text-primary">storefront</span>
                                    {{ $cartItems->count() }} Produk
                                </div>
                            </div>
                            <!-- Label Merchant Toko -->
                            <div class="flex items-center gap-2.5 p-3 rounded-lg bg-surface-container-low mb-4">
                                <div class="w-7 h-7 rounded-full bg-primary text-on-primary flex items-center justify-center shrink-0">
                                    <span class="material-symbols-outlined text-[16px]">store</span>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-2">
                                    <span class="font-body-md text-body-md font-bold text-on-surface">Sentra UMKM Nusantara</span>
                                    <span class="text-caption font-caption text-ink-muted hidden sm:inline">• {{ $totalQuantity }} item dari {{ $cartItems->count() }} produk</span>
                                </div>
                            </div>
                            <!-- Daftar Item Pesanan Ringkas -->
                            <div class="divide-y divide-border-default">
                                @foreach ($cartItems as $item)
                                    <div class="py-4 first:pt-0 last:pb-0 flex items-center justify-between gap-4">
                                        <div class="flex items-center gap-3.5">
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="w-16 h-16 rounded-lg bg-surface-container overflow-hidden shrink-0 border border-border-default relative block">
                                                <img class="w-full h-full object-cover" src="{{ $item->product->image }}" alt="{{ $item->product->name }}">
                                            </a>
                                            <div>
                                                <h3 class="font-body-md text-body-md font-bold text-on-surface line-clamp-1">
                                                    <a href="{{ route('products.show', $item->product->slug) }}" class="hover:text-primary transition-colors">{{ $item->product->name }}</a>
                                                </h3>
                                                <div class="text-caption font-caption text-on-surface-variant mt-1">
                                                    {{ $item->quantity }} pcs × <span class="font-code-mono">Rp {{ number_format((float) $item->product->price, 0, ',', '.') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right shrink-0">
                                            <span class="font-price-sm text-price-sm font-bold text-on-surface">Rp {{ number_format((float) $item->product->price * $item->quantity, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <!-- Catatan Pengiriman Khusus Pembeli -->
                            <div class="mt-6 pt-5 border-t border-border-default">
                                <label class="block font-caption text-caption font-bold text-on-surface mb-1.5 flex items-center gap-1.5" for="shipping-notes">
                                    <span class="material-symbols-outlined text-[16px] text-ink-muted">note_alt</span>
                                    Catatan Pengiriman untuk Penjual / Kurir (Opsional)
                                </label>
                                <input type="text" name="notes" id="shipping-notes" class="w-full h-11 px-3.5 rounded-lg border border-border-input bg-surface-card font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2" placeholder="Contoh: Titipkan di pos sekuriti jika rumah kosong, paket sambal mohon dihandle hati-hati...">
                            </div>
                        </section>
                    </div>
                    <!-- Kolom Kanan: Sticky Ringkasan Belanja -->
                    <div class="lg:col-span-4 sticky top-16 flex flex-col gap-4">
                        <div class="bg-surface-card rounded-xl border border-border-default p-5 shadow-sm">
                            <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface pb-3.5 border-b border-border-default">
                                Ringkasan Belanja
                            </h2>
                            <div class="py-4 space-y-3 font-body-md text-body-md">
                                <div class="flex items-center justify-between text-on-surface-variant">
                                    <span>Total Harga Barang ({{ $cartItems->count() }} produk, {{ $totalQuantity }} item)</span>
                                    <span class="font-bold text-on-surface font-code-mono">Rp {{ number_format((float) $total, 0, ',', '.') }}</span>
                                </div>
                                <!-- Ongkir -->
                                <div class="flex items-start justify-between gap-3 text-on-surface-variant">
                                    <div class="flex flex-col">
                                        <span>Ongkos Kirim</span>
                                        <span class="font-caption text-caption text-primary flex items-center gap-1 mt-0.5">
                                            <span class="material-symbols-outlined text-[14px]">local_shipping</span>
                                            Dihitung saat konfirmasi (gratis sementara)
                                        </span>
                                    </div>
                                    <span class="font-caption text-caption font-bold text-on-surface whitespace-nowrap">Rp 0</span>
                                </div>
                                <div class="flex items-center justify-between text-on-surface-variant">
                                    <div class="flex items-center gap-1">
                                        <span>Biaya Layanan &amp; Asuransi</span>
                                        <span class="material-symbols-outlined text-[16px] text-ink-muted" title="Dilindungi sistem jaminan UMKM Lokalku">info</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="line-through text-caption text-ink-muted mr-1">Rp 2.000</span>
                                        <span class="font-bold text-success-text">Rp 0 (Gratis)</span>
                                    </div>
                                </div>
                            </div>
                            <div class="py-3 border-y border-dashed border-border-default">
                                <div class="flex items-baseline justify-between">
                                    <div>
                                        <span class="font-headline-sm text-headline-sm font-bold text-on-surface">Total Tagihan</span>
                                        <p class="font-caption text-caption text-ink-muted mt-0.5">Belum termasuk penyesuaian kurir lokal</p>
                                    </div>
                                    <span class="font-price-lg text-price-lg font-bold text-primary">Rp {{ number_format((float) $total, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            <!-- CTA Utama -->
                            <div class="pt-5 flex flex-col gap-3">
                                <button type="submit" id="btn-submit-checkout" @disabled($addresses->isEmpty()) class="w-full h-12 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-body-md text-body-md font-bold transition-all shadow-md flex items-center justify-center gap-2 disabled:bg-[#E5E5E5] disabled:text-ink-muted disabled:cursor-not-allowed">
                                    <span>Buat Pesanan &amp; Bayar</span>
                                    <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                                </button>
                                <a href="{{ route('cart.index') }}" class="w-full text-center py-2 text-caption font-caption font-bold text-ink-muted hover:text-primary transition-colors flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                                    Kembali ke Keranjang Belanja
                                </a>
                            </div>
                            <!-- Trust Badge Keamanan Transaksi -->
                            <div class="mt-4 pt-4 border-t border-border-default flex items-start gap-2.5 bg-surface-container-low/60 p-3 rounded-lg">
                                <span class="material-symbols-outlined text-success-text text-[20px] shrink-0 mt-0.5">verified_user</span>
                                <p class="font-caption text-caption text-ink-muted leading-tight">
                                    Transaksi 100% aman terlindungi <strong class="text-on-surface">Rekening Bersama Lokalku</strong> &amp; <strong class="text-on-surface">QRIS Standar Bank Indonesia</strong>.
                                </p>
                            </div>
                        </div>
                        <!-- Informasi Bantuan Cepat Merchant -->
                        <div class="bg-surface-card rounded-xl border border-border-default p-4 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-blue-subtle text-primary flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[20px]">support_agent</span>
                            </div>
                            <div class="text-caption font-caption">
                                <span class="font-bold text-on-surface block">Butuh Bantuan Checkout?</span>
                                <span class="text-ink-muted">Hubungi Layanan UMKM 24/7 di <a class="text-primary underline" href="{{ route('home') }}">Pusat Bantuan</a></span>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if (! $cartItems->isEmpty() && ! $addresses->isEmpty())
<script>
    (function () {
        const addressInput = document.getElementById('selected-address-id');
        const submitButton = document.getElementById('btn-submit-checkout');
        if (!addressInput) return;

        const selectedCardClasses = 'relative cursor-pointer block rounded-xl p-4 sm:p-5 border-2 border-primary-container bg-[#F9FBFE] ring-2 ring-primary-container transition-all hover:shadow-sm';
        const unselectedCardClasses = 'relative cursor-pointer block rounded-xl p-4 sm:p-5 border border-border-default bg-surface-card transition-all hover:border-primary-fixed-dim';
        const selectedIconClasses = 'material-symbols-outlined text-primary-container text-[24px]';
        const unselectedIconClasses = 'material-symbols-outlined text-outline text-[24px] opacity-20';

        const selectAddress = (card) => {
            if (!card) return;
            addressInput.value = card.dataset.addressId;

            document.querySelectorAll('[data-address-card]').forEach((other) => {
                const isSelected = other === card;
                other.className = isSelected ? selectedCardClasses : unselectedCardClasses;

                const radio = other.querySelector('[data-address-radio]');
                const icon = other.querySelector('[data-check-icon]');
                if (radio) radio.checked = isSelected;
                if (icon) {
                    icon.className = isSelected ? selectedIconClasses : unselectedIconClasses;
                    icon.textContent = isSelected ? 'check_circle' : 'radio_button_unchecked';
                }
            });

            if (submitButton) submitButton.disabled = false;
        };

        document.querySelectorAll('[data-address-card]').forEach((card) => {
            card.addEventListener('click', (event) => {
                if (event.target.closest('a')) return;
                selectAddress(card);
            });
        });

        document.querySelectorAll('[data-address-radio]').forEach((radio) => {
            radio.addEventListener('change', () => {
                selectAddress(radio.closest('[data-address-card]'));
            });
        });
    })();
</script>
@endif
@endpush
