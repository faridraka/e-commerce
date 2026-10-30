<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('store_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('original_price', 12, 2)->nullable()->after('price');
            $table->unsignedInteger('sold_count')->default(0)->after('stock');
            $table->decimal('rating', 2, 1)->default(0.0)->after('sold_count');
            $table->string('origin')->nullable()->after('rating');
            // URL gambar produk bisa panjang, perluas dari varchar(255).
            $table->text('image')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['store_id']);
            $table->dropColumn(['store_id', 'original_price', 'sold_count', 'rating', 'origin']);
        });
    }
};
