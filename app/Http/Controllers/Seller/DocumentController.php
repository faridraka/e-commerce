<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentController extends Controller
{
    /**
     * Halaman cetak invoice pesanan: format faktur standar dengan kop toko,
     * rincian item, biaya, dan status pelunasan.
     */
    public function invoice(Request $request, Order $order): View
    {
        $store = $request->user()->store;

        abort_unless($this->belongsToStore($order, $store->id), 404);

        $order->load(['items.product', 'address', 'payments', 'user']);

        return view('seller.documents.invoice', [
            'store' => $store,
            'order' => $order,
        ]);
    }

    /**
     * Halaman cetak label pengiriman: pengirim, penerima, kode pesanan, dan resi.
     */
    public function label(Request $request, Order $order): View
    {
        $store = $request->user()->store;

        abort_unless($this->belongsToStore($order, $store->id), 404);

        $order->load(['items', 'address', 'user']);

        return view('seller.documents.label', [
            'store' => $store,
            'order' => $order,
        ]);
    }

    /**
     * Pastikan pesanan ini memuat produk milik toko penjual yang login.
     */
    private function belongsToStore(Order $order, int $storeId): bool
    {
        return $order->items()->whereHas('product', fn ($q) => $q->where('store_id', $storeId))->exists();
    }
}
