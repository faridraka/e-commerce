<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StockController extends Controller
{
    /**
     * Manajemen & Riwayat Stok.
     */
    public function index(Request $request): View
    {
        $store = $request->user()->store;

        $products = Product::query()
            ->with('category')
            ->where('store_id', $store->id)
            ->orderBy('name')
            ->get();

        $movements = StockMovement::query()
            ->with(['product', 'order'])
            ->whereHas('product', fn ($q) => $q->where('store_id', $store->id))
            ->latest()
            ->take(20)
            ->get();

        return view('seller.stock', [
            'store' => $store,
            'products' => $products,
            'movements' => $movements,
        ]);
    }

    /**
     * Sesuaikan stok produk: masuk (+) atau keluar (-).
     */
    public function adjust(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'direction' => ['required', 'in:masuk,keluar'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100000'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $product = Product::where('store_id', $request->user()->store->id)
            ->findOrFail($validated['product_id']);

        $delta = $validated['direction'] === 'masuk' ? $validated['quantity'] : -$validated['quantity'];

        if ($product->stock + $delta < 0) {
            return back()->withErrors([
                'quantity' => "Stok \"{$product->name}\" tidak boleh negatif (tersisa {$product->stock}).",
            ])->onlyInput('product_id');
        }

        DB::transaction(function () use ($product, $delta, $validated) {
            $product->increment('stock', $delta);

            $product->movements()->create([
                'type' => 'penyesuaian',
                'quantity' => $delta,
                'note' => $validated['note'] ?: ('Penyesuaian ' . $validated['direction'] . ' stok'),
            ]);
        });

        return back()->with('status', 'Stok "' . $product->name . '" diperbarui menjadi ' . $product->fresh()->stock . '.');
    }
}
