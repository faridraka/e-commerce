<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Tambahkan produk ke keranjang user yang sedang login.
     * Tamu diarahkan ke halaman daftar oleh sisi klien.
     */
    public function store(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
        ]);

        if ($product->stock < 1) {
            return response()->json([
                'message' => 'Stok produk sedang habis.',
            ], 409);
        }

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $quantity = $validated['quantity'] ?? 1;

        $item = $cart->items()->where('product_id', $product->id)->first();
        $newQuantity = min(($item?->quantity ?? 0) + $quantity, $product->stock);

        if ($item) {
            $item->update(['quantity' => $newQuantity]);
        } else {
            $cart->items()->create([
                'product_id' => $product->id,
                'quantity' => $newQuantity,
            ]);
        }

        return response()->json([
            'message' => "{$product->name} ditambahkan ke keranjang!",
            'count' => $cart->totalQuantity(),
        ]);
    }
}
