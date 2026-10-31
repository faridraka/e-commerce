@extends('layouts.app')

@section('title', 'Pembayaran Pesanan - Lokalku')

@section('content')
<div class="max-w-[1280px] w-full mx-auto px-4 py-6 md:py-8 space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('orders.index') }}" class="flex h-10 w-10 items-center justify-center rounded-full bg-surface-container text-ink-secondary transition hover:bg-surface-container-high">
                <span class="material-symbols-outlined text-[20px]">arrow_back</span>
            </a>
            <div>
                <h1 class="font-display text-2xl font-bold leading-tight text-ink-primary">Pembayaran Pesanan</h1>
                <p class="font-body text-sm text-ink-secondary">Selesaikan pembayaran untuk pesanan <span class="font-mono font-semibold text-primary">{{ $order->order_code }}</span></p>
            </div>
        </div>
        @php
            $paymentStatus = $payment?->status ?? 'pending';
            $statusBadge = match ($paymentStatus) {
                'paid' => ['label' => 'Lunas', 'class' => 'bg-tertiary-fixed/40 text-tertiary'],
                'failed' => ['label' => 'Gagal', 'class' => 'bg-error-container text-secondary'],
                'expired' => ['label' => 'Kedaluwarsa', 'class' => 'bg-surface-container text-ink-muted'],
                default => ['label' => 'Menunggu Pembayaran', 'class' => 'bg-accent-yellow/20 text-on-accent-yellow'],
            };
        @endphp
        <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 font-body text-xs font-medium {{ $statusBadge['class'] }}">
            <span class="material-symbols-outlined text-[14px]">{{ $paymentStatus === 'paid' ? 'check_circle' : 'hourglass_top' }}</span>
            <span>{{ $statusBadge['label'] }}</span>
        </span>
    </div>

    @if (session('status'))
    <div class="flex items-start gap-3 rounded-2xl border border-tertiary/30 bg-tertiary-fixed/20 p-4">
        <span class="material-symbols-outlined text-[24px] text-tertiary">check_circle</span>
        <p class="font-body text-sm font-medium text-ink-primary">{{ session('status') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left Column --}}
        <div class="space-y-6 lg:col-span-2">
            {{-- Order Summary --}}
            <div class="rounded-2xl border border-outline-variant bg-surface-card p-6">
                <h2 class="font-display text-lg font-bold text-ink-primary">Ringkasan Pesanan</h2>
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between border-b border-outline-variant pb-3">
                        <span class="font-body text-sm text-ink-secondary">Kode Pesanan</span>
                        <span class="font-mono text-sm font-semibold text-primary">{{ $order->order_code }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-outline-variant pb-3">
                        <span class="font-body text-sm text-ink-secondary">Jumlah Item</span>
                        <span class="font-body text-sm font-medium text-ink-primary">{{ $order->items->count() }} item</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="font-body text-sm font-semibold text-ink-primary">Total Pembayaran</span>
                        <span class="font-display text-xl font-bold text-primary">Rp {{ number_format((float) ($payment?->amount ?? $order->total_amount), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment Instructions --}}
            <div class="rounded-2xl border border-outline-variant bg-surface-card p-6">
                <h2 class="font-display text-lg font-bold text-ink-primary">Cara Pembayaran</h2>
                <p class="mt-1 font-body text-sm text-ink-secondary">Pilih salah satu cara di bawah untuk menyelesaikan pembayaran.</p>
                <ol class="mt-4 space-y-3">
                    <li class="flex items-start gap-3 rounded-xl bg-surface-container p-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary font-body text-sm font-bold text-on-primary">1</span>
                        <div>
                            <p class="font-body text-sm font-semibold text-ink-primary">Bayar langsung ke penjual</p>
                            <p class="mt-0.5 font-body text-sm text-ink-secondary">Transfer, tunai saat bertemu, atau sesuai kesepakatan dengan penjual.</p>
                        </div>
                    </li>
                    <li class="flex items-start gap-3 rounded-xl bg-surface-container p-4">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-primary font-body text-sm font-bold text-on-primary">2</span>
                        <div>
                            <p class="font-body text-sm font-semibold text-ink-primary">Tekan tombol konfirmasi di bawah</p>
                            <p class="mt-0.5 font-body text-sm text-ink-secondary">Setelah membayar, tandai pesanan sebagai sudah dibayar agar penjual segera memprosesnya.</p>
                        </div>
                    </li>
                </ol>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">
            <div class="rounded-2xl border border-outline-variant bg-surface-card p-6">
                <h2 class="font-display text-lg font-bold text-ink-primary">Selesaikan Pembayaran</h2>
                @if ($payment?->status === 'paid')
                <div class="mt-4 rounded-xl bg-tertiary-fixed/20 p-4 text-center">
                    <span class="material-symbols-outlined text-[40px] text-tertiary">check_circle</span>
                    <p class="mt-2 font-body text-sm font-semibold text-ink-primary">Pembayaran sudah dikonfirmasi.</p>
                    <p class="mt-0.5 font-body text-xs text-ink-secondary">Penjual sedang memproses pesanan Anda.</p>
                </div>
                <a href="{{ route('orders.show', $order->order_code) }}" class="mt-4 flex w-full items-center justify-center gap-2 rounded-xl bg-primary py-3.5 font-body text-sm font-semibold text-on-primary transition hover:bg-primary/90">
                    Lihat Detail Pesanan
                </a>
                @elseif ($paymentStatus === 'expired' || $paymentStatus === 'failed' || $paymentStatus === 'cancelled')
                <div class="mt-4 rounded-xl bg-surface-container p-4 text-center">
                    <p class="font-body text-sm font-semibold text-ink-primary">Pembayaran ini tidak aktif.</p>
                    <p class="mt-0.5 font-body text-xs text-ink-secondary">Silakan buat pesanan baru untuk membeli kembali.</p>
                </div>
                @else
                <div class="mt-4 rounded-xl bg-blue-subtle/50 p-4 text-center">
                    <p class="font-body text-xs text-ink-secondary">Total yang harus dibayar</p>
                    <p class="font-mono text-xl font-bold text-primary">Rp {{ number_format((float) ($payment?->amount ?? $order->total_amount), 0, ',', '.') }}</p>
                </div>
                <form method="POST" action="{{ route('payment.confirm', $order->order_code) }}" class="mt-4">
                    @csrf
                    <button type="submit" class="w-full rounded-xl bg-primary py-3.5 font-body text-sm font-semibold text-on-primary transition hover:bg-primary/90">
                        Saya Sudah Bayar
                    </button>
                </form>
                @endif
                <p class="mt-4 text-center font-body text-xs text-ink-secondary">Pembayaran terverifikasi langsung oleh penjual.</p>
            </div>
        </div>
    </div>
</div>
@endsection
