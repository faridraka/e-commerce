@extends('layouts.seller')

@section('title', 'Detail Pesanan Online - '.$order->order_code)

@section('content')
@php
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
    $payment = $order->payments->first();
    $paymentStatusMap = [
        'pending' => ['Belum Dibayar', 'bg-accent-yellow text-on-accent-yellow'],
        'paid' => ['Lunas', 'bg-tertiary-fixed text-on-tertiary-fixed'],
        'failed' => ['Gagal', 'bg-error-container text-secondary'],
        'expired' => ['Kedaluwarsa', 'bg-surface-container text-ink-muted'],
    ];
    [$paymentLabel, $paymentClass] = $payment
        ? ($paymentStatusMap[$payment->status] ?? [$payment->status, 'bg-surface-container text-ink-muted'])
        : ['Belum Ada Pembayaran', 'bg-surface-container text-ink-muted'];
    $buyerName = $order->customer_name ?? $order->user?->name ?? 'Pembeli Tamu';
    $initials = collect(preg_split('/\s+/', trim($buyerName)))->filter()->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->take(2)->implode('');
    $waNumber = null;
    if ($order->address?->phone) {
        $digits = preg_replace('/[^0-9]/', '', $order->address->phone);
        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        }
        $waNumber = $digits;
    }
    $itemsSubtotal = (float) $order->items->sum('subtotal');
    $otherCosts = max((float) $order->total_amount - $itemsSubtotal, 0);
    $totalQty = (int) $order->items->sum('quantity');
    $stepper = [
        'menunggu_pembayaran' => ['done' => 1, 'active' => 2],
        'dibayar' => ['done' => 2, 'active' => 3],
        'diproses' => ['done' => 3, 'active' => 4],
        'dikirim' => ['done' => 4, 'active' => 5],
        'selesai' => ['done' => 5, 'active' => 0],
        'dibatalkan' => ['done' => 1, 'active' => 0],
        'kedaluwarsa' => ['done' => 1, 'active' => 0],
    ][$order->status] ?? ['done' => 1, 'active' => 0];
    $progressWidth = [
        'menunggu_pembayaran' => '15%',
        'dibayar' => '40%',
        'diproses' => '60%',
        'dikirim' => '80%',
        'selesai' => '100%',
        'dibatalkan' => '15%',
        'kedaluwarsa' => '15%',
    ][$order->status] ?? '15%';
    $steps = [
        ['label' => '1. Pesanan Dibuat', 'icon' => 'check'],
        ['label' => '2. Pembayaran', 'icon' => 'account_balance_wallet'],
        ['label' => '3. Diproses', 'icon' => 'inventory_2'],
        ['label' => '4. Dikirim', 'icon' => 'local_shipping'],
        ['label' => '5. Selesai', 'icon' => 'verified'],
    ];
    $stepSubs = [
        'Checkout Web Store',
        $order->status === 'menunggu_pembayaran' ? 'Menunggu Pembayaran' : 'Lunas',
        $order->status === 'diproses' ? 'Sedang Diproses' : 'Menunggu Packing',
        in_array($order->status, ['dikirim', 'selesai']) ? 'Dalam Pengiriman' : 'Menunggu Resi',
        $order->status === 'selesai' ? 'Pesanan Selesai' : 'Konfirmasi Terima',
    ];
    $canCancel = in_array($order->status, ['menunggu_pembayaran', 'dibayar']);
    $auditLog = [];
    if ($order->status === 'selesai') {
        $auditLog[] = ['Pesanan Diselesaikan', ($order->updated_at?->translatedFormat('d M Y, H:i:s') ?? '-').' WIB • Konfirmasi pesanan diterima'];
    }
    if ($order->shipping_tracking_number) {
        $auditLog[] = ['Nomor Resi Diinput & Dikirim', 'Resi '.$order->shipping_tracking_number];
    }
    if (in_array($order->status, ['diproses', 'dikirim', 'selesai'])) {
        $auditLog[] = ['Status Pesanan Diubah ke "Diproses"', ($order->updated_at?->translatedFormat('d M Y, H:i:s') ?? '-').' WIB • Persiapan barang oleh toko'];
    }
    if ($payment && $payment->status === 'paid') {
        $auditLog[] = ['Pembayaran Berhasil Terverifikasi', ($payment->created_at?->translatedFormat('d M Y, H:i:s') ?? '-').' WIB • via '.($payment->payment_type ?? 'Payment Gateway')];
    }
    $auditLog[] = ['Pesanan Dibuat oleh Pembeli', ($order->created_at?->translatedFormat('d M Y, H:i:s') ?? '-').' WIB • Checkout Web Store'];
    $fullAddress = $order->address
        ? trim($order->address->recipient_name.' ('.$order->address->phone.') — '.$order->address->address_detail.', '.$order->address->city.' '.$order->address->postal_code)
        : '';
