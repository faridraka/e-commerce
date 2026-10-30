<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Beranda publik: etalase kategori dan produk terbaru.
     */
    public function index(): View
    {
        $categories = Category::withCount([
            'products' => fn ($query) => $query->where('is_active', true),
        ])->orderBy('name')->get();

        $latestProducts = Product::query()
            ->with(['category', 'store'])
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        return view('home', compact('categories', 'latestProducts'));
    }
}
