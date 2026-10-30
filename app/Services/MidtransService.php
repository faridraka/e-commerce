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