@endphp
<!-- Breadcrumb & Back Link -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-space-xs text-caption">
<div class="flex items-center flex-wrap gap-2 text-ink-muted">
<span class="hover:text-primary transition-colors cursor-pointer">Portal Penjual</span>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="hover:text-primary transition-colors cursor-pointer">Pesanan Toko</span>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<a class="hover:text-primary transition-colors" href="{{ route('seller.orders') }}">Pesanan Online</a>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="font-code-mono text-code-mono font-medium text-on-surface">{{ $order->order_code }}</span>
</div>
<a class="inline-flex items-center gap-1 font-body-md text-caption font-bold text-primary hover:text-blue-hover transition-colors" href="{{ route('seller.orders') }}">
<span class="material-symbols-outlined text-[16px]">arrow_back</span>
<span class="">Kembali ke Daftar Pesanan Online</span>
</a>
</div>
<!-- Header Section with State Badges & Global Actions -->
<div class="bg-surface-card rounded-xl p-space-lg shadow-sm border border-border-default flex flex-col lg:flex-row lg:items-center justify-between gap-space-lg">
<div class="flex flex-col gap-2">
<div class="flex flex-wrap items-center gap-space-sm">
<h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight flex items-center gap-2">
<span class="">Pesanan</span>
<span class="font-code-mono text-headline-md text-primary tracking-normal">{{ $order->order_code }}</span>
</h1>
<!-- Dynamic Header Badge -->
<div class="inline-flex items-center gap-1 px-3 py-1 rounded-full {{ $statusClass }} font-caption text-caption font-bold" id="header-status-badge">
<span class="w-1.5 h-1.5 rounded-full bg-current"></span>
<span id="header-status-text" class="">{{ $statusLabel }}</span>
</div>
<span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-subtle text-primary font-caption text-caption font-medium">
<span class="material-symbols-outlined text-[14px]">storefront</span>
          {{ ($order->channel ?? 'online') === 'pos' ? 'Kasir POS (Offline)' : 'Web Store (Marketplace)' }}
        </span>
