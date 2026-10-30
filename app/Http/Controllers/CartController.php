<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CartController extends Controller
{
    /**
     * Halaman Keranjang Belanja.
     */
    public function index(Request $request): View
    {
        $cart = $request->user()->cart()->with('items.product')->first();

        $cartItems = $cart?->items ?? collect();
        $total = $cartItems->sum(fn (CartItem $item) => (float) $item->product->price * $item->quantity);

        return view('cart.index', compact('cartItems', 'total'));
    }

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

    /**
     * Ubah jumlah salah satu item keranjang milik user.
     */
    public function updateItem(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $newQuantity = min($validated['quantity'], $cartItem->product->stock);

        if ($newQuantity < 1) {
            $cartItem->delete();
        } else {
            $cartItem->update(['quantity' => $newQuantity]);
        }

        return back();
    }

    /**
     * Hapus item dari keranjang.
     */
    public function removeItem(Request $request, CartItem $cartItem): RedirectResponse
    {
        abort_unless($cartItem->cart->user_id === $request->user()->id, 404);

        $cartItem->delete();

        return back()->with('status', 'Produk dihapus dari keranjang.');
    }

    /**
     * Kosongkan seluruh keranjang.
     */
    public function clear(Request $request): RedirectResponse
    {
        $request->user()->cart?->items()->delete();

        return back()->with('status', 'Keranjang dikosongkan.');
    }
}
