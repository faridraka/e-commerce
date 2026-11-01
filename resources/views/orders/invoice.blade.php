@extends('layouts.print')

@section('title', 'Invoice ' . $order->order_code . ' - Lokalku')

@section('document')
@php
    $payment = $order->payments->firstWhere('status', 'paid') ?? $order->payments->first();
    $storeName = $order->items->first()?->product?->store?->name ?? 'Penjual Lokalku';
    $itemsSubtotal = (float) $order->items->sum('subtotal');
    $shippingCost = max((float) $order->total_amount - $itemsSubtotal, 0);
    $paid = $payment && $payment->status === 'paid';
    $payDate = $payment?->paid_at ?? $payment?->created_at;
    $methodLabel = match ($payment?->payment_type ?? $payment?->method) {
        'cash' => 'Tunai (POS)',
        'manual' => 'Pembayaran Manual',
        default => ($payment?->method ?? '—'),
    };
@endphp
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
    <!-- Kop faktur -->
    <div class="flex flex-wrap items-start justify-between gap-4 pb-6 border-b-2 border-slate-900">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500 font-bold">Lokalku · Marketplace UMKM</p>
            <h1 class="text-2xl font-extrabold text-slate-900 mt-1">INVOICE</h1>
            <p class="font-mono text-sm text-slate-700 mt-1">{{ $order->order_code }}</p>
        </div>
        <div class="text-right">
            <p class="text-sm font-bold text-slate-900">{{ $storeName }}</p>
            <p class="text-xs text-slate-500 mt-2">Diterbitkan: {{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB</p>
            <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold {{ $paid ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-800' }}">
                {{ $paid ? 'LUNAS' : 'BELUM LUNAS' }}
            </span>
        </div>
    </div>

    <!-- Pembeli dan pembayaran -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 py-6 border-b border-slate-200">
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mb-2">Ditagihkan Kepada</p>
            <p class="text-sm font-bold text-slate-900">{{ $order->user?->name ?? $order->customer_name ?? 'Pembeli' }}</p>
            @if ($order->user?->email)
                <p class="text-xs text-slate-500">{{ $order->user->email }}</p>
            @endif
            @if ($order->address)
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                    {{ $order->address->recipient_name }} ({{ $order->address->phone }})<br>
                    {{ $order->address->address_detail }}, {{ $order->address->city }} {{ $order->address->postal_code }}
                </p>
            @endif
        </div>
        <div>
            <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mb-2">Detail Pembayaran</p>
            <dl class="text-xs text-slate-600 space-y-1">
                <div class="flex justify-between gap-3"><dt>Metode</dt><dd class="font-semibold text-slate-900 text-right">{{ $methodLabel }}</dd></div>
                <div class="flex justify-between gap-3"><dt>Waktu Lunas</dt><dd class="text-slate-900 text-right">{{ $payDate ? $payDate->translatedFormat('d M Y, H:i') . ' WIB' : '—' }}</dd></div>
                @if ($order->shipping_tracking_number)
                    <div class="flex justify-between gap-3"><dt>No. Resi</dt><dd class="font-mono text-slate-900 text-right">{{ $order->shipping_tracking_number }}</dd></div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Tabel item -->
    <table class="w-full text-sm mt-6">
        <thead>
            <tr class="text-left text-xs uppercase tracking-wider text-slate-500 border-b border-slate-200">
                <th class="py-2 pr-2 font-bold">No</th>
                <th class="py-2 pr-2 font-bold">Produk</th>
                <th class="py-2 pr-2 font-bold text-right">Harga</th>
                <th class="py-2 pr-2 font-bold text-right">Qty</th>
                <th class="py-2 font-bold text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @foreach ($order->items as $i => $item)
                <tr>
                    <td class="py-2.5 pr-2 text-slate-500">{{ $i + 1 }}</td>
                    <td class="py-2.5 pr-2 font-semibold text-slate-900">{{ $item->product_name }}</td>
                    <td class="py-2.5 pr-2 text-right font-mono text-slate-700">Rp {{ number_format((float) $item->price, 0, ',', '.') }}</td>
                    <td class="py-2.5 pr-2 text-right font-mono text-slate-700">{{ $item->quantity }}</td>
                    <td class="py-2.5 text-right font-mono font-bold text-slate-900">Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Ringkasan biaya -->
    <div class="flex justify-end mt-6">
        <div class="w-full sm:w-72 text-sm space-y-1.5">
            <div class="flex justify-between text-slate-600"><span>Subtotal ({{ $order->items->sum('quantity') }} barang)</span><span class="font-mono">Rp {{ number_format($itemsSubtotal, 0, ',', '.') }}</span></div>
            <div class="flex justify-between text-slate-600"><span>Ongkos kirim &amp; layanan</span><span class="font-mono">{{ $shippingCost > 0 ? 'Rp ' . number_format($shippingCost, 0, ',', '.') : 'Gratis' }}</span></div>
            <div class="flex justify-between items-baseline pt-2 border-t-2 border-slate-900"><span class="font-bold text-slate-900">TOTAL TAGIHAN</span><span class="font-mono font-extrabold text-lg text-slate-900">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span></div>
        </div>
    </div>

    <div class="mt-8 pt-4 border-t border-slate-200 text-[11px] text-slate-500 leading-relaxed">
        <p>Dokumen ini adalah bukti transaksi elektronik yang sah dari Lokalku untuk pesanan {{ $order->order_code }}.</p>
        <p class="mt-1">Simpan invoice ini sebagai bukti pembelian Anda.</p>
    </div>
</div>
@endsection
