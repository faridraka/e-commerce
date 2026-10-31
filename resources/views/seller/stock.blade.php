@extends('layouts.seller')

@section('title', 'Manajemen & Riwayat Stok - Portal Penjual Lokalku')

@section('content')
@php
    $isPaginated = $products instanceof \Illuminate\Contracts\Pagination\Paginator;
    $productRows = $isPaginated ? $products->getCollection() : collect($products);
    $totalSku = $isPaginated ? $products->total() : $productRows->count();
    $lowStock = $productRows->filter(fn ($p) => $p->stock > 0 && $p->stock <= 5);
    $outStock = $productRows->filter(fn ($p) => $p->stock <= 0);
@endphp
<!-- BREADCRUMB & PAGE HEADER -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-space-md">
<div>
<div class="flex items-center gap-2 font-caption text-caption text-ink-muted mb-1">
<span class="">Portal Penjual</span>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="">Produk &amp; Stok</span>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="font-bold text-on-surface">Kelola Stok</span>
</div>
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">Manajemen &amp; Riwayat Stok</h1>
<p class="font-body-md text-body-md text-ink-muted mt-1 max-w-2xl">
        Pantau kuota inventaris fisik kasir POS dan etalase online secara realtime dengan audit mutasi stok otomatis.
      </p>
</div>
<!-- Top Action Buttons -->
<div class="flex items-center gap-space-sm flex-wrap">
<button class="h-10 px-4 rounded-full bg-surface-card border border-border-default text-on-surface font-body-md text-body-md font-medium hover:bg-surface-container-high transition-colors flex items-center gap-1.5 shadow-sm" type="button">
<span class="material-symbols-outlined text-[18px]">download</span>
<span class="">Unduh Laporan Stok (CSV)</span>
</button>
<button class="h-10 px-5 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-body-md text-body-md font-bold transition-colors flex items-center gap-1.5 shadow-sm" id="btn-quick-adjust" type="button">
<span class="material-symbols-outlined text-[18px]">tune</span>
<span class="">+ Penyesuaian Stok Cepat</span>
</button>
</div>
</div>
<!-- TOP SUMMARY CARDS (3 COLUMNS) -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
<!-- Total SKU -->
<div class="bg-surface-card rounded-xl p-space-md border border-border-default shadow-sm flex items-start justify-between">
<div>
<span class="font-caption text-caption text-ink-muted uppercase tracking-wider block font-bold">Total Inventaris Terdaftar</span>
<div class="flex items-baseline gap-2 mt-1">
<span class="font-code-mono text-price-lg font-bold text-on-surface">{{ $totalSku }} SKU</span>
<span class="font-caption text-caption text-success-text font-bold">Tersinkron POS &amp; Web</span>
</div>
<p class="font-body-md text-caption text-ink-muted mt-2">Seluruh varian produk aktif memiliki barcode scan.</p>
</div>
<div class="w-10 h-10 rounded-full bg-blue-subtle text-primary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]">inventory_2</span>
</div>
</div>
<!-- Stok Menipis -->
<div class="bg-surface-card rounded-xl p-space-md border border-border-default shadow-sm flex items-start justify-between relative overflow-hidden">
<div class="relative z-10">
<div class="flex items-center gap-2">
<span class="font-caption text-caption text-ink-muted uppercase tracking-wider font-bold">Stok Menipis</span>
<span class="px-2 py-0.5 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-caption font-bold">≤ 5 Unit</span>
</div>
<div class="flex items-baseline gap-2 mt-1">
<span class="font-code-mono text-price-lg font-bold text-on-surface">{{ $lowStock->count() }} SKU</span>
<span class="font-caption text-caption text-error font-medium">Perlu restok segera</span>
</div>
<p class="font-body-md text-caption text-ink-muted mt-2 line-clamp-1">
          {{ $lowStock->take(3)->pluck('name')->implode(', ') ?: 'Semua stok dalam kondisi aman' }}
        </p>
