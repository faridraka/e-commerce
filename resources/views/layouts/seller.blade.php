<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="web_dashboard" name="shell-type">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Penjual - Lokalku')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&amp;family=JetBrains+Mono:wght@500&amp;display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet">
    <style>@layer base{html,body{margin:0;padding:0;}body{overscroll-behavior:none;}main>:first-child{margin-top:0!important;}main>:last-child{margin-bottom:0!important;}}::-webkit-scrollbar{display:none;}</style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">tailwind.config={darkMode:"class",theme:{extend:{colors:{"background":"#fcf9f8","on-tertiary-fixed":"#131f00","border-input":"#8A8A8A","on-primary":"#ffffff","on-error":"#ffffff","surface":"#fcf9f8","primary-fixed-dim":"#adc6ff","tertiary":"#3d5800","ink-muted":"#707070","tertiary-fixed-dim":"#a3d73a","surface-bright":"#fcf9f8","on-background":"#1c1b1b","surface-dim":"#dcd9d9","on-primary-fixed":"#001a41","secondary-fixed-dim":"#ffb3ad","primary-container":"#0064d2","error":"#ba1a1a","error-container":"#ffdad6","primary":"#004da4","surface-container-lowest":"#ffffff","tertiary-fixed":"#bef455","on-tertiary-container":"#c2f959","on-surface":"#1c1b1b","secondary-container":"#dc3132","on-secondary-container":"#fffbff","blue-hover":"#004FB3","on-secondary-fixed":"#410003","inverse-surface":"#313030","canvas-background":"#F7F7F7","secondary":"#b8121d","outline":"#727785","on-secondary":"#ffffff","on-error-container":"#93000a","on-tertiary":"#ffffff","red-graphic":"#E53238","surface-container-high":"#eae7e7","surface-container-highest":"#e5e2e1","blue-subtle":"#E6F0FB","success-text":"#4D7A00","on-accent-yellow":"#191919","on-secondary-fixed-variant":"#930010","on-tertiary-fixed-variant":"#364e00","primary-fixed":"#d8e2ff","outline-variant":"#c2c6d5","surface-tint":"#005bc0","surface-variant":"#e5e2e1","on-primary-fixed-variant":"#004493","on-surface-variant":"#424753","on-primary-container":"#dfe7ff","secondary-fixed":"#ffdad6","border-default":"#E5E5E5","surface-container-low":"#f6f3f2","surface-card":"#FFFFFF","surface-container":"#f0eded","accent-yellow":"#F5AF02","inverse-on-surface":"#f3f0ef","tertiary-container":"#507200","inverse-primary":"#adc6ff"},borderRadius:{"DEFAULT":"0.25rem","lg":"0.5rem","xl":"0.75rem","full":"9999px"},spacing:{"margin":"1rem","space-xs":"0.25rem","space-md":"0.75rem","margin-desktop":"1rem","space-xl":"1.5rem","space-lg":"1rem","gutter-mobile":"0.75rem","gutter":"1rem","space-sm":"0.5rem"},fontFamily:{"code-mono":["JetBrains Mono"],"display":["DM Sans"],"price-sm":["DM Sans"],"caption":["DM Sans"],"headline-lg":["DM Sans"],"body-md":["DM Sans"],"strikethrough":["DM Sans"],"headline-sm":["DM Sans"],"price-lg":["DM Sans"],"headline-md":["DM Sans"],"display-mobile":["DM Sans"]},fontSize:{"code-mono":["14px",{"lineHeight":"20px","fontWeight":"500"}],"display":["36px",{"lineHeight":"44px","letterSpacing":"-0.01em","fontWeight":"700"}],"price-sm":["16px",{"lineHeight":"24px","fontWeight":"700"}],"caption":["12px",{"lineHeight":"16px","fontWeight":"500"}],"headline-lg":["28px",{"lineHeight":"36px","letterSpacing":"-0.01em","fontWeight":"700"}],"body-md":["14px",{"lineHeight":"20px","fontWeight":"400"}],"strikethrough":["14px",{"lineHeight":"20px","fontWeight":"400"}],"headline-sm":["18px",{"lineHeight":"24px","fontWeight":"500"}],"price-lg":["24px",{"lineHeight":"32px","fontWeight":"700"}],"headline-md":["22px",{"lineHeight":"28px","fontWeight":"700"}],"display-mobile":["28px",{"lineHeight":"36px","letterSpacing":"-0.01em","fontWeight":"700"}]}}}};</script>