</div>
<div class="flex items-center gap-4 text-caption text-ink-muted">
<span class="inline-flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">calendar_today</span>
          {{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB
        </span>
<span class="">•</span>
<span class="inline-flex items-center gap-1">
<span class="material-symbols-outlined text-[16px]">schedule</span>
          Batas respons toko: <strong class="text-on-surface font-medium">{{ $order->created_at->copy()->addDay()->translatedFormat('d M Y, H:i') }} WIB</strong>
</span>
</div>
</div>
<!-- Quick Action Toolbar -->
<div class="flex flex-wrap items-center gap-space-sm">
<a class="h-[44px] px-4 rounded-full border border-border-default bg-surface-card hover:bg-surface-container-high text-on-surface font-body-md text-body-md font-medium transition-colors inline-flex items-center gap-2 shadow-sm" href="{{ route('seller.orders.label', $order->order_code) }}" target="_blank">
<span class="material-symbols-outlined text-[18px]">local_shipping</span>
<span class="">Cetak Label</span>
</a>
<a class="h-[44px] px-4 rounded-full border border-border-default bg-surface-card hover:bg-surface-container-high text-on-surface font-body-md text-body-md font-medium transition-colors inline-flex items-center gap-2 shadow-sm" href="{{ route('seller.orders.invoice', $order->order_code) }}" target="_blank">
<span class="material-symbols-outlined text-[18px]">receipt_long</span>
<span class="">Cetak Invoice</span>
</a>
@if ($canCancel)
<button class="h-[44px] px-4 rounded-full border border-secondary text-secondary hover:bg-error-container/20 font-body-md text-body-md font-bold transition-colors inline-flex items-center gap-1.5" id="btn-cancel-order" onclick="openCancelModal()">
<span class="material-symbols-outlined text-[18px]">cancel</span>
<span class="">Batalkan Pesanan</span>
</button>
@endif
</div>
</div>
@if ($order->status === 'menunggu_pembayaran')
<!-- Pending Payment Warning Banner (Conditional) -->
<div class="bg-accent-yellow/15 border-l-4 border-accent-yellow p-4 rounded-xl flex items-start gap-3" id="banner-pending-payment">
<span class="material-symbols-outlined text-on-accent-yellow text-[24px]">hourglass_top</span>
<div class="flex-1">
<h4 class="font-headline-sm text-body-md font-bold text-on-surface">Menunggu Pembayaran Pembeli</h4>
<p class="font-body-md text-caption text-on-surface-variant mt-0.5">
        Seluruh proses pengemasan dan input nomor resi ditangguhkan sampai pelunasan diverifikasi otomatis melalui payment gateway.
      </p>
</div>
</div>
@endif
<!-- Main 8+4 Layout Grid -->
<div class="grid grid-cols-1 lg:grid-cols-12 gap-space-lg items-start">
<!-- LEFT COLUMN (8 cols): Progress, Items, Notes & Timeline -->
<div class="lg:col-span-8 flex flex-col space-y-6">
<!-- Card 1: Progress Status Pesanan -->
<div class="bg-surface-card rounded-xl p-space-lg shadow-sm border border-border-default flex flex-col space-y-6">
<div class="flex items-center justify-between pb-space-sm border-b border-border-default">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[22px]">timeline</span>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Progres Status Pesanan</h2>
</div>
<span class="font-caption text-caption text-ink-muted">Terakhir diperbarui: {{ $order->updated_at?->translatedFormat('d M Y, H:i') ?? '-' }}</span>
</div>
<!-- 5-step Stepper -->
<div class="relative py-2">
<div class="hidden sm:block absolute top-5 left-8 right-8 h-1 bg-surface-container -z-0"></div>
<div class="hidden sm:block absolute top-5 left-8 h-1 bg-primary transition-all duration-500 -z-0" id="stepper-progress-bar" style="width: {{ $progressWidth }};"></div>
<div class="grid grid-cols-2 sm:grid-cols-5 gap-4 relative z-10">
@foreach ($steps as $i => $step)
@php
                            $stepNo = $i + 1;
                            $isDone = $stepNo <= $stepper['done'];
                            $isActive = $stepNo === $stepper['active'];
                            $isPendingYellow = $isActive && $order->status === 'menunggu_pembayaran' && $stepNo === 2;
                        @endphp
<div class="flex flex-col items-center text-center" id="step-node-{{ $stepNo }}">
<div class="step-circle w-10 h-10 rounded-full flex items-center justify-center font-bold font-body-md transition-all {{ $isDone ? 'bg-tertiary text-on-tertiary shadow-sm' : ($isPendingYellow ? 'bg-accent-yellow text-on-accent-yellow shadow-sm' : ($isActive ? 'bg-primary-container text-on-primary ring-4 ring-primary-fixed shadow-sm' : 'bg-surface-container text-ink-muted')) }}">
<span class="material-symbols-outlined text-[20px]">{{ $isPendingYellow ? 'schedule' : $step['icon'] }}</span>
</div>
<span class="step-label font-headline-sm text-caption {{ $isDone || $isActive ? 'font-bold text-on-surface' : 'font-medium text-ink-muted' }} {{ $isActive && ! $isPendingYellow ? 'text-primary' : '' }} mt-2">{{ $step['label'] }}</span>
<span class="font-caption text-[11px] text-ink-muted">{{ $stepSubs[$i] }}</span>
</div>
@endforeach
</div>
</div>
<!-- Dynamic Action Banner Box based on state -->
@if ($order->status === 'dibayar')
<div class="p-4 rounded-xl bg-blue-subtle/50 border border-primary-fixed flex flex-col md:flex-row md:items-center justify-between gap-4" id="workflow-action-box">
<div class="flex items-start gap-3">
<div class="w-10 h-10 rounded-full bg-primary-container text-on-primary flex items-center justify-center flex-shrink-0" id="action-box-icon"><span class="material-symbols-outlined text-[22px]">package_2</span></div>
<div>
<h3 class="font-headline-sm text-body-md font-bold text-on-surface" id="action-box-title">Pesanan Siap Dikemas</h3>
<p class="font-body-md text-caption text-on-surface-variant" id="action-box-desc">Pembayaran terverifikasi penuh. Segera siapkan barang dari etalase toko dan tandai pesanan diproses.</p>
</div>
</div>
<div class="flex items-center gap-2 flex-shrink-0">
<form action="{{ route('seller.orders.status', $order->order_code) }}" method="POST">
@csrf
<input name="status" type="hidden" value="diproses">
<button class="h-[44px] px-6 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-body-md text-body-md font-bold transition-all shadow-sm flex items-center gap-2" id="primary-workflow-btn" type="submit"><span class="material-symbols-outlined text-[18px]">inventory_2</span><span class="">Proses Pesanan</span></button>
</form>
</div>
</div>
@elseif ($order->status === 'diproses')
<div class="p-4 rounded-xl bg-blue-subtle/50 border border-primary-fixed flex flex-col md:flex-row md:items-center justify-between gap-4" id="workflow-action-box">
<div class="flex items-start gap-3 flex-1">
<div class="w-10 h-10 rounded-full bg-primary text-on-primary flex items-center justify-center flex-shrink-0" id="action-box-icon"><span class="material-symbols-outlined text-[22px]">conveyor_belt</span></div>
<div class="flex-1">
<h3 class="font-headline-sm text-body-md font-bold text-on-surface" id="action-box-title">Pengemasan Sedang Berlangsung</h3>
<p class="font-body-md text-caption text-on-surface-variant" id="action-box-desc">Barang dalam proses packing. Setelah paket siap, input nomor resi kurir pengiriman (opsional) lalu kirim pesanan.</p>
<form action="{{ route('seller.orders.status', $order->order_code) }}" method="POST" class="mt-3 flex flex-col sm:flex-row items-stretch sm:items-end gap-2">
@csrf
<input name="status" type="hidden" value="dikirim">
<div class="flex flex-col space-y-1.5 flex-1">
<label class="font-headline-sm text-caption font-bold text-on-surface" for="input-tracking-number">Nomor Resi / AWB (Opsional)</label>
<input class="w-full h-11 px-3 bg-surface-card border border-border-input rounded-lg font-code-mono text-body-md text-on-surface tracking-wider uppercase focus:outline-none focus:ring-2 focus:ring-primary-container" id="input-tracking-number" name="tracking_number" placeholder="Contoh: 004128912389 / JNE123456789" type="text" value="{{ old('tracking_number', $order->shipping_tracking_number) }}">
</div>
<button class="h-[44px] px-6 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-body-md text-body-md font-bold transition-all shadow-sm flex items-center gap-2" id="primary-workflow-btn" type="submit"><span class="material-symbols-outlined text-[18px]">local_shipping</span><span class="">Kirim Pesanan</span></button>
</form>
</div>
</div>
</div>
@elseif ($order->status === 'dikirim')
<div class="p-4 rounded-xl bg-blue-subtle/50 border border-primary-fixed flex flex-col md:flex-row md:items-center justify-between gap-4" id="workflow-action-box">
<div class="flex items-start gap-3">
<div class="w-10 h-10 rounded-full bg-tertiary text-on-tertiary flex items-center justify-center flex-shrink-0" id="action-box-icon"><span class="material-symbols-outlined text-[22px]">route</span></div>
<div>
<h3 class="font-headline-sm text-body-md font-bold text-on-surface" id="action-box-title">Paket Sedang Dalam Pengantaran</h3>
<p class="font-body-md text-caption text-on-surface-variant" id="action-box-desc">Pesanan menunggu konfirmasi penerimaan dari pembeli. Tombol selesai hanya tampil di sisi pembeli.</p>
</div>
</div>
<div class="flex items-center gap-2 flex-shrink-0">
<span class="h-[44px] px-6 rounded-full bg-surface-container text-ink-muted font-body-md text-body-md font-medium border border-border-default flex items-center gap-2" id="primary-workflow-btn"><span class="material-symbols-outlined text-[18px]">hourglass_empty</span><span class="">Menunggu Pembeli</span></span>
</div>
</div>
@elseif ($order->status === 'selesai')
<div class="p-4 rounded-xl bg-blue-subtle/50 border border-primary-fixed flex flex-col md:flex-row md:items-center justify-between gap-4" id="workflow-action-box">
<div class="flex items-start gap-3">
<div class="w-10 h-10 rounded-full bg-tertiary text-on-tertiary flex items-center justify-center flex-shrink-0" id="action-box-icon"><span class="material-symbols-outlined text-[22px]">task_alt</span></div>
<div>
<h3 class="font-headline-sm text-body-md font-bold text-on-surface" id="action-box-title">Transaksi Selesai &amp; Dana Dicairkan</h3>
<p class="font-body-md text-caption text-on-surface-variant" id="action-box-desc">Pembeli telah mengonfirmasi penerimaan barang. Dana penjualan Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }} telah masuk ke saldo toko Anda.</p>
</div>
</div>
<div class="flex items-center gap-2 flex-shrink-0">
<button class="h-[44px] px-6 rounded-full bg-canvas-background text-ink-muted cursor-not-allowed font-body-md text-body-md font-medium border border-border-default flex items-center gap-2" disabled id="primary-workflow-btn" type="button"><span class="material-symbols-outlined text-[18px]">done_all</span><span class="">Transaksi Diarsipkan</span></button>
</div>
</div>
@elseif ($order->status === 'menunggu_pembayaran')
<div class="p-4 rounded-xl bg-accent-yellow/15 border border-accent-yellow/40 flex flex-col md:flex-row md:items-center justify-between gap-4" id="workflow-action-box">
<div class="flex items-start gap-3">
<div class="w-10 h-10 rounded-full bg-accent-yellow text-on-accent-yellow flex items-center justify-center flex-shrink-0" id="action-box-icon"><span class="material-symbols-outlined text-[22px]">hourglass_top</span></div>
<div>
<h3 class="font-headline-sm text-body-md font-bold text-on-surface" id="action-box-title">Menunggu Pembayaran Pelanggan</h3>
<p class="font-body-md text-caption text-on-surface-variant" id="action-box-desc">Pesanan baru bisa diproses setelah pembayaran diverifikasi otomatis melalui payment gateway.</p>
</div>
</div>
<div class="flex items-center gap-2 flex-shrink-0">
<button class="h-[44px] px-6 rounded-full bg-canvas-background text-ink-muted cursor-not-allowed font-body-md text-body-md font-medium border border-border-default flex items-center gap-2" disabled id="primary-workflow-btn" type="button"><span class="material-symbols-outlined text-[18px]">hourglass_empty</span><span class="">Menunggu Pembayaran</span></button>
</div>
</div>
@else
<div class="p-4 rounded-xl bg-error-container/30 border border-secondary/20 flex flex-col md:flex-row md:items-center justify-between gap-4" id="workflow-action-box">
<div class="flex items-start gap-3 w-full">
<div class="w-10 h-10 rounded-full bg-error-container text-secondary flex items-center justify-center flex-shrink-0">
<span class="material-symbols-outlined text-[22px]">{{ $order->status === 'kedaluwarsa' ? 'timer_off' : 'cancel' }}</span>
</div>
<div class="flex-1">
<h3 class="font-headline-sm text-body-md font-bold text-secondary">{{ $order->status === 'kedaluwarsa' ? 'Pesanan Kedaluwarsa' : 'Pesanan Dibatalkan' }}</h3>
<p class="font-body-md text-caption text-on-surface-variant mt-0.5">
            Tidak ada tindakan lebih lanjut yang diperlukan untuk pesanan bernilai <strong>Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</strong> ini.
          </p>
