@extends('layouts.seller')

@section('title', 'Struk & Ringkasan Transaksi - Portal Penjual - Lokalku')

@section('content')
@php
    $totalAmount = (float) $order->total_amount;
    $payStatus = $payment?->status ?? 'paid';
    $isCash = $payment !== null && $payment->payment_type === 'cash';
    $received = (float) ($payment?->amount_received ?? 0);
    $showChange = $isCash && $received >= $totalAmount;
    $change = $received - $totalAmount;
    $ppn = (int) round($totalAmount - ($totalAmount / 1.11));
    $methodLabel = match ($payment?->method) {
        'manual' => 'Manual (Tunai)',
        default => '—',
    };
@endphp
<div class="flex flex-col w-full">
    <div class="w-full max-w-5xl mx-auto px-margin py-space-lg flex flex-col items-center gap-space-lg">

        <!-- Live Toast Message Area -->
        <div class="hidden transition-all duration-300 w-full max-w-md bg-inverse-surface text-inverse-on-surface py-2 px-4 rounded-full shadow-lg flex items-center justify-between gap-2" id="toastNotification">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-tertiary-fixed text-[20px]" id="toastIcon">check_circle</span>
                <span class="font-body-md text-body-md text-sm" id="toastMessage">Perintah cetak dikirim ke printer thermal.</span>
            </div>
            <button class="text-inverse-on-surface opacity-75 hover:opacity-100" onclick="hideToast()">
                <span class="material-symbols-outlined text-[16px]">close</span>
            </button>
        </div>

        <!-- Main Content Area: Centered Receipt -->
        <div class="w-full flex flex-col items-center justify-center relative">

            <!-- Zigzag Pattern Top Decoration (Perforated Thermal Receipt Cut) -->
            <div class="w-[360px] max-w-full overflow-hidden leading-none select-none flex items-end">
                <svg class="w-full h-3 text-surface-card fill-current drop-shadow-sm" preserveAspectRatio="none" viewBox="0 0 360 12">
                    <polygon points="0,12 6,0 12,12 18,0 24,12 30,0 36,12 42,0 48,12 54,0 60,12 66,0 72,12 78,0 84,12 90,0 96,12 102,0 108,12 114,0 120,12 126,0 132,12 138,0 144,12 150,0 156,12 162,0 168,12 174,0 180,12 186,0 192,12 198,0 204,12 210,0 216,12 222,0 228,12 234,0 240,12 246,0 252,12 258,0 264,12 270,0 276,12 282,0 288,12 294,0 300,12 306,0 312,12 318,0 324,12 330,0 336,12 342,0 348,12 354,0 360,12"></polygon>
                </svg>
            </div>

            <!-- Thermal Receipt Paper Card (360px wide) -->
            <div class="w-[360px] max-w-full bg-surface-card px-5 py-6 shadow-md relative transition-transform duration-200" id="thermalReceiptCard">
                <!-- Watermark/Receipt Accent Texture -->
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-surface-container-low/10 to-transparent pointer-events-none"></div>

                <!-- 1. Header -->
                <div class="flex flex-col items-center text-center relative z-10">
                    <div class="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center mb-2 shadow-sm">
                        <span class="material-symbols-outlined text-on-primary text-[22px]">storefront</span>
                    </div>
                    <span class="font-caption text-caption text-ink-muted uppercase tracking-wider">Struk Pembayaran Resmi — Transaksi Kasir Toko</span>
                    <h1 class="font-headline-md text-headline-md text-on-surface font-bold tracking-tight">{{ $store->name }}</h1>
                    <p class="font-body-md text-body-md text-xs text-on-surface-variant mt-1 leading-snug">
                        Toko: {{ $store->name }} • Kode Toko: {{ $store->activation_code }}
                    </p>
                </div>

                <!-- Dashed Separator -->
                <div class="my-3 border-b border-dashed border-outline-variant"></div>

                <!-- 2. Transaction Metadata -->
                <div class="flex flex-col gap-1.5 text-xs font-body-md relative z-10">
                    <div class="flex justify-between items-center">
                        <span class="text-ink-muted font-body-md text-body-md text-xs">Waktu</span>
                        <span class="font-code-mono text-code-mono text-xs text-on-surface">{{ $order->created_at->translatedFormat('d M Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-ink-muted font-body-md text-body-md text-xs">No. Transaksi</span>
                        <span class="font-code-mono text-code-mono text-xs font-bold text-primary">{{ $order->order_code }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-ink-muted font-body-md text-body-md text-xs">Kasir</span>
                        <span class="text-on-surface font-medium">{{ auth()->user()->name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-ink-muted font-body-md text-body-md text-xs">Pelanggan</span>
                        <span class="text-on-surface font-medium">{{ $order->customer_name ?? 'Pelanggan Walk-in' }}</span>
                    </div>
                </div>

                <!-- Dashed Separator -->
                <div class="my-3 border-b border-dashed border-outline-variant"></div>

                <!-- 3. Purchased Line Items -->
                <div class="flex flex-col gap-2 relative z-10">
                    @foreach ($order->items as $item)
                        <div class="flex flex-col {{ $loop->first ? '' : 'pt-1' }}">
                            <div class="flex justify-between items-start">
                                <span class="font-body-md text-body-md text-xs font-medium text-on-surface leading-tight max-w-[210px]">
                                    {{ $item->product_name }}
                                </span>
                                <span class="font-code-mono text-code-mono text-xs font-medium text-on-surface">
                                    Rp {{ number_format((float) $item->subtotal, 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="flex justify-between text-[11px] text-ink-muted font-code-mono">
                                <span class="">{{ $item->quantity }} × Rp {{ number_format((float) $item->price, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-tertiary font-bold tracking-tight">ID: #{{ $item->product_id }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Dashed Separator -->
                <div class="my-3 border-b border-dashed border-outline-variant"></div>

                <!-- 4. Totals & Payment Breakdown -->
                <div class="flex flex-col gap-1.5 relative z-10">
                    <div class="flex justify-between text-xs">
                        <span class="text-ink-muted">Subtotal ({{ $order->items->count() }} Item)</span>
                        <span class="font-code-mono text-code-mono text-xs text-on-surface">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-ink-muted">PPN Toko (11% Termasuk)</span>
                        <span class="font-code-mono text-code-mono text-xs text-ink-muted">Rp {{ number_format($ppn, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-baseline pt-1">
                        <span class="font-headline-sm text-headline-sm text-sm font-bold text-on-surface">TOTAL BELANJA</span>
                        <span class="font-headline-lg text-price-lg text-primary font-bold tracking-tight">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                    </div>

                    <!-- Micro Divider Inside Totals -->
                    <div class="my-2 border-b border-dashed border-outline-variant/60"></div>

                    <div class="flex justify-between text-xs">
                        <span class="text-ink-muted">Metode Bayar</span>
                        <span class="font-body-md font-medium text-on-surface">{{ $methodLabel }}</span>
                    </div>
                    @if ($showChange)
                        <div class="flex justify-between text-xs">
                            <span class="text-ink-muted">Tunai Diterima</span>
                            <span class="font-code-mono text-code-mono text-xs text-on-surface font-medium">Rp {{ number_format($received, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-xs">
                            <span class="text-success-text font-bold">KEMBALIAN</span>
                            <span class="font-code-mono text-code-mono text-sm font-bold text-success-text">Rp {{ number_format($change, 0, ',', '.') }}</span>
                        </div>
                    @endif

                    <!-- Status Badge -->
                    <div class="mt-2.5 flex justify-center">
                        @if ($payStatus === 'paid')
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-tertiary-fixed text-on-accent-yellow shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-tertiary font-bold">verified</span>
                                <span class="font-headline-sm text-caption font-bold tracking-wider uppercase">LUNAS</span>
                            </div>
                        @elseif ($payStatus === 'pending')
                            <div class="flex flex-col items-center gap-1.5">
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-accent-yellow text-on-accent-yellow shadow-sm">
                                    <span class="material-symbols-outlined text-[16px] font-bold">hourglass_top</span>
                                    <span class="font-headline-sm text-caption font-bold tracking-wider uppercase">Menunggu Pembayaran</span>
                                </div>
                                <span class="font-caption text-caption text-ink-muted">Konfirmasikan pembayaran dari halaman pesanan penjual</span>
                            </div>
                        @elseif ($payStatus === 'failed')
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-error-container text-on-error-container shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-error font-bold">error</span>
                                <span class="font-headline-sm text-caption font-bold tracking-wider uppercase">Gagal</span>
                            </div>
                        @else
                            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-surface-container-high text-ink-muted shadow-sm">
                                <span class="material-symbols-outlined text-[16px] font-bold">timer_off</span>
                                <span class="font-headline-sm text-caption font-bold tracking-wider uppercase">Kedaluwarsa</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Dashed Separator -->
                <div class="my-3 border-b border-dashed border-outline-variant"></div>

                <!-- 5. Receipt Footer & QR Code Barcode -->
                <div class="flex flex-col items-center text-center gap-2 relative z-10">
                    <!-- Barcode / QR Simulation Block -->
                    <div class="flex flex-col items-center bg-surface-container-low p-2.5 rounded-lg w-full">
                        <div class="flex items-center justify-center gap-0.5 h-10 w-full px-2 overflow-hidden bg-surface-card rounded py-1">
                            <!-- High-density simulated thermal barcode stripes -->
                            <span class="w-1 h-full bg-on-background"></span>
                            <span class="w-0.5 h-full bg-transparent"></span>
                            <span class="w-1.5 h-full bg-on-background"></span>
                            <span class="w-0.5 h-full bg-transparent"></span>
                            <span class="w-0.5 h-full bg-on-background"></span>
                            <span class="w-1 h-full bg-transparent"></span>
                            <span class="w-2 h-full bg-on-background"></span>
                            <span class="w-0.5 h-full bg-transparent"></span>
                            <span class="w-1 h-full bg-on-background"></span>
                            <span class="w-1.5 h-full bg-transparent"></span>
                            <span class="w-2 h-full bg-on-background"></span>
                            <span class="w-0.5 h-full bg-transparent"></span>
                            <span class="w-0.5 h-full bg-on-background"></span>
                            <span class="w-1 h-full bg-transparent"></span>
                            <span class="w-1.5 h-full bg-on-background"></span>
                            <span class="w-0.5 h-full bg-transparent"></span>
                            <span class="w-1 h-full bg-on-background"></span>
                            <span class="w-0.5 h-full bg-transparent"></span>
                            <span class="w-2 h-full bg-on-background"></span>
                            <span class="w-1 h-full bg-transparent"></span>
                            <span class="w-1 h-full bg-on-background"></span>
                            <span class="w-0.5 h-full bg-transparent"></span>
                            <span class="w-1.5 h-full bg-on-background"></span>
                            <span class="w-1 h-full bg-transparent"></span>
                            <span class="w-0.5 h-full bg-on-background"></span>
                            <span class="w-1 h-full bg-transparent"></span>
                            <span class="w-2 h-full bg-on-background"></span>
                        </div>
                        <span class="font-code-mono text-code-mono text-[10px] text-ink-muted mt-1.5 tracking-wider">
                            {{ $order->order_code }}
                        </span>
                    </div>
                    <p class="font-headline-sm text-caption font-bold text-on-surface mt-1">
                        Terima kasih telah berbelanja!
                    </p>
                    <p class="font-caption text-caption text-[10px] text-ink-muted leading-tight">
                        Barang yang sudah dibeli tidak dapat ditukar kecuali perjanjian terlebih dahulu. Simpan struk ini sebagai bukti pembayaran yang sah.
                    </p>
                    <div class="mt-1 flex items-center gap-1 text-[11px] font-medium text-primary">
                        <span class="">Dukungan Produk Lokal UMKM Indonesia</span>
                        <span class="text-sm">🇮🇩</span>
                    </div>
                </div>
            </div>

            <!-- Zigzag Pattern Bottom Decoration -->
            <div class="w-[360px] max-w-full overflow-hidden leading-none select-none flex items-start">
                <svg class="w-full h-3 text-surface-card fill-current drop-shadow-sm" preserveAspectRatio="none" viewBox="0 0 360 12">
                    <polygon points="0,0 6,12 12,0 18,12 24,0 30,12 36,0 42,12 48,0 54,12 60,0 66,12 72,0 78,12 84,0 90,12 96,0 102,12 108,0 114,12 120,0 126,12 132,0 138,12 144,0 150,12 156,0 162,12 168,0 174,12 180,0 186,12 192,0 198,12 204,0 210,12 216,0 222,12 228,0 234,12 240,0 246,12 252,0 258,12 264,0 270,12 276,0 282,12 288,0 294,12 300,0 306,12 312,0 318,12 324,0 330,12 336,0 342,12 348,0 354,12 360,0"></polygon>
                </svg>
            </div>
        </div>

        <!-- Digital Receipt Dispatch Card Modal/Flyout -->
        <div class="hidden w-[360px] max-w-full bg-surface-card rounded-xl p-space-lg shadow-md flex-col gap-space-md" id="digitalReceiptPanel">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-blue-subtle text-primary flex items-center justify-center">
                        <span class="material-symbols-outlined text-[18px]">share</span>
                    </div>
                    <span class="font-headline-sm text-headline-sm text-sm font-bold">Kirim Struk Digital</span>
                </div>
                <button class="text-ink-muted hover:text-on-surface" onclick="switchView('thermal')">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                </button>
            </div>
            <div class="flex flex-col gap-space-xs">
                <label class="font-caption text-caption text-on-surface-variant font-medium" for="waNumberInput">Nomor WhatsApp Pelanggan</label>
                <div class="flex items-center rounded-lg bg-surface-container-low px-3 py-2">
                    <span class="font-code-mono text-code-mono text-xs text-ink-muted mr-2">+62</span>
                    <input class="bg-transparent font-code-mono text-code-mono text-sm w-full outline-none text-on-surface" id="waNumberInput" placeholder="812xxxxxxx" type="tel">
                </div>
            </div>
            <div class="flex flex-col gap-space-xs">
                <label class="font-caption text-caption text-on-surface-variant font-medium" for="emailInput">Email Pelanggan (Opsional)</label>
                <div class="flex items-center rounded-lg bg-surface-container-low px-3 py-2">
                    <span class="material-symbols-outlined text-[16px] text-ink-muted mr-2">mail</span>
                    <input class="bg-transparent font-body-md text-xs w-full outline-none text-on-surface" id="emailInput" placeholder="pelanggan@domain.com" type="email">
                </div>
            </div>
            <button class="w-full h-11 rounded-full bg-tertiary-container hover:bg-tertiary text-on-tertiary font-medium flex items-center justify-center gap-2 transition-colors" onclick="sendDigitalReceipt()">
                <span class="material-symbols-outlined text-[18px]">send</span>
                <span class="">Kirim Sekarang</span>
            </button>
        </div>

        <!-- Below the Receipt: POS Action Buttons & Workflow Tools -->
        <div class="w-[360px] max-w-full flex flex-col gap-3">
            <!-- Primary Print Action -->
            <button class="w-full h-12 rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-headline-sm text-sm font-bold flex items-center justify-center gap-2 shadow-sm transition-all transform active:scale-[0.98]" onclick="handlePrintReceipt()">
                <span class="material-symbols-outlined text-[20px]">print</span>
                <span class="">Cetak Struk (Enter / F9)</span>
            </button>
            <!-- Secondary New Transaction Action -->
            <a class="w-full h-12 rounded-full bg-surface-card hover:bg-surface-container-low text-primary-container font-headline-sm text-sm font-bold flex items-center justify-center gap-2 shadow-sm transition-all transform active:scale-[0.98]" href="{{ route('seller.pos') }}">
                <span class="material-symbols-outlined text-[20px]">add_circle</span>
                <span class="">Transaksi Baru (F2)</span>
            </a>
            <!-- Utility Actions Bar -->
            <div class="flex flex-col gap-2 pt-2">
                <div class="grid grid-cols-2 gap-2">
                    <!-- WhatsApp Quick Trigger -->
                    <button class="h-10 px-3 rounded-full bg-tertiary-fixed hover:bg-tertiary-fixed-dim text-on-tertiary-fixed font-caption text-caption font-bold flex items-center justify-center gap-1.5 transition-colors" onclick="quickSendWA()">
                        <span class="material-symbols-outlined text-[16px] text-tertiary">chat</span>
                        <span class="">Kirim WhatsApp</span>
                    </button>
                    <!-- Email Trigger -->
                    <button class="h-10 px-3 rounded-full bg-blue-subtle hover:bg-primary-fixed text-primary font-caption text-caption font-bold flex items-center justify-center gap-1.5 transition-colors" onclick="quickSendEmail()">
                        <span class="material-symbols-outlined text-[16px]">alternate_email</span>
                        <span class="">Kirim Email</span>
                    </button>
                </div>
                <!-- Riwayat Transaksi Link -->
                <a class="h-11 rounded-full bg-surface-container-high hover:bg-surface-dim text-on-surface-variant hover:text-on-surface font-body-md text-xs font-medium flex items-center justify-center gap-1.5 transition-colors" href="{{ route('seller.pos.history') }}">
                    <span class="material-symbols-outlined text-[18px]">history</span>
                    <span class="">Riwayat Transaksi</span>
                </a>
                <!-- Back to POS Terminal Link -->
                <a class="h-11 rounded-full bg-surface-container-high hover:bg-surface-dim text-on-surface-variant hover:text-on-surface font-body-md text-xs font-medium flex items-center justify-center gap-1.5 transition-colors" href="{{ route('seller.pos') }}">
                    <span class="material-symbols-outlined text-[18px]">keyboard_backspace</span>
                    <span class="">Kembali ke Terminal Kasir (Esc)</span>
                </a>
            </div>
        </div>

        <!-- Keyboard Shortcuts Guide Strip -->
        <div class="w-[360px] max-w-full flex items-center justify-between text-ink-muted font-caption text-caption px-2 pt-1">
            <span class="flex items-center gap-1">
                <kbd class="px-1.5 py-0.5 bg-surface-container-high rounded text-[10px] font-code-mono text-on-surface">Enter</kbd> Cetak
            </span>
            <span class="flex items-center gap-1">
                <kbd class="px-1.5 py-0.5 bg-surface-container-high rounded text-[10px] font-code-mono text-on-surface">F2</kbd> Transaksi Baru
            </span>
            <span class="flex items-center gap-1">
                <kbd class="px-1.5 py-0.5 bg-surface-container-high rounded text-[10px] font-code-mono text-on-surface">Esc</kbd> Kasir
            </span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let toastTimeout = null;

    function showToast(msg, isSuccess = true) {
        const toast = document.getElementById('toastNotification');
        const toastMsg = document.getElementById('toastMessage');
        const toastIcon = document.getElementById('toastIcon');

        if (!toast) return;

        toastMsg.innerText = msg;
        toastIcon.innerText = isSuccess ? 'check_circle' : 'info';
        toast.classList.remove('hidden');

        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            toast.classList.add('hidden');
        }, 4000);
    }

    function hideToast() {
        const toast = document.getElementById('toastNotification');
        if (toast) toast.classList.add('hidden');
    }

    function switchView(view) {
        const panel = document.getElementById('digitalReceiptPanel');
        if (!panel) return;
        if (view === 'digital') {
            panel.classList.remove('hidden');
            panel.classList.add('flex');
        } else {
            panel.classList.add('hidden');
            panel.classList.remove('flex');
        }
    }

    function handlePrintReceipt() {
        showToast('Perintah cetak dikirim ke printer thermal.');
        window.print();
    }

    function quickSendWA() {
        switchView('digital');
        document.getElementById('waNumberInput').focus();
    }

    function quickSendEmail() {
        switchView('digital');
        document.getElementById('emailInput').focus();
    }

    function sendDigitalReceipt() {
        const wa = document.getElementById('waNumberInput').value.trim();
        const email = document.getElementById('emailInput').value.trim();
        if (!wa && !email) {
            showToast('Isi nomor WhatsApp atau email pelanggan dahulu.', false);
            return;
        }
        switchView('thermal');
        showToast('Struk digital {{ $order->order_code }} berhasil dikirim.');
    }

    // Hotkey struk: Enter/F9 cetak, F2 transaksi baru, Esc kembali ke kasir.
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === 'F9' || e.key === 'F11') {
            e.preventDefault();
            handlePrintReceipt();
        } else if (e.key === 'F2') {
            e.preventDefault();
            window.location = '{{ route('seller.pos') }}';
        } else if (e.key === 'Escape') {
            e.preventDefault();
            window.location = '{{ route('seller.pos') }}';
        }
    });
</script>
@endpush
