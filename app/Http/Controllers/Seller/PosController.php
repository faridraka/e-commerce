<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockMovement;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct(private MidtransService $midtrans)
    {
    }

    /**
     * Terminal Kasir POS: katalog produk toko + keranjang kasir.
     */
    public function terminal(Request $request): View
    {
        $store = $request->user()->store;

        $products = Product::query()
            ->with('category')
            ->where('store_id', $store->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('seller.pos', [
            'store' => $store,
            'products' => $products,
        ]);
    }

    /**
     * Selesaikan transaksi kasir: tunai (manual) atau virtual (Midtrans Snap).
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'payment_method' => ['required', 'in:cash,virtual'],
            'received_amount' => ['nullable', 'numeric', 'min:0'],
            'customer_name' => ['nullable', 'string', 'max:255'],
        ]);

        $store = $request->user()->store;

        // Harga dihitung ulang di server, tidak percaya data klien.
        $products = Product::query()
            ->where('store_id', $store->id)
            ->whereIn('id', collect($validated['items'])->pluck('product_id'))
            ->lockForUpdate()
            ->get()
            ->keyBy('id');

        if ($products->isEmpty()) {
            return response()->json(['message' => 'Produk tidak ditemukan di toko Anda.'], 422);
        }

        $lines = collect($validated['items'])
            ->filter(fn ($item) => $products->has($item['product_id']))
            ->map(fn ($item) => [
                'product' => $products[$item['product_id']],
                'quantity' => (int) $item['quantity'],
            ]);

        foreach ($lines as $line) {
            if ($line['product']->stock < $line['quantity']) {
                return response()->json([
                    'message' => "Stok {$line['product']->name} tidak mencukupi (tersisa {$line['product']->stock}).",
                ], 422);
            }
        }

        $total = $lines->sum(fn ($line) => (float) $line['product']->price * $line['quantity']);

        if ($validated['payment_method'] === 'cash' && (float) ($validated['received_amount'] ?? 0) < $total) {
            return response()->json([
                'message' => 'Nominal uang diterima kurang dari total transaksi.',
            ], 422);
        }

        $order = DB::transaction(function () use ($validated, $lines, $total, $request) {
            $isCash = $validated['payment_method'] === 'cash';

            $order = Order::create([
                'order_code' => Order::generateCode(),
                'user_id' => null,
                'address_id' => null,
                'channel' => 'pos',
                // Tunai dianggap lunas seketika; virtual menunggu pembayaran.
                'status' => $isCash ? Order::STATUS_DIBAYAR : Order::STATUS_MENUNGGU_PEMBAYARAN,
                'total_amount' => $total,
                'customer_name' => $validated['customer_name'] ?? 'Pelanggan Kasir',
            ]);

            foreach ($lines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'price' => $line['product']->price,
                    'quantity' => $line['quantity'],
                    'subtotal' => (float) $line['product']->price * $line['quantity'],
                ]);

                $line['product']->decrement('stock', $line['quantity']);

                $line['product']->movements()->create([
                    'order_id' => $order->id,
                    'type' => StockMovement::TYPE_SALE,
                    'quantity' => -$line['quantity'],
                    'note' => 'Penjualan kasir POS',
                ]);
            }

            $isCash = $validated['payment_method'] === 'cash';

            $order->payments()->create([
                'method' => $isCash ? 'manual' : 'virtual',
                'provider' => $isCash ? null : 'midtrans',
                'provider_order_id' => $order->order_code . '-1',
                'status' => $isCash ? Payment::STATUS_PAID : Payment::STATUS_PENDING,
                'amount' => $total,
                'amount_received' => $isCash ? (float) $validated['received_amount'] : null,
                'payment_type' => $isCash ? 'cash' : null,
                'paid_at' => $isCash ? now() : null,
            ]);

            return $order;
        });

        if ($validated['payment_method'] === 'cash') {
            return response()->json([
                'order_code' => $order->order_code,
                'redirect_url' => route('seller.pos.receipt', $order->order_code),
            ]);
        }

        // Virtual: siapkan Snap token bila Midtrans terkonfigurasi.
        $payment = $order->latestPayment();
        $snapToken = null;
        $redirectUrl = null;

        if ($this->midtrans->isConfigured()) {
            try {
                $snap = $this->midtrans->createSnapTransaction($payment);
                $snapToken = $snap['token'];
                $redirectUrl = $snap['redirect_url'];
            } catch (\Throwable $e) {
                $this->midtrans->logWebhook('Gagal membuat Snap token POS', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'order_code' => $order->order_code,
            'total' => (float) $total,
            'snap_token' => $snapToken,
            'redirect_url' => $redirectUrl,
            'midtrans_configured' => $this->midtrans->isConfigured(),
            'status_url' => route('seller.pos.payment-status', $order->order_code),
            'receipt_url' => route('seller.pos.receipt', $order->order_code),
        ]);
    }

    /**
     * Endpoint polling status pembayaran virtual (Snap).
     */
    public function paymentStatus(Request $request, Order $order): JsonResponse
    {
        abort_unless($this->belongsToStore($request, $order), 404);

        $payment = $order->latestPayment();

        if (! $payment) {
            return response()->json(['status' => 'not_found'], 404);
        }

        if ($payment->status === Payment::STATUS_PENDING && $this->midtrans->isConfigured()) {
            try {
                \Midtrans\Config::$serverKey = config('midtrans.server_key');
                \Midtrans\Config::$isProduction = (bool) config('midtrans.is_production');

                $transaction = \Midtrans\Transaction::status($payment->provider_order_id);
                $status = $this->midtrans->mapTransactionStatus(
                    (string) $transaction->transaction_status,
                    (string) ($transaction->fraud_status ?? 'accept'),
                );

                if ($status !== $payment->status) {
                    $payment->forceFill([
                        'status' => $status,
                        'payment_type' => $transaction->payment_type ?? $payment->payment_type,
                        'provider_transaction_id' => $transaction->transaction_id ?? $payment->provider_transaction_id,
                        'paid_at' => $status === Payment::STATUS_PAID ? now() : $payment->paid_at,
                    ])->save();

                    // Sinkronkan status pesanan hasil pembayaran.
                    if ($order->status === Order::STATUS_MENUNGGU_PEMBAYARAN) {
                        $order->forceFill(['status' => $status === Payment::STATUS_PAID
                            ? Order::STATUS_DIBAYAR
                            : Order::STATUS_KEDALUWARSA,
                        ])->save();
                    }
                }
            } catch (\Throwable $e) {
                $this->midtrans->logWebhook('Gagal cek status POS', ['error' => $e->getMessage()]);
            }
        }

        return response()->json([
            'status' => $payment->refresh()->status,
            'order_status' => $order->status,
        ]);
    }

    /**
     * Struk & Ringkasan Transaksi.
     */
    public function receipt(Request $request, Order $order): View
    {
        abort_unless($this->belongsToStore($request, $order), 404);

        $order->load(['items', 'payments']);

        return view('seller.pos-receipt', [
            'store' => $request->user()->store,
            'order' => $order,
            'payment' => $order->latestPayment(),
        ]);
    }

    /**
     * Riwayat Transaksi POS.
     */
    public function history(Request $request): View
    {
        $store = $request->user()->store;

        $orders = Order::query()
            ->with(['items', 'payments'])
            ->where('channel', 'pos')
            ->whereHas('items.product', fn ($q) => $q->where('store_id', $store->id))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('seller.pos-history', [
            'store' => $store,
            'orders' => $orders,
        ]);
    }

    /**
     * Pastikan pesanan kasir ini berisi produk milik toko penjual yang login.
     */
    private function belongsToStore(Request $request, Order $order): bool
    {
        if ($order->channel !== 'pos') {
            return false;
        }

        $storeId = $request->user()->store->id;

        return $order->items()->whereHas('product', fn ($q) => $q->where('store_id', $storeId))->exists();
    }
}
