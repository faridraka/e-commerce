<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Lokalku - UMKM Lokal & Kasir POS')</title>
    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <style>@layer base { html, body { margin: 0; padding: 0; } body { overscroll-behavior: none; } } ::-webkit-scrollbar { display: none; }</style>
    <script src="https://cdn.tailwindcss.com"></script>
    <script id="tailwind-config">tailwind.config = { darkMode: "class", theme: { extend: { "colors": { "surface-container-high": "#eae7e7", "on-secondary": "#ffffff", "surface-container-low": "#f6f3f2", "accent-yellow": "#F5AF02", "on-surface-variant": "#424753", "tertiary-fixed": "#bef455", "inverse-on-surface": "#f3f0ef", "on-primary-fixed-variant": "#004493", "tertiary": "#3d5800", "tertiary-container": "#507200", "on-tertiary-fixed-variant": "#364e00", "on-secondary-container": "#fffbff", "success-text": "#4D7A00", "surface-dim": "#dcd9d9", "secondary-fixed": "#ffdad6", "secondary": "#b8121d", "secondary-fixed-dim": "#ffb3ad", "primary-container": "#0064d2", "on-secondary-fixed": "#410003", "inverse-surface": "#313030", "primary-fixed": "#d8e2ff", "canvas-background": "#F7F7F7", "primary": "#004da4", "on-secondary-fixed-variant": "#930010", "on-primary-fixed": "#001a41", "surface-container-lowest": "#ffffff", "primary-fixed-dim": "#adc6ff", "on-accent-yellow": "#191919", "blue-hover": "#004FB3", "on-primary": "#ffffff", "surface-container-highest": "#e5e2e1", "tertiary-fixed-dim": "#a3d73a", "ink-muted": "#707070", "inverse-primary": "#adc6ff", "blue-subtle": "#E6F0FB", "border-default": "#E5E5E5", "red-graphic": "#E53238", "surface-bright": "#fcf9f8", "surface-card": "#FFFFFF", "on-error": "#ffffff", "on-background": "#1c1b1b", "on-surface": "#1c1b1b", "background": "#fcf9f8", "border-input": "#8A8A8A", "surface": "#fcf9f8", "error": "#ba1a1a", "on-error-container": "#93000a", "outline": "#727785", "surface-variant": "#e5e2e1", "on-primary-container": "#dfe7ff", "secondary-container": "#dc3132", "outline-variant": "#c2c6d5", "error-container": "#ffdad6", "on-tertiary-container": "#c2f959", "on-tertiary-fixed": "#131f00", "surface-container": "#f0eded", "on-tertiary": "#ffffff", "surface-tint": "#005bc0" }, "borderRadius": { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" }, "spacing": { "space-md": "0.75rem", "gutter": "1rem", "space-xs": "0.25rem", "space-lg": "1rem", "margin": "1rem", "space-sm": "0.5rem", "space-xl": "1.5rem", "gutter-mobile": "0.75rem", "margin-desktop": "1rem" }, "fontFamily": { "strikethrough": [ "DM Sans" ], "display-mobile": [ "DM Sans" ], "headline-lg": [ "DM Sans" ], "code-mono": [ "JetBrains Mono" ], "caption": [ "DM Sans" ], "display": [ "DM Sans" ], "price-lg": [ "DM Sans" ], "headline-sm": [ "DM Sans" ], "headline-md": [ "DM Sans" ], "price-sm": [ "DM Sans" ], "body-md": [ "DM Sans" ] }, "fontSize": { "strikethrough": [ "14px", { "lineHeight": "20px", "fontWeight": "400" } ], "display-mobile": [ "28px", { "lineHeight": "36px", "letterSpacing": "-0.01em", "fontWeight": "700" } ], "display": [ "36px", { "lineHeight": "44px", "letterSpacing": "-0.01em", "fontWeight": "700" } ], "headline-lg": [ "28px", { "lineHeight": "36px", "letterSpacing": "-0.01em", "fontWeight": "700" } ], "code-mono": [ "14px", { "lineHeight": "20px", "fontWeight": "500" } ], "caption": [ "12px", { "lineHeight": "16px", "fontWeight": "500" } ], "price-lg": [ "24px", { "lineHeight": "32px", "fontWeight": "700" } ], "headline-sm": [ "18px", { "lineHeight": "24px", "fontWeight": "500" } ], "body-md": [ "14px", { "lineHeight": "20px", "fontWeight": "400" } ], "headline-md": [ "22px", { "lineHeight": "28px", "fontWeight": "700" } ], "price-sm": [ "16px", { "lineHeight": "24px", "fontWeight": "700" } ] } } } };</script>
</head>
<body class="bg-canvas-background font-body-md text-on-surface antialiased">
    <header class="fixed top-0 left-0 right-0 z-50 bg-surface-card border-b border-border-default shadow-[0_1px_8px_rgba(0,0,0,0.04)]">
        <div class="h-12 max-w-[1280px] mx-auto px-4 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3 shrink-0">
                <a class="flex items-center gap-2" href="{{ route('home') }}">
                    <span class="font-headline-md text-headline-md tracking-tight font-bold">
                        <span class="text-primary">Lokal</span><span class="text-accent-yellow">k</span><span class="text-red-graphic">u</span>
                    </span>
                </a>
                <nav class="hidden xl:flex items-center gap-4 ml-2">
                    @foreach ($navCategories as $navCategory)
                        <a class="text-on-surface-variant hover:text-on-surface transition-colors font-body-md text-body-md" href="{{ route('products.index', ['kategori' => $navCategory->slug]) }}">{{ $navCategory->name }}</a>
                    @endforeach
                </nav>
            </div>

            <!-- Search -->
            <form class="flex-1 max-w-xl mx-2 hidden md:block" action="{{ route('products.index') }}" method="GET">
                <div class="flex items-center h-9 bg-surface-container-low border border-border-default rounded-full px-3 gap-2 focus-within:border-primary-container transition-all">
                    <select name="kategori" class="bg-transparent text-on-surface-variant text-caption font-caption outline-none border-r border-border-default pr-2 cursor-pointer">
                        <option value="">Semua Kategori</option>
                        @foreach ($navCategories as $navCategory)
                            <option value="{{ $navCategory->slug }}" @selected(request('kategori') === $navCategory->slug)>{{ $navCategory->name }}</option>
                        @endforeach
                    </select>
                    <input name="q" value="{{ request('q') }}" class="w-full bg-transparent text-on-surface text-body-md font-body-md placeholder:text-ink-muted outline-none" placeholder="Cari produk UMKM lokal..." type="text">
                    <button class="text-on-surface-variant hover:text-primary transition-colors" type="submit">
                        <span class="material-symbols-outlined text-[20px] leading-none">search</span>
                    </button>
                </div>
            </form>

            <div class="flex items-center gap-2 shrink-0">
                <a class="relative p-2 text-on-surface-variant hover:text-primary transition-colors rounded-full flex items-center justify-center" href="{{ auth()->check() ? route('cart.index') : route('login') }}" title="Keranjang Belanja">
                    <span class="material-symbols-outlined text-[22px]">shopping_cart</span>
                    <span id="cart-badge" class="absolute top-1 right-1 w-4 h-4 rounded-full bg-secondary text-on-secondary text-caption font-caption items-center justify-center leading-none {{ ($cartCount ?? 0) > 0 ? 'flex' : 'hidden' }}">{{ $cartCount ?? 0 }}</span>
                </a>

                <div class="h-5 w-[1px] bg-border-default mx-1 hidden sm:block"></div>

                @guest
                    <a class="hidden sm:inline-flex items-center justify-center h-8 px-4 rounded-full border border-primary-container bg-surface-card text-primary font-body-md text-body-md font-bold hover:bg-blue-subtle transition-colors" href="{{ route('login') }}">Masuk</a>
                    <a class="inline-flex items-center justify-center h-8 px-4 rounded-full bg-primary-container text-on-primary font-body-md text-body-md font-bold hover:bg-blue-hover transition-colors shadow-sm" href="{{ route('register') }}">Daftar</a>
                @else
                    <!-- Menu akun untuk pengguna yang sudah login -->
                    <div class="relative" x-data="accountMenu">
                        <button class="flex items-center gap-2 group" onclick="toggleAccountMenu()" type="button">
                            <div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center shrink-0">
                                <span class="font-caption text-caption font-bold text-on-primary uppercase">{{ mb_substr(auth()->user()->name, 0, 1) }}</span>
                            </div>
                            <span class="hidden lg:inline font-body-md text-body-md font-medium text-on-surface">{{ explode(' ', trim(auth()->user()->name))[0] }}</span>
                            <span class="material-symbols-outlined text-[16px] text-ink-muted hidden sm:inline">expand_more</span>
                        </button>
                        <div class="absolute right-0 top-11 w-60 bg-surface-card rounded-xl border border-border-default shadow-lg overflow-hidden hidden z-50" id="account-menu">
                            <div class="px-4 py-3 border-b border-border-default bg-surface-container-low">
                                <p class="font-body-md text-body-md font-bold text-on-surface truncate">{{ auth()->user()->name }}</p>
                                <p class="font-caption text-caption text-ink-muted truncate">{{ auth()->user()->email }}</p>
                                <span class="mt-1.5 inline-flex items-center px-2 py-0.5 rounded-full text-caption font-caption font-bold {{ auth()->user()->role === 'seller' ? 'bg-blue-subtle text-primary' : 'bg-[#EBF5D6] text-success-text' }}">
                                    {{ auth()->user()->role === 'seller' ? 'Penjual' : 'Pembeli' }}
                                </span>
                            </div>
                            <a class="flex items-center gap-2.5 px-4 py-2.5 font-body-md text-body-md text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface transition-colors" href="{{ route('profile.show') }}">
                                <span class="material-symbols-outlined text-[18px]">person</span> Profil Saya
                            </a>
                            <a class="flex items-center gap-2.5 px-4 py-2.5 font-body-md text-body-md text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface transition-colors" href="{{ route('orders.index') }}">
                                <span class="material-symbols-outlined text-[18px]">receipt_long</span> Pesanan Saya
                            </a>
                            <a class="flex items-center gap-2.5 px-4 py-2.5 font-body-md text-body-md text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface transition-colors" href="{{ route('addresses.index') }}">
                                <span class="material-symbols-outlined text-[18px]">location_on</span> Alamat Saya
                            </a>
                            @if (auth()->user()->role === 'seller')
                                <a class="flex items-center gap-2.5 px-4 py-2.5 font-body-md text-body-md text-on-surface-variant hover:bg-surface-container-low hover:text-on-surface transition-colors" href="#">
                                    <span class="material-symbols-outlined text-[18px]">storefront</span> Dasbor Penjual
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="w-full flex items-center gap-2.5 px-4 py-2.5 font-body-md text-body-md text-secondary hover:bg-error-container/50 transition-colors" type="submit">
                                    <span class="material-symbols-outlined text-[18px]">logout</span> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </header>

    <main class="w-full pt-12 min-h-screen bg-canvas-background pb-16 md:pb-0">
        @if (session('status'))
            <div class="max-w-[1280px] mx-auto px-4 pt-4">
                <div class="bg-tertiary-fixed/30 border border-tertiary-container/30 rounded-xl p-3 flex items-center gap-2.5">
                    <span class="material-symbols-outlined text-tertiary text-[20px]">check_circle</span>
                    <p class="font-body-md text-body-md font-medium text-tertiary">{{ session('status') }}</p>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="w-full bg-surface-card border-t border-border-default mt-10">
        <div class="max-w-[1280px] mx-auto px-4 py-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="space-y-3">
                    <div class="flex items-center gap-2">
                        <span class="font-headline-sm text-headline-sm font-bold">
                            <span class="text-primary">Lokal</span><span class="text-accent-yellow">k</span><span class="text-red-graphic">u</span>
                        </span>
                    </div>
                    <p class="font-body-md text-body-md text-on-surface-variant leading-relaxed">Platform pasar digital &amp; ekosistem POS pintar pendukung kemandirian UMKM Indonesia.</p>
                    <div class="flex items-center gap-2 text-ink-muted text-caption font-caption">
                        <span class="material-symbols-outlined text-[16px]">verified</span> Terintegrasi Sistem Pembayaran Nasional
                    </div>
                </div>
                <div class="space-y-2">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Toko &amp; Layanan</h3>
                    <p class="font-body-md text-body-md text-on-surface-variant font-medium">Lokalku Sentra UMKM Nusantara</p>
                    <p class="font-caption text-caption text-on-surface-variant flex items-start gap-1"><span class="material-symbols-outlined text-[16px] shrink-0 mt-0.5">location_on</span> Jl. Kebon Sirih No. 42, Gambir, Jakarta Pusat, 10110</p>
                    <p class="font-caption text-caption text-on-surface-variant flex items-center gap-1"><span class="material-symbols-outlined text-[16px] shrink-0">schedule</span> Buka Setiap Hari: 08:00 - 22:00 WIB</p>
                </div>
                <div class="space-y-2">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Tentang &amp; Informasi</h3>
                    <ul class="space-y-1.5 font-body-md text-body-md">
                        <li><a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Tentang Lokalku</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-colors" href="{{ route('register') }}">Gabung Mitra UMKM</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Kebijakan Privasi</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Syarat &amp; Ketentuan</a></li>
                    </ul>
                </div>
                <div class="space-y-2">
                    <h3 class="font-headline-sm text-headline-sm text-on-surface">Bantuan &amp; Hubungi</h3>
                    <ul class="space-y-1.5 font-body-md text-body-md">
                        <li><a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Pusat Bantuan (FAQ)</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Panduan Kasir POS</a></li>
                        <li><a class="text-on-surface-variant hover:text-primary transition-colors" href="#">Layanan Pelanggan WhatsApp</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-border-default mt-8 pt-4 flex flex-col sm:flex-row items-center justify-between gap-2 text-ink-muted font-caption text-caption">
                <p>© 2024 Lokalku. Hak Cipta Dilindungi Undang-Undang.</p>
                <p class="flex items-center gap-1"><span class="material-symbols-outlined text-[14px]">shield</span> Transaksi Aman &amp; Terenkripsi</p>
            </div>
        </div>
    </footer>

    <!-- Bottom nav khusus mobile -->
    <div class="fixed bottom-0 left-0 right-0 z-50 bg-surface-card border-t border-border-default md:hidden h-14">
        <nav class="grid grid-cols-4 h-full">
            <a aria-current="page" class="flex flex-col items-center justify-center gap-0.5 text-primary font-bold" href="{{ route('home') }}">
                <span class="material-symbols-outlined text-[20px]">home</span>
                <span class="font-caption text-caption">Beranda</span>
            </a>
            <a class="flex flex-col items-center justify-center text-on-surface-variant hover:text-on-surface gap-0.5" href="{{ route('products.index') }}">
                <span class="material-symbols-outlined text-[20px]">category</span>
                <span class="font-caption text-caption">Kategori</span>
            </a>
            <a class="relative flex flex-col items-center justify-center text-on-surface-variant hover:text-on-surface gap-0.5" href="{{ auth()->check() ? route('cart.index') : route('login') }}">
                <span class="material-symbols-outlined text-[20px]">shopping_cart</span>
                <span class="font-caption text-caption">Keranjang</span>
            </a>
            @guest
                <a class="flex flex-col items-center justify-center text-on-surface-variant hover:text-on-surface gap-0.5" href="{{ route('login') }}">
                    <span class="material-symbols-outlined text-[20px]">person</span>
                    <span class="font-caption text-caption">Masuk</span>
                </a>
            @else
                <a class="flex flex-col items-center justify-center text-on-surface-variant hover:text-on-surface gap-0.5" href="{{ route('profile.show') }}">
                    <span class="material-symbols-outlined text-[20px]">person</span>
                    <span class="font-caption text-caption">Akun</span>
                </a>
            @endguest
        </nav>
    </div>

    @stack('scripts')

    <script>
        function toggleAccountMenu() {
            document.getElementById('account-menu')?.classList.toggle('hidden');
        }
        document.addEventListener('click', (e) => {
            const menu = document.getElementById('account-menu');
            if (menu && !e.target.closest('button[onclick="toggleAccountMenu()"]') && !menu.contains(e.target)) {
                menu.classList.add('hidden');
            }
        });
    </script>

    <!-- Toast global untuk aksi tambah ke keranjang -->
    <div class="fixed bottom-20 md:bottom-8 right-4 z-50 transform translate-y-24 opacity-0 transition-all duration-300 pointer-events-none" id="cartToast">
        <div class="flex items-center gap-2.5 bg-inverse-surface text-inverse-on-surface px-4 py-3 rounded-xl shadow-xl">
            <span class="material-symbols-outlined text-tertiary-fixed text-[20px]">check_circle</span>
            <span class="font-body-md text-body-md font-medium" id="toastMessage">Produk ditambahkan ke keranjang!</span>
        </div>
    </div>

    <script>
        const REGISTER_URL = @json(route('register'));

        // Klik "Tambah ke Keranjang":
        // - Tamu  -> diarahkan ke halaman Daftar
        // - Login -> produk benar-benar masuk keranjang (badge ikut terupdate)
        document.addEventListener('click', async (e) => {
            const button = e.target.closest('.add-to-cart-btn');
            if (!button) return;

            e.preventDefault();

            @guest
                window.location.href = REGISTER_URL;
                return;
            @endguest

            const quantity = parseInt(button.dataset.qty ?? '1', 10) || 1;

            try {
                const response = await fetch(`{{ url('cart') }}/${button.dataset.productId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ quantity }),
                });

                const data = await response.json();

                if (!response.ok) {
                    showToast(data.message ?? 'Gagal menambahkan ke keranjang.');
                    return;
                }

                const badge = document.getElementById('cart-badge');
                if (badge) {
                    badge.textContent = data.count;
                    badge.classList.remove('hidden');
                    badge.classList.add('flex');
                }

                showToast(data.message ?? 'Produk ditambahkan ke keranjang!');
            } catch {
                showToast('Terjadi kesalahan. Coba lagi.');
            }
        });

        function showToast(message) {
            const toast = document.getElementById('cartToast');
            document.getElementById('toastMessage').textContent = message;
            toast.classList.remove('translate-y-24', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            clearTimeout(showToast._timeout);
            showToast._timeout = setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-24', 'opacity-0');
            }, 2200);
        }
    </script>
</body>
</html>
