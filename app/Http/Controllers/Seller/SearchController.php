<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Live search navbar seller: cari pesanan online dan produk milik toko.
     * Mengembalikan daftar saran dengan URL tujuan masing-masing.
     */
    public function suggest(Request $request): JsonResponse
    {
        $store = $request->user()->store;
        $query = trim((string) $request->query('q', ''));

        if (mb_strlen($query) < 2) {
            return response()->json(['orders' => [], 'products' => []]);
        }

        $orders = Order::query()
            ->where('channel', 'online')
            ->whereHas('items.product', fn ($q) => $q->where('store_id', $store->id))
            ->where(function ($q) use ($query) {
                $q->where('order_code', 'like', "%{$query}%")
                    ->orWhere('customer_name', 'like', "%{$query}%")
                    ->orWhere('shipping_tracking_number', 'like', "%{$query}%");
            })
            ->latest()
            ->take(5)
            ->get(['id', 'order_code', 'status', 'total_amount', 'customer_name'])
            ->map(fn ($order) => [
                'code' => $order->order_code,
                'label' => $order->customer_name ?? 'Tanpa nama',
                'status' => $order->status,
                'total' => (float) $order->total_amount,
                'url' => route('seller.orders.show', $order->order_code),
            ]);

        $products = Product::query()
            ->where('store_id', $store->id)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('slug', 'like', "%{$query}%");
            })
            ->orderBy('name')
            ->take(5)
            ->get(['id', 'name', 'price', 'stock', 'slug'])
            ->map(fn ($product) => [
                'name' => $product->name,
                'price' => (float) $product->price,
                'stock' => (int) $product->stock,
                'url' => route('seller.products.edit', $product->slug),
            ]);

        return response()->json([
            'orders' => $orders,
            'products' => $products,
        ]);
    }
}