</div>
</div>
</div>
@endif
<!-- Shipped Status Courier Tracking Detail Box (Visible when shipped or completed) -->
@if ($order->shipping_tracking_number && in_array($order->status, ['dikirim', 'selesai']))
<div class="p-4 rounded-xl bg-canvas-background border border-border-default flex flex-col space-y-3" id="shipped-tracking-card">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">pin_drop</span>
<span class="font-headline-sm text-caption font-bold text-on-surface">Informasi Pelacakan Real-time</span>
</div>
<span class="font-code-mono text-caption px-2.5 py-0.5 rounded-full bg-surface-card border border-border-default font-medium text-on-surface">
              Resi: <strong>{{ $order->shipping_tracking_number }}</strong>
</span>
</div>
<div class="flex items-center gap-3 text-caption text-ink-muted bg-surface-card p-3 rounded-lg border border-border-default">
<span class="w-2.5 h-2.5 rounded-full bg-tertiary-fixed-dim animate-ping"></span>
<p class="font-body-md text-caption text-on-surface flex-1">
<strong>Status Terakhir:</strong> Paket telah diserahkan ke kurir dan sedang dalam pengiriman ke alamat pembeli.
            </p>
</div>
</div>
@endif
</div>
<!-- Card 2: Rincian Produk Pesanan -->
<div class="bg-surface-card rounded-xl p-space-lg shadow-sm border border-border-default flex flex-col space-y-4">
<div class="flex items-center justify-between pb-space-sm border-b border-border-default">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[22px]">shopping_basket</span>
<h2 class="font-headline-sm text-headline-sm text-on-surface font-bold">Rincian Produk Pesanan</h2>
<span class="px-2 py-0.5 rounded-full bg-surface-container font-caption text-caption text-ink-muted">{{ $order->items->count() }} Item</span>
</div>
<span class="font-caption text-caption text-ink-muted">Total Kuantitas: {{ $totalQty }} barang</span>
</div>
<!-- Product Table / List -->
<div class="flex flex-col divide-y divide-border-default">
@foreach ($order->items as $item)
<div class="py-3 flex items-center justify-between gap-4">
<div class="flex items-center gap-3">
@if ($item->product?->image)
<img class="w-14 h-14 rounded-lg object-cover bg-surface-container flex-shrink-0 border border-border-default" src="{{ $item->product->image }}" alt="{{ $item->product_name }}">
@else
<div class="w-14 h-14 rounded-lg bg-surface-container flex items-center justify-center flex-shrink-0 border border-border-default">
<span class="material-symbols-outlined text-[22px] text-ink-muted">inventory_2</span>
</div>
@endif
<div class="flex flex-col">
<h4 class="font-body-md text-body-md font-bold text-on-surface line-clamp-1">{{ $item->product_name }}</h4>
<div class="flex items-center gap-2 font-caption text-caption text-ink-muted mt-0.5">
<span class="">Jumlah: <strong class="font-code-mono text-on-surface">{{ $item->quantity }}</strong></span>
</div>
</div>
</div>
<div class="text-right flex-shrink-0">
<div class="font-code-mono text-body-md text-ink-muted">Rp {{ number_format((float) $item->price, 0, ',', '.') }} × {{ $item->quantity }}</div>
<div class="font-code-mono text-price-sm font-bold text-on-surface">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</div>
</div>
</div>
@endforeach
</div>
<!-- Itemized Price Calculation Breakdown -->
<div class="bg-canvas-background rounded-lg p-4 flex flex-col space-y-2 font-caption border border-border-default">
<div class="flex justify-between text-on-surface-variant">
<span class="">Subtotal Produk ({{ $totalQty }} barang):</span>
<span class="font-code-mono font-medium">Rp {{ number_format($itemsSubtotal, 0, ',', '.') }}</span>
</div>
<div class="flex justify-between text-on-surface-variant">
<span class="">Biaya Layanan &amp; Ongkos Kirim:</span>
<span class="font-code-mono font-medium">Rp {{ number_format($otherCosts, 0, ',', '.') }}</span>
</div>
<div class="pt-2 border-t border-border-default flex justify-between items-center text-body-md font-bold text-on-surface">
<span class="font-headline-sm text-body-md">Total Tagihan Pesanan:</span>
<span class="font-code-mono text-price-sm font-bold text-primary">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
</div>
</div>
</div>
<!-- Card 3: Buyer Notes & Order Audit Trail -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-space-lg">
<!-- Catatan Pembeli -->
<div class="bg-surface-card rounded-xl p-space-lg shadow-sm border border-border-default flex flex-col space-y-3">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-accent-yellow text-[22px]">comment</span>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Catatan Pembeli</h3>
</div>
<div class="p-3 bg-surface-container-low rounded-lg border border-border-default flex items-start gap-2.5">
<span class="material-symbols-outlined text-ink-muted text-[18px] mt-0.5">format_quote</span>
<p class="font-body-md text-caption text-on-surface italic leading-relaxed">
            Tidak ada catatan dari pembeli untuk pesanan ini.
          </p>
