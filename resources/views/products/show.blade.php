@extends('layouts.app')

@section('title', $product->name . ' - Lokalku')

@section('content')
<div class="flex flex-col w-full">
    <div class="max-w-[1280px] w-full mx-auto px-4 py-6 md:py-8 space-y-8 md:space-y-12">

        <!-- Breadcrumb -->
        <nav aria-label="Breadcrumb" class="flex items-center gap-2 text-caption font-caption text-ink-muted">
            <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('home') }}">
                <span class="material-symbols-outlined text-[16px]">home</span>
                Beranda
            </a>
            <span class="text-ink-muted/60">/</span>
            <a class="hover:text-primary transition-colors" href="{{ route('products.index', array_filter(['kategori' => $product->category?->slug])) }}">{{ $product->category?->name ?? 'Katalog' }}</a>
            <span class="text-ink-muted/60">/</span>
            <span aria-current="page" class="text-on-surface font-semibold truncate max-w-[200px] sm:max-w-none">{{ $product->name }}</span>
        </nav>

        <!-- Showcase & Action Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start">
            <!-- LEFT: Galeri -->
            <div class="lg:col-span-6 space-y-4">
                <div class="relative w-full aspect-square bg-surface-card rounded-xl overflow-hidden shadow-sm flex items-center justify-center group">
                    <img alt="{{ $product->name }}" class="w-full h-full object-cover transition-transform duration-500 ease-out group-hover:scale-105" id="mainProductImg" src="{{ $product->image }}">
                    <div class="absolute top-3 left-3 flex flex-col gap-1.5 z-10">
                        @if ($product->stockStatus() === 'menipis')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-caption font-bold shadow-sm">
                                <span class="material-symbols-outlined text-[14px]">warning</span>
                                Stok Menipis: {{ $product->stock }} Unit
                            </span>
                        @elseif ($product->stockStatus() === 'habis')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-secondary text-on-secondary font-caption text-caption font-bold shadow-sm">
                                <span class="material-symbols-outlined text-[14px]">block</span>
                                Stok Habis
                            </span>
                        @endif
                    </div>
                    <div class="absolute bottom-3 left-3 right-3 z-10">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface-card/95 backdrop-blur text-primary text-caption font-caption font-semibold shadow-sm">
                            <span class="material-symbols-outlined text-tertiary-container text-[16px]">verified</span>
                            100% Produk UMKM {{ $product->origin ?? 'Nusantara' }} Asli
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-surface-card flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-subtle text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]">local_shipping</span>
                        </div>
                        <div>
                            <p class="font-body-md text-body-md font-bold text-on-surface">Pengiriman Seluruh Nusantara</p>
                            <p class="font-caption text-caption text-ink-muted">Packing kardus tebal &amp; bubble wrap gratis tanpa biaya tambahan</p>
                        </div>
                    </div>
                    <span class="hidden sm:inline-flex items-center text-caption font-caption text-success-text font-bold bg-tertiary-fixed/30 px-2.5 py-1 rounded-full shrink-0">
                        Aman &amp; Higienis
                    </span>
                </div>
            </div>

            <!-- RIGHT: Info & Aksi -->
            <div class="lg:col-span-6 space-y-6">
                <div class="bg-surface-card rounded-xl p-5 md:p-6 shadow-sm space-y-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container text-on-surface-variant font-caption text-caption font-medium">
                            <span class="material-symbols-outlined text-[14px]">category</span>
                            {{ $product->category?->name ?? 'Umum' }}
                        </span>
                        <div class="inline-flex items-center gap-1 text-ink-muted font-caption text-caption">
                            <span class="material-symbols-outlined text-[16px] text-accent-yellow" style="font-variation-settings: 'FILL' 1;">star</span>
                            <span class="text-on-surface font-bold">{{ number_format((float) $product->rating, 1) }}</span>
                            <span class="mx-1">•</span>
                            <span class="text-on-surface font-medium">Terjual {{ number_format($product->sold_count, 0, ',', '.') }}+</span>
                        </div>
                    </div>

                    <div class="space-y-1">
                        <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight leading-snug">
                            {{ $product->name }}
                        </h1>
                        @if ($product->store)
                            <p class="font-caption text-caption text-ink-muted flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px] text-primary">storefront</span>
                                Diproduksi langsung oleh <span class="font-bold text-on-surface">{{ $product->store->name }}</span>
                            </p>
                        @endif
                    </div>

                    <div class="p-4 rounded-xl bg-surface-container-low flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <span class="text-caption font-caption text-ink-muted block">Harga Resmi UMKM</span>
                            <div class="flex items-baseline gap-2">
                                <span class="font-price-lg text-price-lg text-primary tracking-tight">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                @if ($product->discountPercent())
                                    <span class="font-strikethrough text-strikethrough text-ink-muted line-through">Rp {{ number_format($product->original_price, 0, ',', '.') }}</span>
                                    <span class="font-caption text-caption px-2 py-0.5 rounded-full bg-error-container text-secondary font-bold">-{{ $product->discountPercent() }}%</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            @if ($product->stock > 0)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-accent-yellow/20 text-on-accent-yellow font-caption text-caption font-bold">
                                    <span class="w-2 h-2 rounded-full bg-accent-yellow animate-pulse"></span>
                                    Tersisa {{ $product->stock }} Unit
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container text-ink-muted font-caption text-caption font-bold">Stok Habis</span>
                            @endif
                        </div>
                    </div>

                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">
                        {{ $product->description }}
                    </p>

                    <!-- Quantity + Aksi -->
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-surface-container-low">
                            <div>
                                <span class="font-body-md text-body-md font-bold text-on-surface block">Atur Jumlah</span>
                                <span class="font-caption text-caption text-ink-muted">Maksimal {{ max($product->stock, 1) }} unit per pesanan</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <button aria-label="Kurangi jumlah" class="w-10 h-10 rounded-full bg-surface-card flex items-center justify-center text-ink-muted disabled:opacity-40 transition-colors shadow-sm" disabled id="btnMinus" onclick="changeQty(-1)" type="button">
                                    <span class="material-symbols-outlined text-[18px]">remove</span>
                                </button>
                                <div class="w-12 h-10 rounded-lg bg-surface-card flex items-center justify-center font-code-mono text-code-mono font-bold text-on-surface shadow-inner">
                                    <span id="qtyCounter">1</span>
                                </div>
                                <button aria-label="Tambah jumlah" class="w-10 h-10 rounded-full bg-surface-card flex items-center justify-center text-on-surface hover:text-primary transition-colors shadow-sm" id="btnPlus" onclick="changeQty(1)" type="button">
                                    <span class="material-symbols-outlined text-[18px]">add</span>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            @if ($product->stock > 0)
                                <button class="w-full h-11 px-6 rounded-full bg-primary-container text-on-primary font-body-md text-body-md font-bold hover:bg-blue-hover active:scale-[0.99] transition-all flex items-center justify-center gap-2 shadow-sm add-to-cart-btn" data-name="{{ $product->name }}" data-product-id="{{ $product->id }}" id="btnAddToCart" type="button">
                                    <span class="material-symbols-outlined text-[20px]">add_shopping_cart</span>
                                    Tambah ke Keranjang
                                </button>
                                <button class="w-full h-11 px-6 rounded-full bg-surface-card text-primary font-body-md text-body-md font-bold hover:bg-blue-subtle active:scale-[0.99] transition-all flex items-center justify-center gap-2 shadow-sm" type="button">
                                    <span class="material-symbols-outlined text-[20px]">bolt</span>
                                    Beli Sekarang
                                </button>
                            @else
                                <button class="w-full h-11 px-6 rounded-full bg-surface-container text-ink-muted font-body-md text-body-md font-bold cursor-not-allowed flex items-center justify-center gap-2" disabled type="button">
                                    <span class="material-symbols-outlined text-[20px]">do_not_disturb</span>
                                    Stok Habis
                                </button>
                            @endif
                        </div>

                        @guest
                            <!-- Prompt guest -->
                            <div class="p-4 rounded-xl bg-blue-subtle flex items-start gap-3">
                                <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">info</span>
                                <div class="flex-1 space-y-1">
                                    <p class="font-body-md text-body-md font-bold text-primary">Anda berbelanja sebagai Pengunjung (Guest)</p>
                                    <p class="font-caption text-caption text-on-surface-variant leading-relaxed">
                                        Masuk atau buat akun baru untuk menyimpan keranjang, menggunakan voucher ongkir UMKM, dan melacak status pesanan secara real-time.
                                    </p>
                                    <div class="pt-1.5 flex items-center gap-3">
                                        <a class="inline-flex items-center justify-center px-4 py-1.5 rounded-full bg-primary text-on-primary font-caption text-caption font-bold hover:bg-blue-hover transition-colors shadow-sm" href="{{ route('login') }}">
                                            Masuk Sekarang
                                        </a>
                                        <a class="font-caption text-caption font-bold text-primary hover:underline" href="{{ route('register') }}">
                                            Daftar Akun Baru →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Kartu toko -->
                @if ($product->store)
                    <div class="bg-surface-card rounded-xl p-5 shadow-sm space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-full overflow-hidden bg-surface-container flex items-center justify-center shadow-inner">
                                    <span class="material-symbols-outlined text-primary text-[24px]">storefront</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-1.5">
                                        <span class="font-headline-sm text-headline-sm font-bold text-on-surface">{{ $product->store->name }}</span>
                                        <span class="material-symbols-outlined text-tertiary-container text-[18px]" title="Mitra Binaan Resmi Lokalku">verified</span>
                                    </div>
                                    <p class="font-caption text-caption text-ink-muted flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">location_on</span>
                                        {{ $product->origin ?? 'Indonesia' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="grid grid-cols-3 gap-2 pt-1 text-center">
                            <div class="p-2.5 rounded-lg bg-surface-container-low">
                                <span class="block font-caption text-caption text-ink-muted">Rating Produk</span>
                                <span class="font-body-md text-body-md font-bold text-on-surface flex items-center justify-center gap-0.5">
                                    <span class="material-symbols-outlined text-accent-yellow text-[16px]" style="font-variation-settings: 'FILL' 1;">star</span>
                                    {{ number_format((float) $product->rating, 1) }} / 5.0
                                </span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-surface-container-low">
                                <span class="block font-caption text-caption text-ink-muted">Total Terjual</span>
                                <span class="font-body-md text-body-md font-bold text-tertiary-container flex items-center justify-center gap-0.5">
                                    <span class="material-symbols-outlined text-[16px]">trending_up</span>
                                    {{ number_format($product->sold_count, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="p-2.5 rounded-lg bg-surface-container-low">
                                <span class="block font-caption text-caption text-ink-muted">Stok Saat Ini</span>
                                <span class="font-body-md text-body-md font-bold text-on-surface">{{ $product->stock }} Unit</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Produk serupa -->
        @if ($relatedProducts->isNotEmpty())
            <section class="space-y-6">
                <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Produk Serupa</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                    @foreach ($relatedProducts as $related)
                        <x-product-card :product="$related" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>

@push('scripts')
<script>
    const maxQty = Math.max({{ $product->stock }}, 1);
    let qty = 1;

    function changeQty(delta) {
        qty = Math.min(Math.max(qty + delta, 1), maxQty);
        document.getElementById('qtyCounter').textContent = qty;
        document.getElementById('btnMinus').disabled = qty <= 1;
        document.getElementById('btnPlus').disabled = qty >= maxQty;
        document.getElementById('btnAddToCart').dataset.qty = qty;
    }

    document.getElementById('btnAddToCart').dataset.qty = qty;
</script>
@endpush
@endsection
