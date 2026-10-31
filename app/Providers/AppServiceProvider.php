<?php

namespace App\Providers;

use App\Models\Cart;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
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
        // Composer wildcard meniru perilaku View::share (tersedia di parent & child
        // view) namun dievaluasi lazy saat render, bukan saat boot aplikasi, agar
        // artisan/package:discover tetap jalan sebelum migrasi. Memo per-request
        // mencegah query berulang untuk tiap view pada halaman yang sama.
        View::composer('*', function ($view): void {
            static $navCategories = null;

            if ($navCategories === null) {
                $navCategories = Schema::hasTable('categories')
                    ? Category::orderBy('name')->get()
                    : collect();
            }

            $view->with('navCategories', $navCategories);
            $view->with('cartCount', Auth::user()?->cart?->totalQuantity() ?? 0);
        });
    }
}