</head>
<body class="bg-canvas-background font-body-md text-body-md text-on-surface min-h-screen">
@php
    $userName = auth()->user()->name ?? 'Penjual';
    $userInitials = collect(explode(' ', trim($userName)))->filter()->take(2)->map(fn ($word) => mb_strtoupper(mb_substr($word, 0, 1)))->implode('');
    $sellerNav = [
        ['label' => 'Dasbor', 'icon' => 'dashboard', 'route' => 'seller.dashboard', 'active' => 'seller.dashboard', 'badge' => null],
        ['label' => 'Kasir POS', 'icon' => 'point_of_sale', 'route' => 'seller.pos', 'active' => 'seller.pos', 'badge' => 'Cepat'],
        ['label' => 'Produk', 'icon' => 'inventory_2', 'route' => 'seller.products', 'active' => 'seller.products*', 'badge' => null],
        ['label' => 'Kategori', 'icon' => 'category', 'route' => 'seller.categories', 'active' => 'seller.categories*', 'badge' => null],
        ['label' => 'Stok', 'icon' => 'inventory', 'route' => 'seller.stock', 'active' => 'seller.stock*', 'badge' => null],
        ['label' => 'Pesanan Online', 'icon' => 'shopping_bag', 'route' => 'seller.orders', 'active' => 'seller.orders*', 'badge' => null],
        ['label' => 'Riwayat Transaksi POS', 'icon' => 'receipt_long', 'route' => 'seller.pos.history', 'active' => 'seller.pos.history*', 'badge' => null],
    ];
@endphp
<aside class="fixed left-0 top-0 h-full w-[240px] bg-surface-card border-r border-border-default z-50 flex flex-col justify-between select-none">
    <div class="flex flex-col">
        <div class="p-space-lg border-b border-border-default bg-surface-card">
            <div class="flex items-center gap-space-sm mb-space-xs">
                <span class="w-8 h-8 rounded-lg bg-primary-container text-on-primary flex items-center justify-center font-display text-headline-sm font-bold">L</span>
                <div class="overflow-hidden">
                    <h1 class="font-headline-sm text-headline-sm text-on-surface truncate leading-tight">Lokalku POS</h1>
                    <span class="font-caption text-caption text-ink-muted uppercase tracking-wider block">Merchant Suite</span>
                </div>
            </div>
            <div class="mt-space-sm pt-space-xs border-t border-border-default">
                <div class="flex items-center justify-between">
                    <span class="font-headline-sm text-caption font-bold text-on-surface truncate max-w-[140px]" title="{{ $store->name ?? 'Toko Saya' }}">{{ $store->name ?? 'Toko Saya' }}</span>
                    <span class="w-2 h-2 rounded-full bg-tertiary-fixed-dim ring-2 ring-tertiary/20" title="Online">
                    </span>
                </div>
                <p class="font-caption text-caption text-ink-muted truncate">{{ $store->slug ?? 'Portal Penjual' }}</p>
                <div class="mt-space-xs inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-tertiary-fixed/30 text-tertiary font-caption text-caption font-medium">
                    <span class="material-symbols-outlined text-[14px]">storefront</span>
                    <span class="">Toko Aktif - Buka</span>
                </div>
            </div>
        </div>
        <nav class="flex flex-col py-space-sm" data-active-classes="bg-blue-subtle text-primary border-l-[3px] border-primary font-bold">
            @foreach ($sellerNav as $item)
                @php $isActive = $item['active'] !== null && request()->routeIs($item['active']); @endphp
                <a @if ($isActive) aria-current="page" @endif class="{{ $isActive ? 'flex items-center justify-between px-space-lg py-2.5 transition-colors bg-blue-subtle text-primary border-l-[3px] border-primary font-bold' : 'flex items-center justify-between px-space-lg py-2.5 text-on-surface-variant hover:bg-surface-container-high hover:text-on-surface transition-colors border-l-[3px] border-transparent' }}" href="{{ $item['route'] !== null ? route($item['route']) : '#' }}">
                    <div class="flex items-center gap-space-md">
                        <span class="material-symbols-outlined text-[20px]">{{ $item['icon'] }}</span>
                        <span class="font-body-md text-body-md">{{ $item['label'] }}</span>
                    </div>
                    @if ($isActive)
                        <span class="w-1.5 h-1.5 rounded-full bg-primary">
                        </span>
                    @elseif ($item['badge'])
                        <span class="px-1.5 py-0.5 rounded-full bg-tertiary-fixed text-on-tertiary-fixed font-caption text-caption font-bold uppercase tracking-wider">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>
    </div>
    <div class="p-space-lg border-t border-border-default bg-surface-card">
        <div class="flex items-center gap-space-sm mb-space-md">
            <div class="w-8 h-8 rounded-full bg-blue-subtle text-primary flex items-center justify-center font-bold text-caption font-caption">{{ $userInitials }}</div>
            <div class="overflow-hidden flex-1">
                <p class="font-body-md text-caption font-bold text-on-surface truncate leading-tight">{{ $userName }}</p>
                <p class="font-caption text-caption text-ink-muted truncate">Penjual Aktif</p>
            </div>
        </div>
        <div class="flex flex-col gap-space-xs">
            <a class="w-full flex items-center justify-center gap-space-xs py-2 px-space-md rounded-full bg-on-surface text-surface-card font-body-md text-caption font-bold hover:bg-inverse-surface transition-colors shadow-sm" href="{{ route('seller.pos') }}">
                <span class="material-symbols-outlined text-[16px]">bolt</span>
                <span class="">Buka Kasir Cepat</span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full flex items-center justify-center gap-space-xs py-1.5 px-space-md rounded-full text-secondary hover:bg-error-container/30 font-caption text-caption font-bold transition-colors" type="submit">
                    <span class="material-symbols-outlined text-[16px]">logout</span>
                    <span class="">Keluar Sesi</span>
                </button>
            </form>
        </div>
    </div>