</div>
<div class="w-10 h-10 rounded-full bg-accent-yellow/20 text-on-accent-yellow flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]">warning</span>
</div>
</div>
<!-- Stok Habis -->
<div class="bg-surface-card rounded-xl p-space-md border border-border-default shadow-sm flex items-start justify-between">
<div>
<div class="flex items-center gap-2">
<span class="font-caption text-caption text-ink-muted uppercase tracking-wider font-bold">Stok Habis</span>
<span class="px-2 py-0.5 rounded-full bg-secondary text-on-secondary font-caption text-caption font-bold">0 Unit</span>
</div>
<div class="flex items-baseline gap-2 mt-1">
<span class="font-code-mono text-price-lg font-bold text-error">{{ $outStock->count() }} SKU</span>
<span class="font-caption text-caption text-ink-muted">Nonaktif di kasir</span>
</div>
<p class="font-body-md text-caption text-ink-muted mt-2 truncate">
          {{ $outStock->take(3)->pluck('name')->implode(', ') ?: 'Tidak ada produk yang habis' }}
        </p>
</div>
<div class="w-10 h-10 rounded-full bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-[22px]">remove_shopping_cart</span>
</div>
</div>
</div>
<!-- NAVIGATION PILL TABS -->
<div class="flex items-center gap-space-xs border-b border-border-default pb-space-xs">
<button class="px-5 py-2.5 rounded-full font-body-md text-body-md font-bold transition-all bg-primary-container text-on-primary shadow-sm flex items-center gap-2" id="tab-btn-stok" onclick="switchTab('stok')" type="button">
<span class="material-symbols-outlined text-[18px]">inventory</span>
<span class="">Stok Produk ({{ $totalSku }})</span>
</button>
<button class="px-5 py-2.5 rounded-full font-body-md text-body-md font-bold transition-all bg-surface-card text-on-surface hover:bg-surface-container-high border border-border-default flex items-center gap-2" id="tab-btn-riwayat" onclick="switchTab('riwayat')" type="button">
<span class="material-symbols-outlined text-[18px]">history</span>
<span class="">Riwayat Perubahan (Audit Mutasi)</span>
</button>
</div>
<!-- TOAST NOTIFICATION CONTAINER -->
<div class="hidden transition-all duration-300 fixed bottom-6 right-6 z-50 bg-inverse-surface text-inverse-on-surface px-4 py-3 rounded-xl shadow-xl flex items-center gap-3 border border-border-default" id="toast-notif">
<span class="material-symbols-outlined text-tertiary-fixed text-[24px]">check_circle</span>
<div>
<p class="font-body-md text-body-md font-bold text-surface-card">Penyesuaian Stok Berhasil!</p>
<p class="font-caption text-caption text-surface-variant">Stok produk diperbarui (POS &amp; Toko Online).</p>
</div>
<button class="text-surface-variant hover:text-surface-card ml-2" onclick="hideToast()" type="button">
<span class="material-symbols-outlined text-[18px]">close</span>
</button>
</div>
<!-- ======================== TAB 1: STOK PRODUK ======================== -->
<section class="space-y-space-md" id="section-tab-stok">
<!-- Filter Toolbar -->
<div class="bg-surface-card rounded-xl p-space-md border border-border-default flex flex-col lg:flex-row items-center justify-between gap-space-md">
<div class="flex items-center gap-space-sm w-full lg:w-auto flex-1 flex-wrap">
<!-- Search Input -->
<div class="relative flex-1 min-w-[260px]">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-ink-muted">search</span>
<input class="w-full h-10 pl-9 pr-3 rounded-lg border border-border-input bg-surface-card font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container" placeholder="Cari nama produk, varian, atau SKU..." type="text">
</div>
<!-- Dropdown Kategori -->
<div class="relative">
<select class="h-10 px-3 pr-8 rounded-lg border border-border-input bg-surface-card font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container cursor-pointer appearance-none">
<option value="all">Semua Kategori</option>
@foreach ($productRows->map(fn ($p) => $p->category?->name)->filter()->unique() as $categoryName)
<option value="{{ $categoryName }}">{{ $categoryName }}</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-ink-muted pointer-events-none">expand_more</span>
</div>
</div>
<!-- Status Chips -->
<div class="flex items-center gap-1.5 overflow-x-auto w-full lg:w-auto pb-1 lg:pb-0">
<button class="h-7 px-3 rounded-full bg-primary-container text-on-primary font-caption text-caption font-bold" type="button">Semua</button>
<button class="h-7 px-3 rounded-full bg-surface-card border border-border-default text-on-surface font-caption text-caption hover:bg-surface-container-high transition-colors" type="button">Stok Aman</button>
<button class="h-7 px-3 rounded-full bg-surface-card border border-border-default text-on-surface font-caption text-caption hover:bg-surface-container-high transition-colors flex items-center gap-1" type="button">
<span class="w-1.5 h-1.5 rounded-full bg-accent-yellow"></span>
<span class="">Menipis ({{ $lowStock->count() }})</span>
</button>
<button class="h-7 px-3 rounded-full bg-surface-card border border-border-default text-on-surface font-caption text-caption hover:bg-surface-container-high transition-colors flex items-center gap-1" type="button">
<span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
<span class="">Habis ({{ $outStock->count() }})</span>
</button>
</div>
</div>
<!-- Table Card Container -->
<div class="bg-surface-card rounded-xl border border-border-default shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low text-ink-muted font-caption text-caption uppercase tracking-wider border-b border-border-default">
<th class="py-3 px-4 font-bold">Produk &amp; SKU</th>
<th class="py-3 px-4 font-bold">Kategori</th>
<th class="py-3 px-4 font-bold">Stok Saat Ini</th>
<th class="py-3 px-4 font-bold">Batas Minimum</th>
<th class="py-3 px-4 font-bold">Status Inventaris</th>
<th class="py-3 px-4 font-bold text-right">Aksi</th>
</tr>
</thead>
<tbody class="divide-y divide-border-default font-body-md text-body-md">
@forelse ($productRows as $product)
@php
                            $isOut = $product->stock <= 0;
                            $isLow = ! $isOut && $product->stock <= 5;
                            $catName = strtolower($product->category?->name ?? '');
                            $catIcon = 'inventory_2';
                            if (str_contains($catName, 'makanan')) { $catIcon = 'restaurant'; }
                            elseif (str_contains($catName, 'minuman')) { $catIcon = 'local_cafe'; }
                            elseif (str_contains($catName, 'fashion')) { $catIcon = 'checkroom'; }
                            elseif (str_contains($catName, 'kriya') || str_contains($catName, 'kerajinan')) { $catIcon = 'shopping_bag'; }
                        @endphp
