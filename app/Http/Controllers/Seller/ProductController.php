<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Manajemen Produk: daftar produk milik toko penjual.
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'movements' => fn ($q) => $q->latest()->take(1)])
            ->where('store_id', $request->user()->store->id)
            ->latest()
            ->get();

        return view('seller.products', [
            'store' => $request->user()->store,
            'products' => $products,
        ]);
    }

    /**
     * Form Tambah Produk.
     */
    public function create(Request $request): View
    {
        return view('seller.product-form', [
            'store' => $request->user()->store,
            'product' => new Product(),
            'categories' => Category::orderBy('name')->get(),
            'isEditing' => false,
        ]);
    }

    /**
     * Halaman Ubah Produk.
     */
    public function edit(Request $request, Product $product): View
    {
        abort_unless($product->store_id === $request->user()->store->id, 404);

        $product->load('category');

        return view('seller.product-form', [
            'store' => $request->user()->store,
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'isEditing' => true,
        ]);
    }

    /**
     * Simpan produk baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);

        $product = $request->user()->store->products()->create(
            $this->productPayload($validated, $request)
        );

        $product->forceFill([
            'image' => $product->image ?: $this->placeholderImage(),
        ])->save();

        // Kemasan awal stok dicatat sebagai penyesuaian masuk.
        if ($product->stock > 0) {
            $product->movements()->create([
                'type' => 'penyesuaian',
                'quantity' => $product->stock,
                'note' => 'Stok awal produk baru',
            ]);
        }

        return redirect()
            ->route('seller.products')
            ->with('status', 'Produk "' . $product->name . '" berhasil ditambahkan.');
    }

    /**
     * Perbarui produk milik toko penjual.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->store_id === $request->user()->store->id, 404);

        $validated = $this->validateProduct($request, $product);
        $oldStock = $product->stock;

        $payload = $this->productPayload($validated, $request);

        // Bila gambar dikosongkan saat mengubah, pertahankan gambar lama.
        if (blank($payload['image'])) {
            unset($payload['image']);
        }

        $product->update($payload);

        // Perubahan stok manual dari form dicatat sebagai penyesuaian.
        if ($product->stock !== $oldStock) {
            $product->movements()->create([
                'type' => 'penyesuaian',
                'quantity' => $product->stock - $oldStock,
                'note' => 'Penyesuaian dari Ubah Produk',
            ]);
        }

        return redirect()
            ->route('seller.products')
            ->with('status', 'Produk "' . $product->name . '" berhasil diperbarui.');
    }

    /**
     * Nonaktifkan / aktifkan produk.
     */
    public function toggle(Request $request, Product $product): RedirectResponse
    {
        abort_unless($product->store_id === $request->user()->store->id, 404);

        $product->update(['is_active' => ! $product->is_active]);

        return back()->with('status', $product->is_active
            ? 'Produk "' . $product->name . '" diaktifkan.'
            : 'Produk "' . $product->name . '" dinonaktifkan.');
    }

    /**
     * Validasi data produk.
     */
    private function validateProduct(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'original_price' => ['nullable', 'numeric', 'min:0', 'gt:price'],
            'stock' => ['required', 'integer', 'min:0', 'max:100000'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'url', 'max:1000'],
            'origin' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($product?->id)],
        ]);
    }

    /**
     * Susun payload produk dari data tervalidasi.
     */
    private function productPayload(array $validated, Request $request): array
    {
        return [
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'category_id' => $validated['category_id'],
            'price' => $validated['price'],
            'original_price' => $validated['original_price'] ?? null,
            'stock' => $validated['stock'],
            'description' => $validated['description'] ?? null,
            'image' => $validated['image'] ?? null,
            'origin' => $validated['origin'] ?? null,
            'is_active' => true,
        ];
    }

    /**
     * Slug unik untuk produk.
     */
    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Product::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    /**
     * Gambar placeholder bila penjual tidak mengisi URL gambar.
     */
    private function placeholderImage(): string
    {
        return 'https://placehold.co/600x600?text=' . urlencode('Produk Lokalku');
    }
}
