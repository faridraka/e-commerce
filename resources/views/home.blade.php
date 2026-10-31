@extends('layouts.app')

@section('title', 'Lokalku - UMKM Lokal & Kasir POS')

@section('content')
<div class="flex flex-col w-full">
    <div class="max-w-[1280px] w-full mx-auto px-4 py-6 md:py-8 space-y-8 md:space-y-12">

        <!-- 1. Hero Banner -->
        <section class="bg-surface-card rounded-2xl shadow-sm overflow-hidden">
            <div class="p-6 md:p-10 lg:p-12 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <div class="lg:col-span-7 flex flex-col items-start space-y-5">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-subtle text-primary font-caption text-caption tracking-wide uppercase">
                        <span class="w-2 h-2 rounded-full bg-primary-container animate-pulse"></span>
                        Sentra Pasar Digital UMKM
                    </div>
                    <h1 class="font-display text-display text-on-surface tracking-tight font-bold">
                        Belanja Produk Lokal, <br class="hidden sm:inline">Dukung UMKM Sekitarmu
                    </h1>
                    <p class="font-body-md text-body-md text-on-surface-variant max-w-xl leading-relaxed">
                        Pesan online, nikmati cita rasa dan kerajinan khas daerah, serta bayar praktis lewat transfer atau tunai saat bertemu.
                    </p>
                    <div class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full sm:w-auto">
                        <a class="inline-flex items-center justify-center h-[44px] px-8 rounded-full bg-primary-container text-on-primary font-body-md text-body-md font-bold hover:bg-blue-hover transition-colors shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2" href="{{ route('products.index') }}">
                            Mulai Belanja
                        </a>
                        <a class="inline-flex items-center justify-center h-[44px] px-6 rounded-full bg-surface-container-low text-primary font-body-md text-body-md font-bold hover:bg-blue-subtle transition-colors focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2" href="{{ route('register') }}">
                            Daftar Jadi Penjual
                        </a>
                    </div>
                    <div class="pt-4 grid grid-cols-1 sm:grid-cols-3 gap-3 w-full">
                        <div class="flex items-center gap-2 text-on-surface font-caption text-caption">
                            <span class="material-symbols-outlined text-[18px] text-tertiary-container" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                            <span class="font-medium">100% Produk Asli</span>
                        </div>
                        <div class="flex items-center gap-2 text-on-surface font-caption text-caption">
                            <span class="material-symbols-outlined text-[18px] text-tertiary-container" style="font-variation-settings: 'FILL' 1;">local_shipping</span>
                            <span class="font-medium">Pengiriman Terjamin</span>
                        </div>
                        <div class="flex items-center gap-2 text-on-surface font-caption text-caption">
                            <span class="material-symbols-outlined text-[18px] text-tertiary-container" style="font-variation-settings: 'FILL' 1;">handshake</span>
                            <span class="font-medium">Dukung Pengrajin</span>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-5 relative w-full">
                    <div class="relative overflow-hidden rounded-xl shadow-md aspect-[4/3] bg-surface-container-high group">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out" alt="Ragam produk UMKM nusantara" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAaGIaXqLGHMXcxJ4lHe-Mzxb7zEmKi2UbHUdxBwo1hGBz4qIKkaG8s_5EaLD3-ZVSVqi5Xrk1DThYT-L3gOVi8Y_ss840lS-BgBn818tRf76lCBFJwzoFxMoDK7DTUl_Br7RX34N71BaGElI08F6rQkVTtEwJsy_nCelkypuxEsSKJ1MdqNcnaCZLnc9drCMCeNlFNQty6ooFcbBjVg3dUOhG4HM1tYHfiohDFpwsU">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent pointer-events-none"></div>
                        <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-on-secondary text-caption font-caption bg-inverse-surface/80 backdrop-blur-sm px-3 py-1.5 rounded-lg">
                            <span class="flex items-center gap-1.5 font-medium">
                                <span class="material-symbols-outlined text-[16px] text-accent-yellow">verified</span> Kurasi Ragam Nusantara
                            </span>
                            <span class="text-tertiary-fixed font-bold">Teruji &amp; Halal</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 2. Category Chips Row -->
        <section class="w-full space-y-3" id="katalog">
            <div class="flex items-center justify-between">
                <h3 class="font-headline-sm text-headline-sm text-on-surface font-bold">Kategori Pilihan</h3>
                <span class="text-ink-muted font-caption text-caption hidden sm:inline">Pilih kategori untuk memfilter</span>
            </div>
            <div class="flex items-center gap-2.5 overflow-x-auto pb-2 scrollbar-none">
                <a href="{{ route('products.index') }}" class="shrink-0 h-8 px-4 inline-flex items-center rounded-full font-body-md text-caption font-bold bg-primary-container text-on-primary transition-all shadow-sm">
                    Semua
                </a>
                @foreach ($categories as $category)
                    <a href="{{ route('products.index', ['kategori' => $category->slug]) }}" class="shrink-0 h-8 px-4 inline-flex items-center rounded-full font-body-md text-caption font-medium bg-surface-card text-on-surface hover:text-primary transition-all shadow-sm">
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </section>

        <!-- 3. Section "Produk Terbaru" -->
        <section class="w-full space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-2">
                <div>
                    <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Produk Terbaru</h2>
                    <p class="font-body-md text-body-md text-on-surface-variant">Pilihan produk unggulan dari pelaku UMKM terkurasi nusantara</p>
                </div>
                <a class="inline-flex items-center gap-1 font-body-md text-body-md font-bold text-primary hover:text-blue-hover transition-colors group" href="{{ route('products.index') }}">
                    <span>Lihat Semua</span>
                    <span class="material-symbols-outlined text-[18px] group-hover:translate-x-0.5 transition-transform">arrow_forward</span>
                </a>
            </div>

            @if ($latestProducts->isEmpty())
                <!-- Komponen tidak ditemukan saat data kosong -->
                <div class="bg-surface-card rounded-xl border border-border-default p-6 shadow-sm">
                    <x-not-found
                        message="Belum ada produk yang tayang di etalase saat ini. Silakan kembali lagi nanti."
                        actionUrl="{{ route('home') }}"
                        actionLabel="Muat Ulang" />
                </div>
            @else
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 md:gap-4">
                    @foreach ($latestProducts as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Value Proposition Bento Strip -->
        <section class="bg-surface-card rounded-2xl p-6 md:p-8 shadow-sm">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-subtle text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">qr_code_scanner</span>
                    </div>
                    <div>
                        <h4 class="font-headline-sm text-headline-sm font-bold text-on-surface">Bayar Serba Mudah</h4>
                        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Bayar sesuai kesepakatan dengan penjual lalu konfirmasi dengan sekali klik.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-subtle text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">storefront</span>
                    </div>
                    <div>
                        <h4 class="font-headline-sm text-headline-sm font-bold text-on-surface">Sentra Kurasi Terpercaya</h4>
                        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Setiap UMKM diverifikasi legalitas dan standar mutu produknya secara berkala.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-subtle text-primary flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-[24px]">point_of_sale</span>
                    </div>
                    <div>
                        <h4 class="font-headline-sm text-headline-sm font-bold text-on-surface">Ekosistem POS Pintar</h4>
                        <p class="font-body-md text-body-md text-on-surface-variant mt-1">Terhubung langsung dengan inventaris toko fisik dan pencatatan kasir terintegrasi.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Toast kini global (lihat layouts/app.blade.php) -->
</div>
@endsection
