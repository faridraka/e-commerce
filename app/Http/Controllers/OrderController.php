<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    /**
     * Halaman Pesanan Saya (riwayat belanja).
     */
    public function index(Request $request): View
    {
        $orders = $request->user()->orders()
            ->with(['items', 'address', 'payments'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('orders.index', compact('orders'));
    }

    /**
     * Halaman Detail Pesanan milik user yang sedang login.
     */
    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load(['items.product', 'address', 'payments']);

        return view('orders.show', compact('order'));
    }

    /**
     * Pembeli mengonfirmasi pesanan telah diterima (status: dikirim → selesai).
     */
    public function complete(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);
        abort_unless($order->status === Order::STATUS_DIKIRIM, 404);

        $order->forceFill(['status' => Order::STATUS_SELESAI])->save();

        return back()->with('status', "Pesanan {$order->order_code} telah diselesaikan. Terima kasih!");
    }

    /**
     * Invoice digital pembeli: format faktur cetak dengan rincian item dan status.
     */
    public function invoice(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load(['items.product', 'address', 'payments']);

        return view('orders.invoice', compact('order'));
    }
}