</aside>
<div class="pl-[240px]">
    <header class="fixed top-0 left-[240px] right-0 h-16 bg-surface-card border-b border-border-default z-40 px-space-lg flex items-center justify-between shadow-[0_1px_4px_rgba(25,25,25,0.04)]">
        <div class="flex items-center gap-space-lg">
            <div class="flex items-center gap-2 font-caption text-caption text-ink-muted">
                <span class="font-body-md text-body-md text-on-surface-variant font-medium">Portal Penjual</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="font-body-md text-body-md font-bold text-on-surface">@yield('breadcrumb', 'Dasbor')</span>
            </div>
            <div class="relative w-80">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-[18px] text-ink-muted">search</span>
                <input class="w-full h-10 pl-9 pr-4 bg-canvas-background border border-border-default rounded-lg font-body-md text-body-md text-on-surface focus:outline-none focus:ring-2 focus:ring-primary-container focus:bg-surface-card transition-all placeholder:text-ink-muted" placeholder="Cari pesanan, SKU, atau nama produk..." type="text">
            </div>
        </div>
        <div class="flex items-center gap-space-md">
            <div class="hidden xl:flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-subtle/70 border border-primary-fixed">
                <span class="material-symbols-outlined text-primary text-[18px]">verified</span>
                <span class="font-caption text-caption font-medium text-primary">Kasir Tunai Aktif</span>
            </div>
            <div class="flex items-center gap-space-xs">
                <a class="h-10 px-4 rounded-full border border-border-default bg-surface-card text-on-surface font-body-md text-body-md font-medium hover:bg-surface-container-high transition-colors flex items-center gap-1.5" href="{{ route('seller.products.create') }}">
                    <span class="material-symbols-outlined text-[18px]">add</span>
                    <span class="hidden md:inline">Tambah Produk</span>
                </a>
                <a class="h-10 px-4 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-body-md text-body-md font-medium transition-colors flex items-center gap-1.5 shadow-sm" href="{{ route('seller.pos') }}">
                    <span class="material-symbols-outlined text-[18px]">shopping_cart_checkout</span>
                    <span class="">Buka Kasir</span>
                </a>
            </div>
            <div class="h-6 w-px bg-border-default mx-1">
            </div>
            <button class="relative p-2 rounded-full text-on-surface-variant hover:bg-surface-container-high transition-colors">
                <span class="material-symbols-outlined text-[22px]">notifications</span>
                <span class="absolute top-1.5 right-1.5 w-2.5 h-2.5 rounded-full bg-secondary ring-2 ring-surface-card">
                </span>
            </button>
            <div class="flex items-center gap-space-sm pl-1">
                <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-primary text-[18px]">person</span>
                </div>
            </div>
        </div>
    </header>
    <main class="relative pt-16 bg-canvas-background min-h-screen px-space-lg py-space-xl">
        @if (session('status'))
            <div class="mx-auto max-w-[1280px] px-4 pt-4"><div class="bg-tertiary-fixed/30 border border-tertiary-container/30 rounded-xl p-3 flex items-center gap-2.5"><span class="material-symbols-outlined text-tertiary text-[20px]">check_circle</span><p class="font-body-md text-body-md font-medium text-tertiary">{{ session('status') }}</p></div></div>
        @endif
        @yield('content')
    </main>
</div>
@stack('scripts')
</body>
</html>
