<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private MidtransService $midtrans)
    {
    }

    /**
     * Halaman Pembayaran Pesanan (Midtrans Snap).
     */
    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $order->load(['items', 'address', 'payments']);
        $payment = $order->latestPayment();

        // Sinkronkan status ke API Midtrans saat halaman dibuka: memulihkan
        // order yang notifikasinya gagal/telat tanpa menunggu webhook.
        if ($payment && $payment->status === Payment::STATUS_PENDING) {
            $this->midtrans->syncFromMidtrans($payment);
            $payment->refresh();
        }

        // Buat token Snap saat pertama kali halaman dibuka (bila Midtrans terkonfigurasi).
        if ($this->midtrans->isConfigured() && $payment && $payment->status === Payment::STATUS_PENDING && blank($payment->snap_token)) {
            try {
                $this->midtrans->createSnapTransaction($payment);
                $payment->refresh();
            } catch (\Throwable $e) {
                $this->midtrans->logWebhook('Gagal membuat Snap token', ['error' => $e->getMessage()]);
            }
        }

        return view('orders.payment', [
            'order' => $order,
            'payment' => $payment,
            'snapToken' => $payment?->snap_token,
            'redirectUrl' => $payment?->redirect_url,
            'isConfigured' => $this->midtrans->isConfigured(),
            'clientKey' => (string) config('midtrans.client_key'),
            'isProduction' => (bool) config('midtrans.is_production'),
        ]);
    }

    /**
     * Redirect balik dari Midtrans Snap setelah user selesai membayar/batal.
     * Memetakan provider_order_id (order_id Midtrans) ke kode pesanan internal,
     * lalu mengarahkan ke halaman order pembeli. Notifikasi status resmi tetap
     * diurus webhook agar tidak bergantung pada browser user.
     */
    public function finish(Request $request): RedirectResponse
    {
        $orderId = (string) $request->query('order_id', '');

        $payment = Payment::where('provider_order_id', $orderId)->first();

        if (! $payment) {
            return redirect()->route('orders.index');
        }

        $order = $payment->order;

        abort_unless($order && $order->user_id === $request->user()->id, 404);

        $transactionStatus = (string) $request->query('transaction_status', '');

        if ($transactionStatus === 'settlement' || $transactionStatus === 'capture') {
            return redirect()->route('payment.show', $order->order_code)
                ->with('status', 'Pembayaran berhasil. Menunggu konfirmasi Midtrans.');
        }

        if ($transactionStatus === 'pending') {
            return redirect()->route('payment.show', $order->order_code)
                ->with('status', 'Menunggu pembayaran Anda diselesaikan.');
        }

        return redirect()->route('orders.show', $order->order_code);
    }

    /**
     * Endpoint JSON: minta Snap token baru untuk pesanan ini.
     */
    public function snapToken(Request $request, Order $order): JsonResponse
    {
        abort_unless($order->user_id === $request->user()->id, 404);

        $payment = $order->latestPayment();

        if (! $payment) {
            return response()->json(['message' => 'Belum ada percobaan pembayaran untuk pesanan ini.'], 404);
        }

        if ($payment->status !== Payment::STATUS_PENDING) {
            return response()->json(['message' => 'Pembayaran pesanan ini sudah diproses.'], 409);
        }

        try {
            $snap = $this->midtrans->createSnapTransaction($payment);

            return response()->json([
                'token' => $snap['token'],
                'redirect_url' => $snap['redirect_url'],
            ]);
        } catch (\Throwable $e) {
            $this->midtrans->logWebhook('Gagal membuat Snap token', ['error' => $e->getMessage()]);

            return response()->json(['message' => 'Gagal membuat transaksi Midtrans. Coba lagi nanti.'], 502);
        }
    }

    /**
     * Webhook notifikasi Midtrans (tanpa CSRF):
     * POST /api/midtrans/notification
     */
    public function webhook(Request $request): JsonResponse
    {
        $payload = $request->all();

        if (! $this->midtrans->isConfigured()) {
            Log::warning('[midtrans] Webhook diterima tapi kredensial belum dikonfigurasi.');

            return response()->json(['message' => 'Midtrans belum dikonfigurasi.'], 503);
        }

        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        if (! $this->midtrans->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('[midtrans] Signature webhook tidak valid.', [
                'order_id' => $orderId,
                'status_code' => $statusCode,
                'gross_amount' => $grossAmount,
                'key_last6' => substr((string) config('midtrans.server_key'), -6),
            ]);

            // Fallback otoritatif: daripada membuang notifikasi, tanyakan status
            // langsung ke API Midtrans (autentikasi server key via HTTPS).
            $payment = Payment::where('provider_order_id', $orderId)->first();

            if ($payment) {
                $this->midtrans->syncFromMidtrans($payment);
            }

            return response()->json(['message' => 'Signature tidak valid.'], 403);
        }

        $payment = Payment::where('provider_order_id', $orderId)->first();

        if (! $payment) {
            Log::warning('[midtrans] Webhook untuk order_id yang tidak dikenal.', ['order_id' => $orderId]);

            return response()->json(['message' => 'Transaksi tidak ditemukan.'], 404);
        }

        $status = $this->midtrans->mapTransactionStatus(
            (string) ($payload['transaction_status'] ?? ''),
            (string) ($payload['fraud_status'] ?? 'accept'),
        );

        $payment->forceFill([
            'status' => $status,
            'payment_type' => $payload['payment_type'] ?? $payment->payment_type,
            'provider_transaction_id' => $payload['transaction_id'] ?? $payment->provider_transaction_id,
            'amount_received' => $status === Payment::STATUS_PAID ? $grossAmount : $payment->amount_received,
            'paid_at' => $status === Payment::STATUS_PAID ? now() : $payment->paid_at,
        ])->save();

        // Sinkronkan status pesanan.
        $order = $payment->order;
        $orderStatus = match ($status) {
            Payment::STATUS_PAID => Order::STATUS_DIBAYAR,
            Payment::STATUS_EXPIRED => Order::STATUS_KEDALUWARSA,
            Payment::STATUS_FAILED => Order::STATUS_DIBATALKAN,
            default => $order->status,
        };

        if ($order->status === Order::STATUS_MENUNGGU_PEMBAYARAN && $orderStatus !== $order->status) {
            $order->forceFill(['status' => $orderStatus])->save();
        }

        $this->midtrans->logWebhook('Status pembayaran diperbarui', [
            'order_id' => $orderId,
            'status' => $status,
        ]);

        return response()->json(['message' => 'OK']);
    }
}
