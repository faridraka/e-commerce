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

        $allOrdersQuery = Order::query()->where($belongsToStore);
        $onlineOrdersQuery = (clone $allOrdersQuery)->where('channel', 'online');
        $posOrdersQuery = (clone $allOrdersQuery)->where('channel', 'pos');

        // Pendapatan: pesanan pada periode terpilih yang pembayarannya lunas
        // (online + kasir walk-in POS).
        $paidInPeriod = fn ($query) => (clone $query)
            ->whereHas('payments', fn ($p) => $p->where('status', 'paid'))
            ->where('created_at', '>=', $start);

        $stats = [
            'period_label' => $label,
            'revenue' => (clone $paidInPeriod($allOrdersQuery))->sum('total_amount'),
            'online_revenue' => (clone $paidInPeriod($onlineOrdersQuery))->sum('total_amount'),
            'pos_revenue' => (clone $paidInPeriod($posOrdersQuery))->sum('total_amount'),
            'orders_count' => (clone $allOrdersQuery)->where('created_at', '>=', $start)->count(),
            'online_orders_count' => (clone $onlineOrdersQuery)->where('created_at', '>=', $start)->count(),
            'pos_orders_count' => (clone $posOrdersQuery)->where('created_at', '>=', $start)->count(),
            'products_count' => (clone $storeProducts)->count(),
            'low_stock_count' => (clone $storeProducts)->whereBetween('stock', [1, 5])->count(),
        ];

        // Rincian pesanan online yang menunggu aksi penjual (POS langsung selesai).
        $pendingCount = (clone $onlineOrdersQuery)->where('status', Order::STATUS_DIBAYAR)->count();
        $processedCount = (clone $onlineOrdersQuery)->where('status', Order::STATUS_DIPROSES)->count();

        // Pesanan terbaru: gabungan online + walk-in POS.
        $recentOrders = (clone $allOrdersQuery)
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