<tr class="hover:bg-canvas-background/60 transition-colors @if ($isOut) bg-secondary-fixed/10 @endif">
<td class="py-3 px-4">
<div class="flex items-center gap-3">
@if ($product->image)
<img class="w-10 h-10 rounded-lg object-cover bg-surface-container shrink-0 border border-border-default" src="{{ $product->image }}" alt="{{ $product->name }}">
@else
<div class="w-10 h-10 rounded-lg bg-surface-container flex items-center justify-center shrink-0 border border-border-default">
<span class="material-symbols-outlined text-[20px] text-ink-muted">{{ $catIcon }}</span>
</div>
@endif
<div>
<div class="font-bold text-on-surface leading-snug">{{ $product->name }}</div>
<div class="font-code-mono text-caption text-ink-muted">SKU: #{{ $product->id }}</div>
</div>
</div>
</td>
<td class="py-3 px-4 text-on-surface-variant font-medium">{{ $product->category?->name ?? '—' }}</td>
<td class="py-3 px-4">
<div class="font-code-mono text-headline-sm font-bold {{ $isOut ? 'text-error' : 'text-on-surface' }} leading-none">{{ $product->stock }} <span class="font-caption text-caption {{ $isOut ? 'text-error' : 'text-ink-muted' }}">unit</span></div>
</td>
<td class="py-3 px-4 font-code-mono text-ink-muted">5 unit</td>
<td class="py-3 px-4">
@if ($isOut)
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-secondary text-on-secondary font-caption text-caption font-bold">
<span class="">⛔ Stok Habis</span>
</span>
@elseif ($isLow)
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-accent-yellow/20 text-on-accent-yellow font-caption text-caption font-bold">
<span class="">⚠️ Stok Menipis</span>
</span>
@else
<span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-caption text-caption font-bold">
<span class="">✓ Stok Aman</span>
</span>
@endif
</td>
<td class="py-3 px-4 text-right">
@if ($isOut)
<button class="h-9 px-4 rounded-full border border-secondary text-secondary font-body-md text-caption font-bold hover:bg-secondary/10 transition-colors" onclick="openAdjustModal({{ $product->id }})" type="button">
                  Restok Segera
                </button>
