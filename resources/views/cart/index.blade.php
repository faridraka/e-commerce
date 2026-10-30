@extends('layouts.app')

@section('title', 'Keranjang Belanja - Lokalku')

@section('content')
@php
    $totalQuantity = $cartItems->sum('quantity');
    $freeShippingThreshold = 250000;
    $shippingGap = max(0, $freeShippingThreshold - $total);
    $shippingProgress = min(100, (int) round(((float) $total / $freeShippingThreshold) * 100));
@endphp
<div class="max-w-[1280px] w-full mx-auto px-4 py-6 md:py-8 space-y-6">
    @if ($cartItems->isEmpty())
        <div class="bg-surface-card rounded-xl border border-border-default p-6 shadow-sm">
            <x-not-found title="Keranjang Belanja Kosong" message="Kamu belum menambahkan produk apa pun. Yuk jelajahi produk UMKM lokal!" actionUrl="{{ route('products.index') }}" actionLabel="Mulai Belanja" />
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Kolom Kiri: Item & Kartu Toko -->
            <div class="lg:col-span-8 flex flex-col gap-5">
                <!-- Baris Judul Keranjang -->
                <div class="bg-surface-card rounded-xl p-4 shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <label class="relative flex items-center justify-center cursor-pointer select-none">
                            <input checked class="w-5 h-5 rounded accent-primary-container cursor-pointer transition-colors" type="checkbox" title="Semua produk ikut di-checkout">
                        </label>
                        <h1 class="font-headline-lg text-headline-sm sm:text-headline-lg text-on-surface font-bold tracking-tight">
                            Keranjang Belanja <span class="text-ink-muted text-body-md font-normal">({{ $totalQuantity }} item)</span>
                        </h1>
                    </div>
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Kosongkan seluruh isi keranjang belanja?')">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-1.5 text-secondary hover:text-red-graphic font-body-md text-caption sm:text-body-md font-medium transition-colors">
                            <span class="material-symbols-outlined text-[18px]">delete_sweep</span>
                            <span>Kosongkan Keranjang</span>
                        </button>
                    </form>
                </div>
                <!-- Kartu Klaster Toko -->
                <div class="bg-surface-card rounded-xl shadow-sm overflow-hidden flex flex-col">
                    <!-- Header Toko -->
                    <div class="bg-surface-container-low px-5 py-3.5 flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-primary-container text-[20px]">storefront</span>
                            <div class="flex items-center gap-1.5">
                                <span class="font-headline-sm text-body-md font-bold text-on-surface">Toko Utama Lokalku Nusantara</span>
                                <span class="material-symbols-outlined text-success-text text-[18px]" style="font-variation-settings: 'FILL' 1;" title="Gerai Resmi Terverifikasi">verified</span>
                            </div>
                            <span class="text-caption font-caption text-ink-muted hidden sm:inline-block">• Sentra Kuliner &amp; Kriya Lokal</span>
                        </div>
                        <span class="text-caption font-caption bg-surface-container px-2.5 py-1 rounded-full text-on-surface-variant font-medium">
                            Kota Jakarta Pusat
                        </span>
                    </div>
                    <!-- Daftar Item Produk -->
                    <div class="flex flex-col">
                        @foreach ($cartItems as $item)
                            @php
                                $itemSubtotal = (float) $item->product->price * $item->quantity;
                            @endphp
                            <div class="p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center gap-4 transition-colors">
                                <div class="flex items-center gap-3 shrink-0">
                                    <input checked class="w-5 h-5 rounded accent-primary-container cursor-pointer" type="checkbox" title="Produk dipilih">
                                    <a href="{{ route('products.show', $item->product->slug) }}" class="relative w-20 h-20 rounded-lg overflow-hidden bg-surface-container shrink-0 block">
                                        <img class="w-full h-full object-cover" src="{{ $item->product->image }}" alt="{{ $item->product->name }}">
                                    </a>
                                </div>
                                <div class="flex-1 min-w-0 flex flex-col gap-1 w-full sm:w-auto">
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="font-headline-sm text-body-md font-medium text-on-surface line-clamp-2">
                                            <a href="{{ route('products.show', $item->product->slug) }}" class="hover:text-primary transition-colors">{{ $item->product->name }}</a>
                                        </h3>
                                    </div>
                                    <div class="flex items-center gap-2 flex-wrap text-caption font-caption">
                                        <span class="text-ink-muted">Rp {{ number_format((float) $item->product->price, 0, ',', '.') }} / item</span>
                                        <span class="w-1 h-1 rounded-full bg-outline-variant"></span>
                                        <span class="{{ $item->product->stock > 0 ? 'text-success-text font-medium bg-tertiary-fixed/30' : 'text-secondary font-medium bg-secondary-fixed/50' }} px-2 py-0.5 rounded-full">
                                            {{ $item->product->stock > 0 ? 'Stok: ' . $item->product->stock : 'Stok Habis' }}
                                        </span>
                                    </div>
                                    @if ($item->product->stock > 0 && $item->product->stock <= 5)
                                        <div class="items-center gap-1.5 text-secondary font-caption text-caption mt-1 flex">
                                            <span class="material-symbols-outlined text-[16px]">warning</span>
                                            <span class="font-medium">Stok tersisa hanya {{ $item->product->stock }} item! Segera pesan sebelum kehabisan.</span>
                                        </div>
                                    @endif
                                </div>
                                <!-- Harga, Stepper & Aksi -->
                                <div class="flex items-center justify-between sm:justify-end gap-5 w-full sm:w-auto mt-2 sm:mt-0 pt-2 sm:pt-0">
                                    <!-- Stepper Kuantitas -->
                                    <form action="{{ route('cart.update', $item) }}" method="POST" data-qty-form>
                                        @method('PATCH')
                                        @csrf
                                        <div class="flex items-center bg-surface-container-low rounded-full p-1 shadow-sm">
                                            <button type="button" aria-label="Kurangi kuantitas" data-qty-step="-1" @disabled($item->quantity <= 1) class="w-8 h-8 rounded-full flex items-center justify-center text-on-surface hover:bg-surface-card transition-colors active:scale-95 disabled:opacity-40">
                                                <span class="material-symbols-outlined text-[18px]">remove</span>
                                            </button>
                                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" data-qty-input aria-label="Kuantitas" class="w-9 text-center font-code-mono text-body-md font-bold text-on-surface bg-transparent outline-none [appearance:textfield] [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:appearance-none">
                                            <button type="button" aria-label="Tambah kuantitas" data-qty-step="1" class="w-8 h-8 rounded-full flex items-center justify-center text-on-surface hover:bg-surface-card transition-colors active:scale-95">
                                                <span class="material-symbols-outlined text-[18px]">add</span>
                                            </button>
                                        </div>
                                    </form>
                                    <!-- Subtotal -->
                                    <div class="text-right min-w-[90px]">
                                        <span class="font-price-sm text-price-sm text-primary-container font-bold block">Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</span>
                                    </div>
                                    <!-- Tombol Hapus -->
                                    <form action="{{ route('cart.remove', $item) }}" method="POST" onsubmit="return confirm('Hapus produk ini dari keranjang?')">
                                        @csrf
                                        <button type="submit" title="Hapus produk" class="w-10 h-10 rounded-full flex items-center justify-center text-ink-muted hover:text-secondary hover:bg-secondary-fixed/40 transition-colors">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            @if (! $loop->last)
                                <div class="h-[1px] w-full bg-surface-container"></div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <!-- Kartu Progres Bebas Ongkir -->
                <div class="bg-surface-card rounded-xl p-5 shadow-sm flex flex-col gap-3">
                    <div class="flex items-center justify-between gap-3 flex-wrap">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary-container text-[22px]">local_shipping</span>
                            @if ($shippingGap > 0)
                                <p class="font-body-md text-caption sm:text-body-md text-on-surface font-medium">Tambah belanjaan <span class="font-bold text-primary-container">Rp {{ number_format($shippingGap, 0, ',', '.') }}</span> lagi untuk <span class="font-bold text-success-text">Bebas Ongkir se-Indonesia!</span></p>
                            @else
                                <p class="font-body-md text-caption sm:text-body-md font-medium"><span class="font-bold text-success-text">Selamat! Belanjaan Anda sudah memenuhi syarat Bebas Ongkir se-Indonesia!</span></p>
                            @endif
                        </div>
                        <span class="font-code-mono text-caption text-ink-muted font-bold">{{ $shippingProgress }}%</span>
                    </div>
                    <div class="w-full bg-surface-container-high h-2.5 rounded-full overflow-hidden">
                        <div class="bg-primary-container h-full rounded-full transition-all duration-300" style="width: {{ $shippingProgress }}%;"></div>
                    </div>
                    <div class="flex justify-between items-center text-caption font-caption text-ink-muted">
                        <span>Rp {{ number_format((float) $total, 0, ',', '.') }}</span>
                        <span>Target: Rp {{ number_format($freeShippingThreshold, 0, ',', '.') }} (Subsidi Ongkir Rp 20.000)</span>
                    </div>
                </div>
                <!-- Widget Kode Voucher -->
                <div class="bg-surface-card rounded-xl p-5 shadow-sm flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-subtle text-primary-container flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[22px]">confirmation_number</span>
                        </div>
                        <div>
                            <p class="font-headline-sm text-body-md font-bold text-on-surface">Punya voucher diskon UMKM?</p>
                            <p class="font-body-md text-caption text-ink-muted">Gunakan kode voucher untuk potongan harga tambahan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1 sm:w-56">
                            <input class="w-full h-10 bg-surface-container-low px-3.5 rounded-lg text-body-md text-on-surface font-body-md uppercase tracking-wider placeholder:normal-case placeholder:tracking-normal placeholder:text-ink-muted focus:bg-surface-card focus:outline-none focus:ring-2 focus:ring-primary-container disabled:opacity-60 disabled:cursor-not-allowed" placeholder="Masukkan Kode" type="text" disabled title="Fitur voucher segera hadir">
                        </div>
                        <button type="button" disabled title="Fitur voucher segera hadir" class="h-10 px-5 rounded-full bg-surface-container-high text-on-surface font-body-md text-body-md font-bold shadow-sm disabled:opacity-50 disabled:cursor-not-allowed">
                            Terapkan
                        </button>
                    </div>
                </div>
            </div>
            <!-- Kolom Kanan: Ringkasan (Sticky) -->
            <div class="lg:col-span-4 sticky top-16">
                <div class="bg-surface-card rounded-xl p-6 shadow-sm flex flex-col gap-5">
                    <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Ringkasan Belanja</h2>
                    <div class="flex flex-col gap-3 font-body-md text-body-md">
                        <div class="flex items-center justify-between text-on-surface-variant">
                            <span>Total Harga ({{ $totalQuantity }} item)</span>
                            <span class="font-medium text-on-surface">Rp {{ number_format((float) $total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-on-surface-variant">
                            <span>Diskon Produk UMKM</span>
                            <span class="text-success-text font-bold">- Rp 0</span>
                        </div>
                        <div class="flex items-center justify-between text-on-surface-variant">
                            <div class="flex items-center gap-1">
                                <span>Estimasi Biaya Pengiriman</span>
                                <span class="material-symbols-outlined text-[16px] text-ink-muted cursor-pointer" title="Dihitung berdasarkan alamat tujuan pengiriman di halaman checkout">info</span>
                            </div>
                            <span class="font-medium text-on-surface">Rp 0</span>
                        </div>
                    </div>
                    <div class="h-[1px] w-full bg-surface-container"></div>
                    <div class="flex items-baseline justify-between">
                        <span class="font-headline-sm text-body-md font-bold text-on-surface">Total Tagihan</span>
                        <div class="text-right">
                            <span class="font-price-lg text-price-lg font-bold text-primary-container">Rp {{ number_format((float) $total, 0, ',', '.') }}</span>
                            <span class="text-caption font-caption text-ink-muted block mt-0.5">Belum termasuk ongkos kirim</span>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 mt-1">
                        <a href="{{ route('checkout.index') }}" class="w-full h-11 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-body-md text-body-md font-bold flex items-center justify-center gap-2 transition-all shadow-sm active:scale-[0.99]">
                            <span>Lanjut ke Checkout</span>
                            <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                        </a>
                        <a href="{{ route('products.index') }}" class="text-center font-body-md text-caption sm:text-body-md font-medium text-primary hover:text-blue-hover transition-colors">
                            ← Lanjut Belanja Produk Lain
                        </a>
                    </div>
                    <div class="bg-surface-container-low rounded-lg p-3.5 flex items-start gap-2.5">
                        <span class="material-symbols-outlined text-success-text text-[20px] shrink-0" style="font-variation-settings: 'FILL' 1;">verified_user</span>
                        <p class="font-caption text-caption text-on-surface-variant leading-relaxed">
                            Keamanan transaksi terjamin 100% dengan <span class="font-bold text-on-surface">QRIS</span> &amp; sistem escrow <span class="font-bold text-on-surface">Rekening Bersama Lokalku</span>.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bar Checkout Sticky Mobile -->
        <div class="fixed bottom-14 left-0 right-0 z-40 bg-surface-card border-t border-border-default p-3 md:hidden shadow-lg">
            <div class="flex items-center justify-between gap-3 max-w-[1280px] mx-auto">
                <div class="flex flex-col">
                    <span class="font-caption text-caption text-ink-muted">Total Tagihan</span>
                    <span class="font-price-sm text-price-sm font-bold text-primary-container">Rp {{ number_format((float) $total, 0, ',', '.') }}</span>
                </div>
                <a href="{{ route('checkout.index') }}" class="h-10 px-6 rounded-full bg-primary-container text-on-primary font-body-md font-bold flex items-center gap-1.5 shadow-sm active:scale-95">
                    <span>Checkout ({{ $totalQuantity }})</span>
                    <span class="material-symbols-outlined text-[18px]">arrow_forward</span>
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
@if (! $cartItems->isEmpty())
<script>
    document.querySelectorAll('form[data-qty-form]').forEach((form) => {
        const input = form.querySelector('input[name="quantity"]');
        if (!input) return;

        const max = Math.max(1, parseInt(input.max, 10) || 1);

        const submitQuantity = (next) => {
            if (Number.isNaN(next) || next < 1) return;
            if (next > max) {
                alert('Maksimal pembelian untuk produk ini adalah ' + max + ' item.');
                input.value = max;
                return;
            }
            input.value = next;
            form.submit();
        };

        form.querySelectorAll('[data-qty-step]').forEach((button) => {
            button.addEventListener('click', () => {
                const delta = parseInt(button.dataset.qtyStep, 10) || 0;
                submitQuantity((parseInt(input.value, 10) || 1) + delta);
            });
        });

        input.addEventListener('change', () => submitQuantity(parseInt(input.value, 10) || 1));
    });
</script>
@endif
@endpush
