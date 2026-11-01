@extends('layouts.app')

@section('title', 'Detail Pesanan ' . $order->order_code . ' - Lokalku')

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
        'pending' => 'Menunggu',
        'paid' => 'Lunas',
        'failed' => 'Gagal',
        'expired' => 'Kedaluwarsa',
    ];
    $paymentStatusLabel = $paymentStatusMap[$payment?->status] ?? 'Menunggu';
    $paymentStatusClass = match ($payment?->status) {
        'paid' => 'bg-surface-container-low text-success-text',
        'pending' => 'bg-accent-yellow/20 text-on-accent-yellow',
        'failed' => 'bg-error-container text-secondary',
        'expired' => 'bg-surface-container text-ink-muted',
        default => 'bg-surface-container text-ink-muted',
    };

    // Timeline progres pesanan: Pesanan Dibuat -> Pembayaran -> Diproses -> Dikirim -> Selesai
    $timelineSteps = [
        ['title' => 'Pesanan Dibuat', 'desc' => 'Pesanan berhasil dibuat pada ' . $order->created_at->translatedFormat('d M Y, H:i') . ' WIB.'],
        ['title' => 'Pembayaran', 'desc' => 'Pembayaran diverifikasi otomatis oleh gerbang pembayaran Lokalku.'],
        ['title' => 'Diproses', 'desc' => 'Penjual sedang menyiapkan dan mengemas pesanan Anda.'],
        ['title' => 'Dikirim', 'desc' => 'Paket diserahkan ke kurir dan sedang dalam perjalanan ke alamat tujuan.'],
        ['title' => 'Selesai', 'desc' => 'Pesanan telah diterima dan transaksi selesai.'],
    ];

    $activeStep = match ($order->status) {
        'menunggu_pembayaran' => 1,
        'dibayar' => 2,
        'diproses' => 3,
        'dikirim' => 4,
        'selesai' => 5, // semua langkah selesai
        default => 0, // dibatalkan / kedaluwarsa
    };

    $stepState = function (int $index) use ($activeStep): string {
        if ($activeStep === 0) {
            return 'pending';
        }
        if ($index < $activeStep) {
            return 'done';
        }
        return $index === $activeStep && $activeStep < 5 ? 'active' : 'pending';
    };

    $itemsSubtotal = (float) $order->items->sum('subtotal');
    $shippingCost = max((float) $order->total_amount - $itemsSubtotal, 0);
