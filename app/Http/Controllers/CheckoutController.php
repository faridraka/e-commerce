<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    /**
     * Halaman Checkout: pengiriman & tinjau pesanan.
     */
    public function show(Request $request): View|RedirectResponse
    {
        $cart = $request->user()->cart()->with('items.product')->first();
        $cartItems = $cart?->items ?? collect();

        // Keranjang kosong: kembali ke halaman keranjang.
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Keranjang belanja masih kosong.');
        }

        $addresses = $request->user()->addresses()->orderByDesc('is_default')->orderBy('id')->get();
        $total = $cartItems->sum(fn (CartItem $item) => (float) $item->product->price * $item->quantity);

        return view('checkout.index', compact('cartItems', 'addresses', 'total'));
    }

    /**
     * Proses checkout: buat pesanan, salin item, kurangi stok, siapkan pembayaran.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'address_id' => ['required', 'exists:addresses,id'],
        ]);

        $address = Address::where('user_id', $request->user()->id)->findOrFail($validated['address_id']);

        $cart = $request->user()->cart()->with('items.product')->first();

        if (! $cart || $cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Keranjang belanja masih kosong.');
        }

        $order = DB::transaction(function () use ($cart, $request, $address) {
            $total = $cart->items->sum(fn (CartItem $item) => (float) $item->product->price * $item->quantity);

            $order = Order::create([
                'order_code' => Order::generateCode(),
                'user_id' => $request->user()->id,
                'address_id' => $address->id,
                'channel' => 'online',
                'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
                'total_amount' => $total,
                'customer_name' => $request->user()->name,
            ]);

            foreach ($cart->items as $item) {
                // Kunci baris produk agar stok tidak berkurang ganda.
                $product = $item->product()->lockForUpdate()->first();

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->product->price * $item->quantity,
                ]);

                $product->decrement('stock', $item->quantity);
            }

            $order->payments()->create([
                'method' => 'manual',
                'provider' => null,
                'provider_order_id' => $order->order_code . '-1',
                'status' => Payment::STATUS_PENDING,
                'amount' => $total,
            ]);

            $cart->items()->delete();

            return $order;
        });

        return redirect()
            ->route('payment.show', $order->order_code)
            ->with('status', "Pesanan {$order->order_code} berhasil dibuat. Silakan selesaikan pembayaran.");
    }
}
