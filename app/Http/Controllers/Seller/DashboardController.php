<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dasbor Penjual: ringkasan performa toko berdasarkan periode terpilih.
     */
    public function __invoke(Request $request): View
    {
        $store = $request->user()->store;

        $period = $request->query('period', '30d');
        abort_unless(in_array($period, ['today', '7d', '30d', 'month'], true), 404);

        [$start, $label] = match ($period) {
            'today' => [now()->startOfDay(), 'Hari Ini'],
            '7d' => [now()->subDays(6)->startOfDay(), '7 Hari Terakhir'],
            '30d' => [now()->subDays(29)->startOfDay(), '30 Hari Terakhir'],
            'month' => [now()->startOfMonth(), 'Bulan Ini'],
        };

        $storeProducts = Product::query()->where('store_id', $store->id);
        $belongsToStore = function ($query) use ($store) {
            $query->whereHas('items.product', fn ($q) => $q->where('store_id', $store->id));
        };

        $ordersQuery = Order::query()->where($belongsToStore);

        // Pendapatan: pesanan pada periode terpilih yang pembayarannya lunas.
        $revenueQuery = (clone $ordersQuery)
            ->whereHas('payments', fn ($p) => $p->where('status', 'paid'))
            ->where('created_at', '>=', $start);

        $stats = [
            'period_label' => $label,
            'revenue' => (clone $revenueQuery)->sum('total_amount'),
            'orders_count' => (clone $ordersQuery)->where('created_at', '>=', $start)->count(),
            'products_count' => (clone $storeProducts)->count(),
            'low_stock_count' => (clone $storeProducts)->whereBetween('stock', [1, 5])->count(),
        ];

        // Rincian pesanan yang menunggu aksi penjual.
        $pendingCount = (clone $ordersQuery)->where('status', Order::STATUS_DIBAYAR)->count();
        $processedCount = (clone $ordersQuery)->where('status', Order::STATUS_DIPROSES)->count();

        $recentOrders = (clone $ordersQuery)
            ->with(['items', 'payments', 'user'])
            ->latest()
            ->take(5)
            ->get();

        return view('seller.dashboard', [
            'store' => $store,
            'stats' => $stats,
            'period' => $period,
            'pendingCount' => $pendingCount,
            'processedCount' => $processedCount,
            'recentOrders' => $recentOrders,
            'lowStockProducts' => (clone $storeProducts)->with('category')->whereBetween('stock', [1, 5])->orderBy('stock')->take(5)->get(),
            'topProducts' => (clone $storeProducts)->where('sold_count', '>', 0)->orderByDesc('sold_count')->take(4)->get(),
        ]);
    }
}
