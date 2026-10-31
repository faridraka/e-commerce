@extends('layouts.seller')

@section('title', 'Pesanan Online - Portal Penjual Lokalku')

@section('content')
@php
    $pageOrders = $orders->getCollection();
    $statusCounts = $pageOrders->groupBy('status');
    $statusMap = [
        'menunggu_pembayaran' => ['Menunggu Pembayaran', 'bg-accent-yellow/20 text-on-accent-yellow'],
        'dibayar' => ['Dibayar', 'bg-tertiary-fixed/40 text-tertiary'],
        'diproses' => ['Diproses', 'bg-blue-subtle text-primary'],
        'dikirim' => ['Dikirim', 'bg-blue-subtle text-primary'],
        'selesai' => ['Selesai', 'bg-[#EBF5D6] text-success-text'],
        'dibatalkan' => ['Dibatalkan', 'bg-error-container text-secondary'],
        'kedaluwarsa' => ['Kedaluwarsa', 'bg-surface-container text-ink-muted'],
    ];
    $statusIcon = [
        'menunggu_pembayaran' => 'hourglass_empty',
        'dibayar' => null,
        'diproses' => 'package_2',
        'dikirim' => 'local_shipping',
        'selesai' => 'task_alt',
        'dibatalkan' => 'cancel',
        'kedaluwarsa' => 'schedule',
    ];
    $needAction = ($statusCounts['dibayar'] ?? collect())->count();
    $shipping = ($statusCounts['dikirim'] ?? collect())->count();
    $done = ($statusCounts['selesai'] ?? collect())->count();
@endphp
<!-- Breadcrumb -->
<div class="mb-space-sm flex items-center gap-2 text-caption font-caption text-ink-muted">
<a class="hover:text-primary transition-colors" href="{{ route('seller.orders') }}">Portal Penjual</a>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<a class="hover:text-primary transition-colors" href="{{ route('seller.orders') }}">Pesanan Toko</a>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="font-bold text-on-surface">Pesanan Online</span>
</div>
<!-- Header Halaman -->
<div class="mb-space-xl flex flex-col gap-space-md lg:flex-row lg:items-center lg:justify-between">
<div class="max-w-2xl">
<div class="flex items-center gap-space-sm">
<h1 class="font-headline-lg text-headline-lg text-on-surface">Pesanan Online</h1>
<span class="rounded-full bg-blue-subtle px-2.5 py-0.5 font-caption text-caption font-bold text-primary">Kanal Aktif</span>
</div>
<p class="mt-space-xs font-body-md text-body-md text-ink-muted leading-relaxed">
        Kelola pesanan masuk dari marketplace etalase online, konfirmasi pembayaran, dan perbarui nomor resi pengiriman kurir.
      </p>
