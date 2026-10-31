@extends('layouts.seller')

@section('title', ($isEditing ? 'Ubah Produk' : 'Tambah Produk') . ' - Portal Penjual - Lokalku')

@section('content')
<div class="flex flex-col w-full">
    <!-- Breadcrumb & Page Header -->
    <div class="flex flex-col gap-3 mb-6">
        <!-- Breadcrumb -->
        <nav class="flex items-center gap-2 font-caption text-caption text-ink-muted">
            <a class="hover:text-primary transition-colors" href="{{ route('seller.dashboard') }}">Portal Penjual</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <a class="hover:text-primary transition-colors" href="{{ route('seller.products') }}">Produk &amp; Stok</a>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="font-bold text-on-surface">{{ $isEditing ? 'Ubah Produk' : 'Tambah Produk' }}</span>
        </nav>
        <!-- Top Header Row -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-3">
                    <a class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-card border border-border-default hover:bg-surface-container-high text-on-surface font-caption text-caption font-medium transition-colors" href="{{ route('seller.products') }}">
                        <span class="material-symbols-outlined text-[16px]">arrow_back</span>
                        <span class="">Kembali ke Daftar Produk</span>
                    </a>
                    @if ($isEditing)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-canvas-background border border-border-default font-code-mono text-caption text-on-surface">
                            SKU: {{ $product->slug }}
                        </span>
                    @endif
                    @if ($isEditing)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full {{ $product->is_active ? 'bg-tertiary-fixed/30 text-tertiary' : 'bg-error-container text-secondary' }} font-caption text-caption font-bold">
                            <span class="w-1.5 h-1.5 rounded-full {{ $product->is_active ? 'bg-tertiary' : 'bg-secondary' }}"></span>
                            {{ $product->is_active ? 'Tayang di Etalase' : 'Nonaktif di Etalase' }}
                        </span>
                    @endif
                </div>
                <h1 class="font-headline-lg text-headline-lg text-on-surface tracking-tight">{{ $isEditing ? 'Ubah Data Produk' : 'Tambah Produk Baru' }}</h1>
                <p class="font-body-md text-body-md text-ink-muted">{{ $isEditing ? 'Perbarui data produk, harga ritel, stok fisik kasir POS, dan media etalase online toko Anda.' : 'Lengkapi data produk baru agar langsung tayang di etalase online toko Anda.' }}</p>
            </div>
            <div class="flex items-center gap-3 self-start lg:self-center">
                @if ($isEditing && $product->is_active)
                    <a class="h-10 px-4 rounded-full border border-primary-container bg-surface-card text-primary-container hover:bg-blue-subtle font-body-md text-body-md font-medium transition-colors inline-flex items-center gap-1.5 shadow-sm" href="{{ route('products.show', $product) }}" target="_blank" rel="noopener">
                        <span class="">Pratinjau Halaman Pembeli</span>
                        <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Form Layout Grid (8 cols + 4 cols) -->
    @if ($isEditing)
        <form class="pb-28" action="{{ route('seller.products.update', $product) }}" method="POST">
            @method('PUT')
            @csrf
    @else
        <form class="pb-28" action="{{ route('seller.products.store') }}" method="POST">
            @csrf
    @endif
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- LEFT COLUMN (8 Columns) -->
            <div class="lg:col-span-8 flex flex-col gap-6">
                <!-- Card 1: Informasi Produk -->
                <div class="bg-surface-card rounded-xl p-6 border border-border-default shadow-sm">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-border-default">
                        <div>
                            <h2 class="font-headline-md text-headline-sm text-on-surface font-bold">Informasi Utama &amp; Deskripsi</h2>
                            <p class="font-caption text-caption text-ink-muted mt-0.5">Identitas produk yang muncul pada etalase online, pencarian, dan detail halaman pembeli.</p>
                        </div>
                        <span class="material-symbols-outlined text-ink-muted text-[24px]">assignment</span>
                    </div>
                    <div class="flex flex-col gap-5">
                        <!-- Nama Produk -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="font-body-md text-body-md font-bold text-on-surface" for="inputTitle">
                                    Nama Produk <span class="text-secondary">*</span>
                                </label>
                                <span class="font-caption text-caption text-ink-muted" id="titleCount">{{ strlen(old('name', $product->name ?? '')) }} / 100</span>
                            </div>
                            <input class="w-full h-11 px-3.5 {{ $errors->has('name') ? 'bg-error-container' : 'bg-surface-card' }} border border-border-input rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 transition-all placeholder:text-ink-muted" id="inputTitle" name="name" maxlength="100" placeholder="Contoh: Kopi Robusta Dampit 250g Kemasan Pouch" type="text" value="{{ old('name', $product->name) }}">
                            @error('name')
                                <p class="font-caption text-caption text-secondary mt-1.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">error</span>
                                    <span class="">{{ $message }}</span>
                                </p>
                            @enderror
                            <p class="font-caption text-caption text-ink-muted mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">info</span>
                                <span class="">Gunakan nama yang ringkas, spesifik, dan memuat berat atau varian kemasan.</span>
                            </p>
                        </div>
                        <!-- Kategori Produk -->
                        <div>
                            <label class="block font-body-md text-body-md font-bold text-on-surface mb-1.5" for="selectCategory">
                                Kategori Produk <span class="text-secondary">*</span>
                            </label>
                            <div class="relative">
                                <select class="w-full h-11 pl-3.5 pr-10 {{ $errors->has('category_id') ? 'bg-error-container' : 'bg-surface-card' }} border border-border-input rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 transition-all appearance-none cursor-pointer" id="selectCategory" name="category_id">
                                    <option value="">Pilih Kategori</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" @selected(old('category_id', $product->category_id) == $category->id)>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-ink-muted text-[20px]">expand_more</span>
                            </div>
                            @error('category_id')
                                <p class="font-caption text-caption text-secondary mt-1.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">error</span>
                                    <span class="">{{ $message }}</span>
                                </p>
                            @enderror
                            <p class="font-caption text-caption text-ink-muted mt-1.5">Kategori menentukan pengelompokan produk pada etalase online toko Anda.</p>
                        </div>
                        <!-- Origin / Asal Produk -->
                        <div>
                            <label class="block font-body-md text-body-md font-bold text-on-surface mb-1.5" for="inputOrigin">
                                Asal Produk <span class="text-ink-muted font-normal">(Opsional)</span>
                            </label>
                            <div class="relative">
                                <input class="w-full h-11 pl-9 pr-3.5 {{ $errors->has('origin') ? 'bg-error-container' : 'bg-surface-card' }} border border-border-input rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 transition-all placeholder:text-ink-muted" id="inputOrigin" name="origin" type="text" placeholder="Contoh: Dampit, Malang, Jawa Timur" value="{{ old('origin', $product->origin) }}">
                                <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-ink-muted text-[18px]">place</span>
                            </div>
                            @error('origin')
                                <p class="font-caption text-caption text-secondary mt-1.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">error</span>
                                    <span class="">{{ $message }}</span>
                                </p>
                            @enderror
                            <p class="font-caption text-caption text-ink-muted mt-1.5">Asal daerah produksi menambah kepercayaan pembeli produk UMKM lokal.</p>
                        </div>
                        <!-- Deskripsi Produk -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="font-body-md text-body-md font-bold text-on-surface" for="inputDesc">
                                    Deskripsi Lengkap <span class="text-secondary">*</span>
                                </label>
                                <span class="font-caption text-caption text-ink-muted" id="descCount">{{ strlen(old('description', $product->description ?? '')) }} / 1000 karakter</span>
                            </div>
                            <textarea class="w-full p-3.5 {{ $errors->has('description') ? 'bg-error-container' : 'bg-surface-card' }} border border-border-input rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 transition-all placeholder:text-ink-muted resize-y" id="inputDesc" name="description" maxlength="1000" placeholder="Jelaskan keunggulan rasa, komposisi bahan, daya simpan, dan saran penyajian..." rows="6">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="font-caption text-caption text-secondary mt-1.5 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">error</span>
                                    <span class="">{{ $message }}</span>
                                </p>
                            @enderror
                            <div class="flex items-center justify-between mt-1.5 font-caption text-caption text-ink-muted">
                                <div class="flex items-center gap-3">
                                    <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">description</span> Jelaskan dengan detail</span>
                                    <span class="">&bull;</span>
                                    <span class="">Saran: Sertakan berat netto dan tanggal kedaluwarsa</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Card 2: Harga & Inventaris -->
                <div class="bg-surface-card rounded-xl p-6 border border-border-default shadow-sm">
                    <div class="flex items-center justify-between pb-4 mb-5 border-b border-border-default">
                        <div>
                            <h2 class="font-headline-md text-headline-sm text-on-surface font-bold">Harga &amp; Inventaris</h2>
                            <p class="font-caption text-caption text-ink-muted mt-0.5">Harga jual etalase online dan ketersediaan stok produk.</p>
                        </div>
                        <span class="material-symbols-outlined text-ink-muted text-[24px]">point_of_sale</span>
                    </div>
                    <div class="flex flex-col gap-5">
                        <!-- Row: Harga Jual & Harga Sebelum Diskon -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Harga Jual -->
                            <div>
                                <label class="block font-body-md text-body-md font-bold text-on-surface mb-1.5" for="inputPrice">
                                    Harga Jual Satuan <span class="text-secondary">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-code-mono text-body-md font-bold text-ink-muted select-none">Rp</span>
                                    <input class="w-full h-11 pl-11 pr-3.5 {{ $errors->has('price') ? 'bg-error-container' : 'bg-surface-card' }} border border-border-input rounded-lg font-code-mono text-price-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 transition-all" id="inputPrice" name="price" type="number" min="0" step="1" placeholder="0" value="{{ old('price', $product->price) }}">
                                </div>
                                @error('price')
                                    <p class="font-caption text-caption text-secondary mt-1.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">error</span>
                                        <span class="">{{ $message }}</span>
                                    </p>
                                @enderror
                                <p class="font-caption text-caption text-ink-muted mt-1.5">Harga final yang dibayar oleh pembeli retail.</p>
                            </div>
                            <!-- Harga Sebelum Diskon -->
                            <div>
                                <label class="block font-body-md text-body-md font-bold text-on-surface mb-1.5" for="inputOriginalPrice">
                                    Harga Sebelum Diskon <span class="text-ink-muted font-normal">(Opsional)</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 font-code-mono text-body-md font-bold text-ink-muted select-none">Rp</span>
                                    <input class="w-full h-11 pl-11 pr-3.5 {{ $errors->has('original_price') ? 'bg-error-container' : 'bg-surface-card' }} border border-border-input rounded-lg font-code-mono text-price-sm text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 transition-all" id="inputOriginalPrice" name="original_price" type="number" min="0" step="1" placeholder="0" value="{{ old('original_price', $product->original_price) }}">
                                </div>
                                @error('original_price')
                                    <p class="font-caption text-caption text-secondary mt-1.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">error</span>
                                        <span class="">{{ $message }}</span>
                                    </p>
                                @enderror
                                <p class="font-caption text-caption text-ink-muted mt-1.5">Kosongkan bila produk tidak sedang dipromosikan.</p>
                            </div>
                        </div>
                        <!-- Row: Stok Fisik -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Stok Tersedia -->
                            <div>
                                <label class="block font-body-md text-body-md font-bold text-on-surface mb-1.5" for="inputStock">
                                    Stok Fisik Tersedia <span class="text-secondary">*</span>
                                </label>
                                <div class="flex items-center">
                                    <button class="w-11 h-11 rounded-l-lg bg-surface-container border border-r-0 border-border-input flex items-center justify-center text-on-surface hover:bg-surface-container-high transition-colors" onclick="adjustStock(-1)" type="button">
                                        <span class="material-symbols-outlined text-[18px]">remove</span>
                                    </button>
                                    <input class="w-full h-11 text-center {{ $errors->has('stock') ? 'bg-error-container' : 'bg-surface-card' }} border-y border-border-input font-code-mono text-price-sm font-bold text-on-surface focus:outline-none focus:ring-0" id="inputStock" name="stock" type="number" min="0" step="1" value="{{ old('stock', $product->stock) }}">
                                    <button class="w-11 h-11 rounded-r-lg bg-surface-container border border-l-0 border-border-input flex items-center justify-center text-on-surface hover:bg-surface-container-high transition-colors" onclick="adjustStock(1)" type="button">
                                        <span class="material-symbols-outlined text-[18px]">add</span>
                                    </button>
                                </div>
                                @error('stock')
                                    <p class="font-caption text-caption text-secondary mt-1.5 flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">error</span>
                                        <span class="">{{ $message }}</span>
                                    </p>
                                @enderror
                                @if ((int) old('stock', $product->stock) <= 5)
                                    <div class="mt-2 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-accent-yellow text-on-accent-yellow font-caption text-caption font-bold">
                                        <span class="material-symbols-outlined text-[14px]">warning</span>
                                        <span class="">Stok Menipis: sisa {{ old('stock', $product->stock) }} unit di etalase toko</span>
                                    </div>
                                @endif
                            </div>
                            <div></div>
                        </div>
                        <!-- Sync Notice Box -->
                        <div class="flex items-start gap-3 p-4 rounded-xl bg-blue-subtle/80 border border-primary-container/20">
                            <span class="material-symbols-outlined text-primary-container text-[22px] shrink-0 mt-0.5">sync_alt</span>
                            <div class="flex flex-col">
                                <span class="font-body-md text-body-md font-bold text-primary">Tayang Otomatis di Etalase</span>
                                <span class="font-caption text-caption text-on-surface-variant leading-relaxed">
                                    Setiap perubahan harga dan stok langsung tampil di etalase online {{ $store->name ?? 'toko Anda' }} begitu produk disimpan.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- RIGHT COLUMN (4 Columns) -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                <!-- Card 1: Media & Foto Produk -->
                <div class="bg-surface-card rounded-xl p-6 border border-border-default shadow-sm">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-border-default">
                        <div>
                            <h2 class="font-headline-md text-headline-sm text-on-surface font-bold">Foto Produk</h2>
                            <p class="font-caption text-caption text-ink-muted mt-0.5">Tampilan utama di etalase (1:1 persegi)</p>
                        </div>
                        <span class="material-symbols-outlined text-ink-muted text-[22px]">photo_library</span>
                    </div>
                    <!-- Primary Image Slot (1:1 Ratio) -->
                    <div class="relative group aspect-square w-full rounded-xl overflow-hidden border border-border-default bg-canvas-background mb-4 flex items-center justify-center" id="mainImageContainer">
                        @if (old('image', $product->image))
                            <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" alt="{{ $product->name }}" id="mainProductImg" src="{{ old('image', $product->image) }}">
                        @else
                            <div class="flex flex-col items-center text-ink-muted" id="mainProductPlaceholder">
                                <span class="material-symbols-outlined text-[36px]">image</span>
                                <span class="font-caption text-caption mt-1">Belum ada foto produk</span>
                            </div>
                        @endif
                        <!-- Overlay Tag -->
                        <div class="absolute top-2.5 left-2.5 px-2.5 py-1 rounded-full bg-on-surface/85 backdrop-blur-sm text-surface-card font-caption text-caption font-bold flex items-center gap-1 shadow-sm">
                            <span class="material-symbols-outlined text-[14px] text-tertiary-fixed">star</span>
                            <span class="">Foto Utama (1:1)</span>
                        </div>
                    </div>
                    <!-- Image URL Input -->
                    <div>
                        <label class="block font-body-md text-body-md font-bold text-on-surface mb-1.5" for="inputImage">
                            URL Foto Produk <span class="text-secondary">*</span>
                        </label>
                        <div class="relative">
                            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-ink-muted text-[18px]">link</span>
                            <input class="w-full h-11 pl-10 pr-3.5 {{ $errors->has('image') ? 'bg-error-container' : 'bg-surface-card' }} border border-border-input rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 transition-all placeholder:text-ink-muted" id="inputImage" name="image" type="url" placeholder="https://contoh.com/foto-produk.jpg" value="{{ old('image', $product->image) }}">
                        </div>
                        @error('image')
                            <p class="font-caption text-caption text-secondary mt-1.5 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>
                                <span class="">{{ $message }}</span>
                            </p>
                        @enderror
                        <p class="font-caption text-caption text-ink-muted mt-1.5">Tempel tautan foto produk (JPG/PNG) dari penyimpanan Anda.</p>
                    </div>
                </div>
                <!-- Card 2: Status & Visibilitas -->
                <div class="bg-surface-card rounded-xl p-6 border border-border-default shadow-sm">
                    <div class="flex items-center justify-between pb-4 mb-4 border-b border-border-default">
                        <div>
                            <h2 class="font-headline-md text-headline-sm text-on-surface font-bold">Visibilitas &amp; Publikasi</h2>
                            <p class="font-caption text-caption text-ink-muted mt-0.5">Kontrol ketersediaan di etalase online</p>
                        </div>
                        <span class="material-symbols-outlined text-ink-muted text-[22px]">visibility</span>
                    </div>
                    <div class="flex flex-col gap-4">
                        <!-- Toggle 1: Etalase Online -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex flex-col">
                                <span class="font-body-md text-body-md font-bold text-on-surface">Tampilkan di Etalase Online</span>
                                <span class="font-caption text-caption text-ink-muted leading-tight mt-0.5">Dapat dicari &amp; dibeli pelanggan toko online Anda.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0 mt-0.5">
                                <input class="sr-only peer" id="toggleOnline" name="is_active" type="checkbox" value="1" @checked(old('is_active', $product->is_active ?? true))>
                                <div class="w-11 h-6 bg-surface-container-highest peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-border-default after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-tertiary"></div>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- Card 3: Riwayat & Log Produk -->
                @if ($isEditing)
                    <div class="bg-surface-card rounded-xl p-5 border border-border-default shadow-sm">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="material-symbols-outlined text-[18px] text-ink-muted">history</span>
                            <h3 class="font-headline-sm text-caption font-bold text-on-surface uppercase tracking-wider">Log &amp; Audit Penjualan</h3>
                        </div>
                        <div class="space-y-2.5 font-caption text-caption">
                            @if ($product->created_at)
                                <div class="flex justify-between items-center py-1 border-b border-border-default/60">
                                    <span class="text-ink-muted">Dibuat pertama kali:</span>
                                    <span class="font-code-mono text-on-surface font-medium">{{ $product->created_at->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                            @endif
                            @if ($product->updated_at)
                                <div class="flex justify-between items-center py-1 border-b border-border-default/60">
                                    <span class="text-ink-muted">Perubahan terakhir:</span>
                                    <span class="font-code-mono text-on-surface font-medium">{{ $product->updated_at->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center pt-1.5">
                                <span class="text-ink-muted">Total Terjual:</span>
                                <span class="font-code-mono font-bold text-primary text-[13px]">{{ number_format((int) $product->sold_count, 0, ',', '.') }} unit <span class="font-caption text-[11px] text-ink-muted font-normal">(Rating {{ number_format((float) $product->rating, 1, ',', '.') }})</span></span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- Sticky Floating Action Footer -->
        <div class="fixed bottom-0 left-[240px] right-0 z-40 bg-surface-card/95 backdrop-blur-md border-t border-border-default shadow-[0_-4px_16px_rgba(25,25,25,0.06)] px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse"></span>
                <span class="font-caption text-caption text-on-surface font-medium hidden sm:inline">Perubahan tersimpan setelah tombol simpan ditekan</span>
            </div>
            <div class="flex items-center gap-3">
                <a class="h-11 px-6 rounded-full border border-primary-container bg-surface-card hover:bg-blue-subtle text-primary-container font-body-md text-body-md font-bold transition-colors shadow-sm inline-flex items-center" href="{{ route('seller.products') }}">
                    Batalkan
                </a>
                <button class="h-11 px-8 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-body-md text-body-md font-bold transition-all shadow-sm flex items-center gap-2" id="btnSubmit" type="submit">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    <span class="">{{ $isEditing ? 'Simpan Perubahan' : 'Tambah Produk' }}</span>
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function adjustStock(delta) {
        const input = document.getElementById('inputStock');
        if (!input) return;
        const current = parseInt(input.value || '0', 10);
        const next = Math.max(0, (isNaN(current) ? 0 : current) + delta);
        input.value = next;
    }

    function updateImagePreview(url) {
        const container = document.getElementById('mainImageContainer');
        if (!container) return;
        let img = document.getElementById('mainProductImg');
        let placeholder = document.getElementById('mainProductPlaceholder');
        if (url && url.trim() !== '') {
            if (!img) {
                img = document.createElement('img');
                img.id = 'mainProductImg';
                img.className = 'w-full h-full object-cover transition-transform duration-300 group-hover:scale-105';
                img.alt = 'Pratinjau foto produk';
                container.insertBefore(img, container.firstChild);
            }
            img.src = url;
            if (placeholder) placeholder.remove();
        } else if (img) {
            img.remove();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const imageInput = document.getElementById('inputImage');
        if (imageInput) {
            imageInput.addEventListener('input', function () {
                updateImagePreview(this.value);
            });
        }

        const titleInput = document.getElementById('inputTitle');
        const titleCount = document.getElementById('titleCount');
        if (titleInput && titleCount) {
            titleInput.addEventListener('input', function () {
                titleCount.textContent = this.value.length + ' / 100';
            });
        }

        const descInput = document.getElementById('inputDesc');
        const descCount = document.getElementById('descCount');
        if (descInput && descCount) {
            descInput.addEventListener('input', function () {
                descCount.textContent = this.value.length + ' / 1000 karakter';
            });
        }
    });
</script>
@endpush