@else
<button class="h-9 px-4 rounded-full border border-primary text-primary font-body-md text-caption font-bold hover:bg-blue-subtle transition-colors" onclick="openAdjustModal({{ $product->id }})" type="button">
                  Sesuaikan Stok
                </button>
@endif
</td>
</tr>
@empty
<tr>
<td class="py-10 px-4 text-center text-ink-muted" colspan="6">Belum ada produk terdaftar. Tambahkan produk terlebih dahulu.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
<!-- Pagination -->
<div class="p-3 px-4 border-t border-border-default bg-surface-card flex flex-col sm:flex-row items-center justify-between font-caption text-caption text-ink-muted gap-2">
<div>
@if ($isPaginated)
            Menampilkan <strong class="text-on-surface">{{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }}</strong> dari {{ $products->total() }} produk
@else
            Menampilkan <strong class="text-on-surface">{{ $productRows->count() }}</strong> produk
@endif
</div>
@if ($isPaginated)
<div class="flex items-center gap-1">
{!! $products->appends(request()->query())->links() !!}
</div>
@endif
</div>
</div>
</section>
<!-- ======================== TAB 2: RIWAYAT PERUBAHAN (MUTASI) ======================== -->
<section class="space-y-space-md hidden" id="section-tab-riwayat">
<!-- Filter Toolbar Riwayat -->
<div class="bg-surface-card rounded-xl p-space-md border border-border-default flex flex-col lg:flex-row items-center justify-between gap-space-md">
<div class="flex items-center gap-space-sm w-full lg:w-auto flex-1 flex-wrap">
<!-- Filter Mutasi -->
<div class="relative">
<select class="h-10 px-3 pr-8 rounded-lg border border-border-input bg-surface-card font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container cursor-pointer appearance-none">
<option value="all">Semua Tipe Mutasi</option>
<option value="penjualan">Penjualan (Kasir &amp; Web)</option>
<option value="penyesuaian">Penyesuaian Manual</option>
<option value="pengembalian">Pengembalian / Retur</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-ink-muted pointer-events-none">expand_more</span>
</div>
<!-- Filter Date -->
<div class="relative">
<select class="h-10 px-3 pr-8 rounded-lg border border-border-input bg-surface-card font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container cursor-pointer appearance-none">
<option value="7">7 Hari Terakhir</option>
<option value="30">30 Hari Terakhir</option>
<option value="custom">Pilih Rentang Tanggal</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-ink-muted pointer-events-none">calendar_today</span>
</div>
<!-- Search Bar -->
<div class="relative flex-1 min-w-[240px]">
<span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-ink-muted">search</span>
<input class="w-full h-10 pl-9 pr-3 rounded-lg border border-border-input bg-surface-card font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container" placeholder="Cari No. Pesanan atau Catatan..." type="text">
</div>
</div>
<div class="flex items-center gap-2 text-ink-muted font-caption text-caption self-end lg:self-center">
<span class="material-symbols-outlined text-[16px] text-primary">sync</span>
<span class="">Audit mutasi stok diperbarui otomatis</span>
</div>
</div>
<!-- Table Card Riwayat Mutasi -->
<div class="bg-surface-card rounded-xl border border-border-default shadow-sm overflow-hidden">
<div class="overflow-x-auto">
<table class="w-full text-left border-collapse">
<thead>
<tr class="bg-surface-container-low text-ink-muted font-caption text-caption uppercase tracking-wider border-b border-border-default">
<th class="py-3 px-4 font-bold">Waktu &amp; Tanggal</th>
<th class="py-3 px-4 font-bold">Produk</th>
<th class="py-3 px-4 font-bold">Tipe Mutasi</th>
<th class="py-3 px-4 font-bold">Jumlah Mutasi</th>
<th class="py-3 px-4 font-bold">Pesanan Terkait</th>
<th class="py-3 px-4 font-bold">Catatan &amp; Operator</th>
</tr>
</thead>
<tbody class="divide-y divide-border-default font-body-md text-body-md">
@forelse ($movements as $movement)
@php
                            [$movementLabel, $movementClass] = match ($movement->type) {
                                'penjualan' => ['Penjualan', 'bg-secondary-fixed text-secondary'],
                                'pengembalian' => ['Pengembalian', 'bg-tertiary-fixed text-on-tertiary-fixed'],
                                default => ['Penyesuaian', 'bg-blue-subtle text-primary'],
                            };
                            $qty = (int) $movement->quantity;
                        @endphp