</div>
<div class="flex flex-wrap items-center gap-space-sm">
<button class="min-h-[44px] px-space-lg rounded-full bg-surface-card text-primary font-body-md text-body-md font-bold shadow-sm hover:bg-surface-container transition-all flex items-center gap-2">
<span class="material-symbols-outlined text-[20px]">download</span>
<span class="">Unduh Laporan (CSV)</span>
</button>
<button class="min-h-[44px] px-space-lg rounded-full bg-primary-container text-on-primary font-body-md text-body-md font-bold shadow-sm hover:bg-blue-hover transition-all flex items-center gap-2">
<span class="material-symbols-outlined text-[20px]">sync</span>
<span class="">Sinkronisasi Pesanan</span>
</button>
</div>
</div>
<!-- 4 Kartu KPI Metrik Ringkasan Cepat -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md mb-space-xl">
<!-- Card 1 -->
<div class="bg-surface-card p-space-lg rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between mb-space-sm">
<span class="font-caption text-caption text-ink-muted uppercase tracking-wider font-bold">Total Pesanan Online</span>
<div class="w-9 h-9 rounded-full bg-blue-subtle flex items-center justify-center text-primary">
<span class="material-symbols-outlined text-[20px]">shopping_cart</span>
</div>
</div>
<div>
<div class="flex items-baseline gap-2">
<span class="font-headline-lg text-headline-lg text-on-surface">{{ $orders->total() }}</span>
<span class="font-body-md text-caption text-ink-muted">Pesanan</span>
</div>
<p class="mt-1 font-code-mono text-caption text-primary font-bold">Omzet Rp {{ number_format((float) $pageOrders->sum('total_amount'), 0, ',', '.') }}</p>
</div>
</div>
<!-- Card 2 -->
<div class="bg-surface-card p-space-lg rounded-xl shadow-sm flex flex-col justify-between relative overflow-hidden">
<div class="absolute left-0 top-0 bottom-0 w-1.5 bg-accent-yellow"></div>
<div class="flex items-center justify-between mb-space-sm">
<span class="font-caption text-caption text-ink-muted uppercase tracking-wider font-bold">Perlu Diproses Segera</span>
<div class="w-9 h-9 rounded-full bg-accent-yellow/20 flex items-center justify-center text-on-accent-yellow">
<span class="material-symbols-outlined text-[20px]">priority_high</span>
</div>
</div>
<div>
<div class="flex items-baseline gap-2">
<span class="font-headline-lg text-headline-lg text-on-surface">{{ $needAction }}</span>
<span class="font-body-md text-caption text-ink-muted">Pesanan Dibayar</span>
</div>
<div class="mt-1 inline-flex items-center gap-1 rounded-full bg-accent-yellow px-2 py-0.5 text-caption font-caption font-bold text-on-accent-yellow">
<span class="w-1.5 h-1.5 rounded-full bg-on-accent-yellow"></span>
<span class="">Perlu Tindakan Kemas</span>
</div>
</div>
</div>
<!-- Card 3 -->
<div class="bg-surface-card p-space-lg rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between mb-space-sm">
<span class="font-caption text-caption text-ink-muted uppercase tracking-wider font-bold">Sedang Dikirim</span>
<div class="w-9 h-9 rounded-full bg-blue-subtle flex items-center justify-center text-primary-container">
<span class="material-symbols-outlined text-[20px]">local_shipping</span>
</div>
</div>
<div>
<div class="flex items-baseline gap-2">
<span class="font-headline-lg text-headline-lg text-on-surface">{{ $shipping }}</span>
<span class="font-body-md text-caption text-ink-muted">Dalam Kurir</span>
</div>
<p class="mt-1 font-body-md text-caption text-ink-muted truncate">Menunggu konfirmasi diterima pembeli</p>
</div>
</div>
<!-- Card 4 -->
<div class="bg-surface-card p-space-lg rounded-xl shadow-sm flex flex-col justify-between">
<div class="flex items-center justify-between mb-space-sm">
<span class="font-caption text-caption text-ink-muted uppercase tracking-wider font-bold">Selesai Bulan Ini</span>
<div class="w-9 h-9 rounded-full bg-tertiary-fixed/40 flex items-center justify-center text-tertiary">
<span class="material-symbols-outlined text-[20px]">check_circle</span>
</div>
</div>
<div>
<div class="flex items-baseline gap-2">
<span class="font-headline-lg text-headline-lg text-on-surface">{{ $done }}</span>
<span class="font-body-md text-caption text-ink-muted">Pesanan</span>
</div>
<p class="mt-1 font-code-mono text-caption text-success-text font-bold">{{ $orders->total() > 0 ? number_format($done / $orders->total() * 100, 1) : '0.0' }}% dari total</p>
</div>
</div>
</div>
<!-- Main Card Container: Tab, Toolbar, Table & Pagination -->
<div class="bg-surface-card rounded-xl shadow-sm flex flex-col overflow-hidden mb-space-xl">
<!-- Tab Status Pesanan (Segmented) -->
<div class="bg-surface-card overflow-x-auto p-space-sm">
<div class="flex items-center gap-space-xs min-w-max" id="status-tabs">
<button class="status-tab min-h-[44px] px-4 py-2 rounded-full font-body-md text-caption font-bold flex items-center gap-2 transition-all bg-primary-container text-on-primary shadow-sm" data-tab="all" onclick="filterStatus('all')">
<span class="">Semua</span>
<span class="px-2 py-0.5 rounded-full text-caption font-caption bg-surface-card/20 text-on-primary">{{ $orders->total() }}</span>
</button>
<button class="status-tab min-h-[44px] px-4 py-2 rounded-full font-body-md text-caption font-medium text-ink-muted hover:bg-surface-container flex items-center gap-2 transition-all" data-tab="menunggu_pembayaran" onclick="filterStatus('menunggu_pembayaran')">
<span class="">Menunggu Pembayaran</span>
<span class="px-2 py-0.5 rounded-full text-caption font-caption font-bold bg-accent-yellow text-on-accent-yellow">{{ ($statusCounts['menunggu_pembayaran'] ?? collect())->count() }}</span>
</button>
<button class="status-tab min-h-[44px] px-4 py-2 rounded-full font-body-md text-caption font-medium text-ink-muted hover:bg-surface-container flex items-center gap-2 transition-all" data-tab="dibayar" onclick="filterStatus('dibayar')">
<span class="">Dibayar</span>
<span class="px-2 py-0.5 rounded-full text-caption font-caption font-bold bg-tertiary-fixed text-on-tertiary-fixed">{{ $needAction }}</span>
</button>
<button class="status-tab min-h-[44px] px-4 py-2 rounded-full font-body-md text-caption font-medium text-ink-muted hover:bg-surface-container flex items-center gap-2 transition-all" data-tab="diproses" onclick="filterStatus('diproses')">
<span class="">Diproses</span>
<span class="px-2 py-0.5 rounded-full text-caption font-caption font-bold bg-blue-subtle text-primary">{{ ($statusCounts['diproses'] ?? collect())->count() }}</span>
</button>
<button class="status-tab min-h-[44px] px-4 py-2 rounded-full font-body-md text-caption font-medium text-ink-muted hover:bg-surface-container flex items-center gap-2 transition-all" data-tab="dikirim" onclick="filterStatus('dikirim')">
<span class="">Dikirim</span>
<span class="px-2 py-0.5 rounded-full text-caption font-caption font-bold bg-primary text-on-primary">{{ $shipping }}</span>
</button>
<button class="status-tab min-h-[44px] px-4 py-2 rounded-full font-body-md text-caption font-medium text-ink-muted hover:bg-surface-container flex items-center gap-2 transition-all" data-tab="selesai" onclick="filterStatus('selesai')">
<span class="">Selesai</span>
<span class="px-2 py-0.5 rounded-full text-caption font-caption font-bold bg-tertiary-container text-on-tertiary">{{ $done }}</span>
</button>
<button class="status-tab min-h-[44px] px-4 py-2 rounded-full font-body-md text-caption font-medium text-ink-muted hover:bg-surface-container flex items-center gap-2 transition-all" data-tab="dibatalkan" onclick="filterStatus('dibatalkan')">
<span class="">Dibatalkan</span>
<span class="px-2 py-0.5 rounded-full text-caption font-caption font-bold bg-secondary text-on-secondary">{{ ($statusCounts['dibatalkan'] ?? collect())->count() }}</span>
</button>
</div>
</div>
<!-- Toolbar Filter & Pencarian -->
<div class="p-space-lg bg-surface-container-low flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-space-md">
<div class="relative flex-1">
<span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-ink-muted">search</span>
<input class="w-full h-11 pl-11 pr-4 bg-surface-card rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary placeholder:text-ink-muted shadow-sm transition-all" id="search-input" oninput="handleSearch(this.value)" placeholder="Cari nomor pesanan (ORD-...), nama pembeli, atau nomor telepon..." type="text">
</div>
<div class="flex flex-wrap items-center gap-space-sm">
<div class="relative min-w-[170px] flex-1 sm:flex-initial">
<select class="w-full h-11 pl-3 pr-8 rounded-lg bg-surface-card font-body-md text-caption text-on-surface focus:outline-none focus:ring-2 focus:ring-primary shadow-sm appearance-none cursor-pointer">
<option value="all">Semua Waktu</option>
<option value="7">7 Hari Terakhir</option>
<option value="30">30 Hari Terakhir</option>
</select>
<span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none text-[18px] text-ink-muted">calendar_today</span>
</div>
<button class="h-11 px-4 rounded-full bg-surface-card hover:bg-surface-container text-on-surface font-body-md text-caption font-bold shadow-sm transition-colors flex items-center gap-1.5">
<span class="material-symbols-outlined text-[18px]">filter_list</span>
<span class="">Filter Lanjutan</span>
</button>
<button class="h-11 px-4 rounded-full bg-surface-container-high hover:bg-surface-dim text-on-surface-variant font-body-md text-caption font-medium transition-colors" onclick="resetFilters()">
          Reset
        </button>
