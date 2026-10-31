<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Pesanan Online yang memuat produk milik toko penjual.
     */
    public function index(Request $request): View
    {
        $store = $request->user()->store;

        $orders = Order::query()
            ->with(['items', 'address', 'payments'])
            ->whereHas('items.product', fn ($q) => $q->where('store_id', $store->id))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('seller.orders', [
            'store' => $store,
            'orders' => $orders,
        ]);
    }

    /**
     * Detail Pesanan Online milik toko penjual.
     */
    public function show(Request $request, Order $order): View
    {
        $store = $request->user()->store;

        abort_unless(
            $order->items()->whereHas('product', fn ($q) => $q->where('store_id', $store->id))->exists(),
            404
        );

        $order->load(['items.product', 'address', 'payments', 'user']);

        return view('seller.order-detail', [
            'store' => $store,
            'order' => $order,
        ]);
    }

    /**
     * Perbarui status pesanan: diproses → dikirim → selesai.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $store = $request->user()->store;

        abort_unless(
            $order->items()->whereHas('product', fn ($q) => $q->where('store_id', $store->id))->exists(),
            404
        );

        $validated = $request->validate([
            'status' => ['required', 'in:' . Order::STATUS_DIPROSES . ',' . Order::STATUS_DIKIRIM . ',' . Order::STATUS_SELESAI],
            'tracking_number' => ['nullable', 'string', 'max:100'],
        ]);

        $order->forceFill([
            'status' => $validated['status'],
            'shipping_tracking_number' => $validated['tracking_number'] ?? $order->shipping_tracking_number,
        ])->save();

        return back()->with('status', "Status pesanan {$order->order_code} diperbarui menjadi {$validated['status']}.");
    }
}
