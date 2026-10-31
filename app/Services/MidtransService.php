<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
    /**
     * Apakah kredensial Midtrans sudah diisi di .env.
     */
    public function isConfigured(): bool
    {
        return filled(config('midtrans.server_key')) && filled(config('midtrans.client_key'));
    }

    /**
     * Buat Snap transaction untuk sebuah payment dan simpan token/redirect URL.
     *
     * @return array{token: string, redirect_url: string}
     */
    public function createSnapTransaction(Payment $payment): array
    {
        $this->configure();

        $order = $payment->order;

        $params = [
            'transaction_details' => [
                'order_id' => $payment->provider_order_id,
                'gross_amount' => (int) round((float) $payment->amount),
            ],
            'customer_details' => [
                'first_name' => $order->customer_name ?? $order->user?->name,
                'email' => $order->user?->email,
                'phone' => $order->address?->phone,
            ],
            'item_details' => $order->items->map(fn ($item) => [
                'id' => (string) $item->product_id,
                'name' => $item->product_name,
                'price' => (int) $item->price,
                'quantity' => $item->quantity,
            ])->all(),
            'expiry' => [
                'unit' => 'hour',
                'duration' => (int) config('midtrans.expiry_hours', 24),
            ],
        ];

        $snap = Snap::createTransaction($params);

        $payment->forceFill([
            'snap_token' => $snap->token,
            'redirect_url' => $snap->redirect_url,
            'expires_at' => now()->addHours((int) config('midtrans.expiry_hours', 24)),
        ])->save();

        return [
            'token' => $snap->token,
            'redirect_url' => $snap->redirect_url,
        ];
    }

    /**
     * Verifikasi signature_key dari webhook Midtrans.
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . config('midtrans.server_key'));

        return hash_equals($expected, (string) $signatureKey);
    }

    /**
     * Sinkronkan status payment pending dengan menanyakan langsung ke API Midtrans.
     * Dipakai sebagai fallback bila webhook gagal/telat, dan untuk memulihkan
     * order yang notifikasinya sudah lewat batas retry Midtrans.
     *
     * @return bool true bila status berhasil diperbarui dari API.
     */
    public function syncFromMidtrans(Payment $payment): bool
    {
        if (! $this->isConfigured() || $payment->status !== Payment::STATUS_PENDING || blank($payment->provider_order_id)) {
            return false;
        }

        try {
            $this->configure();

            $transaction = \Midtrans\Transaction::status($payment->provider_order_id);

            $status = $this->mapTransactionStatus(
                (string) $transaction->transaction_status,
                (string) ($transaction->fraud_status ?? 'accept'),
            );

            if ($status === $payment->status) {
                return false;
            }

            $payment->forceFill([
                'status' => $status,
                'payment_type' => $transaction->payment_type ?? $payment->payment_type,
                'provider_transaction_id' => $transaction->transaction_id ?? $payment->provider_transaction_id,
                'amount_received' => $status === Payment::STATUS_PAID ? (string) ($transaction->gross_amount ?? $payment->amount) : $payment->amount_received,
                'paid_at' => $status === Payment::STATUS_PAID ? now() : $payment->paid_at,
            ])->save();

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

            $this->logWebhook('Status payment disinkronkan dari API Midtrans', [
                'order_id' => $payment->provider_order_id,
                'status' => $status,
            ]);

            return true;
        } catch (\Throwable $e) {
            $this->logWebhook('Gagal sinkron status dari API Midtrans', [
                'order_id' => $payment->provider_order_id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Terjemahkan transaction_status Midtrans menjadi status payment internal.
     */
    public function mapTransactionStatus(string $transactionStatus, string $fraudStatus): string
    {
        return match ($transactionStatus) {
            'capture' => $fraudStatus === 'challenge' ? Payment::STATUS_PENDING : Payment::STATUS_PAID,
            'settlement' => Payment::STATUS_PAID,
            'deny', 'cancel' => Payment::STATUS_FAILED,
            'expire' => Payment::STATUS_EXPIRED,
            default => Payment::STATUS_PENDING,
        };
    }

    private function configure(): void
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Catat log webhook (gagal/valid) tanpa melempar exception.
     */
    public function logWebhook(string $message, array $context = []): void
    {
        Log::info("[midtrans] {$message}", $context);
    }
}
