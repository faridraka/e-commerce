@extends('layouts.seller')

@section('title', 'Manajemen Kategori - Portal Penjual Lokalku')

@section('content')
<div class="flex flex-col w-full">
    @if ($errors->has('category'))
        <div class="mb-space-lg bg-error-container/40 border border-secondary/30 rounded-xl p-3 flex items-center gap-2.5">
            <span class="material-symbols-outlined text-secondary text-[20px]">error</span>
            <p class="font-body-md text-body-md font-medium text-secondary">{{ $errors->first('category') }}</p>
        </div>
    @endif
    <!-- Page Header & Key Actions -->
    <header class="flex flex-col lg:flex-row lg:items-end justify-between gap-space-lg mb-space-xl">
        <div class="flex flex-col gap-1 max-w-2xl">
            <!-- Breadcrumb navigation -->
            <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 font-caption text-caption text-ink-muted mb-1">
                <span class="">Portal Penjual</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="">Produk &amp; Stok</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-on-surface">Manajemen Kategori</span>
            </nav>
            <h1 class="font-headline-lg text-headline-lg text-on-surface">Kategori Produk</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">
                Kelola pengelompokan produk etalase online dan klasifikasi kasir POS UMKM Anda untuk operasional yang cepat dan teratur.
            </p>
        </div>
        <!-- Main Action Button -->
        <div class="flex items-center gap-space-sm shrink-0">
            <button class="h-[44px] px-6 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-headline-sm text-body-md shadow-sm transition-colors flex items-center gap-2 cursor-pointer" onclick="openCreateCategoryModal()" type="button">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span class="">+ Tambah Kategori</span>
            </button>
        </div>
    </header>
    <!-- Stats Strip & Search Controls Container -->
    <section class="mb-space-lg flex flex-col gap-space-md">
        <!-- Quick Analytics Glance (Data Visual Spark) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-space-md">
            <div class="bg-surface-card rounded-xl p-space-md shadow-sm flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="font-caption text-caption text-ink-muted uppercase tracking-wider">Kategori Terdaftar</span>
                    <span class="font-price-lg text-price-lg text-on-surface" id="stat-category-count">{{ $categories->count() }} Kategori</span>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-subtle text-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">category</span>
                </div>
            </div>
            <div class="bg-surface-card rounded-xl p-space-md shadow-sm flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="font-caption text-caption text-ink-muted uppercase tracking-wider">Produk Terhubung</span>
                    <span class="font-price-lg text-price-lg text-primary-container" id="stat-product-count">{{ $categories->sum('products_count') }} Produk</span>
                </div>
                <div class="w-10 h-10 rounded-full bg-blue-subtle text-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">inventory_2</span>
                </div>
            </div>
            <div class="bg-surface-card rounded-xl p-space-md shadow-sm flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="font-caption text-caption text-ink-muted uppercase tracking-wider">Etalase Web Aktif</span>
                    <span class="font-price-lg text-price-lg text-tertiary">100% Aktif</span>
                </div>
                <div class="w-10 h-10 rounded-full bg-tertiary-fixed text-tertiary flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">storefront</span>
                </div>
            </div>
            <div class="bg-surface-card rounded-xl p-space-md shadow-sm flex items-center justify-between">
                <div class="flex flex-col">
                    <span class="font-caption text-caption text-ink-muted uppercase tracking-wider">Kasir POS Cepat</span>
                    <span class="font-price-lg text-price-lg text-on-surface">{{ $categories->count() }} Tab Siap</span>
                </div>
                <div class="w-10 h-10 rounded-full bg-surface-container-high text-on-surface flex items-center justify-center">
                    <span class="material-symbols-outlined text-[22px]">point_of_sale</span>
                </div>
            </div>
        </div>
        <!-- Filter Card Toolbar -->
        <div class="bg-surface-card rounded-xl p-space-md shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-space-md">
            <!-- Search Input -->
            <div class="relative flex-1 max-w-md">
                <span class="material-symbols-outlined absolute left-3.5 top-1/2 -translate-y-1/2 text-[20px] text-ink-muted">search</span>
                <input class="w-full h-[44px] pl-10 pr-4 bg-canvas-background rounded-lg font-body-md text-body-md text-on-surface placeholder:text-ink-muted focus:outline-none focus:bg-surface-card transition-all" id="search-input" onkeyup="handleFilterSearch(this.value)" placeholder="Cari nama kategori atau slug..." type="text">
            </div>
            <!-- Quick Summary and Action Filters -->
            <div class="flex flex-wrap items-center gap-space-sm justify-between md:justify-end">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-surface-container text-ink-muted font-caption text-caption">
                    <span class="w-2 h-2 rounded-full bg-tertiary">
                    </span>
                    <span id="badge-total-info" class="">Total {{ $categories->count() }} Kategori Aktif · {{ $categories->sum('products_count') }} Produk Terhubung</span>
                </div>
                <div class="flex items-center gap-2">
                    <!-- Sort Dropdown -->
                    <div class="relative inline-block text-left">
                        <button class="h-[44px] px-4 rounded-full bg-surface-container-low hover:bg-surface-container text-on-surface font-caption text-body-md flex items-center gap-1.5 transition-colors cursor-pointer" id="sort-menu-button" onclick="toggleSortMenu()" type="button">
                            <span class="material-symbols-outlined text-[18px] text-ink-muted">sort</span>
                            <span id="current-sort-label" class="">Urutkan: Produk Terbanyak</span>
                            <span class="material-symbols-outlined text-[16px]">expand_more</span>
                        </button>
                        <div class="hidden absolute right-0 mt-2 w-52 rounded-xl bg-surface-card shadow-xl z-20 py-1 focus:outline-none" id="sort-dropdown">
                            <button class="w-full text-left px-4 py-2.5 font-body-md text-body-md text-on-surface hover:bg-blue-subtle hover:text-primary-container transition-colors flex items-center justify-between" onclick="setSort('Produk Terbanyak')" type="button">
                                <span class="">Produk Terbanyak</span>
                                <span class="material-symbols-outlined text-[16px] text-primary-container">check</span>
                            </button>
                            <button class="w-full text-left px-4 py-2.5 font-body-md text-body-md text-on-surface hover:bg-blue-subtle hover:text-primary-container transition-colors" onclick="setSort('Nama A-Z')" type="button">
                                <span class="">Nama A-Z</span>
                            </button>
                            <button class="w-full text-left px-4 py-2.5 font-body-md text-body-md text-on-surface hover:bg-blue-subtle hover:text-primary-container transition-colors" onclick="setSort('Terbaru Dibuat')" type="button">
                                <span class="">Terbaru Dibuat</span>
                            </button>
                        </div>
                    </div>
                    <!-- Export Button -->
                    <button class="h-[44px] px-4 rounded-full bg-surface-card hover:bg-surface-container text-primary-container font-headline-sm text-body-md transition-colors flex items-center gap-1.5 cursor-pointer shadow-sm" onclick="alert('Mengekspor daftar kategori ke format CSV/Excel...')" type="button">
                        <span class="material-symbols-outlined text-[18px]">download</span>
                        <span class="hidden sm:inline">Ekspor Daftar</span>
                    </button>
                </div>
            </div>
        </div>
    </section>
    <!-- Table Container (CRUD Table) -->
    <section class="bg-surface-card rounded-xl shadow-sm overflow-hidden flex flex-col mb-space-xl" id="table-container">
        @if ($categories->isEmpty())
            <!-- Empty State View -->
            <div class="py-16 px-6 flex flex-col items-center justify-center text-center" id="empty-state-view">
                <div class="w-20 h-20 rounded-full bg-surface-container flex items-center justify-center text-ink-muted mb-4">
                    <span class="material-symbols-outlined text-[40px]">inventory</span>
                </div>
                <h3 class="font-headline-md text-headline-sm text-on-surface mb-2">Belum Ada Kategori Produk</h3>
                <p class="font-body-md text-body-md text-ink-muted max-w-md mb-6">
                    Kategori memudahkan pembeli menemukan produk di etalase web dan mempercepat kasir dalam memilih item saat transaksi penjualan.
                </p>
                <button class="h-[44px] px-6 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-headline-sm text-body-md shadow-sm transition-colors flex items-center gap-2 cursor-pointer" onclick="openCreateCategoryModal()" type="button">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    <span class="">Buat Kategori Pertama</span>
                </button>
            </div>
        @else
            <div class="overflow-x-auto w-full">
                <table class="w-full text-left border-collapse" id="crud-categories-table">
                    <!-- Table Header -->
                    <thead>
                        <tr class="bg-canvas-background">
                            <th class="py-3.5 px-5 font-caption text-caption text-ink-muted uppercase tracking-wider" scope="col">Nama Kategori</th>
                            <th class="py-3.5 px-5 font-caption text-caption text-ink-muted uppercase tracking-wider" scope="col">Slug URL</th>
                            <th class="py-3.5 px-5 font-caption text-caption text-ink-muted uppercase tracking-wider text-center" scope="col">Jumlah Produk</th>
                            <th class="py-3.5 px-5 font-caption text-caption text-ink-muted uppercase tracking-wider" scope="col">Status Etalase</th>
                            <th class="py-3.5 px-5 font-caption text-caption text-ink-muted uppercase tracking-wider text-right" scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <!-- Table Body -->
                    <tbody id="category-tbody">
                        @php
                            $categoryVariants = [
                                ['icon' => 'restaurant', 'class' => 'bg-accent-yellow/15 text-on-accent-yellow'],
                                ['icon' => 'local_cafe', 'class' => 'bg-blue-subtle text-primary-container'],
                                ['icon' => 'palette', 'class' => 'bg-surface-container-high text-on-surface'],
                                ['icon' => 'checkroom', 'class' => 'bg-surface-container text-primary'],
                                ['icon' => 'spa', 'class' => 'bg-surface-container-high text-tertiary'],
                            ];
                        @endphp
                        @foreach ($categories as $category)
                            @php $variant = $categoryVariants[$loop->index % count($categoryVariants)]; @endphp
                            <tr class="category-row hover:bg-surface-container-low transition-colors" data-count="{{ (int) $category->products_count }}" data-name="{{ $category->name }}" data-slug="{{ $category->slug }}">
                                <td class="py-4 px-5">
                                    <div class="flex items-start gap-3">
                                        <div class="w-10 h-10 rounded-lg {{ $variant['class'] }} flex items-center justify-center shrink-0 mt-0.5">
                                            <span class="material-symbols-outlined text-[20px]">{{ $variant['icon'] }}</span>
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="font-headline-sm text-body-md text-on-surface">{{ $category->name }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 align-middle">
                                    <span class="inline-block font-code-mono text-caption bg-canvas-background text-on-surface px-2 py-1 rounded">
                                        {{ $category->slug }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 align-middle text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-subtle text-primary-container font-headline-sm text-caption">
                                        {{ (int) $category->products_count }} Produk
                                    </span>
                                </td>
                                <td class="py-4 px-5 align-middle">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-tertiary-fixed text-tertiary font-caption text-caption">
                                        <span class="w-1.5 h-1.5 rounded-full bg-tertiary">
                                        </span>
                                        <span class="">Aktif di POS &amp; Web</span>
                                    </span>
                                </td>
                                <td class="py-4 px-5 align-middle text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button class="w-9 h-9 rounded-full bg-surface-container-low text-primary-container hover:bg-blue-subtle transition-colors flex items-center justify-center cursor-pointer" data-form-id="edit-category-form-{{ $category->id }}" data-name="{{ $category->name }}" onclick="openEditCategoryModal(this)" title="Edit Kategori" type="button">
                                            <span class="material-symbols-outlined text-[18px]">edit</span>
                                        </button>
                                        @if ((int) $category->products_count > 0)
                                            <button class="w-9 h-9 rounded-full bg-surface-container-low text-secondary hover:bg-secondary-fixed transition-colors flex items-center justify-center cursor-not-allowed opacity-50" disabled title="Masih memiliki produk" type="button">
                                                <span class="material-symbols-outlined text-[18px]">delete</span>
                                            </button>
                                        @else
                                            <form action="{{ route('seller.categories.destroy', $category) }}" class="inline-flex" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                                @csrf
                                                <button class="w-9 h-9 rounded-full bg-surface-container-low text-secondary hover:bg-secondary-fixed transition-colors flex items-center justify-center cursor-pointer" title="Hapus Kategori" type="submit">
                                                    <span class="material-symbols-outlined text-[18px]">delete</span>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <!-- Form edit per kategori (disubmit lewat modal) -->
                                    <form action="{{ route('seller.categories.update', $category) }}" aria-hidden="true" class="hidden" id="edit-category-form-{{ $category->id }}" method="POST" tabindex="-1">
                                        @method('PATCH')
                                        @csrf
                                        <input name="name" tabindex="-1" type="text" value="{{ $category->name }}">
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Table Footer Pagination / Meta -->
            <footer class="py-3 px-5 bg-canvas-background flex flex-col sm:flex-row items-center justify-between gap-space-sm" id="table-footer-pagination">
                <span class="font-caption text-caption text-ink-muted">Menampilkan {{ $categories->count() }} dari {{ $categories->count() }} kategori terdaftar</span>
                <div class="flex items-center gap-2">
                    <button class="h-8 px-3 rounded-full bg-surface-card text-ink-muted font-caption text-caption cursor-not-allowed shadow-sm" disabled>Sebelumnya</button>
                    <button class="h-8 w-8 rounded-full bg-primary-container text-on-primary font-caption text-caption font-bold shadow-sm">1</button>
                    <button class="h-8 px-3 rounded-full bg-surface-card text-on-surface hover:bg-surface-container font-caption text-caption transition-colors shadow-sm">Selanjutnya</button>
                </div>
            </footer>
        @endif
    </section>
    <!-- MODAL: Tambah / Edit Kategori -->
    <dialog class="hidden fixed inset-0 z-50 w-full h-full bg-inverse-surface/40 backdrop-blur-sm flex items-center justify-center p-4" id="create-category-modal">
        <div class="bg-surface-card w-full max-w-lg rounded-xl shadow-xl overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-200">
            <!-- Modal Header -->
            <div class="px-6 py-5 bg-surface-card flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-blue-subtle text-primary-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-[20px]">new_label</span>
                    </div>
                    <h2 class="font-headline-md text-headline-sm text-on-surface" id="modal-title">Tambah Kategori Baru</h2>
                </div>
                <button class="w-8 h-8 rounded-full text-ink-muted hover:bg-surface-container hover:text-on-surface flex items-center justify-center transition-colors cursor-pointer" onclick="closeModal('create-category-modal')" type="button">
                    <span class="material-symbols-outlined text-[20px]">close</span>
                </button>
            </div>
            <!-- Modal Body -->
            <form action="{{ route('seller.categories.store') }}" class="px-6 py-4 flex flex-col gap-4" id="create-category-form" method="POST" onsubmit="handleCategorySubmit(event)">
                @csrf
                <!-- Input Nama Kategori -->
                <div class="flex flex-col gap-1.5">
                    <label class="font-headline-sm text-body-md text-on-surface flex items-center gap-1" for="cat-name-input">
                        Nama Kategori <span class="text-secondary">*</span>
                    </label>
                    <input class="w-full h-[44px] px-3.5 bg-canvas-background rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:bg-surface-card transition-all placeholder:text-ink-muted" id="cat-name-input" name="name" oninput="updateSlugPreview(this.value)" placeholder="Contoh: Camilan Tradisional" required type="text" value="{{ old('name') }}">
                    @error('name')
                        <p class="font-caption text-caption text-secondary flex items-center gap-1">
                            <span class="material-symbols-outlined text-[14px]">error</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <!-- Slug URL Preview Box -->
                <div class="bg-surface-container-low rounded-lg p-3 flex flex-col gap-1">
                    <div class="flex items-center justify-between">
                        <span class="font-caption text-caption text-ink-muted">Preview Slug URL Otomatis</span>
                        <span class="font-caption text-caption text-primary-container font-semibold">Otomatis Terbit</span>
                    </div>
                    <p class="font-code-mono text-caption text-on-surface break-all" id="slug-preview-text">
                        lokalku.id/kategori/<span class="text-primary-container" id="slug-dynamic">camilan-tradisional</span>
                    </p>
                </div>
                <!-- Modal Footer -->
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button class="h-[44px] px-6 rounded-full bg-surface-container hover:bg-surface-container-high text-on-surface font-headline-sm text-body-md transition-colors cursor-pointer" onclick="closeModal('create-category-modal')" type="button">
                        Batal
                    </button>
                    <button class="h-[44px] px-6 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-headline-sm text-body-md shadow-sm transition-colors cursor-pointer" type="submit">
                        <span id="modal-submit-label">Simpan Kategori</span>
                    </button>
                </div>
            </form>
        </div>
    </dialog>
</div>
@endsection

@push('scripts')
<script>
  // Modal open/close helpers
  function openModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.remove('hidden');
  }

  function closeModal(id) {
    const el = document.getElementById(id);
    if (el) el.classList.add('hidden');
  }

  // State form aktif: null = tambah kategori, string id = edit kategori
  let editingFormId = null;

  function resetCategoryModal() {
    editingFormId = null;
    document.getElementById('modal-title').innerText = 'Tambah Kategori Baru';
    document.getElementById('modal-submit-label').innerText = 'Simpan Kategori';
    const input = document.getElementById('cat-name-input');
    input.value = '';
    updateSlugPreview('');
  }

  function openCreateCategoryModal() {
    resetCategoryModal();
    openModal('create-category-modal');
    document.getElementById('cat-name-input').focus();
  }

  function openEditCategoryModal(trigger) {
    editingFormId = trigger.dataset.formId;
    document.getElementById('modal-title').innerText = 'Edit Kategori';
    document.getElementById('modal-submit-label').innerText = 'Simpan Perubahan';
    const input = document.getElementById('cat-name-input');
    input.value = trigger.dataset.name;
    updateSlugPreview(trigger.dataset.name);
    openModal('create-category-modal');
    input.focus();
  }

  // Kategori baru dikirim langsung oleh form modal (action = store route).
  // Edit kategori diteruskan ke form PATCH per kategori yang disembunyikan di baris tabel.
  function handleCategorySubmit(event) {
    if (!editingFormId) {
      return;
    }
    event.preventDefault();
    const hiddenForm = document.getElementById(editingFormId);
    if (hiddenForm) {
      hiddenForm.querySelector('input[name="name"]').value = document.getElementById('cat-name-input').value;
      hiddenForm.submit();
    }
  }

  // Slug generator micro-interaction
  function updateSlugPreview(val) {
    const dynamicSlug = document.getElementById('slug-dynamic');
    if (!val || val.trim() === '') {
      dynamicSlug.innerText = 'camilan-tradisional';
      return;
    }
    const slugified = val
      .toLowerCase()
      .trim()
      .replace(/[^\w\s-]/g, '')
      .replace(/[\s_-]+/g, '-')
      .replace(/^-+|-+$/g, '');
    dynamicSlug.innerText = slugified || 'nama-kategori';
  }

  // Table Search Filter
  function handleFilterSearch(query) {
    const rows = document.querySelectorAll('.category-row');
    const q = query.toLowerCase().trim();
    rows.forEach(row => {
      const name = row.getAttribute('data-name').toLowerCase();
      const slug = row.getAttribute('data-slug').toLowerCase();
      if (name.includes(q) || slug.includes(q)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  // Sort Dropdown Toggle
  function toggleSortMenu() {
    const menu = document.getElementById('sort-dropdown');
    menu.classList.toggle('hidden');
  }

  function setSort(label) {
    document.getElementById('current-sort-label').innerText = 'Urutkan: ' + label;
    document.getElementById('sort-dropdown').classList.add('hidden');
  }

  // Close menus on outside click
  window.addEventListener('click', function(e) {
    const sortBtn = document.getElementById('sort-menu-button');
    const sortDropdown = document.getElementById('sort-dropdown');
    if (sortBtn && sortDropdown && !sortBtn.contains(e.target) && !sortDropdown.contains(e.target)) {
      sortDropdown.classList.add('hidden');
    }
  });

  @if ($errors->has('name'))
    // Buka kembali modal saat validasi gagal agar pesan error terlihat.
    document.getElementById('cat-name-input').value = @json(old('name'));
    updateSlugPreview(@json(old('name')));
    openModal('create-category-modal');
  @endif
</script>
@endpush