<tr class="hover:bg-canvas-background/60 transition-colors">
<td class="py-3 px-4 font-code-mono text-caption text-on-surface whitespace-nowrap">
<div class="font-bold">{{ $movement->created_at->translatedFormat('d M Y') }}</div>
<div class="text-ink-muted">{{ $movement->created_at->translatedFormat('H:i:s') }} WIB</div>
</td>
<td class="py-3 px-4">
<div class="font-bold text-on-surface">{{ $movement->product?->name ?? '—' }}</div>
</td>
<td class="py-3 px-4">
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full {{ $movementClass }} font-caption text-caption font-bold">
                  {{ $movementLabel }}
                </span>
</td>
<td class="py-3 px-4 font-code-mono font-bold text-price-sm whitespace-nowrap {{ $qty < 0 ? 'text-secondary' : 'text-success-text' }}">
                {{ ($qty < 0 ? '-' : '+').abs($qty) }} unit
              </td>
<td class="py-3 px-4">
@if ($movement->order?->order_code)
<a class="font-code-mono text-caption text-primary hover:underline font-bold" href="{{ route('seller.orders.show', $movement->order->order_code) }}">{{ $movement->order->order_code }}</a>
@else
<span class="font-code-mono text-caption text-ink-muted">—</span>
@endif
</td>
<td class="py-3 px-4">
<div class="text-on-surface">{{ $movement->note ?? 'Penyesuaian stok' }}</div>
<div class="font-caption text-caption text-ink-muted">Operator: <strong class="text-on-surface">{{ $store?->name ?? 'Toko' }}</strong></div>
</td>
</tr>
@empty
<tr>
<td class="py-10 px-4 text-center text-ink-muted" colspan="6">Belum ada riwayat pergerakan stok.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</div>
</section>
<!-- ======================== MODAL SESUAIKAN STOK ======================== -->
<div class="fixed inset-0 z-50 flex items-center justify-center bg-inverse-surface/50 backdrop-blur-sm hidden p-4" id="modal-adjust-stock">
<div class="bg-surface-card rounded-xl border border-border-default shadow-2xl w-full max-w-lg overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-200">
<!-- Modal Header -->
<div class="p-space-lg border-b border-border-default flex items-start justify-between bg-surface-card">
<div>
<h2 class="font-headline-md text-headline-md text-on-surface">Sesuaikan Stok</h2>
<p class="font-code-mono text-caption text-ink-muted mt-0.5">Stok fisik toko &amp; etalase web</p>
</div>
<button class="p-1 rounded-full text-ink-muted hover:text-on-surface hover:bg-surface-container transition-colors" onclick="closeModal()" type="button">
<span class="material-symbols-outlined text-[20px]">close</span>
</button>
</div>
<!-- Modal Body -->
<form action="{{ route('seller.stock.adjust') }}" method="POST" id="form-adjust-stock" class="flex flex-col">
@csrf
<div class="p-space-lg space-y-space-md max-h-[768px] overflow-y-auto">
<!-- Produk -->
<div class="space-y-1.5">
<label class="block font-body-md text-caption font-bold text-on-surface" for="adjust-product">Pilih Produk <span class="text-secondary">*</span></label>
<div class="relative">
<select class="w-full h-11 px-3 pr-8 rounded-lg border border-border-input bg-surface-card font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container cursor-pointer appearance-none" id="adjust-product" name="product_id" onchange="updateStockInfo()">
<option value="">— Pilih produk —</option>
@foreach ($productRows as $product)
<option value="{{ $product->id }}" data-stock="{{ $product->stock }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }} (Stok: {{ $product->stock }})</option>
@endforeach
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-ink-muted pointer-events-none">expand_more</span>
</div>
@error('product_id')
<p class="font-caption text-caption text-secondary font-bold mt-1">{{ $message }}</p>
@enderror
</div>
<!-- Stock Banner Highlight -->
<div class="p-3 bg-blue-subtle/70 rounded-lg flex items-center justify-between border border-primary-fixed">
<div>
<span class="font-caption text-caption text-ink-muted block">Stok Tercatat Saat Ini</span>
<span class="font-code-mono text-price-lg font-bold text-primary" id="label-current-stock">— Unit</span>
</div>
<span class="px-2.5 py-1 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-caption font-bold hidden" id="label-current-badge">
            Stok Menipis
          </span>
