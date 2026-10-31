@extends('layouts.seller')

@section('title', 'Dasbor Penjual - Lokalku')

@section('breadcrumb', 'Dasbor')

@section('content')
<div class="flex flex-col w-full">
    @php
        $posOrderCount = $recentOrders->filter(fn ($order) => ($order->channel ?? 'online') === 'pos')->count();
        $onlineOrderCount = $recentOrders->count() - $posOrderCount;
    @endphp
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-space-md mb-space-xl">
        <div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Dashboard</h1>
            <p class="font-body-md text-body-md text-ink-muted mt-0.5">
                Ikhtisar performa penjualan online marketplace dan kasir fisik (POS) UMKM Anda.
            </p>
        </div>
        <!-- Controls -->
        <div class="flex flex-wrap items-center gap-space-sm">
            <!-- Date Range Dropdown -->
            <div class="relative inline-block text-left" id="date-range-container">
                <button class="h-10 px-4 rounded-full bg-surface-card text-on-surface font-body-md text-body-md font-medium shadow-sm hover:bg-surface-container transition-colors flex items-center gap-2" onclick="toggleDateDropdown()" type="button">
                    <span class="material-symbols-outlined text-[18px] text-primary-container">calendar_today</span>
                    <span id="selected-date-label" class="">30 Hari Terakhir</span>
                    <span class="material-symbols-outlined text-[16px] text-ink-muted">expand_more</span>
                </button>
                <div class="hidden absolute right-0 mt-2 w-52 rounded-xl bg-surface-card shadow-lg py-1.5 z-30" id="date-dropdown-menu">
                    <button class="w-full text-left px-4 py-2 font-body-md text-body-md hover:bg-blue-subtle hover:text-primary transition-colors" onclick="selectDateRange('Hari Ini')">Hari Ini</button>
                    <button class="w-full text-left px-4 py-2 font-body-md text-body-md hover:bg-blue-subtle hover:text-primary transition-colors" onclick="selectDateRange('7 Hari Terakhir')">7 Hari Terakhir</button>
                    <button class="w-full text-left px-4 py-2 font-body-md text-body-md bg-blue-subtle text-primary font-bold transition-colors" onclick="selectDateRange('30 Hari Terakhir')">30 Hari Terakhir</button>
                    <button class="w-full text-left px-4 py-2 font-body-md text-body-md hover:bg-blue-subtle hover:text-primary transition-colors" onclick="selectDateRange('Bulan Ini')">Bulan Ini</button>
                </div>
            </div>
            <!-- Export Button -->
            <button class="h-10 px-4 rounded-full bg-surface-card text-primary-container font-body-md text-body-md font-bold hover:bg-blue-subtle transition-colors flex items-center gap-1.5 shadow-sm" onclick="triggerExportNotice()" type="button">
                <span class="material-symbols-outlined text-[18px]">download</span>
                <span class="">Ekspor Laporan</span>
            </button>
            <!-- New POS Sale Button -->
            <a class="h-10 px-5 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-body-md text-body-md font-bold transition-colors flex items-center gap-1.5 shadow-sm" href="{{ route('seller.pos') }}">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span class="">+ Transaksi POS Baru</span>
            </a>
        </div>
    </div>
    <!-- ================= STATE 1: NORMAL VIEW ================= -->
    <div class="flex flex-col gap-space-xl" id="view-normal">
        <!-- ROW 1: 4 KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-lg">
            <!-- KPI 1 -->
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-space-xs">
                    <span class="font-body-md text-caption uppercase tracking-wider text-ink-muted font-bold">Total Penjualan</span>
                    <div class="w-9 h-9 rounded-full bg-blue-subtle text-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">payments</span>
                    </div>
                </div>
                <div class="mt-space-xs">
                    <div class="font-display text-headline-lg font-bold text-on-surface tracking-tight">Rp {{ number_format((float) $stats['revenue_today'], 0, ',', '.') }}</div>
                    <div class="flex items-center gap-2 mt-space-xs">
                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-caption text-caption font-bold">
                            <span class="material-symbols-outlined text-[14px]">arrow_upward</span>
                            +12% vs bln lalu
                        </span>
                    </div>
                </div>
                <div class="mt-space-md pt-space-xs flex items-center justify-between font-caption text-caption text-ink-muted">
                    <span class="">Online <strong class="text-on-surface">Rp 11.2 jt</strong></span>
                    <span class="">•</span>
                    <span class="">POS <strong class="text-on-surface">Rp 7.25 jt</strong></span>
                </div>
            </div>
            <!-- KPI 2 -->
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-space-xs">
                    <span class="font-body-md text-caption uppercase tracking-wider text-ink-muted font-bold">Jumlah Pesanan</span>
                    <div class="w-9 h-9 rounded-full bg-blue-subtle text-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                    </div>
                </div>
                <div class="mt-space-xs">
                    <div class="font-display text-headline-lg font-bold text-on-surface tracking-tight font-code-mono">{{ (int) $stats['orders_today'] }}</div>
                    <div class="flex items-center gap-2 mt-space-xs">
                        <span class="inline-flex items-center gap-0.5 px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-caption text-caption font-bold">
                            <span class="material-symbols-outlined text-[14px]">arrow_upward</span>
                            +8% minggu ini
                        </span>
                    </div>
                </div>
                <div class="mt-space-md pt-space-xs flex items-center justify-between font-caption text-caption text-ink-muted">
                    <span class=""><strong class="text-on-surface font-code-mono">96</strong> Marketplace</span>
                    <span class="">•</span>
                    <span class=""><strong class="text-on-surface font-code-mono">68</strong> Kasir Toko</span>
                </div>
            </div>
            <!-- KPI 3 -->
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-space-xs">
                    <span class="font-body-md text-caption uppercase tracking-wider text-ink-muted font-bold">Produk Aktif</span>
                    <div class="w-9 h-9 rounded-full bg-blue-subtle text-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                    </div>
                </div>
                <div class="mt-space-xs">
                    <div class="font-display text-headline-lg font-bold text-on-surface tracking-tight font-code-mono">{{ (int) $stats['products_count'] }}</div>
                    <p class="font-caption text-caption text-ink-muted mt-space-xs">
                        Semua produk sinkron di etalase &amp; POS
                    </p>
                </div>
                <div class="mt-space-md pt-space-xs">
                    <a class="inline-flex items-center gap-1 font-body-md text-caption font-bold text-primary-container hover:text-blue-hover" href="{{ route('seller.products') }}">
                        <span class="">Lihat Katalog</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>
            </div>
            <!-- KPI 4 -->
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-space-xs">
                    <span class="font-body-md text-caption uppercase tracking-wider text-ink-muted font-bold">Perlu Diproses</span>
                    <div class="w-9 h-9 rounded-full bg-accent-yellow/20 text-on-accent-yellow flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">pending_actions</span>
                    </div>
                </div>
                <div class="mt-space-xs">
                    <div class="flex items-center gap-space-sm">
                        <span class="font-display text-headline-lg font-bold text-on-surface tracking-tight font-code-mono">6</span>
                        <span class="px-2 py-0.5 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-caption font-bold">
                            Perlu Tindakan
                        </span>
                    </div>
                    <p class="font-caption text-caption text-ink-muted mt-space-xs">
                        4 Perlu Dikemas • 2 Siap Dikirim
                    </p>
                </div>
                <div class="mt-space-md pt-space-xs">
                    <a class="inline-flex items-center gap-1 font-body-md text-caption font-bold text-primary-container hover:text-blue-hover" href="{{ route('seller.orders') }}">
                        <span class="">Buka Pesanan</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
        <!-- ROW 2: Chart (8 cols) + Top Products (4 cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
            <!-- Left Card: Chart (8 cols) -->
            <div class="lg:col-span-8 bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-space-sm mb-space-md">
                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Penjualan Online vs Offline (POS)</h2>
                            <p class="font-caption text-caption text-ink-muted mt-0.5">
                                Perbandingan omzet harian antara transaksi marketplace online dan transaksi langsung kasir toko.
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-subtle text-primary font-caption text-caption font-bold">
                                <span class="w-2.5 h-2.5 rounded-full bg-primary-container"></span>
                                Online: Rp 11.200.000 (60.7%)
                            </span>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-tertiary-fixed/40 text-tertiary font-caption text-caption font-bold">
                                <span class="w-2.5 h-2.5 rounded-full bg-tertiary-fixed-dim"></span>
                                POS: Rp 7.250.000 (39.3%)
                            </span>
                        </div>
                    </div>
                    <!-- SVG Dual-Series Trend Chart -->
                    <div class="relative w-full overflow-hidden pt-2 pb-4">
                        <svg class="w-full h-56 sm:h-64 overflow-visible select-none" viewBox="0 0 740 250" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <linearGradient id="onlineGradient" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#0064d2" stop-opacity="0.22"></stop>
                                    <stop offset="100%" stop-color="#0064d2" stop-opacity="0.00"></stop>
                                </linearGradient>
                                <linearGradient id="posGradient" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#86B817" stop-opacity="0.18"></stop>
                                    <stop offset="100%" stop-color="#86B817" stop-opacity="0.00"></stop>
                                </linearGradient>
                            </defs>
                            <!-- Gridlines (Horizontal) -->
                            <line stroke="#eae7e7" stroke-dasharray="3 3" x1="50" x2="720" y1="30" y2="30"></line>
                            <line stroke="#eae7e7" stroke-dasharray="3 3" x1="50" x2="720" y1="85" y2="85"></line>
                            <line stroke="#eae7e7" stroke-dasharray="3 3" x1="50" x2="720" y1="140" y2="140"></line>
                            <line stroke="#eae7e7" x1="50" x2="720" y1="195" y2="195"></line>
                            <!-- Y-Axis Labels -->
                            <text class="fill-ink-muted text-[11px] font-code-mono" text-anchor="end" x="42" y="34">Rp 3 Jt</text>
                            <text class="fill-ink-muted text-[11px] font-code-mono" text-anchor="end" x="42" y="89">Rp 2 Jt</text>
                            <text class="fill-ink-muted text-[11px] font-code-mono" text-anchor="end" x="42" y="144">Rp 1 Jt</text>
                            <text class="fill-ink-muted text-[11px] font-code-mono" text-anchor="end" x="42" y="199">Rp 0</text>
                            <!-- Series 1: Online (Blue Fill & Line) -->
                            <path d="M 85 120 L 175 95 L 265 135 L 355 70 L 445 80 L 535 50 L 625 65 L 705 45 L 705 195 L 85 195 Z" fill="url(#onlineGradient)"></path>
                            <path d="M 85 120 L 175 95 L 265 135 L 355 70 L 445 80 L 535 50 L 625 65 L 705 45" fill="none" stroke="#0064d2" stroke-linecap="round" stroke-linejoin="round" stroke-width="3"></path>
                            <!-- Series 2: POS Kasir (Green Stroke) -->
                            <path d="M 85 160 L 175 145 L 265 110 L 355 125 L 445 130 L 535 105 L 625 95 L 705 85 L 705 195 L 85 195 Z" fill="url(#posGradient)"></path>
                            <path d="M 85 160 L 175 145 L 265 110 L 355 125 L 445 130 L 535 105 L 625 95 L 705 85" fill="none" stroke="#86B817" stroke-dasharray="4 3" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"></path>
                            <!-- Interactive Hover Anchors with Dots -->
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="85" cy="120" fill="#ffffff" r="4.5" stroke="#0064d2" stroke-width="2.5"></circle>
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="85" cy="160" fill="#ffffff" r="4" stroke="#86B817" stroke-width="2"></circle>
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="175" cy="95" fill="#ffffff" r="4.5" stroke="#0064d2" stroke-width="2.5"></circle>
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="175" cy="145" fill="#ffffff" r="4" stroke="#86B817" stroke-width="2"></circle>
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="265" cy="135" fill="#ffffff" r="4.5" stroke="#0064d2" stroke-width="2.5"></circle>
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="265" cy="110" fill="#ffffff" r="4" stroke="#86B817" stroke-width="2"></circle>
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="355" cy="70" fill="#ffffff" r="4.5" stroke="#0064d2" stroke-width="2.5"></circle>
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="355" cy="125" fill="#ffffff" r="4" stroke="#86B817" stroke-width="2"></circle>
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="445" cy="80" fill="#ffffff" r="4.5" stroke="#0064d2" stroke-width="2.5"></circle>
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="445" cy="130" fill="#ffffff" r="4" stroke="#86B817" stroke-width="2"></circle>
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="535" cy="50" fill="#ffffff" r="4.5" stroke="#0064d2" stroke-width="2.5"></circle>
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="535" cy="105" fill="#ffffff" r="4" stroke="#86B817" stroke-width="2"></circle>
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="625" cy="65" fill="#ffffff" r="4.5" stroke="#0064d2" stroke-width="2.5"></circle>
                            <circle class="cursor-pointer hover:r-6 transition-all" cx="625" cy="95" fill="#ffffff" r="4" stroke="#86B817" stroke-width="2"></circle>
                            <!-- Pt 8 (Peak) -->
                            <circle class="cursor-pointer" cx="705" cy="45" fill="#0064d2" r="5.5" stroke="#ffffff" stroke-width="2.5"></circle>
                            <circle class="cursor-pointer" cx="705" cy="85" fill="#86B817" r="4" stroke="#ffffff" stroke-width="2"></circle>
                            <!-- Tooltip callout for Pt 6 / 21-24 Okt -->
                            <g transform="translate(485, 8)">
                                <rect fill="#1c1b1b" height="34" opacity="0.92" rx="6" width="105"></rect>
                                <text class="text-[10px] font-sans font-bold" fill="#ffffff" x="8" y="15">21-24 Okt</text>
                                <text class="text-[10px] font-code-mono" fill="#adc6ff" x="8" y="27">Onl: Rp 2.45Jt</text>
                                <text class="text-[10px] font-code-mono" fill="#c2f959" x="64" y="27">POS: 1.6Jt</text>
                            </g>
                            <!-- X-Axis Labels -->
                            <text class="fill-ink-muted text-[11px] font-sans" text-anchor="middle" x="85" y="215">1-4 Okt</text>
                            <text class="fill-ink-muted text-[11px] font-sans" text-anchor="middle" x="175" y="215">5-8 Okt</text>
                            <text class="fill-ink-muted text-[11px] font-sans" text-anchor="middle" x="265" y="215">9-12 Okt</text>
                            <text class="fill-ink-muted text-[11px] font-sans" text-anchor="middle" x="355" y="215">13-16 Okt</text>
                            <text class="fill-ink-muted text-[11px] font-sans" text-anchor="middle" x="445" y="215">17-20 Okt</text>
                            <text class="fill-ink-muted text-[11px] font-sans font-bold fill-primary" text-anchor="middle" x="535" y="215">21-24 Okt</text>
                            <text class="fill-ink-muted text-[11px] font-sans" text-anchor="middle" x="625" y="215">25-28 Okt</text>
                            <text class="fill-ink-muted text-[11px] font-sans" text-anchor="middle" x="705" y="215">29-31 Okt</text>
                        </svg>
                    </div>
                </div>
                <div class="mt-space-sm pt-space-sm bg-surface-container-low rounded-lg px-space-md py-2 flex items-center justify-between text-caption font-caption text-ink-muted flex-wrap gap-2">
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-tertiary">insights</span>
                        <span class="">Rata-rata transaksi harian: <strong class="text-on-surface font-code-mono">Rp 615.000</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[16px] text-primary-container">schedule</span>
                        <span class="">Jam tersibuk kasir POS: <strong class="text-on-surface">13:00 - 17:00 WIB</strong></span>
                    </div>
                </div>
            </div>
            <!-- Right Card: Top Products (4 cols) -->
            <div class="lg:col-span-4 bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-space-md">
                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Produk Terlaris</h2>
                            <span class="font-caption text-caption text-ink-muted">Berdasarkan volume unit terjual</span>
                        </div>
                        <span class="w-8 h-8 rounded-full bg-blue-subtle text-primary flex items-center justify-center font-bold text-caption font-caption">
                            {{ $topProducts->count() }}
                        </span>
                    </div>
                    <div class="flex flex-col gap-space-md">
                        @forelse ($topProducts as $product)
                            <!-- Rank {{ $loop->iteration }} -->
                            <div class="flex items-center gap-space-sm group">
                                <div class="relative w-12 h-12 rounded-lg overflow-hidden bg-surface-container flex-shrink-0">
                                    <img class="w-full h-full object-cover" alt="{{ $product->name }}" src="{{ $product->image }}">
                                    <span class="absolute top-0 left-0 {{ $loop->first ? 'bg-accent-yellow text-on-accent-yellow' : 'bg-surface-container-highest text-on-surface' }} font-caption text-[10px] font-bold px-1.5 rounded-br">#{{ $loop->iteration }}</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-body-md text-caption font-bold text-on-surface truncate group-hover:text-primary transition-colors">
                                        {{ $product->name }}
                                    </h4>
                                    <div class="flex items-center justify-between font-caption text-[11px] text-ink-muted mt-0.5">
                                        <span class="font-code-mono font-medium text-tertiary">{{ (int) $product->sold_count }} terjual</span>
                                        <span class="font-code-mono font-bold text-on-surface">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-space-sm rounded-lg bg-surface-container-low text-center">
                                <p class="font-body-md text-caption text-ink-muted">Belum ada produk terlaris di toko Anda.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="mt-space-md pt-space-xs text-center">
                    <a class="inline-flex items-center gap-1 font-body-md text-caption font-bold text-primary-container hover:text-blue-hover" href="#">
                        <span class="">Lihat Analisis Produk Lengkap</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>
            </div>
        </div>
        <!-- ROW 3: Recent Orders Table (8 cols) + Low Stock Alert (4 cols) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
            <!-- Left Table Card (8 cols) -->
            <div class="lg:col-span-8 bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-space-sm mb-space-md">
                        <div>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Pesanan Terbaru</h2>
                            <span class="font-caption text-caption text-ink-muted">Sinkronisasi langsung pesanan Marketplace dan Kasir POS</span>
                        </div>
                        <!-- Filters -->
                        <div class="flex items-center gap-1.5">
                            <button class="px-3 py-1 rounded-full bg-primary-container text-on-primary font-caption text-caption font-bold transition-colors" id="tab-order-semua" onclick="filterOrderTab('semua')" type="button">
                                Semua ({{ $recentOrders->count() }})
                            </button>
                            <button class="px-3 py-1 rounded-full bg-surface-container text-on-surface font-caption text-caption font-bold hover:bg-blue-subtle transition-colors" id="tab-order-online" onclick="filterOrderTab('online')" type="button">
                                Online ({{ $onlineOrderCount }})
                            </button>
                            <button class="px-3 py-1 rounded-full bg-surface-container text-on-surface font-caption text-caption font-bold hover:bg-blue-subtle transition-colors" id="tab-order-pos" onclick="filterOrderTab('pos')" type="button">
                                POS ({{ $posOrderCount }})
                            </button>
                        </div>
                    </div>
                    <!-- Table Container -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-surface-container-low text-ink-muted font-caption text-[11px] uppercase tracking-wider">
                                    <th class="py-2.5 px-3 rounded-l-lg font-bold">Kode Pesanan</th>
                                    <th class="py-2.5 px-3 font-bold">Pelanggan</th>
                                    <th class="py-2.5 px-3 font-bold">Saluran</th>
                                    <th class="py-2.5 px-3 font-bold">Total</th>
                                    <th class="py-2.5 px-3 font-bold">Status</th>
                                    <th class="py-2.5 px-3 rounded-r-lg font-bold text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-transparent font-body-md text-caption">
                                @forelse ($recentOrders as $order)
                                    @php
                                        $buyerName = $order->customer_name ?? $order->user?->name ?? 'Pembeli Tamu';
                                        $isPos = ($order->channel ?? 'online') === 'pos';
                                        $orderTime = $order->created_at->isToday()
                                            ? $order->created_at->format('H:i') . ' WIB'
                                            : ($order->created_at->isYesterday()
                                                ? 'Kemarin ' . $order->created_at->format('H:i')
                                                : $order->created_at->format('d M H:i'));
                                        if ($isPos) {
                                            $action = ['Struk', route('seller.pos.receipt', $order->order_code), 'px-3 py-1 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface font-caption text-caption font-bold transition-colors'];
                                        } elseif ($order->status === 'dibayar') {
                                            $action = ['Proses', route('seller.orders.show', $order->order_code), 'px-3 py-1 rounded-full bg-primary-container text-on-primary font-caption text-caption font-bold hover:bg-blue-hover transition-colors shadow-sm'];
                                        } elseif ($order->status === 'diproses') {
                                            $action = ['Kirim Resi', route('seller.orders.show', $order->order_code), 'px-3 py-1 rounded-full bg-primary-container text-on-primary font-caption text-caption font-bold hover:bg-blue-hover transition-colors shadow-sm'];
                                        } elseif ($order->status === 'dikirim') {
                                            $action = ['Lacak', route('seller.orders.show', $order->order_code), 'px-3 py-1 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface font-caption text-caption font-bold transition-colors'];
                                        } else {
                                            $action = ['Detail', route('seller.orders.show', $order->order_code), 'px-3 py-1 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface font-caption text-caption font-bold transition-colors'];
                                        }
                                        $statusMap = [
                                            'menunggu_pembayaran' => ['Menunggu Pembayaran', 'bg-accent-yellow/20 text-on-accent-yellow'],
                                            'dibayar' => ['Dibayar', 'bg-tertiary-fixed/40 text-tertiary'],
                                            'diproses' => ['Diproses', 'bg-blue-subtle text-primary'],
                                            'dikirim' => ['Dikirim', 'bg-blue-subtle text-primary'],
                                            'selesai' => ['Selesai', 'bg-[#EBF5D6] text-success-text'],
                                            'dibatalkan' => ['Dibatalkan', 'bg-error-container text-secondary'],
                                            'kedaluwarsa' => ['Kedaluwarsa', 'bg-surface-container text-ink-muted'],
                                        ];
                                        [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status, 'bg-surface-container text-ink-muted'];
                                    @endphp
                                    <tr class="order-row {{ $isPos ? 'order-type-pos' : 'order-type-online' }} hover:bg-surface-container-low/60 transition-colors">
                                        <td class="py-3 px-3">
                                            <span class="font-code-mono text-code-mono font-bold text-on-surface">{{ $order->order_code }}</span>
                                            <span class="block text-[11px] text-ink-muted">{{ $orderTime }}</span>
                                        </td>
                                        <td class="py-3 px-3 font-medium text-on-surface">{{ $buyerName }}</td>
                                        <td class="py-3 px-3">
                                            @if ($isPos)
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-tertiary-fixed/30 text-tertiary font-caption text-[11px] font-bold">
                                                    <span class="material-symbols-outlined text-[13px]">point_of_sale</span>
                                                    POS Toko
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-blue-subtle text-primary font-caption text-[11px] font-bold">
                                                    <span class="material-symbols-outlined text-[13px]">shopping_cart</span>
                                                    Online
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-3 font-code-mono font-bold text-on-surface">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</td>
                                        <td class="py-3 px-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full {{ $statusClass }} font-caption text-[11px] font-bold">
                                                {{ $statusLabel }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-3 text-right">
                                            <a class="{{ $action[2] }}" href="{{ $action[1] }}">
                                                {{ $action[0] }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="py-6 px-3 text-center text-ink-muted" colspan="6">Belum ada pesanan masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-space-md pt-space-xs text-center border-t border-border-default/50">
                    <a class="inline-flex items-center gap-1 font-body-md text-caption font-bold text-primary-container hover:text-blue-hover" href="{{ route('seller.orders') }}">
                        <span class="">Lihat Semua Pesanan (164)</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>
            </div>
            <!-- Right Card: Stock Alert (4 cols) -->
            <div class="lg:col-span-4 bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-space-sm">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary text-[22px]">warning</span>
                            <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Peringatan Stok</h2>
                        </div>
                        <a class="font-caption text-caption font-bold text-primary-container hover:text-blue-hover" href="{{ route('seller.stock') }}">
                            Kelola Stok →
                        </a>
                    </div>
                    <p class="font-caption text-caption text-ink-muted mb-space-md">
                        Terdeteksi {{ (int) $stats['low_stock_count'] }} produk berada di bawah batas minimum (5 unit).
                    </p>
                    <div class="flex flex-col gap-space-sm">
                        @forelse ($lowStockProducts as $product)
                            @php $isCritical = (int) $product->stock <= 2; @endphp
                            <div class="p-space-sm rounded-lg {{ $isCritical ? 'bg-error-container/40' : 'bg-surface-container-low' }} flex items-center justify-between gap-space-sm">
                                <div class="min-w-0 flex-1">
                                    <p class="font-body-md text-caption font-bold text-on-surface truncate">
                                        {{ $product->name }}
                                    </p>
                                    <div class="flex items-center gap-2 mt-1">
                                        @if ($isCritical)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-secondary text-on-secondary font-caption text-[11px] font-bold">
                                                Kritis! Sisa {{ (int) $product->stock }} unit
                                            </span>
                                            <span class="font-caption text-[11px] text-secondary">{{ $product->category?->name ?? 'Tanpa Kategori' }}</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-[11px] font-bold">
                                                Sisa {{ (int) $product->stock }} unit
                                            </span>
                                            <span class="font-caption text-[11px] text-ink-muted">{{ $product->category?->name ?? 'Tanpa Kategori' }}</span>
                                        @endif
                                    </div>
                                </div>
                                <a class="px-3 py-1 rounded-full {{ $isCritical ? 'bg-secondary hover:bg-secondary-container text-on-secondary' : 'bg-primary-container hover:bg-blue-hover text-on-primary' }} font-caption text-caption font-bold whitespace-nowrap transition-colors shadow-sm" href="{{ route('seller.stock') }}">
                                    + Tambah
                                </a>
                            </div>
                        @empty
                            <div class="p-space-sm rounded-lg bg-surface-container-low flex items-center gap-2.5">
                                <span class="material-symbols-outlined text-tertiary text-[20px]">check_circle</span>
                                <p class="font-body-md text-caption text-on-surface-variant">Semua stok produk aman di atas batas minimum.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                <!-- Sync Feature Banner -->
                <div class="mt-space-md p-space-sm rounded-lg bg-blue-subtle/80 flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">sync_alt</span>
                    <p class="font-caption text-[11px] text-primary leading-snug">
                        <strong>Sinkronisasi Otomatis Stok:</strong> Setiap transaksi kasir POS langsung mengurangi ketersediaan etalase online secara realtime tanpa perlu update manual.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <!-- ================= STATE 2: LOADING SKELETON ================= -->
    <div class="flex-col gap-space-xl animate-pulse hidden" id="view-skeleton">
        <!-- Skeleton KPI Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-lg">
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm h-36 flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <div class="h-3 w-24 bg-surface-container-high rounded"></div>
                    <div class="h-8 w-8 bg-surface-container-high rounded-full"></div>
                </div>
                <div class="h-8 w-36 bg-surface-container-highest rounded"></div>
                <div class="h-3 w-32 bg-surface-container-high rounded"></div>
            </div>
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm h-36 flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <div class="h-3 w-24 bg-surface-container-high rounded"></div>
                    <div class="h-8 w-8 bg-surface-container-high rounded-full"></div>
                </div>
                <div class="h-8 w-24 bg-surface-container-highest rounded"></div>
                <div class="h-3 w-28 bg-surface-container-high rounded"></div>
            </div>
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm h-36 flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <div class="h-3 w-24 bg-surface-container-high rounded"></div>
                    <div class="h-8 w-8 bg-surface-container-high rounded-full"></div>
                </div>
                <div class="h-8 w-20 bg-surface-container-highest rounded"></div>
                <div class="h-3 w-36 bg-surface-container-high rounded"></div>
            </div>
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm h-36 flex flex-col justify-between">
                <div class="flex justify-between items-center">
                    <div class="h-3 w-24 bg-surface-container-high rounded"></div>
                    <div class="h-8 w-8 bg-surface-container-high rounded-full"></div>
                </div>
                <div class="h-8 w-20 bg-surface-container-highest rounded"></div>
                <div class="h-3 w-32 bg-surface-container-high rounded"></div>
            </div>
        </div>
        <!-- Skeleton Chart + Products -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg">
            <div class="lg:col-span-8 bg-surface-card rounded-xl p-space-lg shadow-sm h-96 flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="h-5 w-56 bg-surface-container-highest rounded"></div>
                    <div class="h-3 w-80 bg-surface-container-high rounded"></div>
                </div>
                <div class="h-52 w-full bg-surface-container-low rounded-lg"></div>
                <div class="h-4 w-full bg-surface-container-high rounded"></div>
            </div>
            <div class="lg:col-span-4 bg-surface-card rounded-xl p-space-lg shadow-sm h-96 flex flex-col justify-between">
                <div class="h-5 w-40 bg-surface-container-highest rounded mb-4"></div>
                <div class="space-y-3">
                    <div class="h-10 w-full bg-surface-container-high rounded"></div>
                    <div class="h-10 w-full bg-surface-container-high rounded"></div>
                    <div class="h-10 w-full bg-surface-container-high rounded"></div>
                    <div class="h-10 w-full bg-surface-container-high rounded"></div>
                    <div class="h-10 w-full bg-surface-container-high rounded"></div>
                </div>
                <div class="h-4 w-32 mx-auto bg-surface-container-high rounded"></div>
            </div>
        </div>
    </div>
    <!-- ================= STATE 3: DATA KOSONG (EMPTY STATE) ================= -->
    <div class="flex-col items-center justify-center bg-surface-card rounded-2xl p-space-xl sm:p-16 shadow-sm text-center my-6 hidden" id="view-empty">
        <div class="w-24 h-24 rounded-full bg-blue-subtle text-primary flex items-center justify-center mb-space-md shadow-inner">
            <span class="material-symbols-outlined text-[48px]">store</span>
        </div>
        <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold mb-2">
            Belum Ada Penjualan di Periode Ini
        </h3>
        <p class="font-body-md text-body-md text-ink-muted max-w-md mb-space-lg">
            Mulai lakukan transaksi kasir pertama Anda atau bagikan tautan toko UMKM Anda ke pelanggan via WhatsApp dan sosial media.
        </p>
        <div class="flex flex-wrap items-center justify-center gap-space-sm">
            <a class="h-11 px-6 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-body-md text-body-md font-bold transition-colors flex items-center gap-2 shadow-sm" href="{{ route('seller.pos') }}">
                <span class="material-symbols-outlined text-[20px]">point_of_sale</span>
                <span class="">Buka Kasir POS Sekarang</span>
            </a>
            <button class="h-11 px-6 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface font-body-md text-body-md font-bold transition-colors flex items-center gap-2" onclick="switchDashboardState('normal')" type="button">
                <span class="material-symbols-outlined text-[20px]">refresh</span>
                <span class="">Kembalikan Data Contoh</span>
            </button>
        </div>
    </div>
    <!-- Micro-interaction Toast / Notification Modal simulation -->
    <div class="fixed bottom-6 right-6 z-50 transform translate-y-24 opacity-0 transition-all duration-300 pointer-events-none" id="dash-toast">
        <div class="bg-on-surface text-on-primary rounded-xl px-4 py-3 shadow-xl flex items-center gap-3 font-body-md text-caption font-medium">
            <span class="material-symbols-outlined text-[20px] text-tertiary-fixed">check_circle</span>
            <span id="toast-text" class="">Laporan Excel berhasil diunduh!</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
  function switchDashboardState(state) {
    const normalView = document.getElementById('view-normal');
    const skeletonView = document.getElementById('view-skeleton');
    const emptyView = document.getElementById('view-empty');

    // Hide all views
    normalView.classList.add('hidden');
    skeletonView.classList.add('hidden');
    emptyView.classList.add('hidden');

    if (state === 'skeleton') {
      skeletonView.classList.remove('hidden');
    } else if (state === 'empty') {
      emptyView.classList.remove('hidden');
    } else {
      normalView.classList.remove('hidden');
    }
  }

  function toggleDateDropdown() {
    const menu = document.getElementById('date-dropdown-menu');
    menu.classList.toggle('hidden');
  }

  function selectDateRange(label) {
    document.getElementById('selected-date-label').innerText = label;
    document.getElementById('date-dropdown-menu').classList.add('hidden');
    showToast('Rentang data disaring: ' + label);
  }

  function triggerExportNotice() {
    showToast('Mengekspor laporan penjualan format .xlsx...');
  }

  function showToast(message) {
    const toast = document.getElementById('dash-toast');
    const toastText = document.getElementById('toast-text');
    toastText.innerText = message;
    toast.classList.remove('translate-y-24', 'opacity-0');
    toast.classList.add('translate-y-0', 'opacity-100');
    setTimeout(() => {
      toast.classList.remove('translate-y-0', 'opacity-100');
      toast.classList.add('translate-y-24', 'opacity-0');
    }, 2800);
  }

  function filterOrderTab(type) {
    const tabSemua = document.getElementById('tab-order-semua');
    const tabOnline = document.getElementById('tab-order-online');
    const tabPos = document.getElementById('tab-order-pos');
    const rows = document.querySelectorAll('.order-row');

    // Reset styles
    [tabSemua, tabOnline, tabPos].forEach(tab => {
      tab.className = 'px-3 py-1 rounded-full bg-surface-container text-on-surface font-caption text-caption font-bold hover:bg-blue-subtle transition-colors';
    });

    if (type === 'semua') {
      tabSemua.className = 'px-3 py-1 rounded-full bg-primary-container text-on-primary font-caption text-caption font-bold transition-colors';
      rows.forEach(r => r.style.display = '');
    } else if (type === 'online') {
      tabOnline.className = 'px-3 py-1 rounded-full bg-primary-container text-on-primary font-caption text-caption font-bold transition-colors';
      rows.forEach(r => {
        r.style.display = r.classList.contains('order-type-online') ? '' : 'none';
      });
    } else if (type === 'pos') {
      tabPos.className = 'px-3 py-1 rounded-full bg-primary-container text-on-primary font-caption text-caption font-bold transition-colors';
      rows.forEach(r => {
        r.style.display = r.classList.contains('order-type-pos') ? '' : 'none';
      });
    }
  }

  // Close dropdown upon outside click
  window.addEventListener('click', function(e) {
    const container = document.getElementById('date-range-container');
    const menu = document.getElementById('date-dropdown-menu');
    if (container && !container.contains(e.target)) {
      menu.classList.add('hidden');
    }
  });
</script>
@endpush
