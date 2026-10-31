@extends('layouts.seller')

@section('title', 'Riwayat Transaksi POS - Portal Penjual - Lokalku')

@section('content')
@php
    $rows = $orders->getCollection();
    $payOf = fn ($o) => $o->payments->first();

    $paidRows = $rows->filter(fn ($o) => $payOf($o)?->status === 'paid');
    $pendingRows = $rows->filter(fn ($o) => $payOf($o)?->status === 'pending');
    $unpaidRows = $rows->filter(fn ($o) => in_array($payOf($o)?->status, ['failed', 'expired', 'cancelled']));

    $salesToday = $paidRows->filter(fn ($o) => $o->created_at->isToday())->sum('total_amount');
    $salesYesterday = $paidRows->filter(fn ($o) => $o->created_at->isYesterday())->sum('total_amount');
    $growth = $salesYesterday > 0 ? round(($salesToday - $salesYesterday) / $salesYesterday * 100, 1) : null;

    $cashPaidRows = $paidRows->filter(fn ($o) => $payOf($o)?->method === 'manual');
    $cashAmount = (float) $cashPaidRows->sum('total_amount');
    $paidAmount = $cashAmount;

    $dateLabel = now()->translatedFormat('d M Y') . ' - ' . now()->translatedFormat('d M Y');