</div>
<!-- Mode Toggle Segmented (Arah Mutasi) -->
<div class="space-y-1.5">
<label class="block font-body-md text-caption font-bold text-on-surface">Arah Penyesuaian <span class="text-secondary">*</span></label>
<div class="flex rounded-lg bg-surface-container p-1 text-caption font-bold">
<label class="flex-1 cursor-pointer">
<input class="peer sr-only" name="direction" type="radio" value="masuk" onchange="updateCalc()" {{ old('direction', 'masuk') === 'masuk' ? 'checked' : '' }}>
<span class="block py-1.5 rounded-md text-center transition-all text-ink-muted hover:text-on-surface peer-checked:bg-surface-card peer-checked:text-primary peer-checked:shadow-sm">+ Stok Masuk</span>
</label>
<label class="flex-1 cursor-pointer">
<input class="peer sr-only" name="direction" type="radio" value="keluar" onchange="updateCalc()" {{ old('direction') === 'keluar' ? 'checked' : '' }}>
<span class="block py-1.5 rounded-md text-center transition-all text-ink-muted hover:text-on-surface peer-checked:bg-surface-card peer-checked:text-secondary peer-checked:shadow-sm">− Stok Keluar</span>
</label>
</div>
@error('direction')
<p class="font-caption text-caption text-secondary font-bold mt-1">{{ $message }}</p>
@enderror
</div>
<!-- Stepper Input Section -->
<div class="bg-canvas-background p-4 rounded-xl border border-border-default space-y-2">
<label class="block font-caption text-caption font-bold text-on-surface" for="input-adjust-qty">
            Kuantitas Penambahan (+) / Pengurangan (−)
          </label>
<div class="flex items-center gap-3">
<button class="w-11 h-11 rounded-lg bg-surface-card border border-border-input flex items-center justify-center font-price-lg text-on-surface hover:bg-surface-container transition-colors" onclick="adjustCount(-1)" type="button">
              −
            </button>
<div class="relative flex-1">
<input class="w-full h-11 text-center font-code-mono text-price-lg font-bold text-primary bg-surface-card rounded-lg border border-border-input focus:ring-2 focus:ring-primary-container focus:outline-none" id="input-adjust-qty" name="quantity" type="number" min="1" step="1" value="{{ old('quantity', 1) }}" oninput="updateCalc()">
</div>
<button class="w-11 h-11 rounded-lg bg-surface-card border border-border-input flex items-center justify-center font-price-lg text-on-surface hover:bg-surface-container transition-colors" onclick="adjustCount(1)" type="button">
              +
            </button>
