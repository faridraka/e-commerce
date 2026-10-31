<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    /**
     * Halaman Pembayaran Pesanan (konfirmasi manual).
     * Pembeli menandai sudah membayar; penjual memproses pesanan.
     */
    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load(['items', 'address', 'payments', 'items.product.store']);
        $payment = $order->latestPayment();

        return view('orders.payment', [
            'order' => $order,
            'payment' => $payment,
        ]);
    }

    /**
     * Konfirmasi pembayaran manual oleh pembeli.
     */
    public function confirm(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $payment = $order->latestPayment();

        if (! $payment || $payment->status !== Payment::STATUS_PENDING) {
            return redirect()->route('orders.show', $order->order_code)
                ->with('status', 'Pembayaran pesanan ini sudah diproses.');
        }

        $payment->forceFill([
            'status' => Payment::STATUS_PAID,
            'payment_type' => 'manual_confirmation',
            'paid_at' => now(),
        ])->save();

        if ($order->status === Order::STATUS_MENUNGGU_PEMBAYARAN) {
            $order->forceFill(['status' => Order::STATUS_DIBAYAR])->save();
        }

        return redirect()->route('orders.show', $order->order_code)
            ->with('status', 'Pembayaran dikonfirmasi. Pesanan Anda sedang diproses penjual.');
    }
}