</div>
<div class="flex items-center gap-1.5 text-caption text-tertiary font-medium">
<span class="material-symbols-outlined text-[16px]">check_circle</span>
<span class="">Catatan pembeli otomatis diteruskan ke tim gudang</span>
</div>
</div>
<!-- Mini Audit Log -->
<div class="bg-surface-card rounded-xl p-space-lg shadow-sm border border-border-default flex flex-col space-y-3">
<div class="flex items-center justify-between">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[22px]">history</span>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Log Riwayat Pesanan</h3>
</div>
<span class="font-caption text-caption text-ink-muted">{{ count($auditLog) }} Catatan</span>
</div>
<div class="flex flex-col space-y-3 text-caption font-body-md" id="audit-log-container">
@foreach ($auditLog as $entry)
<div class="flex items-start gap-2.5">
<span class="w-2 h-2 rounded-full bg-primary mt-1.5 flex-shrink-0"></span>
<div class="flex-1">
<p class="font-medium text-on-surface">{{ $entry[0] }}</p>
<p class="text-ink-muted text-[11px]">{{ $entry[1] }}</p>
</div>
</div>
@endforeach
</div>
</div>
</div>
</div>
<!-- RIGHT COLUMN (4 cols): Buyer Info, Delivery Info, Payment Data -->
<div class="lg:col-span-4 flex flex-col space-y-6">
<!-- Right Card 1: Data Pembeli -->
<div class="bg-surface-card rounded-xl p-space-lg shadow-sm border border-border-default flex flex-col space-y-4">
<div class="flex items-center justify-between pb-space-sm border-b border-border-default">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">person</span>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Data Pembeli</h3>
</div>
<span class="px-2 py-0.5 rounded-full {{ $order->user ? 'bg-tertiary-fixed text-on-tertiary-fixed' : 'bg-surface-container text-ink-muted' }} font-caption text-caption font-bold">
            {{ $order->user ? 'Terdaftar' : 'Tamu' }}
          </span>
