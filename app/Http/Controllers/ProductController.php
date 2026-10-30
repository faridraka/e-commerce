<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Katalog produk publik dengan pencarian, filter kategori,
     * filter ketersediaan, dan pengurutan.
     */
    public function index(Request $request): View
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'kategori' => (string) $request->query('kategori', ''),
            'urut' => in_array($request->query('urut'), ['terbaru', 'termurah', 'termahal'], true)
                ? $request->query('urut')
                : 'terbaru',
            'tersedia' => $request->boolean('tersedia'),
        ];

        $products = $this->productQuery($filters)
            ->paginate(8)
            ->withQueryString();

        return view('products.index', [
            'products' => $products,
            ...$filters,
        ]);
    }

    /**
     * Halaman detail produk berdasarkan slug.
     */
    public function show(Product $product): View
    {
        abort_unless($product->is_active, 404);

        $product->load(['category', 'store']);

        $relatedProducts = Product::query()
            ->with('category')
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->latest()
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Endpoint JSON: GET /api/products?q=&kategori=&urut=&tersedia=
     */
    public function apiIndex(Request $request): JsonResponse
    {
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'kategori' => (string) $request->query('kategori', ''),
            'urut' => in_array($request->query('urut'), ['terbaru', 'termurah', 'termahal'], true)
                ? $request->query('urut')
                : 'terbaru',
            'tersedia' => $request->boolean('tersedia'),
        ];

        $data = $this->productQuery($filters)
            ->get()
            ->map(fn (Product $product) => $this->transform($product));

        return response()->json([
            'meta' => [...$filters, 'total' => $data->count()],
            'data' => $data,
        ]);
    }

    /**
     * Endpoint JSON: GET /api/products/{slug}
     */
    public function apiShow(string $slug): JsonResponse
    {
        $product = Product::query()
            ->with(['category', 'store'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (! $product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        return response()->json(['data' => $this->transform($product)]);
    }

    /**
     * Query dasar produk aktif + seluruh filter katalog.
     */
    private function productQuery(array $filters): Builder
    {
        return Product::query()
            ->with(['category', 'store'])
            ->where('is_active', true)
            ->when($filters['q'] !== '', fn (Builder $query) => $query->search($filters['q']))
            ->when($filters['kategori'] !== '', fn (Builder $query) => $query->whereHas(
                'category',
                fn (Builder $category) => $category->where('slug', $filters['kategori'])
            ))
            ->when($filters['tersedia'], fn (Builder $query) => $query->where('stock', '>', 0))
            ->when(
                $filters['urut'] === 'termurah',
                fn (Builder $query) => $query->orderBy('price'),
                fn (Builder $query) => $filters['urut'] === 'termahal'
                    ? $query->orderByDesc('price')
                    : $query->latest()
            );
    }

    /**
     * Bentuk payload JSON produk.
     */
    private function transform(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => (float) $product->price,
            'original_price' => $product->original_price ? (float) $product->original_price : null,
            'discount_percent' => $product->discountPercent(),
            'stock' => $product->stock,
            'stock_status' => $product->stockStatus(),
            'rating' => (float) $product->rating,
            'sold_count' => $product->sold_count,
            'origin' => $product->origin,
            'image' => $product->image,
            'category' => $product->category?->only(['name', 'slug']),
            'store' => $product->store?->only(['name', 'slug']),
            'url' => route('products.show', $product),
        ];
    }
}
