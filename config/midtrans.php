<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
    // Batas waktu pembayaran (jam) untuk transaksi Snap.
    'expiry_hours' => env('MIDTRANS_EXPIRY_HOURS', 24),
];