</div>
<div class="flex items-center gap-3">
<div class="w-12 h-12 rounded-full bg-blue-subtle text-primary flex items-center justify-center font-bold text-headline-sm flex-shrink-0 border border-primary-fixed">
            {{ $initials ?: 'P' }}
          </div>
<div class="flex flex-col overflow-hidden">
<h4 class="font-headline-sm text-body-md font-bold text-on-surface truncate">{{ $buyerName }}</h4>
<span class="font-body-md text-caption text-ink-muted truncate">{{ $order->user?->email ?? 'Pembeli tanpa akun' }}</span>
<div class="flex items-center gap-1 font-caption text-[11px] text-tertiary font-medium mt-0.5">
<span class="material-symbols-outlined text-[13px]">storefront</span>
<span class="">Checkout via {{ ($order->channel ?? 'online') === 'pos' ? 'Kasir POS' : 'Web Store' }}</span>
</div>
</div>
</div>
<div class="pt-2 border-t border-border-default flex items-center justify-between">
<div class="flex flex-col">
<span class="font-caption text-caption text-ink-muted">Kontak Telepon / WA:</span>
<span class="font-code-mono text-caption font-bold text-on-surface">{{ $order->address?->phone ?? '—' }}</span>
</div>
@if ($waNumber)
<a class="h-9 px-3.5 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface font-body-md text-caption font-bold inline-flex items-center gap-1.5 transition-colors border border-border-default" href="https://wa.me/{{ $waNumber }}" target="_blank">
<span class="material-symbols-outlined text-[16px] text-tertiary">chat</span>
<span class="">Kirim WA</span>
</a>
@endif
</div>
</div>
<!-- Right Card 2: Alamat Pengiriman -->
<div class="bg-surface-card rounded-xl p-space-lg shadow-sm border border-border-default flex flex-col space-y-4">
<div class="flex items-center justify-between pb-space-sm border-b border-border-default">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">local_shipping</span>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Alamat Pengiriman</h3>
</div>
<span class="font-caption text-caption font-bold {{ $order->shipping_tracking_number ? 'text-primary' : 'text-ink-muted' }}">{{ $order->shipping_tracking_number ? 'Resi Terpasang' : 'Belum Dikirim' }}</span>
</div>
<div class="flex flex-col space-y-2">
<div class="flex items-center gap-2 text-caption text-ink-muted">
<span class="material-symbols-outlined text-[16px]">schedule</span>
@if ($order->shipping_tracking_number)
<span class="">Nomor Resi: <strong class="font-code-mono text-on-surface">{{ $order->shipping_tracking_number }}</strong></span>
@else
<span class="">Nomor resi belum diinput penjual</span>
@endif
</div>
@if ($order->address)
<div class="p-3 bg-surface-container-low rounded-lg border border-border-default flex flex-col space-y-1.5">
<div class="flex items-center justify-between font-caption">
<span class="font-bold text-on-surface">{{ $order->address->recipient_name }}</span>
<span class="font-code-mono text-ink-muted">{{ $order->address->phone }}</span>
</div>
<p class="font-body-md text-caption text-on-surface leading-snug">
              {{ $order->address->address_detail }}, {{ $order->address->city }} {{ $order->address->postal_code }}
            </p>
