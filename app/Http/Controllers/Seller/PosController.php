<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    public function __construct()
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
     * Selesaikan transaksi kasir: pembayaran tunai.
     */
    public function checkout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'payment_method' => ['required', 'in:cash'],
            'received_amount' => ['required', 'numeric', 'min:0'],
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

        $order = DB::transaction(function () use ($validated, $lines, $total) {
            $order = Order::create([
                'order_code' => Order::generateCode(),
                'user_id' => null,
                'address_id' => null,
                'channel' => 'pos',
                // Tunai dianggap lunas seketika.
                'status' => Order::STATUS_DIBAYAR,
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

            $order->payments()->create([
                'method' => 'manual',
                'provider' => null,
                'provider_order_id' => $order->order_code . '-1',
                'status' => Payment::STATUS_PAID,
                'amount' => $total,
                'amount_received' => (float) $validated['received_amount'],
                'payment_type' => 'cash',
                'paid_at' => now(),
            ]);

            return $order;
        });

        return response()->json([
            'order_code' => $order->order_code,
            'redirect_url' => route('seller.pos.receipt', $order->order_code),
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
