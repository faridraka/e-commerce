@extends('layouts.seller')

@section('title', 'Dasbor Penjual - Lokalku')

@section('breadcrumb', 'Dasbor')

@php
    $posOrderCount = $recentOrders->filter(fn ($o) => $o->channel === 'pos')->count();
    $onlineOrderCount = $recentOrders->count() - $posOrderCount;

    $periods = [
        'today' => ['Hari Ini', 'calendar_today'],
        '7d' => ['7 Hari Terakhir', 'view_week'],
        '30d' => ['30 Hari Terakhir', 'calendar_view_month'],
        'month' => ['Bulan Ini', 'calendar_view_day'],
    ];

    $statusMap = [
        \App\Models\Order::STATUS_MENUNGGU_PEMBAYARAN => ['Menunggu Pembayaran', 'bg-accent-yellow/20 text-on-accent-yellow'],
        \App\Models\Order::STATUS_DIBAYAR => ['Dibayar', 'bg-tertiary-fixed/40 text-tertiary'],
        \App\Models\Order::STATUS_DIPROSES => ['Diproses', 'bg-blue-subtle text-primary'],
        \App\Models\Order::STATUS_DIKIRIM => ['Dikirim', 'bg-blue-subtle text-primary'],
        \App\Models\Order::STATUS_SELESAI => ['Selesai', 'bg-[#EBF5D6] text-success-text'],
        \App\Models\Order::STATUS_DIBATALKAN => ['Dibatalkan', 'bg-error-container text-secondary'],
        \App\Models\Order::STATUS_KEDALUWARSA => ['Kedaluwarsa', 'bg-surface-container text-ink-muted'],
    ];
@endphp

