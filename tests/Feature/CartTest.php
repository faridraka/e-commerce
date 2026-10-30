<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_add_to_cart_via_endpoint(): void
    {
        $product = Product::factory()->create();

        $this->post(route('cart.store', $product))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_authenticated_user_can_add_product_to_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $response = $this->actingAs($user)
            ->postJson(route('cart.store', $product));

        $response->assertOk();
        $response->assertJson(['count' => 1]);

        $cart = Cart::where('user_id', $user->id)->first();
        $this->assertNotNull($cart);
        $this->assertSame($product->id, $cart->items()->first()->product_id);
    }

    public function test_adding_same_product_increases_quantity(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10]);

        $this->actingAs($user)->postJson(route('cart.store', $product));
        $this->actingAs($user)->postJson(route('cart.store', $product), ['quantity' => 2]);

        $cart = Cart::where('user_id', $user->id)->first();
        $this->assertSame(3, $cart->items()->first()->quantity);
    }

    public function test_quantity_is_capped_at_available_stock(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 3]);

        $this->actingAs($user)->postJson(route('cart.store', $product), ['quantity' => 99]);

        $cart = Cart::where('user_id', $user->id)->first();
        $this->assertSame(3, $cart->items()->first()->quantity);
    }

    public function test_out_of_stock_product_cannot_be_added(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 0]);

        $this->actingAs($user)
            ->postJson(route('cart.store', $product))
            ->assertStatus(409);
    }
}
