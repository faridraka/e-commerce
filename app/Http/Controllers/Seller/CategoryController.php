<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Manajemen Kategori.
     */
    public function index(Request $request): View
    {
        $categories = Category::withCount(['products' => fn ($q) => $q->where('store_id', $request->user()->store->id)])
            ->orderBy('name')
            ->get();

        return view('seller.categories', [
            'store' => $request->user()->store,
            'categories' => $categories,
        ]);
    }

    /**
     * Simpan kategori baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCategory($request);

        Category::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
        ]);

        return back()->with('status', 'Kategori "' . $validated['name'] . '" berhasil ditambahkan.');
    }

    /**
     * Perbarui kategori.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $this->validateCategory($request);

        $category->update([
            'name' => $validated['name'],
            'slug' => $category->name === $validated['name']
                ? $category->slug
                : $this->uniqueSlug($validated['name']),
        ]);

        return back()->with('status', 'Kategori berhasil diperbarui.');
    }

    /**
     * Hapus kategori (tidak boleh bila masih punya produk).
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->withErrors([
                'category' => 'Kategori masih memiliki produk dan tidak dapat dihapus.',
            ]);
        }

        $category->delete();

        return back()->with('status', 'Kategori berhasil dihapus.');
    }

    /**
     * Validasi data kategori.
     */
    private function validateCategory(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);
    }

    /**
     * Slug unik untuk nama kategori.
     */
    private function uniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Category::where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
