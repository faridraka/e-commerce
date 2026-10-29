<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Satu pesanan boleh punya beberapa percobaan pembayaran (PAY-5).
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            // virtual | manual
            $table->string('method', 20);
            // midtrans | null (manual)
            $table->string('provider', 30)->nullable();
            // order_id yang dikirim ke Midtrans. Harus unik per transaksi, jadi
            // bukan order_code mentah (mis. ORD-20261029-0142-1, -2, ...).
            // Unik juga agar notifikasi webhook ganda tidak membuat data ganda.
            $table->string('provider_order_id')->nullable()->unique();
            // transaction_id dari Midtrans (diisi dari webhook / cek status).
            $table->string('provider_transaction_id')->nullable()->index();
            // Midtrans Snap: dipakai untuk "Lanjutkan Pembayaran" (PAY-5)
            // dan untuk menampilkan QR/popup di kasir POS (POS-5).
            $table->string('snap_token')->nullable();
            $table->text('redirect_url')->nullable();
            // Metode sebenarnya yang dipilih pelanggan: qris, bank_transfer, gopay, dll.
            $table->string('payment_type', 50)->nullable();
            // Batas waktu pembayaran (untuk countdown dan penanganan kedaluwarsa).
            $table->timestamp('expires_at')->nullable();
            // pending | paid | failed | expired | cancelled
            $table->string('status', 20)->default('pending');
            $table->decimal('amount', 12, 2);
            $table->decimal('amount_received', 12, 2)->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
