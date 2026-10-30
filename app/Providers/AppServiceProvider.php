<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Data navbar (kategori + jumlah keranjang) untuk seluruh halaman publik.
        View::share('navCategories', Category::orderBy('name')->get());
        View::share('cartCount', Auth::user()?->cart?->totalQuantity() ?? 0);
    }
}
