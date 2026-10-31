<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\EnsureOnboardingCompleted;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home')->middleware(EnsureOnboardingCompleted::class);

// Katalog & detail produk (publik)
Route::get('products', [ProductController::class, 'index'])->name('products.index');
Route::get('products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Halaman untuk tamu (belum login)
Route::middleware('guest')->group(function () {
    Route::get('register', [RegisterController::class, 'create'])->name('register');
    Route::post('register', [RegisterController::class, 'store'])->name('register.store');

    Route::get('login', [LoginController::class, 'create'])->name('login');
    Route::post('login', [LoginController::class, 'store'])->name('login.store');
});

Route::post('logout', [LoginController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

// Onboarding (wajib login)
Route::middleware('auth')->group(function () {
    Route::get('onboarding', [OnboardingController::class, 'showRole'])->name('onboarding.role');
    Route::post('onboarding', [OnboardingController::class, 'storeRole'])->name('onboarding.role.store');

    Route::get('onboarding/verification', [OnboardingController::class, 'showVerification'])->name('onboarding.verification');
    Route::post('onboarding/store', [OnboardingController::class, 'registerStore'])->name('onboarding.store');
    Route::post('onboarding/verification', [OnboardingController::class, 'verify'])->name('onboarding.verification.store');
    Route::post('onboarding/verification/skip', [OnboardingController::class, 'skip'])->name('onboarding.verification.skip');
});

// Tambah produk ke keranjang (khusus user login; tamu diarahkan ke halaman daftar)
Route::post('cart/{product}', [CartController::class, 'store'])
    ->middleware('auth')
    ->name('cart.store');

// Area pembeli (wajib login + onboarding selesai)
Route::middleware(['auth', 'onboarding.completed'])->group(function () {
    // Cart
    Route::get('cart', [CartController::class, 'index'])->name('cart.index');
    Route::patch('cart/items/{cartItem}', [CartController::class, 'updateItem'])->name('cart.update');
    Route::delete('cart/items/{cartItem}', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::delete('cart', [CartController::class, 'clear'])->name('cart.clear');

    // Checkout
    Route::get('checkout', [CheckoutController::class, 'show'])->name('checkout.index');
    Route::post('checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    // Orders & payment
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order:order_code}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('orders/{order:order_code}/payment', [PaymentController::class, 'show'])->name('payment.show');
    Route::post('orders/{order:order_code}/payment/token', [PaymentController::class, 'snapToken'])->name('payment.token');

    // Profile
    Route::get('profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');

    // Addresses
    Route::get('addresses', [AddressController::class, 'index'])->name('addresses.index');
    Route::post('addresses', [AddressController::class, 'store'])->name('addresses.store');
    Route::patch('addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
    Route::delete('addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
    Route::post('addresses/{address}/default', [AddressController::class, 'setDefault'])->name('addresses.setDefault');
});

// Webhook notifikasi Midtrans (dipanggil server Midtrans, tanpa sesi/CSRF)
Route::post('midtrans/notification', [PaymentController::class, 'webhook'])->name('midtrans.webhook');

// Portal Penjual & Kasir POS (wajib seller dengan toko terverifikasi)
use App\Http\Controllers\Seller\CategoryController as SellerCategoryController;
use App\Http\Controllers\Seller\DashboardController;
use App\Http\Controllers\Seller\OrderController as SellerOrderController;
use App\Http\Controllers\Seller\PosController;
use App\Http\Controllers\Seller\ProductController as SellerProductController;
use App\Http\Controllers\Seller\StockController as SellerStockController;

Route::prefix('seller')->middleware(['auth', 'onboarding.completed', 'seller'])->group(function () {
    Route::get('dasbor', DashboardController::class)->name('seller.dashboard');

    // Kategori
    Route::get('kategori', [SellerCategoryController::class, 'index'])->name('seller.categories');
    Route::post('kategori', [SellerCategoryController::class, 'store'])->name('seller.categories.store');
    Route::patch('kategori/{category}', [SellerCategoryController::class, 'update'])->name('seller.categories.update');
    Route::delete('kategori/{category}', [SellerCategoryController::class, 'destroy'])->name('seller.categories.destroy');

    // Produk
    Route::get('produk', [SellerProductController::class, 'index'])->name('seller.products');
    Route::get('produk/tambah', [SellerProductController::class, 'create'])->name('seller.products.create');
    Route::post('produk', [SellerProductController::class, 'store'])->name('seller.products.store');
    Route::get('produk/{product}/ubah', [SellerProductController::class, 'edit'])->name('seller.products.edit');
    Route::put('produk/{product}', [SellerProductController::class, 'update'])->name('seller.products.update');
    Route::patch('produk/{product}/toggle', [SellerProductController::class, 'toggle'])->name('seller.products.toggle');

    // Stok
    Route::get('stok', [SellerStockController::class, 'index'])->name('seller.stock');
    Route::post('stok', [SellerStockController::class, 'adjust'])->name('seller.stock.adjust');

    // Pesanan online
    Route::get('pesanan', [SellerOrderController::class, 'index'])->name('seller.orders');
    Route::get('pesanan/{order:order_code}', [SellerOrderController::class, 'show'])->name('seller.orders.show');
    Route::post('pesanan/{order:order_code}/status', [SellerOrderController::class, 'updateStatus'])->name('seller.orders.status');

    // Kasir POS
    Route::get('pos', [PosController::class, 'terminal'])->name('seller.pos');
    Route::post('pos/checkout', [PosController::class, 'checkout'])->name('seller.pos.checkout');
    Route::get('pos/riwayat', [PosController::class, 'history'])->name('seller.pos.history');
    Route::get('pos/{order:order_code}/payment-status', [PosController::class, 'paymentStatus'])->name('seller.pos.payment-status');
    Route::get('pos/{order:order_code}/struk', [PosController::class, 'receipt'])->name('seller.pos.receipt');
});

// Endpoint JSON publik
Route::prefix('api')->group(function () {
    Route::get('products', [ProductController::class, 'apiIndex'])->name('api.products.index');
    Route::get('products/{slug}', [ProductController::class, 'apiShow'])->name('api.products.show');
});

// Redirect permanen dari URL lama (bahasa Indonesia) ke URL baru (bahasa Inggris)
Route::redirect('keranjang', 'cart', 301);
// Jaring pengaman: halaman lama yang ter-cache masih POST ke /keranjang/{id}.
// 307 mempertahankan method POST dan isi body-nya.
Route::post('keranjang/{product}', fn ($product) => redirect()->to("cart/{$product}", 307));
Route::redirect('katalog', 'products', 301);
Route::get('produk/{slug}', function (string $slug) {
    // Redirect hanya untuk produk yang benar-benar ada; selain itu 404.
    abort_unless(App\Models\Product::where('slug', $slug)->where('is_active', true)->exists(), 404);

    return redirect()->to("products/{$slug}", 301);
});
Route::redirect('profil', 'profile', 301);
Route::redirect('alamat', 'addresses', 301);
Route::redirect('onboarding/verifikasi', 'onboarding/verification', 301);
Route::get('pesanan', fn () => redirect()->to('orders', 301));
Route::get('pesanan/{order:order_code}', fn (App\Models\Order $order) => redirect()->to("orders/{$order->order_code}", 301));
Route::get('pesanan/{order:order_code}/bayar', fn (App\Models\Order $order) => redirect()->to("orders/{$order->order_code}/payment", 301));