</div>
</div>
@if ($orders->isEmpty())
<!-- Empty State -->
<div class="py-16 px-4 text-center flex flex-col items-center justify-center">
<x-not-found title="Belum Ada Pesanan Online" message="Pesanan dari etalase akan muncul di sini." actionUrl="{{ route('seller.pos') }}" actionLabel="Buka Kasir POS" />
</div>
@else
<!-- Tabel Pesanan Online -->
<div class="overflow-x-auto">
<table class="w-full text-left" id="order-table">
<thead class="bg-surface-container font-caption text-caption text-ink-muted uppercase tracking-wider select-none">
<tr>
<th class="py-3 pl-4 pr-2 w-10">
<input class="w-4 h-4 rounded text-primary focus:ring-primary accent-primary cursor-pointer" id="check-all" onclick="toggleSelectAll(this)" type="checkbox">
</th>
<th class="py-3 px-3">Kode Pesanan</th>
<th class="py-3 px-3">Tanggal &amp; Waktu</th>
<th class="py-3 px-3">Pembeli</th>
<th class="py-3 px-3">Item Pesanan</th>
<th class="py-3 px-3">Total Pesanan</th>
<th class="py-3 px-3">Status</th>
<th class="py-3 pl-3 pr-4 text-right">Aksi</th>
</tr>
</thead>
<tbody class="divide-transparent font-body-md text-body-md" id="table-body">
@foreach ($orders as $order)
@php
                        [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status, 'bg-surface-container text-ink-muted'];
                        $payment = $order->payments->first();
                        $itemCount = $order->items->count();
                        $itemNames = $order->items->take(2)->map(fn ($i) => $i->product_name.' ×'.$i->quantity)->implode(', ');
                        $itemExtra = max($itemCount - 2, 0);
                        $itemTitles = $order->items->map(fn ($i) => $i->product_name.' x'.$i->quantity)->implode(', ');
                        $buyerName = $order->customer_name ?? $order->user?->name ?? 'Pembeli Tamu';
                        $isAwaitingPayment = $order->status === 'menunggu_pembayaran' || ($payment && $payment->status === 'pending');
                    @endphp
