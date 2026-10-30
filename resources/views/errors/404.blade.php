@extends('layouts.app')

@section('title', 'Halaman Tidak Ditemukan - Lokalku')

@section('content')
<main class="w-full bg-canvas-background">
    <div class="max-w-[1280px] w-full mx-auto px-4 py-16 md:py-24">
        <div class="bg-surface-card rounded-2xl shadow-sm p-6 md:p-10">
            <x-not-found
                title="Halaman Tidak Ditemukan"
                message="Halaman atau data yang Anda cari tidak tersedia. Mungkin sudah dipindahkan, dihapus, atau alamatnya salah ketik."
                actionUrl="{{ url('/') }}"
                actionLabel="Kembali ke Beranda" />
        </div>
    </div>
</main>
@endsection
