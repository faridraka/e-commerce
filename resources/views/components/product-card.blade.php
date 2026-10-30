@props(['product'])

@php
    $status = $product->stockStatus();
    $diskon = $product->discountPercent();
@endphp

<div class="group bg-surface-card rounded-lg border border-border-default overflow-hidden hover:shadow-[0_1px_3px_rgba(25,25,25,0.08)] hover:border-primary-container transition-all flex flex-col justify-between">
    <div>
        <div class="relative w-full aspect-square bg-surface-container overflow-hidden">
            <a href="{{ route('products.show', $product) }}">
                <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 {{ $status === 'habis' ? 'filter grayscale contrast-75' : '' }}" src="{{ $product->image }}" alt="{{ $product->name }}">
            </a>

            @if ($diskon)
                <span class="absolute top-2 left-2 bg-secondary text-on-secondary font-bold text-[11px] font-caption px-1.5 py-0.5 rounded shadow-sm">-{{ $diskon }}%</span>
            @endif

            @if ($status === 'habis')
                <div class="absolute inset-0 bg-on-surface/40 flex items-center justify-center">
                    <span class="bg-secondary text-on-secondary px-3 py-1 rounded-full text-caption font-caption font-bold shadow-md tracking-wide uppercase">Stok Habis</span>
                </div>
                <span class="absolute top-2 right-2 bg-surface-card/90 text-error font-bold text-[11px] font-caption px-2 py-0.5 rounded-full shadow-sm">Stok 0</span>
            @elseif ($status === 'menipis')
                <span class="absolute top-2 right-2 bg-accent-yellow text-on-accent-yellow font-bold text-[11px] font-caption px-2 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                    <span class="material-symbols-outlined text-[12px]">warning</span> Stok menipis: {{ $product->stock }}
                </span>
            @else
                <span class="absolute top-2 right-2 bg-white/95 backdrop-blur-sm text-tertiary font-bold text-[11px] font-caption px-2 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-tertiary-container"></span> Stok {{ $product->stock }}
                </span>
            @endif

            @if ($product->origin)
                <span class="absolute bottom-2 left-2 bg-surface-card/90 backdrop-blur-sm text-on-surface-variant font-medium text-[10px] px-2 py-0.5 rounded-full">{{ $product->origin }}</span>
            @endif
        </div>

        <div class="p-3">
            <a href="{{ route('products.show', $product) }}">
                <h3 class="font-headline-sm text-body-md font-medium {{ $status === 'habis' ? 'text-ink-muted' : 'text-on-surface' }} line-clamp-2 leading-snug group-hover:text-primary transition-colors">
                    {{ $product->name }}
                </h3>
            </a>
            <div class="mt-2 flex items-baseline gap-1.5 flex-wrap">
                <span class="font-price-sm text-price-sm {{ $status === 'habis' ? 'text-ink-muted' : 'text-on-surface' }} font-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                @if ($diskon)
                    <span class="font-strikethrough text-[12px] line-through text-ink-muted">Rp {{ number_format($product->original_price, 0, ',', '.') }}</span>
                @endif
            </div>
            <div class="mt-1 flex items-center gap-1 text-[11px] text-ink-muted">
                @if ($status === 'habis')
                    <span class="material-symbols-outlined text-[13px] text-ink-muted">block</span>
                    <span>Sementara tidak tersedia</span>
                @else
                    <span class="material-symbols-outlined text-[13px] text-accent-yellow">star</span>
                    <span class="font-bold text-on-surface">{{ number_format((float) $product->rating, 1) }}</span>
                    <span>({{ number_format($product->sold_count, 0, ',', '.') }} terjual)</span>
                @endif
            </div>
        </div>
    </div>

    <div class="p-3 pt-0">
        @if ($status === 'habis')
            <button class="w-full h-9 rounded-full bg-surface-container text-ink-muted font-bold text-caption font-caption cursor-not-allowed flex items-center justify-center gap-1" disabled type="button">
                <span class="material-symbols-outlined text-[16px]">do_not_disturb</span> Habis
            </button>
        @else
            <button class="w-full h-9 rounded-full bg-primary-container text-on-primary font-bold text-caption font-caption hover:bg-blue-hover transition-colors flex items-center justify-center gap-1.5 shadow-sm active:scale-95 add-to-cart-btn" data-name="{{ $product->name }}" data-product-id="{{ $product->id }}" type="button">
                <span class="material-symbols-outlined text-[16px]">add_shopping_cart</span> + Keranjang
            </button>
        @endif
    </div>
</div>
