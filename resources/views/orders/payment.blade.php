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
        <span id="payment-status-badge" class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 font-body text-xs font-medium {{ $statusBadge['class'] }}">
            <span class="material-symbols-outlined text-[14px]">{{ $paymentStatus === 'paid' ? 'check_circle' : ($paymentStatus === 'failed' ? 'cancel' : 'hourglass_top') }}</span>
            <span id="payment-status-text">{{ $statusBadge['label'] }}</span>
        </span>
    </div>

    {{-- Warning Panel --}}
    @if (!$isConfigured)
    <div class="flex items-start gap-3 rounded-2xl border border-accent-yellow/30 bg-accent-yellow/15 p-4">
        <span class="material-symbols-outlined text-[24px] text-on-accent-yellow">warning</span>
        <div>
            <p class="font-body text-sm font-semibold text-on-accent-yellow">Pembayaran Midtrans belum dikonfigurasi.</p>
            <p class="font-body text-sm text-ink-secondary">Isi <code class="font-mono text-xs">MIDTRANS_SERVER_KEY</code> &amp; <code class="font-mono text-xs">MIDTRANS_CLIENT_KEY</code> di <code class="font-mono text-xs">.env</code> untuk mengaktifkan pembayaran.</p>
        </div>
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
                        <span class="font-display text-xl font-bold text-primary">Rp {{ $payment?->amount ? number_format((float) $payment->amount, 0, ',', '.') : number_format((float) $order->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment Methods --}}
            <div class="rounded-2xl border border-outline-variant bg-surface-card p-6">
                <h2 class="font-display text-lg font-bold text-ink-primary">Metode Pembayaran</h2>
                <p class="mt-1 font-body text-sm text-ink-secondary">Pilih metode pembayaran yang tersedia di popup pembayaran.</p>
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                    <div class="flex items-center gap-3 rounded-xl border border-outline-variant bg-surface-container p-3">
                        <span class="material-symbols-outlined text-[24px] text-primary">qr_code_2</span>
                        <span class="font-body text-sm font-medium text-ink-primary">QRIS</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl border border-outline-variant bg-surface-container p-3">
                        <span class="material-symbols-outlined text-[24px] text-primary">smartphone</span>
                        <span class="font-body text-sm font-medium text-ink-primary">GoPay</span>
                    </div>
                    <div class="flex items-center gap-3 rounded-xl border border-outline-variant bg-surface-container p-3">
                        <span class="material-symbols-outlined text-[24px] text-primary">account_balance</span>
                        <span class="font-body text-sm font-medium text-ink-primary">Virtual Account</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column --}}
        <div class="space-y-6">
            <div class="rounded-2xl border border-outline-variant bg-surface-card p-6">
                <h2 class="font-display text-lg font-bold text-ink-primary">Selesaikan Pembayaran</h2>
                @if ($payment?->expires_at)
                <div class="mt-4 rounded-xl bg-blue-subtle/50 p-4 text-center">
                    <p class="font-body text-xs text-ink-secondary">Batas Waktu Pembayaran</p>
                    <p id="countdown-timer" class="font-mono text-xl font-bold text-primary" data-expires-at="{{ $payment->expires_at->toISOString() }}">--:--:--</p>
                </div>
                @endif
                <div class="mt-4 space-y-3">
                    <button id="pay-button" class="w-full rounded-xl bg-primary py-3.5 font-body text-sm font-semibold text-on-primary transition hover:bg-primary/90 disabled:cursor-not-allowed disabled:opacity-50" @if (!$isConfigured || $paymentStatus === 'paid' || $paymentStatus === 'expired') disabled @endif>
                        Bayar Sekarang
                    </button>
                    @if ($redirectUrl)
                    <a href="{{ $redirectUrl }}" target="_blank" rel="noopener noreferrer" class="flex w-full items-center justify-center gap-2 rounded-xl border border-outline-variant py-3.5 font-body text-sm font-semibold text-ink-primary transition hover:bg-surface-container">
                        <span class="material-symbols-outlined text-[18px]">open_in_new</span>
                        Bayar via Halaman Midtrans
                    </a>
                    @endif
                </div>
                <p class="mt-4 text-center font-body text-xs text-ink-secondary">Transaksi dilindungi oleh Midtrans.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if ($isConfigured)
<script src="https://app.{{ $isProduction ? '' : 'sandbox.' }}midtrans.com/snap/snap.js" data-client-key="{{ $clientKey }}"></script>
@endif
<script>
    const snapToken = @json($snapToken);
    const payButton = document.getElementById('pay-button');
    const statusBadge = document.getElementById('payment-status-badge');
    const statusText = document.getElementById('payment-status-text');

    // Countdown timer
    @if ($payment?->expires_at)
    const expiresAt = new Date("{{ $payment->expires_at->toISOString() }}");
    function updateCountdown() {
        const now = new Date();
        const diff = expiresAt - now;
        if (diff <= 0) {
            document.getElementById('countdown-timer').textContent = 'Kedaluwarsa';
            if (payButton) payButton.disabled = true;
            if (statusBadge) {
                statusBadge.className = 'inline-flex items-center gap-1.5 rounded-full px-3 py-1 font-body text-xs font-medium bg-surface-container text-ink-muted';
            }
            if (statusText) statusText.textContent = 'Kedaluwarsa';
            return;
        }
        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);
        document.getElementById('countdown-timer').textContent = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);
    @endif

    if (payButton) {
        payButton.addEventListener('click', async () => {
            let token = snapToken;
            if (!token) {
                try {
                    const res = await fetch('{{ route('payment.token', $order->order_code) }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                        },
                    });
                    const data = await res.json();
                    if (!res.ok) {
                        alert(data.message ?? 'Gagal membuat transaksi');
                        return;
                    }
                    token = data.token;
                } catch (e) {
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                    return;
                }
            }
            window.snap.pay(token, {
                // Callback Snap.js: tutup popup lalu kembali ke halaman order.
                // Status resmi tetap diverifikasi via webhook Midtrans.
                onSuccess: function (result) {
                    window.location.href = '{{ route('payment.finish') }}?order_id=' + encodeURIComponent(result.order_id ?? '') + '&transaction_status=settlement';
                },
                onPending: function (result) {
                    window.location.href = '{{ route('payment.finish') }}?order_id=' + encodeURIComponent(result.order_id ?? '') + '&transaction_status=pending';
                },
                onError: function (result) {
                    window.location.href = '{{ route('payment.finish') }}?order_id=' + encodeURIComponent(result.order_id ?? '') + '&transaction_status=error';
                },
                onClose: function () {
                    // Popup ditutup tanpa menyelesaikan pembayaran: halaman tetap,
                    // user bisa mencoba lagi atau bayar via halaman Midtrans.
                },
            });
        });
    }
</script>
@endpush