<tr class="order-row group {{ $order->status === 'dibayar' ? 'bg-tertiary-fixed/10 hover:bg-tertiary-fixed/20 transition-colors relative' : 'hover:bg-surface-container-low transition-colors' }}" data-status="{{ $order->status }}" data-search="{{ $order->order_code }} {{ $buyerName }} {{ $itemNames }}">
<td class="py-3 pl-4 pr-2 relative">
@if ($order->status === 'dibayar')
<!-- Visual Indicator baris Dibayar -->
<span class="absolute left-0 top-0 bottom-0 w-1 bg-tertiary-container"></span>
@endif
<input class="row-check w-4 h-4 rounded text-primary focus:ring-primary accent-primary cursor-pointer" type="checkbox">
</td>
<td class="py-3 px-3">
<a class="font-code-mono text-caption font-bold {{ in_array($order->status, ['dibatalkan', 'kedaluwarsa']) ? 'text-ink-muted' : 'text-primary' }} hover:underline block leading-tight" href="{{ route('seller.orders.show', $order->order_code) }}">
                {{ $order->order_code }}
              </a>
<span class="inline-block mt-1 px-1.5 py-0.5 rounded {{ ($order->channel ?? 'online') === 'pos' ? 'bg-surface-container-high text-ink-muted' : 'bg-blue-subtle text-primary' }} font-caption text-[11px] font-bold">
                {{ ($order->channel ?? 'online') === 'pos' ? 'Kasir POS' : 'Web Store' }}
              </span>
</td>
<td class="py-3 px-3">
<div class="text-caption font-body-md text-on-surface font-medium leading-tight">{{ $order->created_at->translatedFormat('d M Y') }}</div>
<div class="font-code-mono text-caption text-ink-muted">{{ $order->created_at->translatedFormat('H:i') }} WIB</div>
</td>
<td class="py-3 px-3">
<div class="font-bold text-caption text-on-surface leading-tight">{{ $buyerName }}</div>
<div class="text-caption text-ink-muted flex items-center gap-1 mt-0.5">
<span class="material-symbols-outlined text-[13px]">location_on</span>
<span class="">{{ $order->address?->city ?? '—' }}</span>
</div>
</td>
<td class="py-3 px-3">
<div class="font-medium text-caption text-on-surface">{{ $itemCount }} item</div>
<p class="text-caption text-ink-muted truncate max-w-[200px]" title="{{ $itemTitles }}">
                {{ $itemNames }}{{ $itemExtra > 0 ? ', +'.$itemExtra.' lainnya' : '' }}
              </p>
