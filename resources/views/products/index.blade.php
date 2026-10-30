@extends('layouts.app')

@section('title', $q !== '' ? "Pencarian \"{$q}\" - Lokalku" : 'Katalog Produk - Lokalku')

@section('content')
<div class="flex flex-col w-full">
    <!-- Sub-bar breadcrumb -->
    <div class="w-full bg-surface-card border-b border-border-default shadow-[0_1px_3px_rgba(25,25,25,0.04)]">
        <div class="max-w-[1280px] mx-auto px-4 py-2.5 flex items-center justify-between text-caption font-caption text-ink-muted">
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 flex-wrap">
                <a class="hover:text-primary transition-colors text-ink-muted flex items-center gap-1" href="{{ route('home') }}">
                    <span class="material-symbols-outlined text-[15px]">home</span>
                    Beranda
                </a>
                <span class="material-symbols-outlined text-[13px] text-outline-variant">chevron_right</span>
                <a class="hover:text-primary transition-colors text-ink-muted" href="{{ route('products.index') }}">Katalog</a>
                @if ($q !== '')
                    <span class="material-symbols-outlined text-[13px] text-outline-variant">chevron_right</span>
                    <span class="text-on-surface font-semibold flex items-center gap-1">Pencarian: &ldquo;{{ $q }}&rdquo;</span>
                @elseif ($kategori !== '')
                    <span class="material-symbols-outlined text-[13px] text-outline-variant">chevron_right</span>
                    <span class="text-on-surface font-semibold">{{ ucfirst($kategori) }}</span>
                @endif
            </nav>
            <div class="hidden sm:flex items-center gap-4 text-caption font-caption">
                <span class="inline-flex items-center gap-1 text-tertiary-container bg-surface-container px-2 py-0.5 rounded-full font-medium">
                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary-container animate-pulse"></span>
                    Katalog Terverifikasi Kurasi Lokal
                </span>
            </div>
        </div>
    </div>

    <div class="max-w-[1280px] w-full mx-auto px-4 py-6">
        <!-- Filter form (sidebar desktop) -->
        <form method="GET" action="{{ route('products.index') }}" id="filter-form">
            <input type="hidden" name="q" value="{{ $q }}">

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                <!-- Sidebar filter -->
                <aside class="hidden md:block md:col-span-3 w-full bg-surface-card rounded-xl border border-border-default p-4 shadow-[0_1px_3px_rgba(25,25,25,0.06)] sticky top-16">
                    <div class="flex items-center justify-between pb-3.5 border-b border-border-default">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary text-[20px]">tune</span>
                            <h2 class="font-headline-sm text-headline-sm font-bold text-on-surface">Filter Produk</h2>
                        </div>
                        <a class="text-caption font-caption text-secondary hover:text-red-graphic font-bold hover:underline transition-colors" href="{{ route('products.index') }}">Reset</a>
                    </div>

                    <div class="divide-y divide-border-default text-body-md font-body-md">
                        <!-- Kategori -->
                        <div class="py-4">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-bold text-on-surface text-[14px] uppercase tracking-wider text-caption font-caption">Kategori</span>
                                <span class="material-symbols-outlined text-[16px] text-ink-muted">expand_less</span>
                            </div>
                            <div class="space-y-2.5">
                                @foreach ($navCategories as $navCategory)
                                    <label class="flex items-center justify-between cursor-pointer text-body-md font-body-md select-none rounded-lg px-2 py-1 -mx-2 {{ $kategori === $navCategory->slug ? 'text-primary font-bold bg-blue-subtle/50' : 'text-on-surface-variant hover:text-on-surface' }}">
                                        <span class="flex items-center gap-2.5">
                                            <input class="w-4 h-4 rounded border-border-input text-primary-container focus:ring-0 cursor-pointer" type="checkbox" name="kategori_checkbox" value="{{ $navCategory->slug }}" @checked($kategori === $navCategory->slug) onchange="window.location = '{{ $kategori === $navCategory->slug ? route('products.index', array_filter(['q' => $q])) : route('products.index', array_filter(['q' => $q, 'kategori' => $navCategory->slug])) }}'">
                                            {{ $navCategory->name }}
                                        </span>
                                        <span class="text-caption font-caption {{ $kategori === $navCategory->slug ? 'text-primary bg-blue-subtle font-bold' : 'text-ink-muted bg-surface-container' }} px-1.5 py-0.5 rounded">{{ $navCategory->products_count ?? '' }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Ketersediaan -->
                        <div class="py-4">
                            <span class="block font-bold text-on-surface text-[14px] uppercase tracking-wider text-caption font-caption mb-3">Ketersediaan</span>
                            <label class="flex items-center justify-between cursor-pointer p-2 -mx-2 rounded-lg bg-surface-container-low hover:bg-surface-container transition-colors">
                                <span class="text-on-surface font-medium text-body-md font-body-md">Hanya yang tersedia</span>
                                <input name="tersedia" value="1" @checked($tersedia) class="w-4 h-4 rounded text-primary focus:ring-0 accent-[#0064d2] cursor-pointer" type="checkbox" onchange="document.getElementById('filter-form').submit()">
                            </label>
                        </div>
                    </div>
                </aside>

                <!-- Konten utama -->
                <section class="col-span-1 md:col-span-9 space-y-4">
                    <!-- Header + sort -->
                    <div class="bg-surface-card rounded-xl border border-border-default p-4 shadow-[0_1px_3px_rgba(25,25,25,0.06)]">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-border-default">
                            <div>
                                <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface tracking-tight">
                                    {{ number_format($products->total(), 0, ',', '.') }} produk @if ($q !== '')untuk &ldquo;<span class="text-primary">{{ $q }}</span>&rdquo;@endif
                                </h1>
                                <p class="font-body-md text-body-md text-ink-muted mt-0.5">
                                    @if ($q !== '')
                                        Menampilkan hasil kurasi sesuai kata kunci &ldquo;{{ $q }}&rdquo; dari sentra UMKM nusantara
                                    @elseif ($kategori !== '')
                                        Menampilkan produk pada kategori {{ ucfirst($kategori) }}
                                    @else
                                        Seluruh produk terkurasi dari pelaku UMKM nusantara
                                    @endif
                                </p>
                            </div>
                            <div class="flex items-center gap-3 shrink-0">
                                <div class="flex items-center gap-2">
                                    <span class="font-caption text-caption text-ink-muted hidden lg:inline">Urutan:</span>
                                    <div class="relative">
                                        <select name="urut" onchange="document.getElementById('filter-form').submit()" class="h-9 pl-3 pr-8 rounded-full border border-border-default bg-surface-card text-body-md font-body-md font-medium text-on-surface cursor-pointer focus:outline-none focus:border-primary-container appearance-none shadow-sm">
                                            <option value="terbaru" @selected($urut === 'terbaru')>Terbaru</option>
                                            <option value="termurah" @selected($urut === 'termurah')>Harga Terendah</option>
                                            <option value="termahal" @selected($urut === 'termahal')>Harga Tertinggi</option>
                                        </select>
                                        <span class="material-symbols-outlined text-[18px] text-ink-muted absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none">expand_more</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Filter aktif -->
                        @if ($q !== '' || $kategori !== '' || $tersedia)
                            <div class="pt-3 flex flex-wrap items-center gap-2">
                                <span class="text-caption font-caption text-ink-muted mr-1 font-medium">Filter Aktif:</span>
                                @if ($q !== '')
                                    <span class="inline-flex items-center gap-1.5 h-7 pl-3 pr-2 rounded-full bg-blue-subtle text-primary border border-primary-container/30 text-caption font-caption font-bold">
                                        &ldquo;{{ $q }}&rdquo;
                                        <a class="hover:bg-primary-container hover:text-on-primary rounded-full w-4 h-4 flex items-center justify-center transition-colors" href="{{ route('products.index', array_filter(['kategori' => $kategori, 'urut' => $urut, 'tersedia' => $tersedia ? 1 : null])) }}" title="Hapus pencarian">
                                            <span class="material-symbols-outlined text-[12px]">close</span>
                                        </a>
                                    </span>
                                @endif
                                @if ($kategori !== '')
                                    <span class="inline-flex items-center gap-1.5 h-7 pl-3 pr-2 rounded-full bg-blue-subtle text-primary border border-primary-container/30 text-caption font-caption font-bold">
                                        {{ ucfirst($kategori) }}
                                        <a class="hover:bg-primary-container hover:text-on-primary rounded-full w-4 h-4 flex items-center justify-center transition-colors" href="{{ route('products.index', array_filter(['q' => $q, 'urut' => $urut, 'tersedia' => $tersedia ? 1 : null])) }}" title="Hapus kategori">
                                            <span class="material-symbols-outlined text-[12px]">close</span>
                                        </a>
                                    </span>
                                @endif
                                @if ($tersedia)
                                    <span class="inline-flex items-center gap-1.5 h-7 pl-3 pr-2 rounded-full bg-blue-subtle text-primary border border-primary-container/30 text-caption font-caption font-bold">
                                        Tersedia
                                        <a class="hover:bg-primary-container hover:text-on-primary rounded-full w-4 h-4 flex items-center justify-center transition-colors" href="{{ route('products.index', array_filter(['q' => $q, 'kategori' => $kategori, 'urut' => $urut])) }}" title="Hapus ketersediaan">
                                            <span class="material-symbols-outlined text-[12px]">close</span>
                                        </a>
                                    </span>
                                @endif
                                <a class="text-caption font-caption text-secondary hover:text-red-graphic font-bold ml-2 underline transition-colors" href="{{ route('products.index') }}">Hapus Semua Filter</a>
                            </div>
                        @endif
                    </div>

                    <!-- Grid produk -->
                    @if ($products->isEmpty())
                        <!-- Komponen tidak ditemukan -->
                        <div class="bg-surface-card rounded-xl border border-border-default p-6 shadow-sm">
                            <x-not-found
                                title="Produk Tidak Ditemukan"
                                message="Kami tidak menemukan produk @if($q !== '')&ldquo;{{ $q }}&rdquo;@else dengan kriteria filter yang Anda tentukan @endif. Coba periksa ejaan, gunakan kata kunci lain, atau hapus filter aktif."
                                actionUrl="{{ route('products.index') }}"
                                actionLabel="Hapus Semua Filter" />
                        </div>
                    @else
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                            @foreach ($products as $product)
                                <x-product-card :product="$product" />
                            @endforeach
                        </div>
                    @endif

                    <!-- Pagination -->
                    @if ($products->lastPage() > 1)
                        <div class="pt-6 pb-2 flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-border-default">
                            <p class="font-caption text-caption text-ink-muted order-2 sm:order-1">
                                Menampilkan <span class="font-bold text-on-surface">{{ $products->firstItem() }} - {{ $products->lastItem() }}</span> dari <span class="font-bold text-on-surface">{{ number_format($products->total(), 0, ',', '.') }}</span> produk
                            </p>
                            <nav aria-label="Pagination" class="order-1 sm:order-2">
                                {{ $products->links() }}
                            </nav>
                        </div>
                    @endif
                </section>
            </div>
        </form>
    </div>
</div>
@endsection
