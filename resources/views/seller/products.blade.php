@extends('layouts.seller')

@section('title', 'Manajemen Produk - Portal Penjual - Lokalku')

@section('content')
<div class="flex flex-col w-full">
    @php
        $totalProducts = $products->count();
        $activeProducts = $products->where('is_active', true)->count();
        $lowStockProducts = $products->filter(fn ($p) => (int) $p->stock > 0 && (int) $p->stock <= 5)->count();
        $outOfStockProducts = $products->filter(fn ($p) => (int) $p->stock === 0)->count();
        $categoryOptions = $products->map(fn ($p) => $p->category?->name)->filter()->unique()->sort()->values();
    @endphp

    <!-- Page Header & Actions -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-space-lg mb-space-xl">
        <div>
            <div class="flex items-center gap-2 mb-space-xs font-caption text-caption text-ink-muted">
                <span class="">Portal Penjual</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="">Produk &amp; Stok</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="font-bold text-primary">Daftar Produk</span>
            </div>
            <h1 class="font-display text-headline-lg font-bold text-on-surface tracking-tight">Daftar Produk</h1>
            <p class="font-body-md text-body-md text-on-surface-variant mt-1">
                Kelola inventaris barang etalase online {{ $store->name ?? 'toko Anda' }} dan sinkronisasikan dengan kasir fisik POS.
            </p>
        </div>
        <div class="flex items-center gap-space-sm flex-wrap">
            <a class="h-10 px-4 rounded-full bg-surface-card text-primary-container font-body-md text-body-md font-bold hover:bg-blue-subtle transition-all shadow-sm flex items-center gap-2" href="{{ route('seller.products') }}">
                <span class="material-symbols-outlined text-[18px]">refresh</span>
                <span class="">Muat Ulang Daftar</span>
            </a>
            <a class="h-10 px-5 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-body-md text-body-md font-bold transition-all shadow-sm flex items-center gap-2" href="{{ route('seller.products.create') }}">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span class="">Tambah Produk</span>
            </a>
        </div>
    </div>

    @if ($products->isEmpty())
        <!-- Empty State -->
        <div class="bg-surface-card rounded-xl border border-border-default p-6 shadow-sm">
            <x-not-found
                title="Belum Ada Produk"
                message="Tambahkan produk pertama untuk mulai berjualan."
                :actionUrl="route('seller.products.create')"
                actionLabel="Tambah Produk"
            />
        </div>
    @else
        <!-- KPI Metrics Cards (4 Metrik Mini Card) -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-space-md mb-space-xl">
            <!-- Total Produk -->
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-space-xs">
                    <span class="font-caption text-caption font-bold text-ink-muted uppercase tracking-wider">Total Produk</span>
                    <span class="w-8 h-8 rounded-full bg-blue-subtle text-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">inventory_2</span>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-price-lg font-bold text-on-surface">{{ $totalProducts }}</span>
                    <span class="font-code-mono text-code-mono text-ink-muted">SKU</span>
                </div>
                <span class="font-caption text-caption text-ink-muted mt-space-xs">Semua data terdaftar</span>
            </div>
            <!-- Produk Aktif -->
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-space-xs">
                    <span class="font-caption text-caption font-bold text-ink-muted uppercase tracking-wider">Produk Aktif</span>
                    <span class="w-8 h-8 rounded-full bg-tertiary-fixed/40 text-success-text flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">storefront</span>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-price-lg font-bold text-on-surface">{{ $activeProducts }}</span>
                    <span class="font-code-mono text-code-mono text-ink-muted">SKU</span>
                </div>
                <span class="font-caption text-caption text-success-text font-bold mt-space-xs">Tayang di Etalase</span>
            </div>
            <!-- Stok Menipis -->
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-space-xs">
                    <span class="font-caption text-caption font-bold text-ink-muted uppercase tracking-wider">Stok Menipis</span>
                    <span class="w-8 h-8 rounded-full bg-accent-yellow/20 text-on-accent-yellow flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px] text-accent-yellow">warning</span>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-price-lg font-bold text-on-surface">{{ $lowStockProducts }}</span>
                    <span class="font-code-mono text-code-mono text-ink-muted">SKU</span>
                    <span class="ml-auto px-2 py-0.5 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-caption font-bold">&le; 5 Unit</span>
                </div>
                <span class="font-caption text-caption text-on-surface-variant mt-space-xs">Perlu restok segera</span>
            </div>
            <!-- Stok Habis -->
            <div class="bg-surface-card rounded-xl p-space-lg shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-space-xs">
                    <span class="font-caption text-caption font-bold text-ink-muted uppercase tracking-wider">Stok Habis</span>
                    <span class="w-8 h-8 rounded-full bg-error-container text-secondary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">remove_shopping_cart</span>
                    </span>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="font-display text-price-lg font-bold text-on-surface">{{ $outOfStockProducts }}</span>
                    <span class="font-code-mono text-code-mono text-ink-muted">SKU</span>
                    <span class="ml-auto px-2 py-0.5 rounded-full bg-secondary-container text-on-secondary-container font-caption text-caption font-bold">0 Unit</span>
                </div>
                <span class="font-caption text-caption text-secondary font-bold mt-space-xs">Segera nonaktifkan</span>
            </div>
        </div>

        <!-- Toolbar Kontrol & Filter -->
        <div class="bg-surface-card rounded-xl p-space-lg shadow-sm mb-space-lg flex flex-col gap-space-md">
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-space-md">
                <!-- Search Input (44px) -->
                <div class="relative flex-1 max-w-md">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[20px] text-ink-muted">search</span>
                    <input class="w-full h-11 pl-10 pr-4 bg-canvas-background rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container focus:bg-surface-card transition-all placeholder:text-ink-muted" id="searchInput" oninput="applyFilters()" placeholder="Cari nama produk atau slug..." type="text">
                </div>
                <!-- Filters & Actions -->
                <div class="flex items-center gap-space-sm flex-wrap">
                    <!-- Category Filter -->
                    <div class="relative">
                        <select class="h-11 pl-3 pr-8 bg-canvas-background rounded-lg font-body-md text-body-md text-on-surface appearance-none focus:outline-none focus:ring-2 focus:ring-primary-container cursor-pointer font-medium" id="categoryFilter" onchange="applyFilters()">
                            <option value="all">Semua Kategori</option>
                            @foreach ($categoryOptions as $categoryName)
                                <option value="{{ $categoryName }}">{{ $categoryName }}</option>
                            @endforeach
                        </select>
                        <span class="material-symbols-outlined absolute right-2.5 top-1/2 -translate-y-1/2 text-[18px] text-ink-muted pointer-events-none">expand_more</span>
                    </div>
                    <!-- Segmented Status Pills -->
                    <div class="bg-canvas-background p-1 rounded-full flex items-center">
                        <button class="h-9 px-3.5 rounded-full font-caption text-caption font-bold transition-all bg-primary-container text-on-primary" id="status-btn-all" onclick="setStatusFilter('all')">Semua</button>
                        <button class="h-9 px-3.5 rounded-full font-caption text-caption font-medium transition-all text-on-surface-variant hover:text-on-surface" id="status-btn-active" onclick="setStatusFilter('active')">Aktif</button>
                        <button class="h-9 px-3.5 rounded-full font-caption text-caption font-medium transition-all text-on-surface-variant hover:text-on-surface" id="status-btn-inactive" onclick="setStatusFilter('inactive')">Nonaktif</button>
                    </div>
                    <!-- Stok Menipis Toggle Chip -->
                    <label class="h-11 px-3.5 rounded-full bg-canvas-background hover:bg-surface-container flex items-center gap-2 cursor-pointer select-none transition-colors">
                        <input class="w-4 h-4 rounded text-primary-container focus:ring-primary-container accent-primary-container cursor-pointer" id="stockAlertToggle" onchange="applyFilters()" type="checkbox">
                        <span class="font-body-md text-body-md font-medium text-on-surface flex items-center gap-1">
                            <span class="material-symbols-outlined text-[16px] text-accent-yellow">warning</span> Stok Menipis / Habis
                        </span>
                    </label>
                    <!-- Reset Button -->
                    <button class="h-11 px-3 rounded-full hover:bg-surface-container font-caption text-caption font-bold text-ink-muted hover:text-on-surface transition-colors flex items-center gap-1" onclick="resetFilters()" title="Reset semua filter">
                        <span class="material-symbols-outlined text-[16px]">restart_alt</span>
                        <span class="">Reset</span>
                    </button>
                </div>
            </div>
            <!-- Active Filter Feedback Bar -->
            <div class="hidden flex-wrap items-center gap-space-xs pt-space-xs" id="filterTagsBar">
                <span class="font-caption text-caption text-ink-muted">Filter aktif:</span>
                <span class="px-2.5 py-1 rounded-full bg-blue-subtle text-primary font-caption text-caption font-bold flex items-center gap-1" id="activeFilterChip">
                    <span id="activeFilterText" class="">Stok Menipis / Habis</span>
                    <button class="hover:text-secondary" onclick="resetFilters()"><span class="material-symbols-outlined text-[14px]">close</span></button>
                </span>
            </div>
        </div>

        <!-- Main Product Table Container -->
        <div class="bg-surface-card rounded-xl shadow-sm overflow-hidden flex flex-col">
            <!-- Desktop Table View -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-surface-container-low text-ink-muted font-caption text-caption uppercase tracking-wider font-bold">
                            <th class="py-3 px-4 min-w-[280px]">Produk &amp; Identitas</th>
                            <th class="py-3 px-4 w-32">Kategori</th>
                            <th class="py-3 px-4 w-36">Harga Jual</th>
                            <th class="py-3 px-4 w-36">Stok Fisik</th>
                            <th class="py-3 px-4 w-40 text-center">Etalase</th>
                            <th class="py-3 px-4 w-28 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="font-body-md text-body-md text-on-surface" id="productTableBody">
                        @foreach ($products as $product)
                            @php
                                $stock = (int) $product->stock;
                                $isActive = (bool) $product->is_active;
                            @endphp
                            <tr class="product-row hover:bg-surface-container-low transition-colors" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" data-category="{{ $product->category?->name }}" data-status="{{ $isActive ? 'active' : 'inactive' }}" data-stock="{{ $stock }}">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-space-md">
                                        @if ($product->image)
                                            <img class="w-12 h-12 rounded-lg object-cover bg-surface-container shrink-0" alt="{{ $product->name }}" src="{{ $product->image }}">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-surface-container shrink-0 flex items-center justify-center text-ink-muted">
                                                <span class="material-symbols-outlined text-[20px]">image</span>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <div class="font-headline-sm text-body-md font-bold text-on-surface truncate flex items-center gap-2">
                                                <a class="hover:text-primary hover:underline underline-offset-2" href="{{ route('products.show', $product) }}" target="_blank" rel="noopener">{{ $product->name }}</a>
                                                @unless ($isActive)
                                                    <span class="px-2 py-0.5 rounded-full bg-error-container text-secondary font-caption text-caption font-bold shrink-0">Nonaktif</span>
                                                @endunless
                                            </div>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="font-code-mono text-caption text-on-surface-variant truncate max-w-[180px]">{{ $product->slug }}</span>
                                                @if ($product->origin)
                                                    <span class="text-ink-muted">&bull;</span>
                                                    <span class="font-caption text-caption text-ink-muted truncate max-w-[120px]">{{ $product->origin }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    @if ($product->category?->name)
                                        <span class="px-2.5 py-1 rounded-full bg-surface-container font-caption text-caption font-medium text-on-surface">{{ $product->category->name }}</span>
                                    @else
                                        <span class="font-caption text-caption text-ink-muted">&mdash;</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-price-sm text-price-sm font-bold text-on-surface">
                                    Rp {{ number_format((float) $product->price, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4">
                                    @if ($stock === 0)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-error-container text-secondary font-caption text-caption font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                                            <span class="font-code-mono">{{ $stock }}</span> unit &bull; Stok Habis
                                        </span>
                                    @elseif ($stock <= 5)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-caption font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-on-accent-yellow"></span>
                                            <span class="font-code-mono">{{ $stock }}</span> unit &bull; Stok menipis: {{ $stock }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#EBF5D6] text-success-text font-caption text-caption font-bold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-success-text"></span>
                                            Stok <span class="font-code-mono">{{ $stock }}</span>
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('seller.products.toggle', $product) }}" method="POST" class="inline-flex">
                                        @csrf
                                        <label class="inline-flex items-center cursor-pointer select-none">
                                            <input class="sr-only peer" type="checkbox" name="toggle" onchange="this.form.submit()" @checked($isActive)>
                                            <div class="w-11 h-6 bg-surface-container peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-surface-card after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-surface-card after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-success-text relative"></div>
                                            <span class="ml-2 font-caption text-caption font-bold {{ $isActive ? 'text-success-text' : 'text-ink-muted' }}">{{ $isActive ? 'Nonaktifkan' : 'Aktifkan' }}</span>
                                        </label>
                                    </form>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <a class="w-8 h-8 rounded-full flex items-center justify-center text-on-surface-variant hover:bg-surface-container hover:text-primary transition-colors" href="{{ route('seller.products.edit', $product) }}" title="Ubah Produk">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Responsive Card List (<768px) -->
            <div class="md:hidden flex flex-col divide-y divide-border-default/50" id="productCardListMobile">
                @foreach ($products as $product)
                    @php
                        $stock = (int) $product->stock;
                        $isActive = (bool) $product->is_active;
                    @endphp
                    <div class="p-space-lg flex flex-col gap-space-sm product-card-mobile" data-name="{{ $product->name }}" data-slug="{{ $product->slug }}" data-category="{{ $product->category?->name }}" data-status="{{ $isActive ? 'active' : 'inactive' }}" data-stock="{{ $stock }}">
                        <div class="flex items-start gap-space-md">
                            @if ($product->image)
                                <img class="w-14 h-14 rounded-lg object-cover bg-surface-container shrink-0" alt="{{ $product->name }}" src="{{ $product->image }}">
                            @else
                                <div class="w-14 h-14 rounded-lg bg-surface-container shrink-0 flex items-center justify-center text-ink-muted">
                                    <span class="material-symbols-outlined text-[22px]">image</span>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-headline-sm text-body-md font-bold text-on-surface leading-tight">
                                        <a class="hover:text-primary" href="{{ route('products.show', $product) }}" target="_blank" rel="noopener">{{ $product->name }}</a>
                                        @unless ($isActive)
                                            <span class="ml-1 px-2 py-0.5 rounded-full bg-error-container text-secondary font-caption text-caption font-bold align-middle">Nonaktif</span>
                                        @endunless
                                    </h3>
                                    <span class="font-price-sm text-price-sm font-bold text-on-surface shrink-0">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="flex items-center gap-2 mt-1 font-caption text-caption text-ink-muted">
                                    <span class="font-code-mono">{{ $product->slug }}</span>
                                    @if ($product->category?->name)
                                        <span class="">&bull;</span>
                                        <span class="px-2 py-0.5 rounded-full bg-surface-container text-on-surface">{{ $product->category->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between pt-2">
                            @if ($stock === 0)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-error-container text-secondary font-caption text-caption font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-secondary"></span>
                                    Stok Habis
                                </span>
                            @elseif ($stock <= 5)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-caption font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-on-accent-yellow"></span>
                                    Stok menipis: <span class="font-code-mono">{{ $stock }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-[#EBF5D6] text-success-text font-caption text-caption font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-success-text"></span>
                                    Stok: <span class="font-code-mono">{{ $stock }}</span> unit
                                </span>
                            @endif
                            <div class="flex items-center gap-space-sm">
                                <span class="font-caption text-caption font-bold {{ $isActive ? 'text-success-text' : 'text-ink-muted' }}">{{ $isActive ? 'Etalase Aktif' : 'Etalase Nonaktif' }}</span>
                                <a class="w-9 h-9 rounded-full bg-surface-container flex items-center justify-center text-primary" href="{{ route('seller.products.edit', $product) }}" title="Ubah Produk">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Empty State View (Controlled via JS filters) -->
            <div class="hidden p-12 flex-col items-center justify-center text-center" id="emptyStateView">
                <div class="w-20 h-20 rounded-full bg-blue-subtle text-primary flex items-center justify-center mb-space-md">
                    <span class="material-symbols-outlined text-[36px]">inventory_2</span>
                </div>
                <h3 class="font-headline-md text-headline-md font-bold text-on-surface mb-space-xs">Belum Ada Produk Ditemukan</h3>
                <p class="font-body-md text-body-md text-on-surface-variant max-w-md mb-space-lg">
                    Tidak ada data produk yang sesuai dengan kriteria filter atau pencarian Anda. Coba ubah kata kunci atau hapus filter aktif.
                </p>
                <div class="flex items-center gap-space-sm">
                    <button class="h-10 px-5 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface font-body-md text-body-md font-medium transition-colors" onclick="resetFilters()">
                        Reset Filter Pencarian
                    </button>
                </div>
            </div>

            <!-- Table Footer -->
            <div class="px-space-lg py-3.5 bg-surface-card flex flex-col sm:flex-row items-center justify-between gap-space-md" id="tableFooter">
                <div class="font-caption text-caption text-ink-muted">
                    Menampilkan <span class="font-bold text-on-surface" id="showingCount">{{ $totalProducts }}</span> dari <span class="font-bold text-on-surface">{{ $totalProducts }}</span> produk
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    let currentStatus = 'all';

    function setStatusFilter(status) {
        currentStatus = status;
        ['all', 'active', 'inactive'].forEach(function (key) {
            const btn = document.getElementById('status-btn-' + key);
            if (!btn) return;
            if (key === status) {
                btn.classList.add('bg-primary-container', 'text-on-primary', 'font-bold');
                btn.classList.remove('text-on-surface-variant', 'font-medium');
            } else {
                btn.classList.remove('bg-primary-container', 'text-on-primary', 'font-bold');
                btn.classList.add('text-on-surface-variant', 'font-medium');
            }
        });
        applyFilters();
    }

    function applyFilters() {
        const query = (document.getElementById('searchInput')?.value || '').toLowerCase();
        const category = document.getElementById('categoryFilter')?.value || 'all';
        const stockAlert = document.getElementById('stockAlertToggle')?.checked || false;
        const rows = document.querySelectorAll('.product-row, .product-card-mobile');
        let visible = 0;
        let filterActive = false;

        rows.forEach(function (row) {
            const name = (row.dataset.name || '').toLowerCase();
            const slug = (row.dataset.slug || '').toLowerCase();
            const rowCategory = row.dataset.category || '';
            const status = row.dataset.status || 'active';
            const stock = parseInt(row.dataset.stock || '0', 10);

            let show = true;
            if (query && !name.includes(query) && !slug.includes(query)) show = false;
            if (category !== 'all' && rowCategory !== category) show = false;
            if (currentStatus !== 'all' && status !== currentStatus) show = false;
            if (stockAlert && stock > 5) {
                show = false;
                filterActive = true;
            }
            row.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        const emptyView = document.getElementById('emptyStateView');
        const table = document.querySelector('#productTableBody');
        const cardList = document.getElementById('productCardListMobile');
        const footer = document.getElementById('tableFooter');
        if (emptyView) emptyView.classList.toggle('hidden', visible > 0);
        if (table) table.parentElement.classList.toggle('hidden', visible === 0);
        if (cardList) cardList.classList.toggle('hidden', visible === 0);
        if (footer) footer.classList.toggle('hidden', visible === 0);

        const showingCount = document.getElementById('showingCount');
        if (showingCount) showingCount.textContent = visible;

        const filterTagsBar = document.getElementById('filterTagsBar');
        const activeFilterText = document.getElementById('activeFilterText');
        if (filterTagsBar && activeFilterText) {
            const showBar = stockAlert && filterActive;
            filterTagsBar.classList.toggle('hidden', !showBar);
            filterTagsBar.classList.toggle('flex', showBar);
            if (showBar) activeFilterText.textContent = 'Stok Menipis / Habis';
        }
    }

    function resetFilters() {
        const search = document.getElementById('searchInput');
        const category = document.getElementById('categoryFilter');
        const stockAlert = document.getElementById('stockAlertToggle');
        if (search) search.value = '';
        if (category) category.value = 'all';
        if (stockAlert) stockAlert.checked = false;
        setStatusFilter('all');
    }
</script>
@endpush