</div>
<div class="flex items-center justify-between text-caption pt-1">
<span class="text-ink-muted font-body-md">Estimasi Stok Akhir:</span>
<span class="px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-code-mono font-bold" id="label-final-stock">
              —
            </span>
</div>
@error('quantity')
<p class="font-caption text-caption text-secondary font-bold">{{ $message }}</p>
@enderror
</div>
<!-- Reason Selection -->
<div class="space-y-1.5">
<label class="block font-body-md text-caption font-bold text-on-surface" for="input-reason">
            Alasan Penyesuaian Stok <span class="text-secondary">*</span>
</label>
<input class="w-full h-10 px-3 rounded-lg border border-border-input bg-surface-card font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container" id="input-reason" name="note" placeholder="Misal: Stok masuk dari produksi, Barang rusak/kedaluwarsa, Koreksi fisik" type="text" value="{{ old('note') }}">
<!-- Quick Pills -->
<div class="flex items-center gap-1.5 flex-wrap pt-1">
<button class="h-6 px-2.5 rounded-full bg-surface-card border border-border-default text-ink-muted hover:border-primary hover:text-primary font-caption text-caption transition-colors" onclick="setQuickReason('Stok masuk dari produksi dapur')" type="button">
              + Produksi Baru
            </button>
<button class="h-6 px-2.5 rounded-full bg-surface-card border border-border-default text-ink-muted hover:border-primary hover:text-primary font-caption text-caption transition-colors" onclick="setQuickReason('Koreksi fisik barang rusak/display')" type="button">
              − Koreksi Fisik/Rusak
            </button>
<button class="h-6 px-2.5 rounded-full bg-surface-card border border-border-default text-ink-muted hover:border-primary hover:text-primary font-caption text-caption transition-colors" onclick="setQuickReason('Retur pengembalian dari pembeli')" type="button">
              + Retur Pembeli
            </button>
<button class="h-6 px-2.5 rounded-full bg-surface-card border border-border-default text-ink-muted hover:border-primary hover:text-primary font-caption text-caption transition-colors" onclick="setQuickReason('Digunakan untuk sampel cicip toko')" type="button">
              − Digunakan Sampel
            </button>
</div>
@error('note')
<p class="font-caption text-caption text-secondary font-bold mt-1">{{ $message }}</p>
@enderror
</div>
</div>
<!-- Modal Footer -->
<div class="p-space-lg border-t border-border-default bg-surface-container-low flex items-center justify-end gap-space-sm">
<button class="h-10 px-5 rounded-full border border-border-default bg-surface-card hover:bg-surface-container-high text-on-surface font-body-md text-body-md font-medium transition-colors" onclick="closeModal()" type="button">
          Batal
        </button>
<button class="h-10 px-6 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-body-md text-body-md font-bold transition-colors shadow-sm flex items-center gap-1.5" type="submit">
<span class="material-symbols-outlined text-[18px]">save</span>
<span class="">Simpan Penyesuaian</span>
</button>
</div>
</form>
</div>
</div>
@endsection

