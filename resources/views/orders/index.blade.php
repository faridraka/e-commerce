@extends('layouts.app')

@section('title', 'Pesanan Saya - Lokalku')

@section('content')
<div class="max-w-[1280px] w-full mx-auto px-4 py-6 md:py-8 space-y-6">
    <section class="max-w-[960px] w-full mx-auto px-0 sm:px-6 pt-2">
        <!-- Breadcrumb Navigation -->
        <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-caption font-caption text-ink-muted mb-3">
            <a class="hover:text-primary transition-colors flex items-center gap-1" href="{{ route('home') }}">
                <span class="material-symbols-outlined text-[16px]">home</span>
                Beranda
            </a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a class="hover:text-primary transition-colors" href="#">Akun Saya</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span aria-current="page" class="text-on-surface font-bold">Pesanan Saya</span>
        </nav>
        <!-- Page Header & Quick Search -->
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-6 border-b border-border-default">
            <div>
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight font-bold">Pesanan Saya</h1>
                <p class="font-body-md text-body-md text-ink-muted mt-1 max-w-xl">
                    Pantau status pengiriman dan riwayat transaksi belanja produk UMKM lokal terverifikasi Anda.
                </p>
            </div>
            <!-- Search Bar -->
            <div class="relative w-full md:w-80">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-ink-muted text-[20px]">search</span>
                <input class="w-full h-11 pl-10 pr-9 rounded-lg bg-surface-card border border-border-input font-body-md text-body-md text-on-surface placeholder:text-ink-muted focus:border-primary-container focus:ring-2 focus:ring-primary-container/20 focus:outline-none transition-all" id="order-search-input" placeholder="Cari ID pesanan / nama produk..." type="text">
                <button class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-ink-muted hover:text-on-surface" id="clear-search-btn" type="button">
                    <span class="material-symbols-outlined text-[18px]">cancel</span>
                </button>
            </div>
        </div>
        <!-- Status Filter Chips Horizontal Scrollable Bar -->
        <div class="w-full overflow-x-auto no-scrollbar py-4 -mx-4 px-4 sm:mx-0 sm:px-0">
            <div aria-label="Filter status pesanan" class="flex items-center gap-2 min-w-max" role="tablist">
                <button aria-selected="true" class="filter-pill h-9 px-4 rounded-full font-caption text-caption font-bold bg-primary-container text-on-primary shadow-sm transition-all inline-flex items-center gap-1.5" data-filter="all" role="tab" type="button">
                    <span>Semua</span>
                </button>
                <button aria-selected="false" class="filter-pill h-9 px-4 rounded-full font-caption text-caption font-medium bg-surface-card border border-border-default text-on-surface hover:bg-surface-container transition-all inline-flex items-center gap-1.5" data-filter="menunggu_pembayaran" role="tab" type="button">
                    <span>Menunggu Pembayaran</span>
                </button>
                <button aria-selected="false" class="filter-pill h-9 px-4 rounded-full font-caption text-caption font-medium bg-surface-card border border-border-default text-on-surface hover:bg-surface-container transition-all inline-flex items-center gap-1.5" data-filter="dikirim" role="tab" type="button">
                    <span>Dikirim</span>
                </button>
                <button aria-selected="false" class="filter-pill h-9 px-4 rounded-full font-caption text-caption font-medium bg-surface-card border border-border-default text-on-surface hover:bg-surface-container transition-all inline-flex items-center gap-1.5" data-filter="selesai" role="tab" type="button">
                    <span>Selesai</span>
                </button>
            </div>
        </div>
        <!-- Orders List -->
        <div class="space-y-4 pt-2" id="orders-list">
            @forelse ($orders as $order)
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
                    $firstItem = $order->items->first();
                    $otherItemsCount = max($order->items->count() - 1, 0);
                    $paymentStatus = $order->payments->first()?->status;
                @endphp
                <article class="order-card bg-surface-card border border-border-default rounded-xl p-5 hover:border-primary transition-all duration-200 hover:shadow-sm" data-status="{{ $order->status }}">
                    <header class="flex flex-wrap items-center justify-between gap-3 pb-3 border-b border-border-default">
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center gap-1.5 text-caption font-caption text-ink-muted">
                                <span class="material-symbols-outlined text-[18px] text-primary">storefront</span>
                                <span class="font-bold text-on-surface">Lokalku</span>
                                <span class="text-outline-variant">•</span>
                                <span>{{ $order->created_at->translatedFormat('d M Y') }}</span>
                            </div>
                            <div class="flex items-center gap-1 bg-surface-container px-2 py-0.5 rounded text-caption font-code-mono text-code-mono text-on-surface">
                                <span>{{ $order->order_code }}</span>
                                <button class="text-ink-muted hover:text-primary transition-colors flex items-center" onclick="copyToClipboard('{{ $order->order_code }}')" title="Salin No. Pesanan" type="button">
                                    <span class="material-symbols-outlined text-[15px]">content_copy</span>
                                </button>
                            </div>
                        </div>
                        <!-- Status Badge -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-caption font-caption font-bold {{ $statusClass }}">{{ $statusLabel }}</span>
                    </header>

                    @if ($order->status === 'menunggu_pembayaran')
                        <!-- Urgency Alert Bar -->
                        <div class="mt-3 px-3.5 py-2 rounded-lg bg-error-container/40 border border-error/20 flex items-center justify-between flex-wrap gap-2 text-caption font-caption">
                            <div class="flex items-center gap-1.5 text-secondary font-bold">
                                <span class="material-symbols-outlined text-[18px] text-secondary">timer</span>
                                <span>Selesaikan pembayaran sebelum batas waktu agar pesanan tidak dibatalkan otomatis.</span>
                            </div>
                        </div>
                    @endif

                    @if ($order->status === 'dikirim' && $order->shipping_tracking_number)
                        <!-- Tracking Banner -->
                        <div class="mt-3 px-3.5 py-2.5 rounded-lg bg-surface-container flex items-center justify-between flex-wrap gap-2 text-caption font-caption">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-[18px] text-primary">local_shipping</span>
                                <span class="text-ink-muted">No. Resi:</span>
                                <span class="font-code-mono font-bold text-on-surface bg-surface-card px-2 py-0.5 rounded border border-border-default">{{ $order->shipping_tracking_number }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Item Details -->
                    <a class="py-4 flex flex-col sm:flex-row gap-4 items-start sm:items-center justify-between group" href="{{ route('orders.show', $order->order_code) }}">
                        <div class="flex gap-3.5 items-start">
                            <img alt="{{ $firstItem?->product_name ?? 'Produk' }}" class="w-18 h-18 sm:w-20 sm:h-20 w-[76px] h-[76px] rounded-lg object-cover bg-surface-container shrink-0 border border-border-default" src="{{ $firstItem?->product?->image ?? 'https://placehold.co/96x96?text=%3F' }}">
                            <div class="space-y-1">
                                <h2 class="font-headline-sm text-[15px] leading-snug font-bold text-on-surface line-clamp-1 group-hover:text-primary transition-colors">
                                    {{ $firstItem?->product_name ?? 'Produk' }}
                                </h2>
                                <p class="font-body-md text-caption text-ink-muted">{{ $firstItem?->quantity ?? 0 }} x Rp {{ number_format((float) ($firstItem?->price ?? 0), 0, ',', '.') }}</p>
                                @if ($otherItemsCount > 0)
                                    <span class="text-caption font-caption font-bold text-primary inline-flex items-center gap-0.5">
                                        + dan {{ $otherItemsCount }} produk lainnya
                                        <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="sm:text-right shrink-0 pt-2 sm:pt-0 border-t sm:border-0 border-border-default w-full sm:w-auto">
                            <span class="font-caption text-caption text-ink-muted block">Total Belanja</span>
                            <span class="font-price-lg text-headline-sm sm:text-price-lg font-bold text-on-surface font-headline-md">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                        </div>
                    </a>
                    <!-- Footer Actions -->
                    <footer class="pt-3 border-t border-border-default flex flex-wrap items-center justify-between gap-3">
                        @if ($order->status === 'menunggu_pembayaran' && $paymentStatus === 'pending')
                            <div class="flex items-center gap-1.5 text-caption font-caption text-secondary">
                                <span class="material-symbols-outlined text-[18px]">schedule</span>
                                <span>Menunggu pembayaran via Midtrans</span>
                            </div>
                        @elseif ($paymentStatus === 'paid')
                            <div class="flex items-center gap-1.5 text-caption font-caption text-success-text">
                                <span class="material-symbols-outlined text-[18px]">check_circle</span>
                                <span>Pembayaran diverifikasi otomatis via QRIS Lokalku</span>
                            </div>
                        @elseif (in_array($paymentStatus, ['failed', 'expired']))
                            <div class="flex items-center gap-1.5 text-caption font-caption text-ink-muted">
                                <span class="material-symbols-outlined text-[18px]">info</span>
                                <span>Pembayaran tidak diselesaikan. Pesanan otomatis dibatalkan.</span>
                            </div>
                        @else
                            <div class="text-caption font-caption text-ink-muted flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">schedule</span>
                                <span>Dipesan pada {{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <a class="h-10 px-4 rounded-full border border-border-default font-caption text-caption font-medium text-on-surface hover:bg-surface-container transition-all inline-flex items-center" href="{{ route('orders.show', $order->order_code) }}">
                                Detail Transaksi
                            </a>
                            @if ($order->status === 'menunggu_pembayaran')
                                <a class="h-10 px-5 rounded-full bg-primary font-caption text-caption font-bold text-on-primary hover:bg-blue-hover transition-all shadow-sm inline-flex items-center gap-1.5" href="{{ route('payment.show', $order->order_code) }}">
                                    <span class="material-symbols-outlined text-[18px]">payment</span>
                                    <span>Lanjutkan Pembayaran</span>
                                </a>
                            @endif
                        </div>
                    </footer>
                </article>
            @empty
                <div class="bg-surface-card rounded-xl border border-border-default p-6 shadow-sm">
                    <x-not-found title="Belum Ada Pesanan" message="Kamu belum memiliki pesanan. Mulai belanja dan riwayat pesanan akan tampil di sini." actionUrl="{{ route('products.index') }}" actionLabel="Mulai Belanja" />
                </div>
            @endforelse
        </div>
        <!-- Search No Result State (Hidden by default) -->
        <div class="hidden flex-col items-center justify-center text-center py-12 px-4 bg-surface-card border border-border-default rounded-xl mt-4" id="search-empty-view">
            <span class="material-symbols-outlined text-ink-muted text-[40px] mb-2">search_off</span>
            <h4 class="font-headline-sm text-headline-sm font-bold text-on-surface">Pesanan Tidak Ditemukan</h4>
            <p class="font-body-md text-body-md text-ink-muted mt-1" id="search-empty-query">
                Tidak ditemukan transaksi dengan kata kunci tersebut.
            </p>
            <button class="mt-4 text-caption font-caption font-bold text-primary hover:underline" onclick="resetSearch()" type="button">
                Reset Pencarian
            </button>
        </div>
        <!-- Feedback Toast for Copy Action -->
        <div class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-inverse-surface text-inverse-on-surface px-4 py-2.5 rounded-full font-caption text-caption font-bold shadow-lg transition-all duration-300 opacity-0 pointer-events-none z-50 flex items-center gap-2" id="toast-notification">
            <span class="material-symbols-outlined text-tertiary-fixed text-[18px]">check_circle</span>
            <span id="toast-text">Teks berhasil disalin!</span>
        </div>
        <!-- Pagination -->
        @if ($orders->lastPage() > 1)
            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="font-caption text-caption text-ink-muted">
                    Menampilkan {{ $orders->firstItem() }} - {{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan
                </p>
            </div>
            <div class="pt-2">
                {{ $orders->links() }}
            </div>
        @else
            <div class="pt-4">
                <p class="font-caption text-caption text-ink-muted text-center">
                    @if ($orders->total() > 0)
                        Menampilkan {{ $orders->firstItem() }} - {{ $orders->lastItem() }} dari {{ $orders->total() }} pesanan
                    @endif
                </p>
            </div>
        @endif
    </section>
</div>
@push('scripts')
<script>
    // Filter tab status (visual) + pencarian klien-side pada halaman aktif
    let currentActiveFilter = 'all';

    function filterByStatus(status, buttonElement) {
        currentActiveFilter = status;

        document.querySelectorAll('.filter-pill').forEach(btn => {
            btn.classList.remove('bg-primary-container', 'text-on-primary', 'font-bold', 'shadow-sm');
            btn.classList.add('bg-surface-card', 'border', 'border-border-default', 'text-on-surface', 'font-medium');
            btn.setAttribute('aria-selected', 'false');
        });

        if (buttonElement) {
            buttonElement.classList.add('bg-primary-container', 'text-on-primary', 'font-bold', 'shadow-sm');
            buttonElement.classList.remove('bg-surface-card', 'border', 'border-border-default', 'text-on-surface', 'font-medium');
            buttonElement.setAttribute('aria-selected', 'true');
        }

        applyFiltering();
    }

    function applyFiltering() {
        const searchQuery = document.getElementById('order-search-input').value.toLowerCase().trim();
        const orderCards = document.querySelectorAll('.order-card');
        const searchEmptyState = document.getElementById('search-empty-view');
        const ordersList = document.getElementById('orders-list');

        let visibleCount = 0;

        orderCards.forEach(card => {
            const cardStatus = card.getAttribute('data-status');
            const cardText = card.innerText.toLowerCase();

            const matchesStatus = (currentActiveFilter === 'all') || (cardStatus === currentActiveFilter);
            const matchesSearch = searchQuery === '' || cardText.includes(searchQuery);

            if (matchesStatus && matchesSearch) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (visibleCount === 0 && searchQuery !== '') {
            searchEmptyState.classList.remove('hidden');
            searchEmptyState.classList.add('flex');
            document.getElementById('search-empty-query').innerText = 'Tidak ditemukan transaksi dengan kata kunci "' + searchQuery + '"';
            ordersList.classList.add('hidden');
        } else {
            searchEmptyState.classList.add('hidden');
            searchEmptyState.classList.remove('flex');
            ordersList.classList.remove('hidden');
        }
    }

    const searchInput = document.getElementById('order-search-input');
    const clearBtn = document.getElementById('clear-search-btn');

    searchInput.addEventListener('input', function (e) {
        clearBtn.classList.toggle('hidden', e.target.value.trim().length === 0);
        applyFiltering();
    });

    clearBtn.addEventListener('click', function () {
        resetSearch();
    });

    function resetSearch() {
        searchInput.value = '';
        clearBtn.classList.add('hidden');
        applyFiltering();
        searchInput.focus();
    }

    document.querySelectorAll('.filter-pill').forEach(btn => {
        btn.addEventListener('click', () => filterByStatus(btn.dataset.filter, btn));
    });

    // Salin nomor pesanan ke clipboard + toast
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('"' + text + '" berhasil disalin ke clipboard');
        }).catch(() => {
            showToast('Gagal menyalin teks');
        });
    }

    function showToast(msg) {
        const toast = document.getElementById('toast-notification');
        const toastText = document.getElementById('toast-text');
        toastText.innerText = msg;
        toast.classList.remove('opacity-0', 'pointer-events-none');
        toast.classList.add('opacity-100');
        setTimeout(() => {
            toast.classList.add('opacity-0', 'pointer-events-none');
            toast.classList.remove('opacity-100');
        }, 2400);
    }
</script>
@endpush
