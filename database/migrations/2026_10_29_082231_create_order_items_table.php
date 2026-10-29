<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            // Nullable: riwayat tetap ada walau produk dihapus (PRD-2).
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');      // salinan saat transaksi
            $table->decimal('price', 12, 2);     // salinan saat transaksi
            $table->unsignedInteger('quantity');
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