@push('scripts')
<script>
  let currentStock = 0;

  function switchTab(tabId) {
    const secStok = document.getElementById('section-tab-stok');
    const secRiwayat = document.getElementById('section-tab-riwayat');
    const btnStok = document.getElementById('tab-btn-stok');
    const btnRiwayat = document.getElementById('tab-btn-riwayat');

    if (tabId === 'stok') {
      secStok.classList.remove('hidden');
      secRiwayat.classList.add('hidden');
      btnStok.className = "px-5 py-2.5 rounded-full font-body-md text-body-md font-bold transition-all bg-primary-container text-on-primary shadow-sm flex items-center gap-2";
      btnRiwayat.className = "px-5 py-2.5 rounded-full font-body-md text-body-md font-bold transition-all bg-surface-card text-on-surface hover:bg-surface-container-high border border-border-default flex items-center gap-2";
    } else {
      secStok.classList.add('hidden');
      secRiwayat.classList.remove('hidden');
      btnRiwayat.className = "px-5 py-2.5 rounded-full font-body-md text-body-md font-bold transition-all bg-primary-container text-on-primary shadow-sm flex items-center gap-2";
      btnStok.className = "px-5 py-2.5 rounded-full font-body-md text-body-md font-bold transition-all bg-surface-card text-on-surface hover:bg-surface-container-high border border-border-default flex items-center gap-2";
    }
  }

  function openAdjustModal(productId) {
    const select = document.getElementById('adjust-product');
    if (productId) {
      select.value = String(productId);
    }
    updateStockInfo();
    document.getElementById('modal-adjust-stock').classList.remove('hidden');
  }

  function closeModal() {
    document.getElementById('modal-adjust-stock').classList.add('hidden');
  }

  function updateStockInfo() {
    const select = document.getElementById('adjust-product');
    const option = select.selectedOptions[0];
    currentStock = option && option.dataset.stock !== undefined ? parseInt(option.dataset.stock, 10) : 0;
    const label = document.getElementById('label-current-stock');
    const badge = document.getElementById('label-current-badge');
    label.textContent = (option ? currentStock : '—') + ' Unit';
    if (!option) {
      badge.classList.add('hidden');
    } else if (currentStock <= 0) {
      badge.textContent = 'Stok Habis';
      badge.className = 'px-2.5 py-1 rounded-full bg-secondary text-on-secondary font-caption text-caption font-bold';
      badge.classList.remove('hidden');
    } else if (currentStock <= 5) {
      badge.textContent = 'Stok Menipis';
      badge.className = 'px-2.5 py-1 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-caption font-bold';
      badge.classList.remove('hidden');
    } else {
      badge.textContent = 'Stok Aman';
      badge.className = 'px-2.5 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-caption text-caption font-bold';
      badge.classList.remove('hidden');
    }
    updateCalc();
  }

  function adjustCount(delta) {
    const input = document.getElementById('input-adjust-qty');
    const current = parseInt(input.value, 10);
    input.value = (isNaN(current) ? 0 : current) + delta < 1 ? 1 : (isNaN(current) ? 1 : current) + delta;
    updateCalc();
  }

  function updateCalc() {
    const input = document.getElementById('input-adjust-qty');
    const direction = document.querySelector('input[name="direction"]:checked');
    const qty = Math.abs(parseInt(input.value, 10)) || 0;
    const total = currentStock + (direction && direction.value === 'keluar' ? -qty : qty);
    const badge = document.getElementById('label-final-stock');
    if (!document.getElementById('adjust-product').value) {
      badge.textContent = '—';
      badge.className = 'px-2 py-0.5 rounded-full bg-surface-container text-ink-muted font-code-mono font-bold';
      return;
    }
    if (total <= 0) {
      badge.textContent = total + ' Unit (Habis)';
      badge.className = 'px-2 py-0.5 rounded-full bg-secondary text-on-secondary font-code-mono font-bold';
    } else if (total <= 5) {
      badge.textContent = total + ' Unit (Menipis)';
      badge.className = 'px-2 py-0.5 rounded-full bg-accent-yellow text-on-accent-yellow font-code-mono font-bold';
    } else {
      badge.textContent = total + ' Unit (Aman)';
      badge.className = 'px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-code-mono font-bold';
    }
  }

  function setQuickReason(text) {
    document.getElementById('input-reason').value = text;
  }

  function triggerToastDemo() {
    const toast = document.getElementById('toast-notif');
    toast.classList.remove('hidden');
    setTimeout(() => {
      hideToast();
    }, 4000);
  }

  function hideToast() {
    document.getElementById('toast-notif').classList.add('hidden');
  }

  document.getElementById('btn-quick-adjust').addEventListener('click', () => openAdjustModal(null));
  document.getElementById('modal-adjust-stock').addEventListener('click', (event) => {
    if (event.target === event.currentTarget) closeModal();
  });
</script>
@endpush
