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
                            <article class="group cursor-pointer bg-surface-card rounded-lg p-space-xs flex flex-col justify-between shadow-sm hover:shadow-md hover:bg-blue-subtle/30 transition-all select-none relative h-full" data-category="{{ $product->category?->name }}" data-name="{{ $product->name }}" data-price="{{ $product->price }}" data-product-id="{{ $product->id }}" data-stock="{{ $product->stock }}" onclick="addToCart({{ $product->id }})">
                                <div class="w-full aspect-square rounded-DEFAULT overflow-hidden bg-surface-container relative shrink-0">
                                    <img alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" draggable="false" loading="lazy" src="{{ $product->image ?: 'https://placehold.co/400x400/e6f0fb/004da4?text=' . urlencode(mb_substr($product->name, 0, 1)) }}" onerror="this.onerror=null;this.src='https://placehold.co/400x400/e6f0fb/004da4?text=?';">
                                    <span class="absolute top-1.5 left-1.5 px-2 py-0.5 rounded-full font-caption text-[11px] {{ (int) $product->stock <= 5 ? 'bg-accent-yellow text-on-accent-yellow font-bold' : 'bg-tertiary-fixed text-on-tertiary-fixed font-bold' }}">{{ (int) $product->stock <= 5 ? 'Sisa ' . $product->stock : $product->stock . ' unit' }}</span>
                                    <span class="absolute bottom-1.5 right-1.5 min-w-6 h-6 px-1 rounded-full bg-primary-container text-on-primary hidden items-center justify-center font-bold text-caption shadow pointer-events-none" data-qty-badge="{{ $product->id }}">0</span>
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
                    <div class="hidden items-start gap-space-xs p-space-sm bg-error-container text-on-error-container rounded-lg text-caption font-caption" id="stock-alert-banner" role="alert">
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

                    <!-- PAYMENT METHOD (tunai) -->
                    <div class="w-full flex items-center justify-between bg-surface-container rounded-full px-space-md h-10">
                        <span class="font-caption text-caption text-on-surface-variant">Metode Pembayaran</span>
                        <span class="inline-flex items-center gap-1 font-caption text-caption font-bold text-on-surface">
                            <span class="material-symbols-outlined text-[16px] text-primary">payments</span>
                            Tunai
                        </span>
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

@endsection

@push('scripts')
<script>
    // ===== Konfigurasi dari server =====
    const POS_HISTORY_URL = "{{ route('seller.pos.history') }}";

    // ===== State =====
    const cart = new Map(); // product_id -> { id, name, price, stock, qty }
    let cashReceived = 0;
    let cashTotal = 0;
    let selectedChip = null;

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
        document.getElementById('stock-alert-text').textContent = 'Stok ' + name + ' tersisa ' + stock + ' - kuantitas dibatasi ke batas maksimal ketersediaan.';
        banner.classList.remove('hidden');
        banner.classList.add('flex');
    }
    function hideStockAlert() {
        const banner = document.getElementById('stock-alert-banner');
        if (!banner) return;
        banner.classList.add('hidden');
        banner.classList.remove('flex');
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
            row.className = 'flex items-center gap-2 p-2 bg-surface-container-lowest rounded-lg shadow-sm';
            row.innerHTML = `
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-bold text-on-surface truncate leading-tight">${esc(item.name)}</p>
                    <p class="font-code-mono text-[11px] text-ink-muted leading-tight">@ ${rupiah(item.price)}</p>
                </div>
                <div class="flex items-center gap-0.5 bg-surface-container rounded-full p-0.5 shrink-0">
                    <button aria-label="Kurangi kuantitas" class="w-7 h-7 rounded-full flex items-center justify-center text-on-surface hover:bg-surface-container-highest active:scale-95 transition-transform" onclick="updateQty(${item.id}, -1)">
                        <span class="material-symbols-outlined text-[16px]">remove</span>
                    </button>
                    <span class="font-code-mono text-[13px] font-bold min-w-6 text-center" data-qty>${item.qty}</span>
                    <button aria-label="Tambah kuantitas" class="w-7 h-7 rounded-full flex items-center justify-center text-on-surface hover:bg-surface-container-highest active:scale-95 transition-transform" onclick="updateQty(${item.id}, 1)">
                        <span class="material-symbols-outlined text-[16px]">add</span>
                    </button>
                </div>
                <div class="text-right min-w-[72px] shrink-0">
                    <p class="font-code-mono text-[13px] text-on-surface font-bold leading-tight" data-line-total>${rupiah(item.qty * item.price)}</p>
                </div>
                <button class="w-7 h-7 rounded-full flex items-center justify-center text-ink-muted hover:text-error hover:bg-error-container/40 transition-colors shrink-0" title="Hapus Item" onclick="deleteItem(${item.id})">
                    <span class="material-symbols-outlined text-[16px]">delete_outline</span>
                </button>`;
            container.appendChild(row);
        });

        // Badge kuantitas pada kartu produk
        document.querySelectorAll('#catalog-grid article[data-product-id]').forEach(card => {
            const badge = card.querySelector('[data-qty-badge]');
            if (!badge) return;
            const item = cart.get(card.dataset.productId) || cart.get(Number(card.dataset.productId));
            const qty = item ? item.qty : 0;
            badge.textContent = qty > 99 ? '99+' : qty;
            const visible = qty > 0;
            badge.classList.toggle('hidden', !visible);
            badge.classList.toggle('flex', visible);
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

    // ===== Modal tunai =====
    function openCashModal() {
        const { kinds, pieces, total } = cartTotals();
        cashTotal = total;
        cashReceived = 0;
        selectedChip = null;
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
    function renderCashChips() {
        const wrap = document.getElementById('cash-chips');
        wrap.innerHTML = '';
        selectedChip = null;
        if (cashTotal <= 0) return;
        const baseClass = 'chip-item px-space-md py-1.5 rounded-full text-caption font-code-mono font-bold transition-all shadow-sm border-2';
        const pasClass = baseClass + ' bg-primary-container text-on-primary border-primary-container';
        const normalClass = baseClass + ' bg-surface-container hover:bg-surface-container-high text-on-surface border-transparent';
        const steps = [cashTotal, Math.ceil(cashTotal / 5000) * 5000, Math.ceil(cashTotal / 10000) * 10000, Math.ceil(cashTotal / 50000) * 50000, Math.ceil(cashTotal / 100000) * 100000];
        [...new Set(steps)].forEach((amount, i) => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.dataset.amount = String(amount);
            btn.className = i === 0 ? pasClass : normalClass;
            btn.textContent = i === 0 ? 'Uang Pas (' + rupiah(amount) + ')' : rupiah(amount);
            btn.onclick = () => applyPresetChip(amount);
            wrap.appendChild(btn);
        });
    }
    function applyPresetChip(amount) {
        selectedChip = Number(amount);
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
        if (selectedChip !== null && cashReceived !== selectedChip) {
            selectedChip = null;
        }
        document.querySelectorAll('#cash-chips .chip-item').forEach(btn => {
            const isActive = selectedChip !== null && Number(btn.dataset.amount) === selectedChip;
            btn.className = 'chip-item px-space-md py-1.5 rounded-full text-caption font-code-mono font-bold transition-all shadow-sm border-2'
                + (isActive
                    ? ' bg-primary text-white border-primary ring-2 ring-primary/40'
                    : ' bg-surface-container hover:bg-surface-container-high text-on-surface border-transparent');
        });
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

    function openPaymentModal() {
        const { total } = cartTotals();
        if (total <= 0) return;
        openCashModal();
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
        }
    });

    renderCart();
</script>
@endpush