</div>
@else
<div class="p-3 bg-surface-container-low rounded-lg border border-border-default">
<p class="font-body-md text-caption text-ink-muted">—</p>
</div>
@endif
</div>
@if ($fullAddress)
<button class="w-full py-2 px-3 rounded-full border border-border-default bg-surface-card hover:bg-surface-container text-on-surface font-body-md text-caption font-medium transition-colors flex items-center justify-center gap-1.5" onclick="copyAddress()">
<span class="material-symbols-outlined text-[16px]">content_copy</span>
<span class="">Salin Teks Alamat Lengkap</span>
</button>
@endif
</div>
<!-- Right Card 3: Informasi Pembayaran -->
<div class="bg-surface-card rounded-xl p-space-lg shadow-sm border border-border-default flex flex-col space-y-4">
<div class="flex items-center justify-between pb-space-sm border-b border-border-default">
<div class="flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[20px]">account_balance_wallet</span>
<h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Informasi Pembayaran</h3>
</div>
<span class="px-2.5 py-0.5 rounded-full {{ $paymentClass }} font-caption text-caption font-bold" id="payment-badge-status">{{ $paymentLabel }}</span>
</div>
<div class="flex flex-col space-y-1">
<span class="font-caption text-caption text-ink-muted">Total Pembayaran:</span>
<span class="font-price-lg text-price-lg text-primary font-bold font-code-mono">Rp {{ number_format((float) ($payment?->amount ?? $order->total_amount), 0, ',', '.') }}</span>
</div>
<div class="flex flex-col space-y-2.5 font-caption text-caption pt-2 border-t border-border-default">
<div class="flex items-center justify-between">
<span class="text-ink-muted">Metode Pembayaran:</span>
<span class="font-medium text-on-surface flex items-center gap-1">
<span class="w-2 h-2 rounded-full {{ $payment?->status === 'paid' ? 'bg-tertiary' : 'bg-surface-dim' }}"></span>
              {{ $payment?->payment_type ?? '—' }}
            </span>
</div>
<div class="flex items-center justify-between">
<span class="text-ink-muted">Status Pembayaran:</span>
<span class="font-medium text-on-surface">{{ $paymentLabel }}</span>
</div>
<div class="flex items-center justify-between">
<span class="text-ink-muted">Waktu Pembayaran:</span>
<span class="font-medium text-on-surface" id="payment-timestamp-text">{{ $payment?->created_at?->translatedFormat('d M Y, H:i') ?? '—' }}</span>
</div>
</div>
<div class="p-2.5 rounded-lg bg-blue-subtle/40 border border-primary-fixed flex items-center gap-2">
<span class="material-symbols-outlined text-primary text-[18px]">lock</span>
<span class="text-[11px] text-primary leading-tight font-medium">
            Transaksi diproses melalui payment gateway dengan perlindungan escrow untuk pembeli dan penjual.
          </span>
