<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code', 50)->unique();
            // Null untuk pelanggan walk-in POS.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // Null untuk transaksi POS (tanpa pengiriman).
            $table->foreignId('address_id')->nullable()->constrained()->nullOnDelete();
            // online | pos
            $table->string('channel', 20)->default('online');
            // menunggu_pembayaran | dibayar | diproses | dikirim | selesai | dibatalkan | kedaluwarsa
            $table->string('status', 30)->default('menunggu_pembayaran');
            $table->decimal('total_amount', 12, 2);
            $table->string('customer_name')->nullable();
            $table->string('shipping_tracking_number', 100)->nullable();
            $table->timestamps();

            $table->index(['channel', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
