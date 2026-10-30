<div {{ $attributes->merge(['class' => 'text-center max-w-md mx-auto py-6']) }}>
    <div class="w-16 h-16 rounded-full bg-blue-subtle text-primary mx-auto flex items-center justify-center mb-3">
        <span class="material-symbols-outlined text-[36px]">search_off</span>
    </div>
    <h3 class="font-headline-md text-headline-sm font-bold text-on-surface">{{ $title ?? 'Produk Tidak Ditemukan' }}</h3>
    <p class="font-body-md text-body-md text-ink-muted mt-1 leading-relaxed">
        {{ $message ?? 'Kami tidak menemukan data yang Anda cari. Coba periksa ejaan, gunakan kata kunci lain, atau hapus filter aktif.' }}
    </p>
    <div class="mt-4 flex flex-wrap items-center justify-center gap-3">
        @isset($secondaryUrl)
            <a class="h-9 px-4 inline-flex items-center rounded-full border border-primary-container bg-surface-card text-primary font-bold text-caption font-caption hover:bg-blue-subtle transition-colors" href="{{ $secondaryUrl }}">
                {{ $secondaryLabel ?? 'Kembali' }}
            </a>
        @endisset
        <a class="h-9 px-4 inline-flex items-center rounded-full bg-primary-container text-on-primary font-bold text-caption font-caption hover:bg-blue-hover transition-colors" href="{{ $actionUrl ?? url('/katalog') }}">
            {{ $actionLabel ?? 'Jelajahi Katalog' }}
        </a>
    </div>
</div>
