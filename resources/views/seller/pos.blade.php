@extends('layouts.seller')

@section('title', 'Terminal Kasir POS - Portal Penjual - Lokalku')

@section('content')
@php
    $categories = $products->map(fn ($p) => $p->category?->name)->filter()->unique()->sort()->values();
@endphp
<div class="flex flex-col w-full">

    <!-- Identitas Toko & Kasir (pengganti topbar desain) -->
    <div class="w-full max-w-[1280px] mx-auto px-margin pt-space-md">
        <div class="flex flex-wrap items-center justify-between gap-space-md">
            <div class="flex items-center gap-space-md">
                <h1 class="font-headline-lg text-headline-lg text-on-surface">Terminal Kasir</h1>
                <div class="flex items-center gap-space-xs px-space-sm py-1 bg-surface-container rounded-full">
                    <span class="material-symbols-outlined text-primary text-[18px]">storefront</span>
                    <span class="font-caption text-caption text-on-surface">{{ $store->name }}</span>
                </div>
                <div class="flex items-center gap-space-xs px-space-sm py-1 bg-blue-subtle rounded-full">
                    <span class="font-code-mono text-caption text-primary font-bold">{{ $store->activation_code }}</span>
                </div>
            </div>
            <div class="flex items-center gap-space-xs px-space-sm py-1 bg-blue-subtle rounded-full">
                <span class="w-2 h-2 rounded-full bg-tertiary animate-pulse"></span>
                <span class="font-caption text-caption text-primary font-medium">Kasir · {{ auth()->user()->name }}</span>
            </div>
        </div>
    </div>

    <!-- POS Main Workspace -->
    <div class="w-full max-w-[1280px] mx-auto px-margin py-space-md">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-gutter items-start">

            <!-- LEFT PANE: 8 COLUMNS (CATALOG & SCANNER) -->
            <section class="lg:col-span-8 flex flex-col gap-space-md">

                <!-- Live Search & Barcode Input Bar -->
                <div class="relative w-full">
                    <div class="absolute inset-y-0 left-0 pl-space-md flex items-center pointer-events-none text-ink-muted">
                        <span class="material-symbols-outlined text-[22px]">search</span>
                    </div>
                    <input autocomplete="off" class="w-full h-12 pl-11 pr-24 rounded-full bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder-ink-muted focus:outline-none focus:ring-2 focus:ring-primary-container shadow-sm transition-all" id="barcode-search-input" placeholder="Cari nama produk atau ketik/scan barcode SKU (F2)..." type="text">
                    <div class="absolute inset-y-0 right-0 pr-space-xs flex items-center gap-space-xs">
                        <button class="h-9 px-space-sm bg-blue-subtle text-primary rounded-full flex items-center gap-1 font-code-mono text-[11px] font-bold hover:bg-primary-fixed transition-colors" title="Shortcut Scan F2">
                            <span class="material-symbols-outlined text-[18px]">barcode_scanner</span>
                            <span class="">F2</span>
                        </button>
                    </div>
                </div>

                <!-- Category Horizontal Filter Tabs -->
                <div class="flex items-center gap-space-xs overflow-x-auto pb-1 scrollbar-none" id="category-tabs">
                    <button class="cat-pill active flex-shrink-0 h-9 px-space-md rounded-full font-caption text-caption bg-primary-container text-on-primary font-bold shadow-sm transition-all flex items-center gap-1" data-cat="all" onclick="filterCategory(this)">
                        <span class="">Semua</span>
                        <span class="opacity-80 font-code-mono text-[11px]">({{ $products->count() }})</span>
                    </button>
                    @foreach ($categories as $category)
                        <button class="cat-pill flex-shrink-0 h-9 px-space-md rounded-full font-caption text-caption bg-surface-container-lowest text-on-surface hover:bg-surface-container transition-all flex items-center gap-1" data-cat="{{ $category }}" onclick="filterCategory(this)">
                            <span class="">{{ $category }}</span>
                            <span class="text-ink-muted font-code-mono text-[11px]">({{ $products->filter(fn ($p) => $p->category?->name === $category)->count() }})</span>
                        </button>
                    @endforeach
                </div>

                <!-- Product Grid (4 Columns Desktop / Tablet Landscape) -->
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-space-sm" id="catalog-grid">
                    @foreach ($products as $product)
                        @if ($product->stock > 0)
                            <!-- In Stock -->
                            <article class="group cursor-pointer bg-surface-card rounded-lg p-space-xs flex flex-col justify-between shadow-sm hover:shadow-md hover:bg-blue-subtle/30 transition-all select-none relative" data-category="{{ $product->category?->name }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-product-id="{{ $product->id }}" data-stock="{{ $product->stock }}" onclick="addToCart({{ $product->id }})">
                                <div class="w-full aspect-square rounded-DEFAULT overflow-hidden bg-surface-container relative">
                                    <img alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" src="{{ $product->image }}">
                                    <span class="absolute top-1.5 left-1.5 px-2 py-0.5 rounded-full font-caption text-[11px] {{ (int) $product->stock <= 5 ? 'bg-accent-yellow text-on-accent-yellow font-bold' : 'bg-tertiary-fixed text-on-tertiary-fixed font-bold' }}">{{ (int) $product->stock <= 5 ? 'Sisa ' . $product->stock : $product->stock . ' unit' }}</span>
                                    <span class="absolute bottom-1.5 right-1.5 w-6 h-6 rounded-full bg-primary-container text-on-primary flex items-center justify-center font-bold text-caption shadow hidden" data-qty-badge="{{ $product->id }}">0</span>
                                </div>
                                <div class="p-space-xs flex flex-col gap-0.5">
                                    <h3 class="font-body-md text-body-md text-on-surface font-medium line-clamp-2 leading-snug">{{ $product->name }}</h3>
                                    <p class="font-code-mono text-price-sm text-primary font-bold">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                                </div>
                            </article>
                        @else
                            <!-- Out of stock (DISABLED) -->
                            <article class="opacity-50 cursor-not-allowed bg-surface-container-high rounded-lg p-space-xs flex flex-col justify-between select-none relative pointer-events-none" data-category="{{ $product->category?->name }}" data-name="{{ $product->name }}">
                                <div class="w-full aspect-square rounded-DEFAULT overflow-hidden bg-surface-container relative">
                                    <img alt="{{ $product->name }}" class="w-full h-full object-cover grayscale" src="{{ $product->image }}">
                                    <div class="absolute inset-0 bg-inverse-surface/60 flex items-center justify-center p-2 text-center">
                                        <span class="px-2.5 py-1 rounded-full font-caption text-[12px] bg-error text-on-error font-bold">Stok Habis</span>
                                    </div>
                                </div>
                                <div class="p-space-xs flex flex-col gap-0.5">
                                    <h3 class="font-body-md text-body-md text-on-surface-variant font-medium line-clamp-2 leading-snug">{{ $product->name }}</h3>
                                    <p class="font-code-mono text-price-sm text-ink-muted">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
                                </div>
                            </article>
                        @endif
                    @endforeach
                </div>
            </section>

            <!-- RIGHT PANE: 4 COLUMNS (STICKY CART / CHECKOUT) -->
            <aside class="lg:col-span-4 w-full">
                <div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col gap-space-md sticky top-20">

                    <!-- Cart Header -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-space-xs">
                            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Transaksi Baru</h2>
                            <span class="px-space-xs py-0.5 bg-blue-subtle text-primary font-code-mono text-[12px] font-bold rounded">POS #01</span>
                        </div>
                        <time class="font-code-mono text-caption text-ink-muted" id="live-pos-clock">{{ now()->format('H.i.s') }} WIB</time>
                    </div>

                    <!-- Customer Input Box -->
                    <div class="w-full">
                        <label class="block font-caption text-caption text-on-surface-variant mb-1" for="customer-name-input">Nama Pelanggan (opsional)</label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-2.5 text-[20px] text-ink-muted">person_outline</span>
                            <input class="w-full h-11 pl-10 pr-3 rounded-lg bg-surface-container-low text-on-surface font-body-md text-body-md focus:bg-surface-card focus:outline-none focus:ring-2 focus:ring-primary-container transition-all" id="customer-name-input" placeholder="Pelanggan Walk-in" type="text">
                        </div>
                    </div>

                    <!-- Alert Banner: Dynamic Stock Limit Warning -->
                    <div class="hidden flex items-start gap-space-xs p-space-sm bg-error-container text-on-error-container rounded-lg text-caption font-caption animate-pulse" id="stock-alert-banner">
                        <span class="material-symbols-outlined text-[18px] text-error flex-shrink-0">warning</span>
                        <span id="stock-alert-text"></span>
                    </div>

                    <!-- CART ITEMS LIST -->
                    <div class="flex flex-col gap-space-sm max-h-[340px] overflow-y-auto pr-1 hidden" id="cart-items-container"></div>

                    <!-- EMPTY CART VIEW -->
                    <div class="py-10 px-4 text-center flex flex-col items-center justify-center bg-surface-container-low rounded-lg gap-space-sm" id="cart-empty-view">
                        <div class="w-16 h-16 rounded-full bg-surface-container flex items-center justify-center text-ink-muted mb-1">
                            <span class="material-symbols-outlined text-[32px]">remove_shopping_cart</span>
                        </div>
                        <p class="font-headline-sm text-body-md font-bold text-on-surface">Keranjang Masih Kosong</p>
                        <p class="font-caption text-caption text-ink-muted max-w-xs">
                            Sentuh produk di katalog sebelah kiri atau gunakan pemindai barcode untuk mencatat belanjaan.
                        </p>
                    </div>

                    <!-- SUMMARY & CALCULATION SECTION -->
                    <div class="flex flex-col gap-space-xs bg-surface-container-low p-space-md rounded-xl">
                        <div class="flex justify-between items-center text-caption font-caption text-on-surface-variant">
                            <span class="" id="subtotal-label">Subtotal (0 jenis, 0 barang)</span>
                            <span class="font-code-mono font-medium" id="subtotal-val">Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center text-caption font-caption text-on-surface-variant">
                            <span class="">Diskon Promosi Toko</span>
                            <span class="font-code-mono text-success-text font-medium">- Rp 0</span>
                        </div>
                        <div class="flex justify-between items-center pt-space-xs mt-1 border-t-0">
                            <span class="font-headline-sm text-body-md font-bold text-on-surface">Total Tagihan</span>
                            <span class="font-headline-md text-headline-md text-primary font-bold" id="grand-total-val">Rp 0</span>
                        </div>
                    </div>

                    <!-- PAYMENT METHOD SEGMENTED CONTROL -->
                    <div class="w-full flex flex-col gap-1.5">
                        <label class="font-caption text-caption text-on-surface-variant">Metode Pembayaran:</label>
                        <div class="grid grid-cols-2 p-1 bg-surface-container rounded-full gap-1">
                            <button class="h-10 rounded-full font-caption text-caption font-bold bg-primary text-on-primary shadow-sm flex items-center justify-center gap-1 transition-all" id="btn-pay-virtual" onclick="selectPayMethod('virtual')">
                                <span class="material-symbols-outlined text-[16px]">qr_code_scanner</span>
                                <span class="">Pembayaran Virtual/QRIS</span>
                            </button>
                            <button class="h-10 rounded-full font-caption text-caption text-on-surface-variant hover:text-on-surface flex items-center justify-center gap-1 transition-all" id="btn-pay-manual" onclick="selectPayMethod('manual')">
                                <span class="material-symbols-outlined text-[16px]">payments</span>
                                <span class="">Bayar Tunai</span>
                            </button>
                        </div>
                    </div>

                    <!-- PRIMARY CHECKOUT SUBMIT BUTTON -->
                    <button class="w-full h-14 rounded-full bg-inverse-surface hover:bg-on-accent-yellow active:scale-[0.98] text-surface-container-lowest font-headline-sm text-price-sm font-bold flex items-center justify-center gap-space-sm shadow-md transition-all opacity-50 pointer-events-none" id="btn-checkout-submit" onclick="openPaymentModal()">
                        <span class="material-symbols-outlined text-[22px]">shopping_bag</span>
                        <span id="btn-checkout-label" class="">Bayar Rp 0</span>
                    </button>

                    <!-- QUICK UTILITY LINKS -->
                    <div class="flex items-center justify-between text-caption font-caption pt-1 px-1">
                        <button class="text-secondary hover:underline flex items-center gap-1" onclick="clearCart()">
                            <span class="material-symbols-outlined text-[16px]">delete_sweep</span>
                            <span class="">Kosongkan Keranjang</span>
                        </button>
                        <button class="text-ink-muted hover:text-primary transition-colors flex items-center gap-1" onclick="window.location = '{{ route('seller.pos.history') }}'">
                            <span class="material-symbols-outlined text-[16px]">receipt_long</span>
                            <span class="">Cetak Struk (F9)</span>
                        </button>
                    </div>
                </div>
            </aside>
        </div>
    </div>

    <!-- Inline Error Toast -->
    <div class="hidden fixed top-20 right-4 z-[60] transition-all duration-300 w-full max-w-md bg-inverse-surface text-inverse-on-surface py-2 px-4 rounded-full shadow-lg items-center justify-between gap-2" id="pos-toast">
        <div class="flex items-center gap-2">
            <span class="material-symbols-outlined text-tertiary-fixed text-[20px]" id="pos-toast-icon">check_circle</span>
            <span class="font-body-md text-body-md text-sm" id="pos-toast-message"></span>
        </div>
        <button class="text-inverse-on-surface opacity-75 hover:opacity-100" onclick="hideToast()">
            <span class="material-symbols-outlined text-[16px]">close</span>
        </button>
    </div>

    <!-- ===== MODAL PEMBAYARAN TUNAI (MANUAL) ===== -->
    <div class="fixed inset-0 top-16 bg-on-accent-yellow/60 backdrop-blur-sm z-40 flex items-center justify-center p-gutter overflow-y-auto hidden" id="modal-cash">
        <!-- Modal Dialog Card (520px Width) -->
        <div class="w-full max-w-[520px] bg-surface-card rounded-xl shadow-2xl p-space-xl flex flex-col gap-space-lg relative my-auto animate-in fade-in zoom-in-95 duration-200" id="payment-modal-box">

            <!-- Modal Header -->
            <div class="flex items-start justify-between gap-space-md">
                <div class="flex items-center gap-space-sm">
                    <div class="w-11 h-11 rounded-full bg-blue-subtle text-primary flex items-center justify-center shadow-sm">
                        <span class="material-symbols-outlined text-[26px]" style="font-variation-settings: 'FILL' 1;">payments</span>
                    </div>
                    <div class="flex flex-col">
                        <div class="flex items-center gap-space-xs">
                            <h2 class="font-headline-md text-headline-md text-on-surface">Pembayaran Manual (Tunai)</h2>
                        </div>
                        <div class="flex items-center gap-space-xs mt-0.5">
                            <span class="font-caption text-caption text-ink-muted">Transaksi:</span>
                            <span class="font-code-mono text-caption text-primary font-bold px-1.5 py-0.5 bg-blue-subtle rounded" id="cash-cart-summary">0 barang</span>
                            <span class="w-1 h-1 rounded-full bg-outline-variant"></span>
                            <span class="font-caption text-caption text-on-surface-variant">Kasir {{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </div>
                <button class="w-9 h-9 rounded-full bg-surface-container hover:bg-surface-container-highest text-on-surface-variant hover:text-on-surface flex items-center justify-center transition-colors" onclick="closeCashModal()" title="Tutup [Esc]">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>

            <!-- Bill Overview Tile -->
            <div class="bg-blue-subtle/70 rounded-xl p-space-md flex items-center justify-between shadow-sm">
                <div class="flex flex-col">
                    <span class="font-caption text-caption uppercase tracking-wider text-on-surface-variant font-bold">Total Tagihan Pelanggan</span>
                    <span class="font-caption text-caption text-ink-muted" id="cash-bill-items">0 Item Belanjaan · Termasuk PPN Toko</span>
                </div>
                <div class="text-right">
                    <span class="font-code-mono text-price-lg text-primary tracking-tight font-bold" id="bill-amount-display">Rp 0</span>
                </div>
            </div>

            <!-- Main Input Group: Nominal Received -->
            <div class="flex flex-col gap-space-xs">
                <div class="flex items-center justify-between">
                    <label class="font-headline-sm text-body-md font-bold text-on-surface" for="cash-input-field">
                        Nominal Uang Diterima <span class="text-error">*</span>
                    </label>
                    <span class="font-caption text-caption text-ink-muted">Tekan Angka / Barcode Numpad</span>
                </div>
                <!-- Large Custom Input with 'Rp' Prefix -->
                <div class="relative flex items-center h-14 rounded-lg bg-surface-card px-space-md transition-all shadow-inner bg-surface-container-lowest" id="input-container-wrapper">
                    <span class="font-code-mono text-headline-sm font-bold text-ink-muted select-none mr-2">Rp</span>
                    <input autocomplete="off" class="w-full bg-transparent font-code-mono text-headline-lg font-bold text-on-surface focus:outline-none tracking-tight" id="cash-input-field" min="0" name="received_amount" oninput="handleCashInput(this.value)" placeholder="0" type="number"/>
                    <button class="text-ink-muted hover:text-on-surface p-1 rounded-full hover:bg-surface-container transition-colors" onclick="clearCashInput()" title="Hapus input">
                        <span class="material-symbols-outlined text-[20px]">backspace</span>
                    </button>
                </div>
                <span class="font-caption text-caption text-ink-muted">Ketik nominal tunai atau pilih pecahan kilat di bawah:</span>
            </div>

            <!-- Quick Amount Cash Chips -->
            <div class="flex flex-wrap items-center gap-space-xs" id="cash-chips"></div>

            <!-- Calculation Result Area: Dynamic Change or Alert Shortage -->
            <div class="rounded-xl p-space-md transition-all flex flex-col gap-space-xs bg-tertiary-fixed/30 shadow-sm" id="calculation-result-card">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-space-xs">
                        <span class="material-symbols-outlined text-[20px] text-tertiary" id="result-status-icon" style="font-variation-settings: 'FILL' 1;">change_circle</span>
                        <span class="font-headline-sm text-caption uppercase tracking-wider font-bold text-tertiary" id="result-label">Kembalian Pelanggan</span>
                    </div>
                    <span class="px-space-sm py-0.5 rounded-full text-[11px] font-code-mono font-bold bg-tertiary-container text-on-tertiary-container" id="result-badge">LUNAS</span>
                </div>
                <div class="flex items-baseline justify-between pt-1">
                    <div class="font-code-mono text-price-lg font-bold text-success-text tracking-tight" id="result-amount-display">
                        Rp 0
                    </div>
                    <div class="font-code-mono text-caption text-ink-muted text-right" id="breakdown-note">
                        Diterima: Rp 0 · Tagihan: Rp 0
                    </div>
                </div>
                <!-- Shortage Warning Banner (Hidden by Default) -->
                <div class="hidden flex items-center gap-space-xs p-space-sm bg-error-container text-on-error-container rounded-lg text-caption font-body-md font-medium mt-1" id="shortage-warning-box">
                    <span class="material-symbols-outlined text-[18px] text-error" style="font-variation-settings: 'FILL' 1;">error</span>
                    <span id="shortage-warning-text"></span>
                </div>
            </div>

            <!-- Action Button Group -->
            <div class="flex flex-col sm:flex-row items-center gap-space-md pt-space-xs">
                <button class="w-full sm:flex-1 h-12 rounded-full bg-on-accent-yellow hover:bg-black text-on-primary font-headline-sm text-body-md font-bold flex items-center justify-center gap-space-xs shadow-md active:scale-[0.99] transition-all opacity-50 pointer-events-none" id="btn-confirm-payment" onclick="confirmCashPayment()">
                    <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">receipt_long</span>
                    <span>Selesaikan Transaksi</span>
                </button>
                <button class="w-full sm:w-auto px-space-lg h-12 rounded-full bg-surface-container hover:bg-blue-subtle text-primary font-body-md font-bold flex items-center justify-center gap-space-xs transition-colors" onclick="switchToVirtual()">
                    <span class="material-symbols-outlined text-[18px]">swap_horiz</span>
                    <span>Ganti Metode</span>
                </button>
            </div>

            <!-- POS Hardware Shortcuts Footnote -->
            <div class="pt-space-xs flex items-center justify-between text-caption text-ink-muted">
                <div class="flex items-center gap-1.5 font-code-mono text-[11px]">
                    <span class="px-1 py-0.5 bg-surface-container-high rounded text-on-surface font-bold">[Enter]</span>
                    <span>Lunas &amp; Buka Laci Kasir</span>
                    <span class="text-outline-variant">·</span>
                    <span class="px-1 py-0.5 bg-surface-container-high rounded text-on-surface font-bold">[Esc]</span>
                    <span>Batal</span>
                </div>
                <div class="flex items-center gap-1 font-caption text-[11px] text-tertiary-container font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>
                    <span>Printer Kasir Siap</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== MODAL PEMBAYARAN VIRTUAL (QRIS / VA) ===== -->
    <div class="fixed inset-0 top-16 z-40 bg-on-background/70 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto hidden" id="modal-virtual">
        <!-- MAIN MODAL (520px radius 12px Level 3 shadow) -->
        <div class="relative z-50 w-full max-w-[520px] bg-surface-container-lowest rounded-xl shadow-2xl overflow-hidden p-6 sm:p-7 flex flex-col gap-5 my-auto animate-in fade-in zoom-in duration-150" id="payment-modal-card">

            <!-- STATE A: DEFAULT / DYNAMIC QRIS OR VA VIEW -->
            <div class="flex flex-col gap-5" id="view-active-flow">

                <!-- Header Row -->
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <!-- Badge QRIS partner networks -->
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-subtle text-primary rounded-full font-caption text-caption font-bold">
                            <span class="material-symbols-outlined text-[16px]">qr_code_2</span>
                            <span class="">QRIS &amp; E-Wallet Terintegrasi</span>
                        </div>
                        <button class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-surface-container hover:bg-error-container text-ink-muted hover:text-on-error-container transition-colors" onclick="closeVirtualModal()" title="Tutup atau Batalkan">
                            <span class="material-symbols-outlined text-[20px]">close</span>
                        </button>
                    </div>
                    <div class="flex flex-wrap items-baseline justify-between gap-2 pt-1">
                        <div>
                            <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Pembayaran Virtual</h2>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="font-caption text-caption text-ink-muted">No. Pesanan:</span>
                                <span class="font-code-mono text-[12px] bg-surface-container text-on-surface font-bold px-2 py-0.5 rounded" id="virtual-order-code">-</span>
                            </div>
                        </div>
                        <!-- Quick Help -->
                        <button class="inline-flex items-center gap-1 text-primary hover:text-blue-hover font-caption text-caption font-medium">
                            <span class="material-symbols-outlined text-[16px]">support_agent</span>
                            <span class="">Bantuan Kasir</span>
                        </button>
                    </div>
                </div>

                <!-- Total Tagihan Banner -->
                <div class="bg-surface-container-low rounded-xl p-4 flex items-center justify-between shadow-inner">
                    <div class="flex flex-col">
                        <span class="font-caption text-caption text-ink-muted font-medium">Total Tagihan Pelanggan</span>
                        <span class="font-price-lg text-price-lg text-primary font-bold tracking-tight" id="virtual-total-display">Rp 0</span>
                    </div>
                    <div class="text-right">
                        <span class="px-2.5 py-1 rounded-full bg-accent-yellow/20 text-on-accent-yellow font-caption text-caption font-bold inline-flex items-center gap-1" id="virtual-status-badge">
                            <span class="w-2 h-2 rounded-full bg-accent-yellow animate-pulse"></span>
                            <span id="virtual-status-text">Menunggu Bayar</span>
                        </span>
                    </div>
                </div>

                <!-- Peringatan Midtrans belum dikonfigurasi -->
                <div class="hidden flex items-start gap-2.5 p-3 rounded-lg bg-error-container text-on-error-container" id="virtual-not-configured">
                    <span class="material-symbols-outlined text-error text-[18px] mt-0.5">error</span>
                    <p class="font-caption text-caption leading-relaxed">Pembayaran Midtrans belum dikonfigurasi (.env). Atur MIDTRANS_SERVER_KEY &amp; MIDTRANS_CLIENT_KEY lalu buat transaksi baru.</p>
                </div>

                <!-- Method Switcher Tabs (Segmented Pill) -->
                <div class="grid grid-cols-2 p-1 bg-surface-container rounded-full gap-1">
                    <button class="tab-trigger inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-full font-caption text-caption font-bold transition-all bg-primary text-on-primary shadow-sm min-h-[40px]" id="tab-btn-qris" onclick="switchTab('qris')">
                        <span class="material-symbols-outlined text-[18px]">qr_code_scanner</span>
                        <span class="">QRIS Dinamis (Utama)</span>
                    </button>
                    <button class="tab-trigger inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-full font-caption text-caption font-medium text-on-surface hover:bg-surface-container-highest transition-all min-h-[40px]" id="tab-btn-va" onclick="switchTab('va')">
                        <span class="material-symbols-outlined text-[18px]">account_balance</span>
                        <span class="">Virtual Account Bank</span>
                    </button>
                </div>

                <!-- TAB 1 CONTENT: QRIS DINAMIS -->
                <div class="tab-panel flex flex-col items-center gap-3" id="panel-qris">
                    <div class="w-full bg-surface-container-lowest rounded-xl p-5 flex flex-col items-center shadow-sm">
                        <!-- QRIS Brand Header Bar -->
                        <div class="w-full max-w-[260px] flex items-center justify-between pb-2 mb-2 bg-surface-container-lowest">
                            <div class="flex items-center gap-1">
                                <span class="font-display text-[15px] font-black text-secondary tracking-tighter">QRIS</span>
                                <span class="text-[10px] text-ink-muted font-medium ml-1">Standar Nasional</span>
                            </div>
                            <!-- Supported wallets pill -->
                            <span class="text-[9px] font-code-mono font-bold bg-surface-container px-1.5 py-0.5 rounded text-ink-muted">Gopay · OVO · Dana · BCA</span>
                        </div>
                        <!-- High-Fidelity Clean 240x240 QR Canvas Graphic -->
                        <div class="relative w-[240px] h-[240px] bg-white p-3 rounded-lg shadow-sm flex flex-col items-center justify-between">
                            <!-- Real SVG QR Matrix Simulation -->
                            <svg class="w-full h-full text-on-surface" fill="currentColor" viewBox="0 0 100 100">
                                <rect class="text-on-surface" height="28" rx="2" width="28" x="0" y="0"></rect>
                                <rect fill="#FFFFFF" height="20" rx="1" width="20" x="4" y="4"></rect>
                                <rect class="text-on-surface" height="12" rx="1" width="12" x="8" y="8"></rect>
                                <rect class="text-on-surface" height="28" rx="2" width="28" x="72" y="0"></rect>
                                <rect fill="#FFFFFF" height="20" rx="1" width="20" x="76" y="4"></rect>
                                <rect class="text-on-surface" height="12" rx="1" width="12" x="80" y="8"></rect>
                                <rect class="text-on-surface" height="28" rx="2" width="28" x="0" y="72"></rect>
                                <rect fill="#FFFFFF" height="20" rx="1" width="20" x="4" y="76"></rect>
                                <rect class="text-on-surface" height="12" rx="1" width="12" x="8" y="80"></rect>
                                <rect height="4" width="4" x="34" y="2"></rect>
                                <rect height="4" width="4" x="42" y="2"></rect>
                                <rect height="4" width="4" x="54" y="2"></rect>
                                <rect height="4" width="4" x="62" y="2"></rect>
                                <rect height="4" width="8" x="38" y="8"></rect>
                                <rect height="4" width="4" x="50" y="8"></rect>
                                <rect height="4" width="8" x="58" y="8"></rect>
                                <rect height="6" width="6" x="34" y="18"></rect>
                                <rect height="8" width="4" x="46" y="16"></rect>
                                <rect height="4" width="6" x="56" y="18"></rect>
                                <rect height="4" width="6" x="2" y="34"></rect>
                                <rect height="4" width="8" x="12" y="34"></rect>
                                <rect height="4" width="4" x="24" y="34"></rect>
                                <rect height="6" width="4" x="34" y="30"></rect>
                                <rect height="4" width="12" x="42" y="34"></rect>
                                <rect height="8" width="4" x="58" y="30"></rect>
                                <rect height="4" width="8" x="68" y="34"></rect>
                                <rect height="4" width="6" x="82" y="34"></rect>
                                <rect height="4" width="6" x="92" y="34"></rect>
                                <rect height="8" width="4" x="6" y="42"></rect>
                                <rect height="4" width="8" x="16" y="44"></rect>
                                <rect height="6" width="6" x="28" y="42"></rect>
                                <rect height="4" width="6" x="68" y="42"></rect>
                                <rect height="8" width="4" x="80" y="42"></rect>
                                <rect height="6" width="6" x="90" y="44"></rect>
                                <rect fill="#FFFFFF" height="24" rx="4" width="24" x="38" y="38"></rect>
                                <rect class="text-primary" height="20" rx="3" width="20" x="40" y="40"></rect>
                                <text fill="#FFFFFF" font-family="'DM Sans', sans-serif" font-size="9" font-weight="900" text-anchor="middle" x="50" y="54">L</text>
                                <rect height="4" width="6" x="2" y="56"></rect>
                                <rect height="6" width="4" x="14" y="58"></rect>
                                <rect height="4" width="8" x="22" y="56"></rect>
                                <rect height="4" width="8" x="66" y="56"></rect>
                                <rect height="4" width="6" x="78" y="58"></rect>
                                <rect height="6" width="6" x="90" y="56"></rect>
                                <rect height="6" width="4" x="34" y="66"></rect>
                                <rect height="4" width="10" x="44" y="68"></rect>
                                <rect height="6" width="6" x="58" y="66"></rect>
                                <rect height="6" width="6" x="34" y="76"></rect>
                                <rect height="4" width="6" x="46" y="74"></rect>
                                <rect height="4" width="8" x="56" y="78"></rect>
                                <rect height="6" width="6" x="68" y="74"></rect>
                                <rect height="4" width="8" x="80" y="76"></rect>
                                <rect height="8" width="6" x="92" y="74"></rect>
                                <rect height="4" width="8" x="34" y="86"></rect>
                                <rect height="8" width="4" x="46" y="84"></rect>
                                <rect height="6" width="8" x="54" y="86"></rect>
                                <rect height="4" width="6" x="68" y="86"></rect>
                                <rect height="6" width="4" x="80" y="86"></rect>
                                <rect height="4" width="6" x="90" y="86"></rect>
                            </svg>
                        </div>
                        <!-- Footer Meta for QRIS -->
                        <div class="w-full text-center mt-3 pt-2 bg-surface-container-lowest flex flex-col gap-0.5">
                            <p class="font-code-mono text-[11px] text-on-surface font-bold">NMID: {{ $store->activation_code }} - {{ $store->name }}</p>
                            <p class="font-caption text-caption text-ink-muted">A/N: Kasir {{ auth()->user()->name }}</p>
                        </div>
                    </div>
                    <p class="font-body-md text-body-md text-ink-muted text-center max-w-[420px]">
                        Minta pelanggan memindai QRIS di atas dengan aplikasi mobile banking atau dompet digital apa pun.
                    </p>
                </div>

                <!-- TAB 2 CONTENT: VIRTUAL ACCOUNT -->
                <div class="tab-panel hidden flex flex-col gap-4" id="panel-va">
                    <!-- Bank Selector -->
                    <div class="flex flex-col gap-1.5">
                        <label class="font-caption text-caption text-ink-muted font-medium">Pilih Bank Virtual Account:</label>
                        <div class="grid grid-cols-4 gap-2">
                            <button class="bank-pill flex flex-col items-center justify-center p-2.5 rounded-lg bg-blue-subtle text-primary font-bold text-caption font-code-mono min-h-[44px]" id="bank-bca" onclick="selectBank('BCA')">
                                <span class="">BCA</span>
                                <span class="text-[10px] font-sans font-normal text-primary">Otomatis</span>
                            </button>
                            <button class="bank-pill flex flex-col items-center justify-center p-2.5 rounded-lg bg-surface-container text-on-surface font-bold text-caption font-code-mono min-h-[44px] hover:bg-surface-container-high" id="bank-mandiri" onclick="selectBank('Mandiri')">
                                <span class="">Mandiri</span>
                                <span class="text-[10px] font-sans font-normal text-ink-muted">Livin</span>
                            </button>
                            <button class="bank-pill flex flex-col items-center justify-center p-2.5 rounded-lg bg-surface-container text-on-surface font-bold text-caption font-code-mono min-h-[44px] hover:bg-surface-container-high" id="bank-bri" onclick="selectBank('BRI')">
                                <span class="">BRI</span>
                                <span class="text-[10px] font-sans font-normal text-ink-muted">BRIMO</span>
                            </button>
                            <button class="bank-pill flex flex-col items-center justify-center p-2.5 rounded-lg bg-surface-container text-on-surface font-bold text-caption font-code-mono min-h-[44px] hover:bg-surface-container-high" id="bank-bni" onclick="selectBank('BNI')">
                                <span class="">BNI</span>
                                <span class="text-[10px] font-sans font-normal text-ink-muted">Mobile</span>
                            </button>
                        </div>
                    </div>
                    <!-- Large Copyable VA Box -->
                    <div class="bg-surface-container-low rounded-xl p-4 flex flex-col gap-2 shadow-inner">
                        <div class="flex items-center justify-between">
                            <span class="font-caption text-caption text-ink-muted font-medium" id="va-bank-label">Nomor BCA Virtual Account:</span>
                            <span class="font-caption text-caption text-tertiary font-bold flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-tertiary"></span>
                                <span>Siap Menerima</span>
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-2 bg-surface-container-lowest p-3 rounded-lg shadow-sm">
                            <span class="font-code-mono text-[18px] sm:text-[20px] font-bold text-on-surface tracking-wider" id="va-number">8277 0812 3456 7890</span>
                            <button class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary text-on-primary hover:bg-blue-hover rounded-full font-caption text-caption font-bold shadow-sm transition-all min-h-[36px]" id="copy-btn" onclick="copyVANumber()">
                                <span class="material-symbols-outlined text-[16px]">content_copy</span>
                                <span id="copy-label" class="">Salin</span>
                            </button>
                        </div>
                        <p class="font-caption text-caption text-ink-muted mt-1">
                            Nama Rekening: <strong class="text-on-surface">{{ strtoupper($store->name) }}</strong>
                        </p>
                    </div>
                    <!-- Instruction Callout -->
                    <div class="flex items-start gap-2.5 p-3 rounded-lg bg-blue-subtle/60 text-on-surface">
                        <span class="material-symbols-outlined text-primary text-[18px] mt-0.5">verified_user</span>
                        <p class="font-caption text-caption text-on-surface-variant leading-relaxed">
                            Pembayaran diverifikasi secara otomatis dalam <strong class="text-primary font-bold">5–10 detik</strong> setelah pelanggan mentransfer sesuai nominal persis <strong class="text-primary font-bold" id="va-exact-amount">Rp 0</strong>.
                        </p>
                    </div>
                </div>

                <!-- REAL-TIME STATUS BAR & LIVE COUNTDOWN TIMER -->
                <div class="bg-surface-container-low rounded-xl p-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-inner">
                    <div class="flex items-center gap-2.5">
                        <!-- Animated blue spinner -->
                        <div class="w-5 h-5 rounded-full border-2 border-primary/20 border-t-primary animate-spin"></div>
                        <div class="flex flex-col">
                            <span class="font-body-md text-body-md text-on-surface font-medium leading-tight">Menunggu pembayaran pelanggan...</span>
                            <span class="font-caption text-[11px] text-ink-muted">Sinkronisasi otomatis aktif</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between sm:justify-end gap-2 bg-surface-container-lowest px-3 py-1.5 rounded-lg shadow-sm">
                        <span class="font-caption text-caption text-ink-muted font-medium">Sisa Waktu:</span>
                        <span class="font-code-mono font-bold text-error text-[16px] tracking-wide" id="countdown-timer">15:00</span>
                    </div>
                </div>

                <!-- Modal Action Footer Buttons (min height 44px) -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 pt-2">
                    <!-- Urgent Outline Button -->
                    <button class="flex-1 min-h-[44px] px-5 py-2.5 rounded-full bg-surface-container-lowest hover:bg-error-container text-error hover:text-on-error-container font-headline-sm text-[14px] font-bold shadow-sm transition-all flex items-center justify-center gap-1.5" onclick="closeVirtualModal()">
                        <span class="material-symbols-outlined text-[18px]">cancel</span>
                        <span class="">Batalkan Transaksi</span>
                    </button>
                    <!-- Secondary Switch Method Button -->
                    <button class="flex-1 min-h-[44px] px-5 py-2.5 rounded-full bg-blue-subtle text-primary hover:bg-primary hover:text-on-primary font-headline-sm text-[14px] font-bold shadow-sm transition-all flex items-center justify-center gap-1.5" onclick="switchToCash()">
                        <span class="material-symbols-outlined text-[18px]">swap_horiz</span>
                        <span class="">Ganti Metode</span>
                    </button>
                </div>

                <!-- Secondary Print Key option -->
                <div class="flex items-center justify-between pt-1">
                    <button class="inline-flex items-center gap-1.5 text-ink-muted hover:text-on-surface font-caption text-caption font-medium" onclick="window.print()">
                        <span class="material-symbols-outlined text-[16px]">print</span>
                        <span class="">Cetak Kode Bayar Pelanggan (F8)</span>
                    </button>
                    <span class="font-caption text-[11px] text-ink-muted">Tekan <kbd class="px-1.5 py-0.5 bg-surface-container font-code-mono rounded text-[10px]">Esc</kbd> untuk tutup</span>
                </div>
            </div>

            <!-- STATE B: OVERLAY SUCCESS CARD -->
            <div class="hidden flex-col items-center text-center py-4 gap-5 animate-in fade-in duration-200" id="view-success">
                <div class="w-20 h-20 rounded-full bg-tertiary-fixed flex items-center justify-center shadow-lg">
                    <span class="material-symbols-outlined text-tertiary-container text-[48px]" style="font-variation-settings: &quot;FILL&quot; 1;">check_circle</span>
                </div>
                <div class="flex flex-col gap-1 max-w-[380px]">
                    <span class="px-3 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed-variant font-caption text-caption font-bold inline-block mx-auto mb-1">
                        Transaksi Berhasil Terverifikasi
                    </span>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Pembayaran Diterima!</h2>
                    <p class="font-price-lg text-price-lg text-tertiary font-bold tracking-tight" id="success-amount-line">Rp 0 LUNAS</p>
                    <p class="font-caption text-caption text-ink-muted mt-1">
                        Metode: <strong class="text-on-surface">QRIS / Virtual Account (Midtrans)</strong><br>
                        No. Pesanan: <span class="font-code-mono text-on-surface font-bold" id="success-order-code">-</span>
                    </p>
                </div>
                <!-- Success Struk Details Box -->
                <div class="w-full bg-surface-container-low rounded-xl p-4 text-left flex flex-col gap-2 font-caption text-caption">
                    <div class="flex justify-between">
                        <span class="text-ink-muted">Waktu Bayar:</span>
                        <span class="font-code-mono font-medium text-on-surface" id="success-paid-at">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ink-muted">Kasir Bertugas:</span>
                        <span class="text-on-surface font-medium">{{ auth()->user()->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ink-muted">Penerima Dana:</span>
                        <span class="text-on-surface font-medium">{{ $store->name }}</span>
                    </div>
                </div>
                <!-- Action Pills -->
                <div class="w-full flex flex-col sm:flex-row gap-3">
                    <button class="flex-1 min-h-[44px] bg-primary text-on-primary hover:bg-blue-hover rounded-full font-headline-sm text-[14px] font-bold shadow-md transition-all flex items-center justify-center gap-2" onclick="window.print()">
                        <span class="material-symbols-outlined text-[20px]">print</span>
                        <span class="">Cetak Struk Transaksi (F11)</span>
                    </button>
                    <button class="flex-1 min-h-[44px] bg-surface-container hover:bg-surface-container-highest text-on-surface rounded-full font-headline-sm text-[14px] font-bold shadow-sm transition-all flex items-center justify-center gap-2" onclick="closeVirtualModal()">
                        <span class="material-symbols-outlined text-[20px]">done_all</span>
                        <span class="">Selesai &amp; Buka Baru (F10)</span>
                    </button>
                </div>
            </div>

            <!-- STATE C: OVERLAY EXPIRED CARD -->
            <div class="hidden flex-col items-center text-center py-4 gap-5 animate-in fade-in duration-200" id="view-expired">
                <div class="w-20 h-20 rounded-full bg-error-container flex items-center justify-center shadow-lg">
                    <span class="material-symbols-outlined text-error text-[48px]">timer_off</span>
                </div>
                <div class="flex flex-col gap-1 max-w-[380px]">
                    <span class="px-3 py-1 rounded-full bg-error-container text-on-error-container font-caption text-caption font-bold inline-block mx-auto mb-1">
                        Status: Batas Waktu Terlampaui
                    </span>
                    <h2 class="font-headline-lg text-headline-lg text-error font-bold">Waktu Pembayaran Habis</h2>
                    <p class="font-body-md text-body-md text-ink-muted leading-relaxed">
                        Kode QRIS dinamis tidak lagi valid untuk transaksi <strong class="text-on-surface" id="expired-order-code">-</strong> demi keamanan saldo kasir.
                    </p>
                </div>
                <div class="w-full bg-error-container/40 p-4 rounded-xl flex items-center gap-3 text-left">
                    <span class="material-symbols-outlined text-error text-[24px]">info</span>
                    <p class="font-caption text-caption text-on-surface">
                        Jangan menerima pembayaran jika pelanggan mentransfer setelah sesi kedaluwarsa. Silakan refresh kode bayar.
                    </p>
                </div>
                <!-- Action Pills -->
                <div class="w-full flex flex-col sm:flex-row gap-3">
                    <button class="flex-1 min-h-[44px] bg-primary text-on-primary hover:bg-blue-hover rounded-full font-headline-sm text-[14px] font-bold shadow-md transition-all flex items-center justify-center gap-2" onclick="closeVirtualModal()">
                        <span class="material-symbols-outlined text-[20px]">refresh</span>
                        <span class="">Buat Ulang Kode QRIS</span>
                    </button>
                    <button class="flex-1 min-h-[44px] bg-surface-container hover:bg-surface-container-highest text-on-surface rounded-full font-headline-sm text-[14px] font-bold shadow-sm transition-all flex items-center justify-center gap-2" onclick="closeVirtualModal()">
                        <span class="">Kembali ke Keranjang</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // ===== Konfigurasi dari server =====
    const MIDTRANS_SNAP_URL = "{{ config('midtrans.is_production') ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com' }}/snap/snap.js";
    const MIDTRANS_CLIENT_KEY = "{{ config('midtrans.client_key') }}";
    const POS_HISTORY_URL = "{{ route('seller.pos.history') }}";

    // ===== State =====
    const cart = new Map(); // product_id -> { id, name, price, stock, qty }
    let selectedPayMethod = 'virtual';
    let cashReceived = 0;
    let cashTotal = 0;
    let pollTimer = null;
    let countdownTimer = null;
    let snapLoaded = false;
    let activeOrderCode = null;

    const rupiah = (n) => 'Rp ' + Math.round(n).toLocaleString('id-ID');
    const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

    // ===== Jam live =====
    function updateClock() {
        const el = document.getElementById('live-pos-clock');
        if (el) el.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }).replaceAll(':', '.') + ' WIB';
    }
    setInterval(updateClock, 1000);

    // ===== Toast =====
    let toastTimeout = null;
    function showToast(message, isError = false) {
        const toast = document.getElementById('pos-toast');
        const icon = document.getElementById('pos-toast-icon');
        const msg = document.getElementById('pos-toast-message');
        if (!toast) return;
        msg.textContent = message;
        icon.textContent = isError ? 'error' : 'check_circle';
        toast.classList.toggle('bg-error-container', isError);
        toast.classList.toggle('text-on-error-container', isError);
        toast.classList.toggle('bg-inverse-surface', !isError);
        toast.classList.toggle('text-inverse-on-surface', !isError);
        toast.classList.remove('hidden');
        toast.classList.add('flex');
        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(hideToast, 4000);
    }
    function hideToast() {
        const toast = document.getElementById('pos-toast');
        if (toast) { toast.classList.add('hidden'); toast.classList.remove('flex'); }
    }

    // ===== Katalog: pencarian & kategori =====
    let currentCategory = 'all';
    function filterCategory(btn) {
        currentCategory = btn.dataset.cat || 'all';
        document.querySelectorAll('.cat-pill').forEach(el => {
            el.className = 'cat-pill flex-shrink-0 h-9 px-space-md rounded-full font-caption text-caption bg-surface-container-lowest text-on-surface hover:bg-surface-container transition-all flex items-center gap-1';
        });
        btn.className = 'cat-pill active flex-shrink-0 h-9 px-space-md rounded-full font-caption text-caption bg-primary-container text-on-primary font-bold shadow-sm transition-all flex items-center gap-1';
        applyCatalogFilter();
    }
    function applyCatalogFilter() {
        const query = document.getElementById('barcode-search-input').value.toLowerCase().trim();
        document.querySelectorAll('#catalog-grid article').forEach(card => {
            const matchCat = currentCategory === 'all' || (card.dataset.category || '') === currentCategory;
            const matchQuery = !query || (card.dataset.name || '').toLowerCase().includes(query);
            card.style.display = matchCat && matchQuery ? '' : 'none';
        });
    }
    document.getElementById('barcode-search-input').addEventListener('input', () => applyCatalogFilter());

    // ===== Keranjang kasir =====
    function getProductCard(id) {
        return document.querySelector('#catalog-grid article[data-product-id="' + id + '"]');
    }
    function addToCart(id) {
        const card = getProductCard(id);
        if (!card) return;
        const stock = parseInt(card.dataset.stock, 10);
        const item = cart.get(id) || { id: Number(id), name: card.dataset.name, price: parseFloat(card.dataset.price), stock: stock, qty: 0 };
        if (item.qty + 1 > stock) {
            showStockAlert(item.name, stock);
            return;
        }
        hideStockAlert();
        item.qty += 1;
        cart.set(id, item);
        renderCart();
    }
    function showStockAlert(name, stock) {
        const banner = document.getElementById('stock-alert-banner');
        document.getElementById('stock-alert-text').textContent = '⚠️ Stok ' + name + ' tersisa ' + stock + ' - kuantitas dibatasi ke batas maksimal ketersediaan.';
        banner.classList.remove('hidden');
        banner.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    function hideStockAlert() {
        const banner = document.getElementById('stock-alert-banner');
        if (banner) banner.classList.add('hidden');
    }
    function updateQty(id, delta) {
        const item = cart.get(id) ?? cart.get(Number(id));
        if (!item) return;
        item.qty += delta;
        if (item.qty <= 0) { cart.delete(item.id); }
        renderCart();
    }
    function deleteItem(id) {
        cart.delete(String(id)); cart.delete(Number(id));
        renderCart();
    }
    function clearCart() {
        cart.clear();
        renderCart();
    }
    function cartTotals() {
        let kinds = 0, pieces = 0, total = 0;
        cart.forEach(item => { kinds += 1; pieces += item.qty; total += item.qty * item.price; });
        return { kinds, pieces, total };
    }
    function renderCart() {
        const container = document.getElementById('cart-items-container');
        const emptyView = document.getElementById('cart-empty-view');
        const checkoutBtn = document.getElementById('btn-checkout-submit');
        container.innerHTML = '';

        cart.forEach(item => {
            const row = document.createElement('div');
            row.className = 'flex items-center justify-between p-space-xs bg-surface-container-lowest rounded-lg shadow-sm gap-2';
            row.innerHTML = `
                <div class="flex-1 min-w-0">
                    <p class="font-body-md text-body-md text-on-surface font-bold truncate">${esc(item.name)}</p>
                    <p class="font-code-mono text-[12px] text-ink-muted">@ ${rupiah(item.price)}</p>
                </div>
                <div class="flex items-center bg-surface-container rounded-full p-0.5">
                    <button aria-label="Kurangi kuantitas" class="w-11 h-11 rounded-full flex items-center justify-center text-on-surface hover:bg-surface-container-highest active:scale-95 transition-transform" onclick="updateQty(${item.id}, -1)">
                        <span class="material-symbols-outlined text-[18px]">remove</span>
                    </button>
                    <span class="font-code-mono text-body-md font-bold px-2 w-7 text-center" data-qty>${item.qty}</span>
                    <button aria-label="Tambah kuantitas" class="w-11 h-11 rounded-full flex items-center justify-center text-on-surface hover:bg-surface-container-highest active:scale-95 transition-transform" onclick="updateQty(${item.id}, 1)">
                        <span class="material-symbols-outlined text-[18px]">add</span>
                    </button>
                </div>
                <div class="text-right min-w-[76px]">
                    <p class="font-code-mono text-price-sm text-on-surface font-bold" data-line-total>${rupiah(item.qty * item.price)}</p>
                </div>
                <button class="w-9 h-9 rounded-full flex items-center justify-center text-ink-muted hover:text-error hover:bg-error-container/40 transition-colors" title="Hapus Item" onclick="deleteItem(${item.id})">
                    <span class="material-symbols-outlined text-[18px]">delete_outline</span>
                </button>`;
            container.appendChild(row);
        });

        // Badge kuantitas pada kartu produk
        document.querySelectorAll('#catalog-grid article[data-product-id]').forEach(card => {
            const badge = card.querySelector('[data-qty-badge]');
            if (!badge) return;
            const item = cart.get(card.dataset.productId) || cart.get(Number(card.dataset.productId));
            const qty = item ? item.qty : 0;
            badge.textContent = qty;
            badge.classList.toggle('hidden', qty === 0);
        });

        const { kinds, pieces, total } = cartTotals();
        const empty = cart.size === 0;
        container.classList.toggle('hidden', empty);
        emptyView.classList.toggle('hidden', !empty);
        document.getElementById('subtotal-label').textContent = 'Subtotal (' + kinds + ' jenis, ' + pieces + ' barang)';
        document.getElementById('subtotal-val').textContent = rupiah(total);
        document.getElementById('grand-total-val').textContent = rupiah(total);
        document.getElementById('btn-checkout-label').textContent = empty ? 'Keranjang Kosong' : 'Bayar ' + rupiah(total);
        checkoutBtn.classList.toggle('opacity-50', empty);
        checkoutBtn.classList.toggle('pointer-events-none', empty);
    }

    // ===== Metode pembayaran =====
    function selectPayMethod(method) {
        selectedPayMethod = method === 'manual' ? 'manual' : 'virtual';
        const virtualBtn = document.getElementById('btn-pay-virtual');
        const manualBtn = document.getElementById('btn-pay-manual');
        if (selectedPayMethod === 'virtual') {
            virtualBtn.className = 'h-10 rounded-full font-caption text-caption font-bold bg-primary text-on-primary shadow-sm flex items-center justify-center gap-1 transition-all';
            manualBtn.className = 'h-10 rounded-full font-caption text-caption text-on-surface-variant hover:text-on-surface flex items-center justify-center gap-1 transition-all';
        } else {
            manualBtn.className = 'h-10 rounded-full font-caption text-caption font-bold bg-primary text-on-primary shadow-sm flex items-center justify-center gap-1 transition-all';
            virtualBtn.className = 'h-10 rounded-full font-caption text-caption text-on-surface-variant hover:text-on-surface flex items-center justify-center gap-1 transition-all';
        }
    }

    // ===== Modal tunai =====
    function openCashModal() {
        const { kinds, pieces, total } = cartTotals();
        cashTotal = total;
        cashReceived = 0;
        document.getElementById('cash-input-field').value = '';
        document.getElementById('cash-cart-summary').textContent = pieces + ' barang';
        document.getElementById('cash-bill-items').textContent = kinds + ' Item Belanjaan · Termasuk PPN Toko';
        document.getElementById('bill-amount-display').textContent = rupiah(total);
        renderCashChips();
        recalcCash();
        document.getElementById('modal-cash').classList.remove('hidden');
    }
    function closeCashModal() {
        document.getElementById('modal-cash').classList.add('hidden');
    }
    function switchToVirtual() {
        closeCashModal();
        startVirtualCheckout();
    }
    function switchToCash() {
        stopPolling();
        closeVirtualModal();
        openCashModal();
    }
    function renderCashChips() {
        const wrap = document.getElementById('cash-chips');
        wrap.innerHTML = '';
        if (cashTotal <= 0) return;
        const pasClass = 'chip-item px-space-md py-1.5 rounded-full text-caption font-code-mono font-bold transition-all bg-primary-container text-on-primary-container shadow-sm';
        const normalClass = 'chip-item px-space-md py-1.5 rounded-full text-caption font-code-mono font-bold transition-all bg-surface-container hover:bg-surface-container-high text-on-surface shadow-sm';
        const steps = [cashTotal, Math.ceil(cashTotal / 5000) * 5000, Math.ceil(cashTotal / 10000) * 10000, Math.ceil(cashTotal / 50000) * 50000, Math.ceil(cashTotal / 100000) * 100000];
        [...new Set(steps)].forEach((amount, i) => {
            const btn = document.createElement('button');
            btn.className = i === 0 ? pasClass : normalClass;
            btn.textContent = i === 0 ? 'Uang Pas (' + rupiah(amount) + ')' : rupiah(amount);
            btn.onclick = () => applyPresetChip(amount);
            wrap.appendChild(btn);
        });
    }
    function applyPresetChip(amount) {
        const input = document.getElementById('cash-input-field');
        input.value = amount;
        handleCashInput(input.value);
    }
    function clearCashInput() {
        const input = document.getElementById('cash-input-field');
        input.value = '';
        handleCashInput(input.value);
        input.focus();
    }
    function recalcCash() {
        const received = cashReceived;
        const change = received - cashTotal;
        const enough = received >= cashTotal && received > 0;
        const box = document.getElementById('shortage-warning-box');
        const amountEl = document.getElementById('result-amount-display');
        const badge = document.getElementById('result-badge');
        const confirmBtn = document.getElementById('btn-confirm-payment');

        document.getElementById('breakdown-note').textContent = 'Diterima: ' + rupiah(received) + ' · Tagihan: ' + rupiah(cashTotal);
        box.classList.toggle('hidden', enough || received <= 0);

        if (received > 0 && !enough) {
            document.getElementById('shortage-warning-text').textContent = '⚠️ Nominal kurang ' + rupiah(cashTotal - received) + ' — Belum mencukupi total tagihan';
            amountEl.textContent = 'Kurang ' + rupiah(cashTotal - received);
            amountEl.className = 'font-code-mono text-price-lg font-bold text-error tracking-tight';
            badge.textContent = 'KURANG';
            badge.className = 'px-space-sm py-0.5 rounded-full text-[11px] font-code-mono font-bold bg-error text-on-error';
        } else {
            amountEl.textContent = rupiah(Math.max(change, 0));
            amountEl.className = 'font-code-mono text-price-lg font-bold text-success-text tracking-tight';
            badge.textContent = 'LUNAS';
            badge.className = 'px-space-sm py-0.5 rounded-full text-[11px] font-code-mono font-bold bg-tertiary-container text-on-tertiary-container';
        }
        confirmBtn.classList.toggle('opacity-50', !enough);
        confirmBtn.classList.toggle('pointer-events-none', !enough);
    }
    function handleCashInput(value) {
        cashReceived = parseFloat(value) || 0;
        recalcCash();
    }
    async function confirmCashPayment() {
        if (cashReceived < cashTotal || cashTotal <= 0) return;
        const btn = document.getElementById('btn-confirm-payment');
        btn.classList.add('opacity-50', 'pointer-events-none');
        try {
            const res = await fetch('{{ route('seller.pos.checkout') }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
                body: JSON.stringify({
                    items: [...cart.values()].map(item => ({ product_id: item.id, quantity: item.qty })),
                    payment_method: 'cash',
                    received_amount: cashReceived,
                    customer_name: document.getElementById('customer-name-input').value.trim() || null,
                }),
            });
            const data = await res.json();
            if (!res.ok) { showToast(data.message || 'Transaksi gagal diproses.', true); btn.classList.remove('opacity-50', 'pointer-events-none'); return; }
            clearCart();
            window.location = data.redirect_url;
        } catch (e) {
            showToast('Gagal terhubung ke server. Coba lagi.', true);
            btn.classList.remove('opacity-50', 'pointer-events-none');
        }
    }

    // ===== Modal virtual (QRIS / VA via Midtrans Snap) =====
    function openVirtualModalShell() {
        ['view-active-flow', 'view-success', 'view-expired'].forEach(id => {
            const el = document.getElementById(id);
            el.classList.toggle('hidden', id !== 'view-active-flow');
            if (id !== 'view-active-flow') el.classList.remove('flex');
        });
        document.getElementById('virtual-not-configured').classList.add('hidden');
        document.getElementById('modal-virtual').classList.remove('hidden');
        startCountdown();
    }
    function closeVirtualModal() {
        stopPolling();
        stopCountdown();
        document.getElementById('modal-virtual').classList.add('hidden');
    }
    function startVirtualCheckout() {
        if (cart.size === 0) return;
        fetch('{{ route('seller.pos.checkout') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            body: JSON.stringify({
                items: [...cart.values()].map(item => ({ product_id: item.id, quantity: item.qty })),
                payment_method: 'virtual',
                customer_name: document.getElementById('customer-name-input').value.trim() || null,
            }),
        }).then(async (res) => {
            const data = await res.json();
            if (!res.ok) { showToast(data.message || 'Transaksi gagal diproses.', true); return; }
            activeOrderCode = data.order_code;
            openVirtualModalShell();
            document.getElementById('virtual-order-code').textContent = data.order_code;
            document.getElementById('virtual-total-display').textContent = rupiah(data.total);
            document.getElementById('va-exact-amount').textContent = rupiah(data.total);
            if (!data.midtrans_configured || !data.snap_token) {
                document.getElementById('virtual-not-configured').classList.remove('hidden');
            } else {
                loadSnapScript(() => window.snap.pay(data.snap_token));
            }
            startPolling(data.status_url, data.receipt_url, data.order_code, data.total);
        }).catch(() => showToast('Gagal terhubung ke server. Coba lagi.', true));
    }
    function loadSnapScript(onReady) {
        if (snapLoaded && window.snap) { onReady(); return; }
        const script = document.createElement('script');
        script.src = MIDTRANS_SNAP_URL;
        script.setAttribute('data-client-key', MIDTRANS_CLIENT_KEY);
        script.onload = () => { snapLoaded = true; onReady(); };
        script.onerror = () => showToast('Gagal memuat Midtrans Snap. Periksa koneksi.', true);
        document.body.appendChild(script);
    }
    function startPolling(statusUrl, receiptUrl, orderCode, total) {
        stopPolling();
        pollTimer = setInterval(async () => {
            try {
                const res = await fetch(statusUrl, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;
                const data = await res.json();
                if (data.status === 'paid') {
                    stopPolling();
                    stopCountdown();
                    document.getElementById('success-order-code').textContent = orderCode;
                    document.getElementById('success-amount-line').textContent = rupiah(total) + ' LUNAS';
                    document.getElementById('success-paid-at').textContent = new Date().toLocaleString('id-ID') + ' WIB';
                    showVirtualView('view-success');
                    setTimeout(() => { window.location = receiptUrl; }, 1200);
                } else if (data.status === 'expired' || data.status === 'failed' || data.status === 'cancelled') {
                    stopPolling();
                    stopCountdown();
                    if (data.status === 'expired') {
                        document.getElementById('expired-order-code').textContent = orderCode;
                        showVirtualView('view-expired');
                    } else {
                        showToast('Pembayaran ' + data.status + '. Transaksi dibatalkan.', true);
                        closeVirtualModal();
                    }
                }
            } catch (e) { /* coba lagi pada siklus berikutnya */ }
        }, 3000);
    }
    function stopPolling() {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    }
    function showVirtualView(viewId) {
        ['view-active-flow', 'view-success', 'view-expired'].forEach(id => {
            const el = document.getElementById(id);
            el.classList.toggle('hidden', id !== viewId);
            if (id === viewId) el.classList.add('flex');
        });
    }
    function startCountdown() {
        stopCountdown();
        let remaining = 15 * 60;
        const el = document.getElementById('countdown-timer');
        const tick = () => {
            const m = String(Math.floor(remaining / 60)).padStart(2, '0');
            const s = String(remaining % 60).padStart(2, '0');
            el.textContent = m + ':' + s;
            if (remaining <= 0) {
                stopCountdown(); stopPolling();
                document.getElementById('expired-order-code').textContent = activeOrderCode || '-';
                showVirtualView('view-expired');
                return;
            }
            remaining -= 1;
        };
        tick();
        countdownTimer = setInterval(tick, 1000);
    }
    function stopCountdown() {
        if (countdownTimer) { clearInterval(countdownTimer); countdownTimer = null; }
    }
    function switchTab(tab) {
        const qrisBtn = document.getElementById('tab-btn-qris');
        const vaBtn = document.getElementById('tab-btn-va');
        const activeClass = 'tab-trigger inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-full font-caption text-caption font-bold transition-all bg-primary text-on-primary shadow-sm min-h-[40px]';
        const inactiveClass = 'tab-trigger inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-full font-caption text-caption font-medium text-on-surface hover:bg-surface-container-highest transition-all min-h-[40px]';
        qrisBtn.className = tab === 'qris' ? activeClass : inactiveClass;
        vaBtn.className = tab === 'va' ? activeClass : inactiveClass;
        document.getElementById('panel-qris').classList.toggle('hidden', tab !== 'qris');
        document.getElementById('panel-va').classList.toggle('hidden', tab !== 'va');
    }
    function selectBank(bank) {
        document.querySelectorAll('.bank-pill').forEach(el => {
            el.className = 'bank-pill flex flex-col items-center justify-center p-2.5 rounded-lg bg-surface-container text-on-surface font-bold text-caption font-code-mono min-h-[44px] hover:bg-surface-container-high';
        });
        document.getElementById('bank-' + bank.toLowerCase()).className = 'bank-pill flex flex-col items-center justify-center p-2.5 rounded-lg bg-blue-subtle text-primary font-bold text-caption font-code-mono min-h-[44px]';
        document.getElementById('va-bank-label').textContent = 'Nomor ' + bank + ' Virtual Account:';
    }
    function copyVANumber() {
        navigator.clipboard?.writeText(document.getElementById('va-number').textContent.trim());
        const label = document.getElementById('copy-label');
        label.textContent = 'Tersalin';
        setTimeout(() => { label.textContent = 'Salin'; }, 2000);
    }
    function openPaymentModal() {
        const { total } = cartTotals();
        if (total <= 0) return;
        if (selectedPayMethod === 'manual') openCashModal();
        else startVirtualCheckout();
    }

    // ===== Hotkey kasir =====
    window.addEventListener('keydown', (e) => {
        if (e.key === 'F2') {
            e.preventDefault();
            document.getElementById('barcode-search-input').focus();
        } else if (e.key === 'F9') {
            e.preventDefault();
            window.location = POS_HISTORY_URL;
        } else if (e.key === 'Escape') {
            closeCashModal();
            closeVirtualModal();
        }
    });

    renderCart();
</script>
@endpush