@endphp
<div class="flex flex-col w-full">
    <div class="w-full max-w-7xl mx-auto px-margin py-space-xl flex flex-col gap-space-xl">

        <!-- Header Section with Breadcrumb & Quick Action -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-space-md">
            <div class="flex flex-col gap-1">
                <!-- Breadcrumb / Shortcut Return -->
                <div class="flex items-center gap-space-xs font-caption text-caption text-on-surface-variant">
                    <a class="flex items-center gap-0.5 hover:text-primary transition-colors text-primary font-medium" href="{{ route('seller.pos') }}">
                        <span class="material-symbols-outlined text-[15px]">arrow_back</span>
                        <span class="">Terminal Kasir (F2)</span>
                    </a>
                    <span class="text-ink-muted">/</span>
                    <span class="text-on-surface font-medium">Riwayat Transaksi POS</span>
                </div>
                <div class="flex items-baseline gap-space-md mt-1">
                    <h1 class="font-headline-lg text-headline-lg text-on-surface">Riwayat Transaksi POS</h1>
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full bg-blue-subtle text-primary font-code-mono text-caption">
                        <span class="w-1.5 h-1.5 rounded-full bg-primary animate-ping"></span>
                        KASIR • {{ strtoupper(auth()->user()->name) }}
                    </span>
                </div>
            </div>
            <!-- Quick Action Buttons -->
            <div class="flex items-center gap-space-sm flex-wrap">
                <button class="h-10 px-space-md rounded-full bg-surface-card hover:bg-surface-container text-on-surface font-body-md text-body-md font-medium shadow-sm transition-all flex items-center gap-space-xs" onclick="handleExport()">
                    <span class="material-symbols-outlined text-[18px] text-tertiary">table_view</span>
                    <span class="">Ekspor Laporan (CSV)</span>
                </button>
                <button class="h-10 px-space-md rounded-full bg-on-accent-yellow hover:bg-surface-dim text-on-primary font-body-md text-body-md font-medium shadow-sm transition-all flex items-center gap-space-xs" onclick="handlePrintRekap()">
                    <span class="material-symbols-outlined text-[18px]">print</span>
                    <span class="">Cetak Rekap Harian (F9)</span>
                </button>
            </div>
        </div>

        <!-- Metric Summary Cards (Bento Style) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-space-md">
            <!-- Card 1: Total Sales Today -->
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between relative overflow-hidden group">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col">
                        <span class="font-caption text-caption text-ink-muted">TOTAL PENJUALAN HARI INI</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="font-price-lg text-price-lg text-on-surface tracking-tight">Rp {{ number_format($salesToday, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-blue-subtle text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">payments</span>
                    </div>
                </div>
                <div class="mt-space-md pt-space-xs flex items-center gap-space-xs font-caption text-caption text-success-text">
                    <span class="material-symbols-outlined text-[16px]">trending_up</span>
                    <span class="">@if ($growth !== null){{ ($growth >= 0 ? '+' : '') . number_format($growth, 1, ',', '.') }}% dibanding kemarin (Rp {{ number_format($salesYesterday, 0, ',', '.') }})@else Belum ada penjualan kemarin sebagai pembanding @endif</span>
                </div>
            </div>
            <!-- Card 2: Transactions Volume -->
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between relative overflow-hidden group">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col">
                        <span class="font-caption text-caption text-ink-muted">JUMLAH TRANSAKSI</span>
                        <div class="flex items-baseline gap-1 mt-1">
                            <span class="font-price-lg text-price-lg text-on-surface tracking-tight">{{ $orders->total() }}</span>
                            <span class="font-body-md text-body-md text-on-surface-variant font-medium">Transaksi</span>
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-surface-container text-on-surface flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">receipt_long</span>
                    </div>
                </div>
                <div class="mt-space-md pt-space-xs flex items-center gap-1.5 flex-wrap">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-caption text-caption font-bold">
                        {{ $paidRows->count() }} Selesai
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-caption font-bold">
                        {{ $pendingRows->count() }} Menunggu
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-secondary-fixed text-on-secondary-fixed font-caption text-caption font-bold">
                        {{ $unpaidRows->count() }} Batal
                    </span>
                </div>
            </div>
            <!-- Card 3: Payment Breakdown -->
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between relative overflow-hidden group">
                <div class="flex items-start justify-between">
                    <div class="flex flex-col">
                        <span class="font-caption text-caption text-ink-muted">METODE PEMBAYARAN</span>
                        <div class="flex items-center gap-space-sm mt-1">
                            <span class="font-body-md text-body-md font-bold text-on-surface">Tunai</span>
                        </div>
                    </div>
                    <div class="w-11 h-11 rounded-full bg-surface-container-low text-tertiary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[22px]">account_balance_wallet</span>
                    </div>
                </div>
                <div class="mt-space-md pt-space-xs flex flex-col gap-1">
                    <div class="flex items-center justify-between font-caption text-caption text-ink-muted">
                        <span class="font-code-mono text-on-surface">Total Penjualan Lunas</span>
                        <span class="font-code-mono text-on-surface">Rp {{ number_format($cashAmount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar Container -->
        <div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col gap-space-md">
            <!-- Upper Toolbar: Date Presets & Custom Range -->
            <div class="flex flex-wrap items-center justify-between gap-space-md">
                <!-- Date Preset Pills -->
                <div class="flex items-center gap-space-xs flex-wrap">
                    <button class="preset-pill px-space-md py-1.5 rounded-full font-body-md text-body-md font-medium transition-all bg-primary text-on-primary shadow-sm" id="preset-today" onclick="setPreset('today')">
                        Hari Ini
                    </button>
                    <button class="preset-pill px-space-md py-1.5 rounded-full font-body-md text-body-md font-medium transition-all bg-surface-card hover:bg-surface-container text-on-surface" id="preset-7days" onclick="setPreset('7days')">
                        7 Hari Terakhir
                    </button>
                    <button class="preset-pill px-space-md py-1.5 rounded-full font-body-md text-body-md font-medium transition-all bg-surface-card hover:bg-surface-container text-on-surface" id="preset-month" onclick="setPreset('month')">
                        Bulan Ini
                    </button>
                    <button class="preset-pill px-space-md py-1.5 rounded-full font-body-md text-body-md font-medium transition-all bg-surface-card hover:bg-surface-container text-on-surface" id="preset-custom" onclick="setPreset('custom')">
                        Kustom (Pilih Rentang)
                    </button>
                </div>
                <!-- Date Range Display / Input -->
                <div class="flex items-center gap-space-xs bg-surface-container-low px-space-md py-2 rounded-xl text-on-surface">
                    <span class="material-symbols-outlined text-[18px] text-primary">calendar_today</span>
                    <span class="font-code-mono text-caption text-on-surface font-medium" id="date-label">{{ $dateLabel }}</span>
                    <span class="material-symbols-outlined text-[16px] text-ink-muted cursor-pointer hover:text-on-surface">keyboard_arrow_down</span>
                </div>
            </div>
            <!-- Lower Toolbar: Search Bar & Status Filter Chips -->
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-space-md pt-space-xs">
                <!-- Search Input Bar (Touch Target 44px, autofocus) -->
                <div class="relative flex-1 min-w-[280px]">
                    <div class="absolute inset-y-0 left-0 pl-space-md flex items-center pointer-events-none text-ink-muted">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </div>
                    <input class="w-full h-11 pl-10 pr-space-md rounded-full bg-surface-container-low focus:bg-surface-card text-on-surface placeholder-ink-muted font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all" id="search-input" oninput="handleSearch(this.value)" placeholder="Cari kode transaksi POS-..., nama pelanggan, atau nominal..." type="text">
                </div>
                <!-- Status Filter Segmented Controls -->
                <div class="flex items-center gap-space-xs overflow-x-auto pb-1 lg:pb-0 shrink-0">
                    <span class="font-caption text-caption text-ink-muted mr-1 hidden sm:inline">Status:</span>
                    <button class="status-btn px-3 py-1.5 rounded-full font-caption text-caption font-bold bg-primary text-on-primary transition-all" id="filter-all" onclick="filterStatus('ALL')">
                        Semua Status ({{ $rows->count() }})
                    </button>
                    <button class="status-btn px-3 py-1.5 rounded-full font-caption text-caption font-medium bg-surface-container hover:bg-surface-container-high text-on-surface transition-all flex items-center gap-1" id="filter-dibayar" onclick="filterStatus('PAID')">
                        <span class="w-2 h-2 rounded-full bg-tertiary-container"></span>
                        <span class="">Dibayar ({{ $paidRows->count() }})</span>
                    </button>
                    <button class="status-btn px-3 py-1.5 rounded-full font-caption text-caption font-medium bg-surface-container hover:bg-surface-container-high text-on-surface transition-all flex items-center gap-1" id="filter-menunggu" onclick="filterStatus('PENDING')">
                        <span class="w-2 h-2 rounded-full bg-accent-yellow"></span>
                        <span class="">Menunggu ({{ $pendingRows->count() }})</span>
                    </button>
                    <button class="status-btn px-3 py-1.5 rounded-full font-caption text-caption font-medium bg-surface-container hover:bg-surface-container-high text-on-surface transition-all flex items-center gap-1" id="filter-dibatalkan" onclick="filterStatus('FAILED')">
                        <span class="w-2 h-2 rounded-full bg-secondary-container"></span>
                        <span class="">Gagal/Kedaluwarsa ({{ $unpaidRows->count() }})</span>
                    </button>
                </div>
            </div>
        </div>

        @if ($orders->isEmpty())
            <!-- Empty State: Belum ada transaksi POS -->
            <div class="bg-surface-card rounded-xl shadow-sm p-12 flex flex-col items-center justify-center text-center">
                <x-not-found title="Belum Ada Transaksi POS" message="Mulai transaksi pertamamu di terminal kasir." actionUrl="{{ route('seller.pos') }}" actionLabel="Buka Kasir POS" />
            </div>
        @else
            <!-- Main Data Table Container (Level 1, Elevated) -->
            <div class="bg-surface-card rounded-xl shadow-sm overflow-hidden flex flex-col" id="data-state-container">
                <div class="overflow-x-auto w-full">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-surface-container-low text-on-surface-variant font-caption text-caption tracking-wider uppercase select-none">
                                <th class="py-3.5 px-space-lg font-bold" scope="col">KODE TRANSAKSI</th>
                                <th class="py-3.5 px-space-lg font-bold" scope="col">WAKTU</th>
                                <th class="py-3.5 px-space-lg font-bold" scope="col">PELANGGAN</th>
                                <th class="py-3.5 px-space-lg font-bold" scope="col">METODE</th>
                                <th class="py-3.5 px-space-lg font-bold text-right" scope="col">TOTAL TAGIHAN</th>
                                <th class="py-3.5 px-space-lg font-bold text-center" scope="col">STATUS</th>
                                <th class="py-3.5 px-space-lg font-bold text-right" scope="col">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="font-body-md text-body-md divide-y-0 text-on-surface" id="transaction-rows">
                            @foreach ($rows as $order)
                                @php
                                    $pay = $payOf($order);
                                    $payStatus = $pay?->status ?? 'pending';
                                    $isVoid = in_array($payStatus, ['failed', 'expired', 'cancelled']);
                                @endphp
                                <tr class="hover:bg-surface-container-low/60 transition-colors {{ $payStatus === 'pending' ? 'bg-accent-yellow/5' : '' }}{{ $isVoid ? ' opacity-80' : '' }} group" data-date="{{ $order->created_at->toDateString() }}" data-status="{{ $payStatus }}" data-search="{{ strtolower($order->order_code . ' ' . ($order->customer_name ?? 'Pelanggan Walk-in') . ' ' . number_format((float) $order->total_amount, 0, ',', '.')) }}">
                                    <td class="py-space-md px-space-lg whitespace-nowrap">
                                        <a class="{{ $isVoid ? 'font-code-mono text-ink-muted font-bold hover:underline flex items-center gap-1 line-through' : 'font-code-mono text-primary font-bold hover:underline flex items-center gap-1' }}" href="{{ route('seller.pos.receipt', $order->order_code) }}">
                                            <span class="">{{ $order->order_code }}</span>
                                            @unless ($isVoid)
                                                <span class="material-symbols-outlined text-[14px] opacity-0 group-hover:opacity-100 transition-opacity">open_in_new</span>
                                            @endunless
                                        </a>
                                    </td>
                                    <td class="py-space-md px-space-lg whitespace-nowrap text-ink-muted font-caption text-caption">
                                        <div class="flex flex-col">
                                            <span class="text-on-surface font-medium">{{ $order->created_at->format('H:i') }} WIB</span>
                                            <span class="text-[11px]">{{ $order->created_at->translatedFormat('d M Y') }}</span>
                                        </div>
                                    </td>
                                    <td class="py-space-md px-space-lg whitespace-nowrap">
                                        <span class="font-medium {{ $isVoid ? 'text-ink-muted' : 'text-on-surface' }}">{{ $order->customer_name ?? 'Pelanggan Walk-in' }}</span>
                                    </td>
                                    <td class="py-space-md px-space-lg whitespace-nowrap">
                                        @if (($pay?->method) === 'manual')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full {{ $isVoid ? 'bg-surface-container text-ink-muted' : 'bg-surface-container text-on-surface' }} font-caption text-caption font-medium">
                                                <span class="material-symbols-outlined text-[14px]">payments</span>
                                                <span class="">Manual (Tunai)</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-surface-container text-ink-muted font-caption text-caption font-medium">
                                                <span class="material-symbols-outlined text-[14px]">payments</span>
                                                <span class="">Belum dibayar</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-space-md px-space-lg whitespace-nowrap text-right">
                                        <span class="font-code-mono font-bold {{ $isVoid ? 'text-ink-muted line-through' : 'text-on-surface' }}">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="py-space-md px-space-lg whitespace-nowrap text-center">
                                        @if ($payStatus === 'paid')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-caption text-caption font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-tertiary-container"></span>
                                                <span class="">Dibayar</span>
                                            </span>
                                        @elseif ($payStatus === 'pending')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-caption font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-on-accent-yellow animate-pulse"></span>
                                                <span class="">Menunggu Pembayaran</span>
                                            </span>
                                        @elseif ($payStatus === 'failed')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-error-container text-on-error-container font-caption text-caption font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                                                <span class="">Gagal</span>
                                            </span>
                                        @elseif ($payStatus === 'expired')
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-surface-container-high text-ink-muted font-caption text-caption font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-outline"></span>
                                                <span class="">Kedaluwarsa</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed font-caption text-caption font-bold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                                                <span class="">Dibatalkan</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-space-md px-space-lg whitespace-nowrap text-right">
                                        <a class="h-9 px-space-md rounded-full bg-surface-card hover:bg-surface-container text-primary font-caption text-caption font-bold shadow-sm transition-all inline-flex items-center gap-1" href="{{ route('seller.pos.receipt', $order->order_code) }}">
                                            <span class="material-symbols-outlined text-[16px]">receipt</span>
                                            <span class="">Lihat Struk</span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Bar -->
                <div class="px-space-lg py-space-md bg-surface-card flex flex-col sm:flex-row items-center justify-between gap-space-sm">
                    <div class="font-caption text-caption text-ink-muted">
                        Menampilkan <span class="font-bold text-on-surface">{{ $orders->firstItem() }} - {{ $orders->lastItem() }}</span> dari <span class="font-bold text-on-surface">{{ $orders->total() }}</span> transaksi
                    </div>
                    <div class="flex items-center gap-space-xs">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>

            <!-- Empty State View (hasil filter tidak menemukan apa pun) -->
            <div class="hidden bg-surface-card rounded-xl shadow-sm p-12 flex-col items-center justify-center text-center" id="empty-state-container">
                <div class="w-20 h-20 rounded-full bg-surface-container-low text-ink-muted flex items-center justify-center mb-space-md">
                    <span class="material-symbols-outlined text-[42px] text-outline">event_busy</span>
                </div>
                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">
                    Tidak ada transaksi pada rentang tanggal ini
                </h3>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-md mt-1 mb-space-lg">
                    Belum ada riwayat transaksi yang tercatat pada rentang tanggal atau kriteria filter yang Anda pilih. Coba sesuaikan kata kunci atau rentang pencarian.
                </p>
                <div class="flex items-center gap-space-sm">
                    <button class="h-11 px-space-lg rounded-full bg-primary hover:bg-blue-hover text-on-primary font-body-md text-body-md font-bold transition-all shadow-sm flex items-center gap-space-xs" onclick="resetFilters()">
                        <span class="material-symbols-outlined text-[18px]">restart_alt</span>
                        <span class="">Reset Filter</span>
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    let currentStatus = 'ALL';
    let currentQuery = '';
    let currentDateFrom = null; // 'YYYY-MM-DD' atau null (semua tanggal)
    let currentDateTo = null;

    const activeStatusClass = 'status-btn px-3 py-1.5 rounded-full font-caption text-caption font-bold bg-primary text-on-primary transition-all flex items-center gap-1';
    const inactiveStatusClass = 'status-btn px-3 py-1.5 rounded-full font-caption text-caption font-medium bg-surface-container hover:bg-surface-container-high text-on-surface transition-all flex items-center gap-1';
    const activePresetClass = 'preset-pill px-space-md py-1.5 rounded-full font-body-md text-body-md font-medium transition-all bg-primary text-on-primary shadow-sm';
    const inactivePresetClass = 'preset-pill px-space-md py-1.5 rounded-full font-body-md text-body-md font-medium transition-all bg-surface-card hover:bg-surface-container text-on-surface';

    function setPreset(preset) {
        ['preset-today', 'preset-7days', 'preset-month', 'preset-custom'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.className = inactivePresetClass;
        });
        const activeEl = document.getElementById('preset-' + preset);
        if (activeEl) activeEl.className = activePresetClass;

        const label = document.getElementById('date-label');
        const fmt = (d) => d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' }).replaceAll('.', ' ');
        const today = new Date();
        const toISO = (d) => d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');

        if (preset === 'today') {
            currentDateFrom = toISO(today); currentDateTo = toISO(today);
            label.innerText = fmt(today) + ' - ' + fmt(today);
        } else if (preset === '7days') {
            const from = new Date(today); from.setDate(today.getDate() - 6);
            currentDateFrom = toISO(from); currentDateTo = toISO(today);
            label.innerText = fmt(from) + ' - ' + fmt(today);
        } else if (preset === 'month') {
            const from = new Date(today.getFullYear(), today.getMonth(), 1);
            currentDateFrom = toISO(from); currentDateTo = toISO(today);
            label.innerText = fmt(from) + ' - ' + fmt(today);
        } else {
            const start = window.prompt('Tanggal mulai (YYYY-MM-DD):', toISO(today));
            if (!start) { setPreset('today'); return; }
            const end = window.prompt('Tanggal selesai (YYYY-MM-DD):', toISO(today));
            if (!end) { setPreset('today'); return; }
            currentDateFrom = start; currentDateTo = end;
            label.innerText = start + ' - ' + end;
        }
        applyFilters();
    }

    function filterStatus(status) {
        currentStatus = status;
        ['filter-all', 'filter-dibayar', 'filter-menunggu', 'filter-dibatalkan'].forEach(id => {
            const el = document.getElementById(id);
            if (el) el.className = inactiveStatusClass;
        });
        const map = { ALL: 'filter-all', PAID: 'filter-dibayar', PENDING: 'filter-menunggu', FAILED: 'filter-dibatalkan' };
        const activeBtn = document.getElementById(map[status]);
        if (activeBtn) activeBtn.className = activeStatusClass;
        applyFilters();
    }

    function handleSearch(query) {
        currentQuery = query.toLowerCase().trim();
        applyFilters();
    }

    function applyFilters() {
        const dataContainer = document.getElementById('data-state-container');
        const emptyContainer = document.getElementById('empty-state-container');
        if (!dataContainer || !emptyContainer) return;
        const rows = document.querySelectorAll('#transaction-rows tr');
        let matchedCount = 0;
        rows.forEach(row => {
            const statusOk = currentStatus === 'ALL'
                || (currentStatus === 'PAID' && row.dataset.status === 'paid')
                || (currentStatus === 'PENDING' && row.dataset.status === 'pending')
                || (currentStatus === 'FAILED' && ['failed', 'expired', 'cancelled'].includes(row.dataset.status));
            const date = row.dataset.date;
            const dateOk = (!currentDateFrom || date >= currentDateFrom) && (!currentDateTo || date <= currentDateTo);
            const searchOk = !currentQuery || (row.dataset.search || '').includes(currentQuery);
            const visible = statusOk && dateOk && searchOk;
            row.style.display = visible ? '' : 'none';
            if (visible) matchedCount++;
        });

        const isEmpty = matchedCount === 0;
        dataContainer.classList.toggle('hidden', isEmpty);
        emptyContainer.classList.toggle('hidden', !isEmpty);
        emptyContainer.classList.toggle('flex', isEmpty);
    }

    function resetFilters() {
        const searchInput = document.getElementById('search-input');
        if (searchInput) searchInput.value = '';
        currentQuery = '';
        setPreset('today');
        filterStatus('ALL');
    }

    function handleExport() {
        const rows = [...document.querySelectorAll('#transaction-rows tr')].filter(r => r.style.display !== 'none');
        const header = ['Kode Transaksi', 'Waktu', 'Pelanggan', 'Metode', 'Total Tagihan', 'Status'];
        const lines = rows.map(r => {
            const cells = [...r.querySelectorAll('td')].map(td => td.innerText.replace(/\s+/g, ' ').trim());
            return [cells[0], cells[1], cells[2], cells[3], cells[4], cells[5]].map(v => '"' + (v || '').replaceAll('"', '""') + '"').join(';');
        });
        const csv = [header.join(';'), ...lines].join('\n');
        const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'Laporan_POS_' + new Date().toISOString().slice(0, 10).replaceAll('-', '') + '.csv';
        link.click();
        URL.revokeObjectURL(link.href);
    }

    function handlePrintRekap() {
        window.print();
    }

    // Inisialisasi: filter "Hari Ini" aktif sesuai desain.
    setPreset('today');
    filterStatus('ALL');
</script>
@endpush