</div>
</div>
</div>
</div>
@if ($canCancel)
<!-- MODAL: Batalkan Pesanan (Urgent / Red) -->
<div class="fixed inset-0 bg-on-surface/50 backdrop-blur-sm z-50 flex items-center justify-center p-4 hidden" id="modal-cancel">
<div class="bg-surface-card rounded-2xl max-w-lg w-full p-space-xl shadow-xl border border-secondary/30 flex flex-col space-y-5 animate-in fade-in zoom-in-95 duration-200">
<div class="flex items-center justify-between pb-3 border-b border-border-default">
<div class="flex items-center gap-2">
<div class="w-9 h-9 rounded-full bg-error-container text-secondary flex items-center justify-center">
<span class="material-symbols-outlined text-[20px]">warning</span>
</div>
<div>
<h3 class="font-headline-sm text-headline-sm text-secondary font-bold">Batalkan Pesanan Online</h3>
<p class="font-caption text-caption text-ink-muted">{{ $order->order_code }}</p>
</div>
</div>
<button class="p-1 rounded-full text-ink-muted hover:text-on-surface hover:bg-surface-container transition-colors" onclick="closeCancelModal()">
<span class="material-symbols-outlined text-[22px]">close</span>
</button>
</div>
<!-- Warning Callout -->
<div class="p-3.5 rounded-xl bg-error-container/30 border border-secondary/20 flex items-start gap-2.5">
<span class="material-symbols-outlined text-secondary text-[20px] mt-0.5">report_problem</span>
<div class="text-caption text-on-surface flex-1">
<strong class="text-secondary font-bold">Perhatian:</strong> Pesanan yang dibatalkan tidak dapat dikembalikan. Dana pembeli sejumlah <strong>Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</strong> akan direfund secara otomatis ke rekening asal pembeli.
        </div>
</div>
<form action="{{ route('seller.orders.status', $order->order_code) }}" method="POST" id="form-cancel-order" class="flex flex-col space-y-3">
@csrf
<input name="status" type="hidden" value="dibatalkan">
<div class="flex flex-col space-y-1.5">
<label class="font-headline-sm text-caption font-bold text-on-surface" for="cancel-reason-select">Pilih / Jelaskan Alasan Pembatalan <span class="text-secondary">*</span></label>
<select class="w-full h-11 px-3 bg-surface-card border border-border-input rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-secondary" id="cancel-reason-select" name="note">
<option value="Stok fisik rusak/cacat saat pengecekan di gudang">Stok fisik rusak/cacat saat pengecekan di gudang</option>
<option value="Stok barang telah habis terjual di kasir fisik POS">Stok barang telah habis terjual di kasir fisik POS</option>
<option value="Permintaan langsung dari pembeli melalui chat WhatsApp">Permintaan langsung dari pembeli melalui chat WhatsApp</option>
<option value="Kendala logistik kurir pengiriman di area toko">Kendala logistik kurir pengiriman di area toko</option>
</select>
</div>
<div class="p-2.5 rounded-lg bg-surface-container-low text-[11px] text-ink-muted flex items-center gap-1.5">
<span class="material-symbols-outlined text-[16px] text-tertiary">inventory</span>
<span class="">Stok {{ $order->items->count() }} item akan otomatis direlease kembali ke etalase online &amp; POS.</span>
</div>
<div class="pt-3 border-t border-border-default flex items-center justify-end gap-3">
<button class="h-11 px-5 rounded-full border border-border-default bg-surface-card text-on-surface font-body-md text-body-md font-medium hover:bg-surface-container transition-colors" onclick="closeCancelModal()" type="button">
          Kembali
        </button>
<button class="h-11 px-6 rounded-full bg-secondary hover:bg-red-graphic text-on-secondary font-body-md text-body-md font-bold transition-all shadow-sm flex items-center gap-1.5" type="submit">
<span class="material-symbols-outlined text-[18px]">cancel</span>
<span class="">Ya, Batalkan Pesanan</span>
</button>
</div>
</form>
</div>
</div>
@endif
@endsection

@push('scripts')
<script>
  const fullAddress = @json($fullAddress);

  function copyAddress() {
    navigator.clipboard.writeText(fullAddress).then(() => {
      alert('Alamat disalin ke clipboard!');
    });
  }

  function openCancelModal() {
    document.getElementById('modal-cancel').classList.remove('hidden');
  }

  function closeCancelModal() {
    document.getElementById('modal-cancel').classList.add('hidden');
  }

  document.querySelectorAll('#modal-cancel').forEach(modal => {
    modal.addEventListener('click', (event) => {
      if (event.target === modal) closeCancelModal();
    });
  });
</script>
@endpush
