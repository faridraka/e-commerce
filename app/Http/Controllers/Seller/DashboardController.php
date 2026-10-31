<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dasbor Penjual: ringkasan performa toko hari ini.
     */
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $store = $user->store;

        $storeProducts = Product::query()->where('store_id', $store->id);
        $storeOrderIds = function ($query) use ($store) {
            $query->whereHas('items.product', fn ($q) => $q->where('store_id', $store->id));
        };

        $ordersQuery = Order::query()->where($storeOrderIds);

        $stats = [
            'revenue_today' => (clone $ordersQuery)
                ->whereHas('payments', fn ($p) => $p->where('status', 'paid')->whereDate('paid_at', today()))
                ->sum('total_amount'),
            'orders_today' => (clone $ordersQuery)->whereDate('created_at', today())->count(),
            'products_count' => (clone $storeProducts)->count(),
            'low_stock_count' => (clone $storeProducts)->whereBetween('stock', [1, 5])->count(),
        ];

        return view('seller.dashboard', [
            'store' => $store,
            'stats' => $stats,
            'recentOrders' => (clone $ordersQuery)->with(['items', 'payments'])->latest()->take(5)->get(),
            'lowStockProducts' => (clone $storeProducts)->with('category')->orderBy('stock')->take(5)->get(),
            'topProducts' => (clone $storeProducts)->orderByDesc('sold_count')->take(4)->get(),
        ]);
    }
}