@section('content')
<div class="flex flex-col w-full">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-space-md mb-space-xl">
        <div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Dashboard</h1>
            <p class="font-body-md text-body-md text-ink-muted mt-0.5">
                Ikhtisar performa penjualan online dan kasir POS toko Anda.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-space-sm">
            <!-- Filter periode (link GET nyata, memuat ulang data) -->
            <div class="flex items-center gap-1 p-1 rounded-full bg-surface-card shadow-sm">
                @foreach ($periods as $key => [$label, $icon])
                    @if ($key === $period)
                        <span class="h-8 px-3 rounded-full bg-primary-container text-on-primary font-caption text-caption font-bold inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[16px]">{{ $icon }}</span>
                            {{ $label }}
                        </span>
                    @else
                        <a class="h-8 px-3 rounded-full text-on-surface font-caption text-caption font-bold hover:bg-surface-container transition-colors inline-flex items-center gap-1.5" href="{{ route('seller.dashboard', ['period' => $key]) }}">
                            {{ $label }}
                        </a>
                    @endif
                @endforeach
            </div>
            <a class="h-10 px-5 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-body-md text-body-md font-bold transition-colors flex items-center gap-1.5 shadow-sm" href="{{ route('seller.pos') }}">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span>+ Transaksi POS Baru</span>
            </a>
        </div>
    </div>

    <!-- ROW 1: 4 KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-lg">
        <!-- KPI 1: Pendapatan periode -->
        <div class="bg-surface-card rounded-xl p-space-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-space-xs">
                <span class="font-body-md text-caption uppercase tracking-wider text-ink-muted font-bold">Total Penjualan</span>
                <div class="w-9 h-9 rounded-full bg-blue-subtle text-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">payments</span>
                </div>
            </div>
            <div class="font-display text-headline-lg font-bold text-on-surface tracking-tight">Rp {{ number_format((float) $stats['revenue'], 0, ',', '.') }}</div>
            <div class="mt-space-xs font-caption text-caption text-ink-muted">{{ $stats['period_label'] }}</div>
            <div class="mt-space-md pt-space-xs flex items-center justify-between font-caption text-caption text-ink-muted">
                <span>Online Rp {{ number_format((float) $stats['online_revenue'], 0, ',', '.') }}</span>
                <span>POS Rp {{ number_format((float) $stats['pos_revenue'], 0, ',', '.') }}</span>
            </div>
            <div class="mt-space-md pt-space-xs">
                <a class="inline-flex items-center gap-1 font-body-md text-caption font-bold text-primary-container hover:text-blue-hover" href="{{ route('seller.pos.history') }}">
                    <span>Riwayat Transaksi</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>
        <!-- KPI 2: Pesanan periode -->
        <div class="bg-surface-card rounded-xl p-space-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-space-xs">
                <span class="font-body-md text-caption uppercase tracking-wider text-ink-muted font-bold">Jumlah Pesanan</span>
                <div class="w-9 h-9 rounded-full bg-blue-subtle text-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">shopping_bag</span>
                </div>
            </div>
            <div class="font-display text-headline-lg font-bold text-on-surface tracking-tight font-code-mono">{{ (int) $stats['orders_count'] }}</div>
            <div class="mt-space-xs font-caption text-caption text-ink-muted">{{ $stats['period_label'] }} • Online {{ (int) $stats['online_orders_count'] }} / Walk-in POS {{ (int) $stats['pos_orders_count'] }}</div>
            <div class="mt-space-md pt-space-xs">
                <a class="inline-flex items-center gap-1 font-body-md text-caption font-bold text-primary-container hover:text-blue-hover" href="{{ route('seller.orders') }}">
                    <span>Buka Pesanan</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>
        <!-- KPI 3: Produk aktif -->
        <div class="bg-surface-card rounded-xl p-space-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-space-xs">
                <span class="font-body-md text-caption uppercase tracking-wider text-ink-muted font-bold">Produk Aktif</span>
                <div class="w-9 h-9 rounded-full bg-blue-subtle text-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">inventory_2</span>
                </div>
            </div>
            <div class="font-display text-headline-lg font-bold text-on-surface tracking-tight font-code-mono">{{ (int) $stats['products_count'] }}</div>
            <p class="mt-space-xs font-caption text-caption text-ink-muted">
                Semua produk sinkron di etalase &amp; POS
            </p>
            <div class="mt-space-md pt-space-xs">
                <a class="inline-flex items-center gap-1 font-body-md text-caption font-bold text-primary-container hover:text-blue-hover" href="{{ route('seller.products') }}">
                    <span>Lihat Katalog</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>
        <!-- KPI 4: Perlu diproses -->
        <div class="bg-surface-card rounded-xl p-space-lg shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-space-xs">
                <span class="font-body-md text-caption uppercase tracking-wider text-ink-muted font-bold">Perlu Diproses</span>
                <div class="w-9 h-9 rounded-full bg-accent-yellow/20 text-on-accent-yellow flex items-center justify-center">
                    <span class="material-symbols-outlined text-[20px]">pending_actions</span>
                </div>
            </div>
            <div class="flex items-center gap-space-sm">
                <span class="font-display text-headline-lg font-bold text-on-surface tracking-tight font-code-mono">{{ $pendingCount }}</span>
                @if ($pendingCount > 0)
                    <span class="px-2 py-0.5 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-caption font-bold">Perlu Tindakan</span>
                @endif
            </div>
            <p class="mt-space-xs font-caption text-caption text-ink-muted">
                {{ $pendingCount }} Perlu Dikemas • {{ $processedCount }} Siap Dikirim
            </p>
            <div class="mt-space-md pt-space-xs">
                <a class="inline-flex items-center gap-1 font-body-md text-caption font-bold text-primary-container hover:text-blue-hover" href="{{ route('seller.orders') }}">
                    <span>Buka Pesanan</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ROW 2: Recent Orders (8 cols) + Top Products (4 cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg mt-space-xl">
        <!-- Left: Recent Orders -->
        <div class="lg:col-span-8 bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-space-sm mb-space-md">
                    <div>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Pesanan Terbaru</h2>
                        <span class="font-caption text-caption text-ink-muted">5 pesanan terakhir dari etalase online dan kasir POS</span>
                    </div>
                </div>
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
                                    $isPos = $order->channel === 'pos';
                                    $orderTime = $order->created_at->isToday()
                                        ? $order->created_at->format('H:i') . ' WIB'
                                        : ($order->created_at->isYesterday()
                                            ? 'Kemarin ' . $order->created_at->format('H:i')
                                            : $order->created_at->format('d M H:i'));
                                    if ($isPos) {
                                        $action = ['Struk', route('seller.pos.receipt', $order->order_code), 'px-3 py-1 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface font-caption text-caption font-bold transition-colors'];
                                    } elseif ($order->status === \App\Models\Order::STATUS_DIBAYAR) {
                                        $action = ['Proses', route('seller.orders.show', $order->order_code), 'px-3 py-1 rounded-full bg-primary-container text-on-primary font-caption text-caption font-bold hover:bg-blue-hover transition-colors shadow-sm'];
                                    } elseif ($order->status === \App\Models\Order::STATUS_DIPROSES) {
                                        $action = ['Kirim Resi', route('seller.orders.show', $order->order_code), 'px-3 py-1 rounded-full bg-primary-container text-on-primary font-caption text-caption font-bold hover:bg-blue-hover transition-colors shadow-sm'];
                                    } elseif ($order->status === \App\Models\Order::STATUS_DIKIRIM) {
                                        $action = ['Lacak', route('seller.orders.show', $order->order_code), 'px-3 py-1 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface font-caption text-caption font-bold transition-colors'];
                                    } else {
                                        $action = ['Detail', route('seller.orders.show', $order->order_code), 'px-3 py-1 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface font-caption text-caption font-bold transition-colors'];
                                    }
                                    [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status, 'bg-surface-container text-ink-muted'];
                                @endphp
                                <tr class="hover:bg-surface-container-low/60 transition-colors">
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
                                        <a class="{{ $action[2] }}" href="{{ $action[1] }}">{{ $action[0] }}</a>
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
                    <span>Lihat Semua Pesanan</span>
                    <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            </div>
        </div>
        <!-- Right column -->
        <div class="lg:col-span-4 flex flex-col gap-space-lg">
            <!-- Top Products -->
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm">
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
                        <div class="flex items-center gap-space-sm group">
                            <div class="relative w-12 h-12 rounded-lg overflow-hidden bg-surface-container flex-shrink-0">
                                <img class="w-full h-full object-cover" alt="{{ $product->name }}" src="{{ $product->image }}">
                                <span class="absolute top-0 left-0 {{ $loop->first ? 'bg-accent-yellow text-on-accent-yellow' : 'bg-surface-container-highest text-on-surface' }} font-caption text-[10px] font-bold px-1.5 rounded-br">#{{ $loop->iteration }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-body-md text-caption font-bold text-on-surface truncate group-hover:text-primary transition-colors">{{ $product->name }}</h4>
                                <div class="flex items-center justify-between font-caption text-[11px] text-ink-muted mt-0.5">
                                    <span class="font-code-mono font-medium text-tertiary">{{ (int) $product->sold_count }} terjual</span>
                                    <span class="font-code-mono font-bold text-on-surface">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-space-sm rounded-lg bg-surface-container-low text-center">
                            <p class="font-body-md text-caption text-ink-muted">Belum ada penjualan produk. Angka terlaris muncul setelah ada transaksi.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            <!-- Low Stock Alert -->
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm">
                <div class="flex items-center justify-between mb-space-sm">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary text-[22px]">warning</span>
                        <h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Peringatan Stok</h2>
                    </div>
                    <a class="font-caption text-caption font-bold text-primary-container hover:text-blue-hover" href="{{ route('seller.stock') }}">Kelola Stok →</a>
                </div>
                <p class="font-caption text-caption text-ink-muted mb-space-md">
                    Terdeteksi {{ (int) $stats['low_stock_count'] }} produk berada di bawah batas minimum (5 unit).
                </p>
                <div class="flex flex-col gap-space-sm">
                    @forelse ($lowStockProducts as $product)
                        @php $isCritical = (int) $product->stock <= 2; @endphp
                        <div class="p-space-sm rounded-lg {{ $isCritical ? 'bg-error-container/40' : 'bg-surface-container-low' }} flex items-center justify-between gap-space-sm">
                            <div class="min-w-0 flex-1">
                                <p class="font-body-md text-caption font-bold text-on-surface truncate">{{ $product->name }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    @if ($isCritical)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-secondary text-on-secondary font-caption text-[11px] font-bold">Kritis! Sisa {{ (int) $product->stock }} unit</span>
                                        <span class="font-caption text-[11px] text-secondary">{{ $product->category?->name ?? 'Tanpa Kategori' }}</span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-[11px] font-bold">Sisa {{ (int) $product->stock }} unit</span>
                                        <span class="font-caption text-[11px] text-ink-muted">{{ $product->category?->name ?? 'Tanpa Kategori' }}</span>
                                    @endif
                                </div>
                            </div>
                            <a class="px-3 py-1 rounded-full {{ $isCritical ? 'bg-secondary hover:bg-secondary-container text-on-secondary' : 'bg-primary-container hover:bg-blue-hover text-on-primary' }} font-caption text-caption font-bold whitespace-nowrap transition-colors shadow-sm" href="{{ route('seller.stock') }}">+ Tambah</a>
                        </div>
                    @empty
                        <div class="p-space-sm rounded-lg bg-surface-container-low flex items-center gap-2.5">
                            <span class="material-symbols-outlined text-tertiary text-[20px]">check_circle</span>
                            <p class="font-body-md text-caption text-on-surface-variant">Semua stok produk aman di atas batas minimum.</p>
                        </div>
                    @endforelse
                </div>
                <div class="mt-space-md p-space-sm rounded-lg bg-blue-subtle/80 flex items-start gap-2.5">
                    <span class="material-symbols-outlined text-primary text-[20px] shrink-0 mt-0.5">sync_alt</span>
                    <p class="font-caption text-[11px] text-primary leading-snug">
                        <strong>Sinkronisasi Otomatis Stok:</strong> Setiap transaksi kasir POS langsung mengurangi ketersediaan etalase online secara realtime tanpa perlu update manual.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