</td>
<td class="py-3 px-3">
<div class="font-code-mono text-price-sm {{ in_array($order->status, ['dibatalkan', 'kedaluwarsa']) ? 'text-ink-muted line-through' : 'text-on-surface' }} font-bold">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</div>
<span class="font-caption text-caption flex items-center gap-1 mt-0.5 {{ $isAwaitingPayment ? 'text-accent-yellow font-bold' : 'text-ink-muted' }}">
@if ($isAwaitingPayment)
<span class="material-symbols-outlined text-[14px]">schedule</span> Menunggu Pembayaran
@elseif ($payment && $payment->status === 'failed')
<span class="material-symbols-outlined text-[14px]">error</span> Pembayaran Gagal
@elseif ($payment && $payment->status === 'expired')
<span class="material-symbols-outlined text-[14px]">timer_off</span> Pembayaran Kedaluwarsa
@elseif ($payment && $payment->payment_type)
<span class="material-symbols-outlined text-[14px] {{ str_contains(strtolower($payment->payment_type), 'qris') ? 'text-tertiary' : '' }}">{{ str_contains(strtolower($payment->payment_type), 'qris') ? 'qr_code_2' : (str_contains(strtolower($payment->payment_type), 'va') || str_contains(strtolower($payment->payment_type), 'bank') ? 'account_balance' : 'account_balance_wallet') }}</span> {{ $payment->payment_type }}
@else
<span class="material-symbols-outlined text-[14px]">payments</span> Belum Ada Pembayaran
@endif
              </span>
</td>
<td class="py-3 px-3">
<div class="flex flex-col gap-0.5">
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full {{ $statusClass }} font-caption text-caption font-bold w-max">
@if (($statusIcon[$order->status] ?? null) === null)
<span class="w-1.5 h-1.5 rounded-full bg-tertiary animate-pulse"></span>
@else
<span class="material-symbols-outlined text-[14px]">{{ $statusIcon[$order->status] ?? 'info' }}</span>
@endif
                {{ $statusLabel }}
              </span>
@if ($order->status === 'menunggu_pembayaran')
<span class="text-[11px] text-ink-muted mt-0.5">Menunggu pembayaran pelanggan</span>
@elseif ($order->status === 'dikirim' && $order->shipping_tracking_number)
<span class="font-code-mono text-[11px] text-ink-muted mt-0.5 font-bold">{{ $order->shipping_tracking_number }}</span>
@endif
</div>
</td>
<td class="py-3 pl-3 pr-4 text-right">
<div class="flex items-center justify-end gap-1.5">
@if ($order->status === 'dibayar')
<a class="min-h-[40px] px-3.5 rounded-full bg-primary-container text-on-primary hover:bg-blue-hover font-caption text-caption font-bold shadow-sm transition-all flex items-center gap-1" href="{{ route('seller.orders.show', $order->order_code) }}">
<span class="material-symbols-outlined text-[16px]">inventory</span>
<span class="">Proses Pesanan</span>
</a>
@elseif ($order->status === 'diproses')
<a class="min-h-[40px] px-3.5 rounded-full bg-surface-card hover:bg-blue-subtle text-primary font-caption text-caption font-bold shadow-sm transition-all flex items-center gap-1" href="{{ route('seller.orders.show', $order->order_code) }}">
<span class="material-symbols-outlined text-[16px]">local_shipping</span>
<span class="">Input Resi</span>
</a>
@elseif ($order->status === 'dikirim')
<a class="min-h-[40px] px-3.5 rounded-full bg-surface-card hover:bg-surface-container text-primary font-caption text-caption font-bold shadow-sm transition-all flex items-center gap-1" href="{{ route('seller.orders.show', $order->order_code) }}">
<span class="material-symbols-outlined text-[16px]">location_searching</span>
<span class="">Lacak Resi</span>
</a>
@endif
<a class="min-h-[40px] px-3 rounded-full {{ in_array($order->status, ['dibatalkan', 'kedaluwarsa']) ? 'bg-surface-card hover:bg-surface-container text-ink-muted' : 'bg-surface-card hover:bg-surface-container text-primary font-bold' }} font-caption text-caption shadow-sm transition-colors flex items-center" href="{{ route('seller.orders.show', $order->order_code) }}">
                Detail
              </a>
