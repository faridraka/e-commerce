<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SellerPortalTest extends TestCase
{
    use RefreshDatabase;

    private function seller(): User
    {
        $user = User::factory()->create([
            'role' => 'seller',
            'onboarding_completed_at' => now(),
        ]);

        Store::factory()->create([
            'user_id' => $user->id,
            'verified_at' => now(),
        ]);

        return $user->fresh();
    }

    public function test_buyer_cannot_access_seller_portal(): void
    {
        $buyer = User::factory()->create([
            'role' => 'buyer',
            'onboarding_completed_at' => now(),
        ]);

        $this->actingAs($buyer)->get(route('seller.dashboard'))->assertForbidden();
        $this->actingAs($buyer)->get(route('seller.pos'))->assertForbidden();
    }

    public function test_unverified_seller_is_redirected_to_verification(): void
    {
        $user = User::factory()->create(['role' => 'seller', 'onboarding_completed_at' => now()]);
        Store::factory()->create(['user_id' => $user->id, 'verified_at' => null]);

        $this->actingAs($user)->get(route('seller.dashboard'))
            ->assertRedirect(route('onboarding.verification'));
    }

    public function test_seller_dashboard_renders_with_stats(): void
    {
        $seller = $this->seller();

        $this->actingAs($seller)->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee($seller->store->name);
    }

    public function test_seller_can_manage_categories(): void
    {
        $seller = $this->seller();

        $this->actingAs($seller)->post(route('seller.categories.store'), [
            'name' => 'Pakaian Adat',
        ])->assertRedirect();

        $category = Category::where('slug', 'pakaian-adat')->first();
        $this->assertNotNull($category);

        $this->actingAs($seller)->patch(route('seller.categories.update', $category), [
            'name' => 'Batik Nusantara',
        ])->assertRedirect();

        $this->actingAs($seller)->delete(route('seller.categories.destroy', $category->fresh()))
            ->assertRedirect();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_with_products_cannot_be_deleted(): void
    {
        $seller = $this->seller();
        $category = Category::factory()->create();
        Product::factory()->create([
            'category_id' => $category->id,
            'store_id' => $seller->store->id,
        ]);

        $this->actingAs($seller)
            ->delete(route('seller.categories.destroy', $category))
            ->assertSessionHasErrors('category');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_seller_sees_only_own_store_products(): void
    {
        $seller = $this->seller();
        Product::factory()->create(['name' => 'Produk Toko Saya', 'store_id' => $seller->store->id]);
        Product::factory()->create(['name' => 'Produk Toko Lain', 'store_id' => null]);

        $this->actingAs($seller)->get(route('seller.products'))
            ->assertOk()
            ->assertSee('Produk Toko Saya')
            ->assertDontSee('Produk Toko Lain');
    }

    public function test_seller_can_edit_product(): void
    {
        $seller = $this->seller();
        $product = Product::factory()->create([
            'store_id' => $seller->store->id,
            'stock' => 10,
        ]);

        $this->actingAs($seller)->get(route('seller.products.edit', $product))
            ->assertOk()
            ->assertSee($product->name);

        $this->actingAs($seller)->put(route('seller.products.update', $product), [
            'name' => 'Sambal Roa Botol 200ml',
            'category_id' => $product->category_id,
            'price' => 40000,
            'stock' => 15,
            'description' => $product->description,
            'image' => $product->image,
        ])->assertRedirect(route('seller.products'));

        $product->refresh();
        $this->assertSame('Sambal Roa Botol 200ml', $product->name);
        $this->assertSame(40000.0, (float) $product->price);
        $this->assertSame(15, $product->stock);

        // Penyesuaian stok tercatat: +5
        $this->assertSame(1, $product->movements()->count());
        $this->assertSame(5, $product->movements()->first()->quantity);
    }

    public function test_seller_cannot_edit_other_store_product(): void
    {
        $seller = $this->seller();
        $product = Product::factory()->create(['store_id' => null]);

        $this->actingAs($seller)
            ->get(route('seller.products.edit', $product))
            ->assertNotFound();
    }

    public function test_seller_can_adjust_stock(): void
    {
        $seller = $this->seller();
        $product = Product::factory()->create([
            'store_id' => $seller->store->id,
            'stock' => 10,
        ]);

        $this->actingAs($seller)->post(route('seller.stock.adjust'), [
            'product_id' => $product->id,
            'direction' => 'masuk',
            'quantity' => 25,
            'note' => 'Restock dari pemasok',
        ])->assertRedirect();

        $this->assertSame(35, $product->fresh()->stock);

        $movement = $product->movements()->first();
        $this->assertSame(25, $movement->quantity);
        $this->assertSame('penyesuaian', $movement->type);

        $this->actingAs($seller)->post(route('seller.stock.adjust'), [
            'product_id' => $product->id,
            'direction' => 'keluar',
            'quantity' => 40,
        ])->assertSessionHasErrors();
    }

    public function test_seller_orders_list_and_detail(): void
    {
        $seller = $this->seller();
        $buyer = User::factory()->create();
        $product = Product::factory()->create(['store_id' => $seller->store->id]);

        $order = Order::create([
            'order_code' => 'ORD-20261031-0142',
            'user_id' => $buyer->id,
            'channel' => 'online',
            'status' => Order::STATUS_DIBAYAR,
            'total_amount' => 65000,
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price,
        ]);

        $this->actingAs($seller)->get(route('seller.orders'))
            ->assertOk()
            ->assertSee('ORD-20261031-0142');

        $this->actingAs($seller)->get(route('seller.orders.show', $order->order_code))
            ->assertOk()
            ->assertSee($product->name);

        $this->actingAs($seller)->post(route('seller.orders.status', $order->order_code), [
            'status' => Order::STATUS_DIPROSES,
        ])->assertRedirect();

        $this->assertSame(Order::STATUS_DIPROSES, $order->fresh()->status);
    }

    public function test_pos_cash_checkout_creates_paid_order_and_deducts_stock(): void
    {
        $seller = $this->seller();
        $product = Product::factory()->create([
            'store_id' => $seller->store->id,
            'price' => 20000,
            'stock' => 10,
        ]);

        $response = $this->actingAs($seller)->postJson(route('seller.pos.checkout'), [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 3],
            ],
            'payment_method' => 'cash',
            'received_amount' => 70000,
        ]);

        $response->assertOk();

        $order = Order::where('channel', 'pos')->first();
        $this->assertNotNull($order);
        $this->assertSame(60000.0, (float) $order->total_amount);
        $this->assertSame(Order::STATUS_DIBAYAR, $order->status);

        $payment = $order->payments()->first();
        $this->assertSame(Payment::STATUS_PAID, $payment->status);
        $this->assertSame('manual', $payment->method);
        $this->assertSame(70000.0, (float) $payment->amount_received);

        $this->assertSame(7, $product->fresh()->stock);
        $movement = $product->movements()->first();
        $this->assertSame(-3, $movement->quantity);
        $this->assertSame('penjualan', $movement->type);

        // Struk & riwayat tampil
        $this->actingAs($seller)->get(route('seller.pos.receipt', $order->order_code))
            ->assertOk()
            ->assertSee($order->order_code);
        $this->actingAs($seller)->get(route('seller.pos.history'))
            ->assertOk()
            ->assertSee($order->order_code);
    }

    public function test_pos_rejects_insufficient_cash_and_stock(): void
    {
        $seller = $this->seller();
        $product = Product::factory()->create(['store_id' => $seller->store->id, 'price' => 20000, 'stock' => 2]);

        $this->actingAs($seller)->postJson(route('seller.pos.checkout'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'cash',
            'received_amount' => 5000,
        ])->assertStatus(422);

        $this->actingAs($seller)->postJson(route('seller.pos.checkout'), [
            'items' => [['product_id' => $product->id, 'quantity' => 5]],
            'payment_method' => 'cash',
            'received_amount' => 200000,
        ])->assertStatus(422);

        $this->assertSame(2, $product->fresh()->stock);
    }

    public function test_pos_rejects_non_cash_payment_method(): void
    {
        $seller = $this->seller();
        $product = Product::factory()->create(['store_id' => $seller->store->id, 'price' => 20000, 'stock' => 5]);

        $this->actingAs($seller)->postJson(route('seller.pos.checkout'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'virtual',
        ])->assertStatus(422);
    }

    public function test_pos_pages_require_seller(): void
    {
        $buyer = User::factory()->create([
            'role' => 'buyer',
            'onboarding_completed_at' => now(),
        ]);

        $this->actingAs($buyer)->get(route('seller.pos'))->assertForbidden();
        $this->actingAs($buyer)->get(route('seller.pos.history'))->assertForbidden();
    }

    public function test_other_seller_cannot_view_pos_receipt_or_status(): void
    {
        $seller = $this->seller();
        $other = $this->seller();
        $product = Product::factory()->create([
            'store_id' => $seller->store->id,
            'price' => 20000,
            'stock' => 10,
        ]);

        $this->actingAs($seller)->postJson(route('seller.pos.checkout'), [
            'items' => [['product_id' => $product->id, 'quantity' => 1]],
            'payment_method' => 'cash',
            'received_amount' => 20000,
        ])->assertOk();

        $order = Order::where('channel', 'pos')->first();
        $this->assertNotNull($order);

        $this->actingAs($other)->get(route('seller.pos.receipt', $order->order_code))->assertNotFound();
    }

    public function test_other_seller_cannot_view_online_order_detail(): void
    {
        $seller = $this->seller();
        $other = $this->seller();
        $buyer = User::factory()->create();
        $product = Product::factory()->create(['store_id' => $seller->store->id]);

        $order = Order::create([
            'order_code' => 'ORD-20261031-0901',
            'user_id' => $buyer->id,
            'channel' => 'online',
            'status' => Order::STATUS_DIBAYAR,
            'total_amount' => 65000,
        ]);
        $order->items()->create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'subtotal' => $product->price,
        ]);

        $this->actingAs($other)->get(route('seller.orders.show', $order->order_code))->assertNotFound();
    }
}