@endphp
<div class="max-w-[1280px] w-full mx-auto px-4 py-6 md:py-8 space-y-6">
    <main class="w-full max-w-[1280px] mx-auto px-0 sm:px-0 flex flex-col gap-6">
        <!-- Breadcrumb & Back Action -->
        <section class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <nav aria-label="Navigasi rekam jejak" class="flex items-center flex-wrap gap-1 text-caption font-caption text-ink-muted">
                <a class="hover:text-primary transition-colors" href="{{ route('home') }}">Beranda</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <a class="hover:text-primary transition-colors" href="#">Akun Saya</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <a class="hover:text-primary transition-colors" href="{{ route('orders.index') }}">Pesanan Saya</a>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="font-code-mono text-code-mono text-on-surface font-semibold">{{ $order->order_code }}</span>
            </nav>
            <a class="inline-flex items-center gap-1.5 text-caption font-caption text-primary hover:text-blue-hover transition-colors font-medium" href="{{ route('orders.index') }}">
                <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                <span>Kembali ke Daftar Pesanan</span>
            </a>
        </section>
        <!-- Urgent Pending Payment Banner (State: Menunggu Pembayaran) -->
        <div class="{{ $order->status === 'menunggu_pembayaran' ? '' : 'hidden' }} rounded-xl bg-accent-yellow p-4 shadow-sm text-on-accent-yellow" id="banner-menunggu">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <span class="material-symbols-outlined text-[24px] text-on-accent-yellow flex-shrink-0 mt-0.5">hourglass_top</span>
                    <div class="flex flex-col gap-0.5">
                        <span class="font-headline-sm text-headline-sm font-bold text-on-accent-yellow">Menunggu Pembayaran Sebelum Batas Waktu</span>
                        <p class="font-body-md text-body-md text-on-accent-yellow">
                            Selesaikan pembayaran segera agar pesanan tidak dibatalkan otomatis oleh sistem Lokalku.
                        </p>
                    </div>
                </div>
                <a class="inline-flex items-center justify-center gap-2 h-11 px-6 rounded-lg bg-surface-container-lowest text-on-surface shadow-sm font-body-md text-body-md font-bold self-end sm:self-center hover:bg-surface-container transition-colors" href="{{ route('payment.show', $order->order_code) }}">
                    <span class="material-symbols-outlined text-[18px] text-secondary">qr_code_2</span>
                    <span>Bayar Sekarang</span>
                </a>
            </div>
        </div>
        <!-- Header Block: Title, Meta, Badge -->
        <header class="bg-surface-card rounded-xl p-5 sm:p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-col gap-2">
                <div class="flex items-center flex-wrap gap-3">
                    <h1 class="font-headline-lg text-headline-lg text-on-surface font-bold tracking-tight">Detail Pesanan</h1>
                    <span class="font-code-mono text-code-mono font-bold bg-surface-container px-3 py-1 rounded-md text-on-surface">{{ $order->order_code }}</span>
                    <!-- Dynamic Status Badge -->
                    <div id="header-status-badge">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-caption font-caption font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>
                </div>
                <div class="flex items-center gap-2 text-ink-muted text-caption font-caption">
                    <span class="material-symbols-outlined text-[16px]">calendar_today</span>
                    <span>Dipesan pada <span class="font-code-mono text-code-mono font-medium text-on-surface">{{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB</span></span>
                </div>
            </div>
            <!-- Action buttons depending on state -->
            <div class="flex items-center flex-wrap gap-2.5" id="header-quick-actions">
                <a class="h-11 px-5 rounded-full bg-surface-container-low text-on-surface hover:bg-surface-container transition-colors inline-flex items-center gap-2 text-caption font-caption font-bold" href="{{ route('orders.invoice', $order->order_code) }}" target="_blank">
                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                    <span>Invoice Digital</span>
                </a>
                @if ($order->status === 'menunggu_pembayaran')
                    <a class="h-11 px-6 rounded-full bg-primary-container text-on-primary hover:bg-blue-hover transition shadow-sm font-body-md text-body-md font-bold inline-flex items-center justify-center gap-2" href="{{ route('payment.show', $order->order_code) }}">
                        <span class="material-symbols-outlined text-[18px]">qr_code_2</span>
                        <span>Bayar Sekarang</span>
                    </a>
                @else
                    <a class="h-11 px-5 rounded-full bg-primary-container text-on-primary hover:bg-blue-hover transition shadow-sm font-body-md text-body-md font-bold inline-flex items-center justify-center gap-2" href="{{ route('products.index') }}">
                        <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                        <span>Lihat Semua Produk</span>
                    </a>
                @endif
            </div>
        </header>
        <!-- Main Layout: 8 cols + 4 cols -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- LEFT COLUMN: 8 COLS -->
            <section aria-label="Rincian Pengiriman dan Produk" class="lg:col-span-8 flex flex-col gap-6">
                <!-- Card 1: Status &amp; Pelacakan Pengiriman -->
                <article class="bg-surface-card rounded-xl p-5 sm:p-6 shadow-sm flex flex-col gap-6">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 gap-3 bg-surface-container-lowest">
                        <div class="flex items-center gap-2.5">
                            <div class="w-10 h-10 rounded-full bg-blue-subtle text-primary flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px]">local_shipping</span>
                            </div>
                            <div class="flex flex-col">
                                <span class="font-headline-sm text-headline-sm text-on-surface font-bold">Status &amp; Pelacakan Pengiriman</span>
                                <span class="font-caption text-caption text-ink-muted">Layanan Kurir Terintegrasi Lokalku</span>
                            </div>
                        </div>
                        <!-- Resi number & Copy trigger -->
                        @if ($order->shipping_tracking_number)
                            <div class="flex items-center gap-2 bg-surface-container-low px-3 py-1.5 rounded-lg">
                                <div class="flex flex-col">
                                    <span class="font-caption text-caption text-ink-muted">No. Resi:</span>
                                    <span class="font-code-mono text-code-mono font-bold text-on-surface" id="tracking-number">{{ $order->shipping_tracking_number }}</span>
                                </div>
                                <button aria-label="Salin nomor resi" class="h-8 px-2.5 rounded-full bg-surface hover:bg-primary-container hover:text-on-primary transition text-primary text-caption font-caption font-bold inline-flex items-center gap-1 shadow-sm" onclick="copyResi('{{ $order->shipping_tracking_number }}')" type="button">
                                    <span class="material-symbols-outlined text-[16px]" id="copy-icon">content_copy</span>
                                    <span id="copy-label">Salin</span>
                                </button>
                            </div>
                        @else
                            <div class="flex items-center gap-2 bg-surface-container-low px-3 py-1.5 rounded-lg">
                                <div class="flex flex-col">
                                    <span class="font-caption text-caption text-ink-muted">No. Resi:</span>
                                    <span class="font-caption text-caption font-bold text-ink-muted">Belum tersedia</span>
                                </div>
                            </div>
                        @endif
                    </div>
                    <!-- Vertical Timeline Steps -->
                    <div class="relative pl-6 sm:pl-8 flex flex-col gap-8" id="delivery-timeline">
                        @foreach ($timelineSteps as $stepIndex => $step)
                            @php
                                $state = $stepState($stepIndex);
                                $isLast = $stepIndex === count($timelineSteps) - 1;
                                $nextState = ! $isLast ? $stepState($stepIndex + 1) : null;
                            @endphp
                            <div class="timeline-step relative flex items-start gap-4">
                                @unless ($isLast)
                                    <!-- Marker line -->
                                    <div class="absolute left-[-24px] sm:left-[-32px] top-6 bottom-[-32px] w-0.5 {{ $nextState !== 'pending' ? 'bg-success-text' : 'bg-surface-variant' }}"></div>
                                @endunless
                                <!-- Circle -->
                                <div class="absolute left-[-34px] sm:left-[-42px] top-0 w-6 h-6 rounded-full flex items-center justify-center shadow-sm {{ $state === 'done' ? 'bg-success-text text-on-primary' : ($state === 'active' ? 'bg-primary-container text-on-primary' : 'bg-surface-container text-ink-muted') }}">
                                    @if ($state === 'done')
                                        <span class="material-symbols-outlined text-[16px] font-bold">check</span>
                                    @elseif ($state === 'active')
                                        <span class="w-2.5 h-2.5 rounded-full bg-on-primary animate-ping"></span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-outline"></span>
                                    @endif
                                </div>
                                <div class="flex flex-col gap-0.5">
                                    <div class="flex items-center gap-2">
                                        <span class="font-body-md text-body-md {{ $state === 'done' ? 'font-bold text-on-surface' : ($state === 'active' ? 'font-bold text-primary' : 'font-medium text-ink-muted') }}">{{ $stepIndex + 1 }}. {{ $step['title'] }}</span>
                                        @if ($state === 'active')
                                            <span class="bg-blue-subtle text-primary font-medium text-caption font-caption px-2 py-0.5 rounded-md font-semibold">Aktif</span>
                                        @elseif ($state === 'done' && $stepIndex === 1 && in_array($payment?->status, ['paid']))
                                            <span class="bg-surface-container-low text-success-text text-caption font-caption px-2 py-0.5 rounded-md font-semibold">Tervalidasi</span>
                                        @endif
                                    </div>
                                    <p class="font-caption text-caption text-ink-muted leading-relaxed">
                                        {{ $step['desc'] }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <!-- Action Box: Controlled by dynamic state -->
                    <div class="mt-4 pt-4 bg-surface-container-lowest" id="shipping-interactive-box">
                        @if ($order->status === 'dikirim')
                            <!-- Box for "Dikirim" -->
                            <div class="bg-blue-subtle rounded-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4" id="action-box-dikirim">
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-[22px] text-primary flex-shrink-0 mt-0.5">info</span>
                                    <div class="flex flex-col">
                                        <span class="font-body-md text-body-md font-bold text-on-surface">Barang sudah Anda terima?</span>
                                        <p class="font-caption text-caption text-ink-muted">Konfirmasi hanya setelah barang fisik Anda terima dan periksa dengan baik.</p>
                                    </div>
                                </div>
                                <form action="{{ route('orders.complete', $order->order_code) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    <button class="h-11 px-6 rounded-full bg-primary-container text-on-primary hover:bg-blue-hover transition shadow-sm font-body-md text-body-md font-bold flex items-center justify-center gap-2" type="submit">
                                        <span class="material-symbols-outlined text-[18px]">verified</span>
                                        <span>Pesanan Diterima</span>
                                    </button>
                                </form>
                            </div>
                        @elseif ($order->status === 'menunggu_pembayaran')
                            <!-- Box for "Menunggu Pembayaran" -->
                            <div class="bg-surface-container rounded-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4" id="action-box-menunggu">
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-[22px] text-on-accent-yellow flex-shrink-0 mt-0.5">payments</span>
                                    <div class="flex flex-col">
                                        <span class="font-body-md text-body-md font-bold text-on-surface">Selesaikan Pembayaran Sekarang</span>
                                        <p class="font-caption text-caption text-ink-muted">Konfirmasi setelah membayar ke penjual sesuai kesepakatan.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 w-full sm:w-auto">
                                    <button class="h-11 px-5 rounded-full bg-surface text-secondary hover:bg-secondary hover:text-on-secondary transition font-body-md text-body-md font-bold flex-shrink-0 shadow-sm" type="button">
                                        Batalkan Pesanan
                                    </button>
                                    <a class="h-11 px-6 rounded-full bg-primary-container text-on-primary hover:bg-blue-hover transition shadow-sm font-body-md text-body-md font-bold flex-shrink-0 flex items-center justify-center gap-2" href="{{ route('payment.show', $order->order_code) }}">
                                        <span class="material-symbols-outlined text-[18px]">payments</span>
                                        <span>Bayar Sekarang</span>
                                    </a>
                                </div>
                            </div>
                        @elseif (in_array($order->status, ['dibayar', 'diproses']))
                            <!-- Box for "Dibayar / Diproses" -->
                            <div class="bg-blue-subtle rounded-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4" id="action-box-proses">
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-[22px] text-primary flex-shrink-0 mt-0.5">inventory_2</span>
                                    <div class="flex flex-col">
                                        <span class="font-body-md text-body-md font-bold text-on-surface">Pesanan Sedang Diproses Penjual</span>
                                        <p class="font-caption text-caption text-ink-muted">Pembaruan status pengiriman akan muncul di halaman ini.</p>
                                    </div>
                                </div>
                            </div>
                        @elseif ($order->status === 'selesai')
                            <!-- Box for "Selesai" -->
                            <div class="bg-surface-container-low rounded-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4" id="action-box-selesai">
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-[22px] text-success-text flex-shrink-0 mt-0.5">task_alt</span>
                                    <div class="flex flex-col">
                                        <span class="font-body-md text-body-md font-bold text-on-surface">Transaksi Selesai</span>
                                        <p class="font-caption text-caption text-ink-muted">Terima kasih telah mendukung UMKM lokal melalui Lokalku.</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2.5 w-full sm:w-auto">
                                    <a class="h-11 px-5 rounded-full bg-surface text-on-surface hover:bg-surface-container transition font-body-md text-body-md font-bold flex-shrink-0 shadow-sm inline-flex items-center" href="{{ route('products.index') }}">
                                        Beli Lagi
                                    </a>
                                    <button class="h-11 px-6 rounded-full bg-primary-container text-on-primary hover:bg-blue-hover transition shadow-sm font-body-md text-body-md font-bold flex-shrink-0 flex items-center justify-center gap-1.5" type="button">
                                        <span class="material-symbols-outlined text-[18px]">rate_review</span>
                                        <span>Beri Ulasan Produk</span>
                                    </button>
                                </div>
                            </div>
                        @else
                            <!-- Box for "Dibatalkan / Kedaluwarsa" -->
                            <div class="bg-error-container/40 rounded-xl p-4 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4" id="action-box-batal">
                                <div class="flex items-start gap-3">
                                    <span class="material-symbols-outlined text-[22px] text-secondary flex-shrink-0 mt-0.5">info</span>
                                    <div class="flex flex-col">
                                        <span class="font-body-md text-body-md font-bold text-on-surface">Pesanan Tidak Jadi Diproses</span>
                                        <p class="font-caption text-caption text-ink-muted">Pesanan ini {{ $statusLabel === 'Dibatalkan' ? 'dibatalkan' : 'kedaluwarsa' }} dan tidak dapat dilanjutkan.</p>
                                    </div>
                                </div>
                                <a class="h-11 px-6 rounded-full bg-primary-container text-on-primary hover:bg-blue-hover transition shadow-sm font-body-md text-body-md font-bold flex-shrink-0 flex items-center justify-center gap-2" href="{{ route('products.index') }}">
                                    <span class="material-symbols-outlined text-[18px]">shopping_bag</span>
                                    <span>Lihat Semua Produk</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </article>
                <!-- Card 2: Daftar Produk yang Dibeli -->
                <article class="bg-surface-card rounded-xl p-5 sm:p-6 shadow-sm flex flex-col gap-6">
                    <!-- Merchant header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 bg-surface-container-lowest">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-primary">
                                <span class="material-symbols-outlined text-[22px]">store</span>
                            </div>
                            <div class="flex flex-col">
                                <div class="flex items-center gap-2">
                                    <span class="font-headline-sm text-headline-sm font-bold text-on-surface">{{ $order->items->first()?->product?->store?->name ?? 'Penjual Lokalku' }}</span>
                                    <span class="inline-flex items-center gap-1 bg-surface-container-low text-success-text text-caption font-caption px-2 py-0.5 rounded-full font-semibold">
                                        <span class="material-symbols-outlined text-[14px]">verified</span>
                                        UMKM Terverifikasi
                                    </span>
                                </div>
                                <span class="font-caption text-caption text-ink-muted">Mitra UMKM Lokalku</span>
                            </div>
                        </div>
                        <!-- Merchant communication links -->
                        <div class="flex items-center gap-2">
                            <a class="h-9 px-3 rounded-full bg-surface-container-low hover:bg-surface-container text-on-surface text-caption font-caption font-semibold inline-flex items-center gap-1 transition" href="{{ route('products.index') }}">
                                <span class="material-symbols-outlined text-[16px] text-ink-muted">storefront</span>
                                <span>Kunjungi Toko</span>
                            </a>
                            <button class="h-9 px-3.5 rounded-full bg-blue-subtle hover:bg-primary-container hover:text-on-primary text-primary text-caption font-caption font-semibold inline-flex items-center gap-1 transition" type="button">
                                <span class="material-symbols-outlined text-[16px]">chat</span>
                                <span>Tanya Penjual (Chat)</span>
                            </button>
                        </div>
                    </div>
                    @foreach ($order->items as $item)
                        <!-- Product Item -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 py-2">
                            <div class="flex items-center gap-4">
                                <!-- Thumbnail -->
                                <div class="w-20 h-20 rounded-lg overflow-hidden bg-surface-container flex-shrink-0 shadow-sm relative">
                                    <img class="w-full h-full object-cover" alt="{{ $item->product_name }}" src="{{ $item->product?->image ?? 'https://placehold.co/96x96?text=%3F' }}">
                                </div>
                                <!-- Meta -->
                                <div class="flex flex-col gap-1">
                                    <span class="font-body-md text-body-md font-bold text-on-surface leading-snug">
                                        {{ $item->product_name }}
                                    </span>
                                    <div class="flex items-center gap-2 font-code-mono text-code-mono text-caption text-ink-muted mt-1">
                                        <span>Rp {{ number_format((float) $item->price, 0, ',', '.') }}</span>
                                        <span>×</span>
                                        <span class="font-bold text-on-surface">{{ $item->quantity }} pcs</span>
                                    </div>
                                </div>
                            </div>
                            <!-- Subtotal -->
                            <div class="text-right sm:self-center self-end">
                                <span class="font-caption text-caption text-ink-muted block">Subtotal Produk</span>
                                <span class="font-price-sm text-price-sm font-bold text-on-surface font-code-mono">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                    <!-- Value Added Callout for Local Purchase -->
                    <div class="bg-surface-container-low rounded-xl p-3.5 flex items-center gap-3">
                        <span class="material-symbols-outlined text-primary text-[20px]">workspace_premium</span>
                        <p class="font-caption text-caption text-on-surface leading-tight">
                            <strong>100% Produk UMKM Lokal:</strong> Pembelian ini berkontribusi langsung pada pemberdayaan pelaku usaha mikro, kecil, dan menengah lokal Indonesia.
                        </p>
                    </div>
                </article>
            </section>
            <!-- RIGHT COLUMN: 4 COLS -->
            <aside aria-label="Informasi Pengiriman dan Rincian Biaya" class="lg:col-span-4 flex flex-col gap-6">
                <!-- Card 1: Alamat Pengiriman -->
                <article class="bg-surface-card rounded-xl p-5 sm:p-6 shadow-sm flex flex-col gap-4">
                    <div class="flex items-center justify-between pb-3 bg-surface-container-lowest">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">pin_drop</span>
                            <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Alamat Pengiriman</h2>
                        </div>
                        <span class="bg-surface-container px-2.5 py-0.5 rounded-full text-caption font-caption text-ink-muted font-semibold">Alamat Utama</span>
                    </div>
                    @if ($order->address)
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center justify-between">
                                <span class="font-body-md text-body-md font-bold text-on-surface">{{ $order->address->recipient_name }}</span>
                                <span class="font-code-mono text-code-mono text-caption text-ink-muted font-medium">{{ $order->address->phone }}</span>
                            </div>
                            <p class="font-body-md text-body-md text-on-surface leading-relaxed">
                                {{ $order->address->address_detail }}, {{ $order->address->city }} {{ $order->address->postal_code }}
                            </p>
                        </div>
                    @else
                        <p class="font-body-md text-body-md text-ink-muted">—</p>
                    @endif
                </article>
                <!-- Card 2: Ringkasan Pembayaran -->
                <article class="bg-surface-card rounded-xl p-5 sm:p-6 shadow-sm flex flex-col gap-5">
                    <div class="flex items-center justify-between pb-3 bg-surface-container-lowest">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">account_balance_wallet</span>
                            <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Ringkasan Pembayaran</h2>
                        </div>
                        <div id="payment-status-badge">
                            <span class="{{ $paymentStatusClass }} font-caption font-bold px-2.5 py-0.5 rounded-full">{{ $paymentStatusLabel }}</span>
                        </div>
                    </div>
                    <!-- Method & Timestamp details -->
                    <div class="bg-surface-container-low rounded-lg p-3 flex flex-col gap-2">
                        <div class="flex items-center justify-between">
                            <span class="font-caption text-caption text-ink-muted">Metode Pembayaran</span>
                            <div class="flex items-center gap-1.5 font-caption font-caption font-bold text-on-surface">
                                <span class="material-symbols-outlined text-secondary text-[16px]">payments</span>
                                <span>Pembayaran Manual</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-caption text-caption text-ink-muted">Waktu Pembayaran</span>
                            <span class="font-code-mono text-code-mono text-caption font-semibold text-on-surface" id="payment-timestamp">{{ $payment?->created_at?->translatedFormat('d M Y, H:i') ?? '—' }}</span>
                        </div>
                    </div>
                    <!-- Price Calculations -->
                    <div class="flex flex-col gap-3 font-body-md text-body-md">
                        <div class="flex justify-between items-center text-ink-muted">
                            <span>Total Harga Barang ({{ $order->items->count() }} item)</span>
                            <span class="font-code-mono text-on-surface">Rp {{ number_format($itemsSubtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-ink-muted">
                            <div class="flex items-center gap-1">
                                <span>Ongkos Kirim</span>
                                <span class="material-symbols-outlined text-[14px] text-ink-muted" title="Disubsidi program Lokalku">help_outline</span>
                            </div>
                            <div class="flex items-center gap-1.5 font-code-mono">
                                @if ($shippingCost > 0)
                                    <span class="text-on-surface">Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-success-text font-bold">Rp 0 (Subsidi UMKM)</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex justify-between items-center text-ink-muted">
                            <span>Biaya Asuransi Pengiriman</span>
                            <span class="font-code-mono text-success-text font-bold">Gratis</span>
                        </div>
                        <div class="flex justify-between items-center text-ink-muted">
                            <span>Biaya Layanan Gerbang Pembayaran</span>
                            <span class="font-code-mono text-success-text font-bold">Rp 0</span>
                        </div>
                        <div class="pt-3 mt-1 bg-surface-container-lowest flex justify-between items-baseline">
                            <span class="font-headline-sm text-headline-sm font-bold text-on-surface">Total Tagihan</span>
                            <span class="font-price-lg text-price-lg font-bold text-primary font-code-mono">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <!-- Secondary Download Invoice Button -->
                    <a class="w-full h-11 rounded-full bg-surface text-primary hover:bg-blue-subtle transition font-body-md text-body-md font-bold inline-flex items-center justify-center gap-2 shadow-sm" href="{{ route('orders.invoice', $order->order_code) }}" target="_blank">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span>Unduh Bukti Pembayaran (Invoice)</span>
                    </a>
                </article>
                <!-- Card 3: Bantuan &amp; Kendala -->
                <article class="bg-surface-card rounded-xl p-5 sm:p-6 shadow-sm flex flex-col gap-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-ink-muted text-[20px]">help_center</span>
                        <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Bantuan &amp; Kendala</h2>
                    </div>
                    <p class="font-caption text-caption text-ink-muted">
                        Ada kendala dengan pesanan ini? Tim dukungan pembeli Lokalku siap mendampingi transaksi aman Anda.
                    </p>
                    <div class="flex flex-col gap-2 pt-1">
                        <a class="h-10 px-4 rounded-lg bg-surface-container-low hover:bg-surface-container text-on-surface font-caption text-caption font-semibold flex items-center justify-between transition" href="#">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-primary">support_agent</span>
                                <span>Pusat Bantuan Lokalku</span>
                            </span>
                            <span class="material-symbols-outlined text-[16px] text-ink-muted">open_in_new</span>
                        </a>
                        <a class="h-10 px-4 rounded-lg bg-surface-container-low hover:bg-surface-container text-secondary font-caption text-caption font-semibold flex items-center justify-between transition" href="#">
                            <span class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[16px] text-secondary">report_problem</span>
                                <span>Laporkan Masalah Pengiriman</span>
                            </span>
                            <span class="material-symbols-outlined text-[16px] text-ink-muted">chevron_right</span>
                        </a>
                    </div>
                </article>
            </aside>
        </div>
    </main>
    <!-- Interactive Feedback Toast -->
    <div class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none bg-inverse-surface text-inverse-on-surface px-5 py-3 rounded-xl shadow-lg flex items-center gap-2 font-caption text-caption" id="toast-notification">
        <span class="material-symbols-outlined text-tertiary-fixed text-[20px]">check_circle</span>
        <span id="toast-message">Teks notifikasi</span>
    </div>
</div>
@endsection
@push('scripts')
<script>
    function showToast(message) {
        const toast = document.getElementById('toast-notification');
        const toastMsg = document.getElementById('toast-message');
        if (!toast || !toastMsg) return;
        toastMsg.textContent = message;
        toast.classList.remove('translate-y-20', 'opacity-0', 'pointer-events-none');
        toast.classList.add('translate-y-0', 'opacity-100');
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0', 'pointer-events-none');
            toast.classList.remove('translate-y-0', 'opacity-100');
        }, 2800);
    }

    function copyResi(code) {
        navigator.clipboard.writeText(code).then(() => {
            const label = document.getElementById('copy-label');
            const icon = document.getElementById('copy-icon');
            if (label) label.textContent = 'Tersalin!';
            if (icon) icon.textContent = 'done';
            showToast('Nomor resi ' + code + ' berhasil disalin ke papan klip');
            setTimeout(() => {
                if (label) label.textContent = 'Salin';
                if (icon) icon.textContent = 'content_copy';
            }, 2000);
        }).catch(() => {
            showToast('Gagal menyalin nomor resi');
        });
    }
</script>
@endpush