</div>
</td>
</tr>
@endforeach
</tbody>
</table>
<!-- Empty State Container (Shown via filter/simulation) -->
<div class="hidden py-16 px-4 text-center flex flex-col items-center justify-center" id="empty-state">
<div class="w-16 h-16 rounded-full bg-blue-subtle text-primary flex items-center justify-center mb-space-md shadow-sm">
<span class="material-symbols-outlined text-[32px]">inbox</span>
</div>
<h3 class="font-headline-sm text-headline-sm text-on-surface mb-1">Tidak Ada Pesanan Ditemukan</h3>
<p class="font-body-md text-caption text-ink-muted max-w-sm mb-space-lg">
          Tidak ada pesanan online yang sesuai dengan kata kunci pencarian atau kriteria status saat ini.
        </p>
<button class="min-h-[44px] px-6 rounded-full bg-primary-container text-on-primary font-body-md text-caption font-bold hover:bg-blue-hover shadow-sm transition-all flex items-center gap-2" onclick="resetFilters()">
<span class="material-symbols-outlined text-[18px]">refresh</span>
<span class="">Pulihkan Semua Pesanan</span>
</button>
</div>
<!-- Pagination & Footer Tabel -->
<div class="p-space-lg bg-surface-card flex flex-col sm:flex-row items-center justify-between gap-space-md" id="table-footer">
<div class="text-caption font-body-md text-ink-muted">
        Menampilkan <span class="font-bold text-on-surface" id="showing-count">{{ $orders->firstItem() ?? 0 }} – {{ $orders->lastItem() ?? 0 }}</span> dari <span class="font-bold text-on-surface">{{ $orders->total() }}</span> pesanan
      </div>
<div class="flex items-center gap-1 select-none">
{!! $orders->appends(request()->query())->links() !!}
</div>
</div>
@endif
</div>
</div>
@endsection

@push('scripts')
<script>
  let currentStatus = 'all';
  let searchQuery = '';

  function filterStatus(status) {
    currentStatus = status;
    updateTabsUI(status);
    applyRowVisibility();
  }

  function updateTabsUI(activeStatus) {
    const tabs = document.querySelectorAll('.status-tab');
    tabs.forEach(tab => {
      const tabTarget = tab.getAttribute('data-tab');
      if (tabTarget === activeStatus) {
        tab.className = 'status-tab min-h-[44px] px-4 py-2 rounded-full font-body-md text-caption font-bold flex items-center gap-2 transition-all bg-primary-container text-on-primary shadow-sm';
      } else {
        tab.className = 'status-tab min-h-[44px] px-4 py-2 rounded-full font-body-md text-caption font-medium text-ink-muted hover:bg-surface-container flex items-center gap-2 transition-all';
      }
    });
  }

  function handleSearch(val) {
    searchQuery = val.trim().toLowerCase();
    applyRowVisibility();
  }

  function applyFilters() {
    applyRowVisibility();
  }

  function applyRowVisibility() {
    const rows = document.querySelectorAll('.order-row');
    const emptyState = document.getElementById('empty-state');
    const tableFooter = document.getElementById('table-footer');
    const showingText = document.getElementById('showing-count');
    let visibleCount = 0;

    rows.forEach(row => {
      const status = row.getAttribute('data-status');
      const searchData = (row.getAttribute('data-search') || '').toLowerCase();

      const matchStatus = (currentStatus === 'all') || (status === currentStatus);
      const matchSearch = (!searchQuery) || searchData.includes(searchQuery);

      if (matchStatus && matchSearch) {
        row.style.display = '';
        visibleCount++;
      } else {
        row.style.display = 'none';
      }
    });

    if (!emptyState) return;

    if (visibleCount === 0) {
      emptyState.classList.remove('hidden');
      if (tableFooter) tableFooter.classList.add('hidden');
    } else {
      emptyState.classList.add('hidden');
      if (tableFooter) tableFooter.classList.remove('hidden');
      if (showingText) showingText.textContent = '1 – ' + visibleCount;
    }
  }

  function resetFilters() {
    currentStatus = 'all';
    searchQuery = '';
    const searchInput = document.getElementById('search-input');
    if (searchInput) searchInput.value = '';
    updateTabsUI('all');
    applyRowVisibility();
  }

  function toggleSelectAll(master) {
    const checkboxes = document.querySelectorAll('.row-check');
    checkboxes.forEach(cb => {
      cb.checked = master.checked;
    });
  }
</script>
@endpush
