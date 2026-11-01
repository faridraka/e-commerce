@extends('layouts.print')

@section('title', 'Label Pengiriman ' . $order->order_code . ' - Lokalku')

@section('document')
@php
    $buyerName = $order->customer_name ?? $order->user?->name ?? 'Pembeli Tamu';
    $totalQty = (int) $order->items->sum('quantity');
@endphp
<div class="bg-white rounded-xl shadow-sm border-2 border-dashed border-slate-900 p-8">
    <!-- Baris atas: logo + kode -->
    <div class="flex flex-wrap items-center justify-between gap-3 pb-5 border-b border-slate-300">
        <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-full bg-slate-900 text-white flex items-center justify-center font-extrabold text-lg">L</div>
            <div>
                <p class="text-lg font-extrabold tracking-tight">LOKALKU</p>
                <p class="text-xs text-slate-500">Label Pengiriman · {{ $order->channel === 'pos' ? 'Kasir POS' : 'Pesanan Online' }}</p>
            </div>
        </div>
        <div class="text-right">
            <p class="font-mono font-bold text-lg">{{ $order->order_code }}</p>
            <p class="text-xs text-slate-500">{{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB</p>
        </div>
    </div>

    <!-- Pengirim dan penerima -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-5">
        <div class="border border-slate-300 rounded-lg p-4">
            <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mb-2">Dari (Pengirim)</p>
            <p class="text-sm font-bold text-slate-900">{{ $store->name }}</p>
            <p class="text-xs text-slate-500 mt-0.5">Kode Toko: {{ $store->activation_code ?? '—' }}</p>
        </div>
        <div class="border-2 border-slate-900 rounded-lg p-4">
            <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mb-2">Kepada (Penerima)</p>
            <p class="text-base font-extrabold text-slate-900">{{ $order->address?->recipient_name ?? $buyerName }}</p>
            <p class="text-sm font-bold text-slate-900 mt-0.5">{{ $order->address?->phone ?? '—' }}</p>
            <p class="text-sm text-slate-700 mt-1 leading-snug">
                @if ($order->address)
                    {{ $order->address->address_detail }}, {{ $order->address->city }} {{ $order->address->postal_code }}
                @else
                    —
                @endif
            </p>
        </div>
    </div>

    <!-- Resi dan isi -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4">
        <div class="border border-slate-300 rounded-lg p-4">
            <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mb-1">No. Resi / AWB</p>
            <p class="font-mono font-bold text-lg tracking-wider">{{ $order->shipping_tracking_number ?? '— BELUM ADA —' }}</p>
        </div>
        <div class="border border-slate-300 rounded-lg p-4">
            <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mb-1">Isi Paket</p>
            <p class="text-sm font-bold text-slate-900">{{ $order->items->count() }} jenis · {{ $totalQty }} barang</p>
            <ul class="text-xs text-slate-600 mt-1 space-y-0.5">
                @foreach ($order->items as $item)
                    <li>{{ $item->quantity }}× {{ $item->product_name }}</li>
                @endforeach
            </ul>
        </div>
    </div>

    <p class="mt-5 text-center text-[11px] text-slate-500">Tempel label ini pada paket. Pastikan nama penerima, alamat, dan nomor resi terbaca jelas oleh kurir.</p>
</div>
@endsection
