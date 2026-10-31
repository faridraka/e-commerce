<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_cart_checkout_and_orders(): void
    {
        $this->get(route('cart.index'))->assertRedirect(route('login'));
        $this->get(route('checkout.index'))->assertRedirect(route('login'));
        $this->get(route('orders.index'))->assertRedirect(route('login'));
        $this->get(route('profile.show'))->assertRedirect(route('login'));
        $this->get(route('addresses.index'))->assertRedirect(route('login'));
    }

    public function test_cart_page_shows_items_and_total(): void
    {
        $user = $this->buyer();
        $cart = Cart::create(['user_id' => $user->id]);
        $product = Product::factory()->create(['price' => 35000]);
        $cart->items()->create(['product_id' => $product->id, 'quantity' => 2]);

        $response = $this->actingAs($user)->get(route('cart.index'));

        $response->assertOk();
        $response->assertSee($product->name);
        $response->assertSee('Rp 70.000');
    }

    public function test_cart_page_shows_not_found_when_empty(): void
    {
        $user = $this->buyer();

        $this->actingAs($user)->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Keranjang Belanja Kosong');
    }

    public function test_user_can_checkout_and_get_pending_order(): void
    {
        $user = $this->buyer();
        $address = Address::create([
            'user_id' => $user->id,
            'recipient_name' => 'Budi Santoso',
            'phone' => '081234567890',
            'address_detail' => 'Jl. Kebon Sirih No. 42',
            'city' => 'Jakarta Pusat',
            'postal_code' => '10110',
            'is_default' => true,
        ]);
        $cart = Cart::create(['user_id' => $user->id]);
        $p1 = Product::factory()->create(['price' => 65000, 'stock' => 10]);
        $p2 = Product::factory()->create(['price' => 18000, 'stock' => 10]);
        $cart->items()->createMany([
            ['product_id' => $p1->id, 'quantity' => 1],
            ['product_id' => $p2->id, 'quantity' => 2],
        ]);

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'address_id' => $address->id,
        ]);

        $order = Order::where('user_id', $user->id)->first();
        $this->assertNotNull($order);
        $response->assertRedirect(route('payment.show', $order->order_code));

        // Item tersalin & stok berkurang & keranjang dikosongkan.
        $this->assertSame(2, $order->items()->count());
        $this->assertSame(9, $p1->fresh()->stock);
        $this->assertSame(8, $p2->fresh()->stock);
        $this->assertSame(0, $cart->fresh()->items()->count());
        $this->assertSame(101000.0, (float) $order->total_amount);
        $this->assertSame(Order::STATUS_MENUNGGU_PEMBAYARAN, $order->status);
        $this->assertSame(Payment::STATUS_PENDING, $order->payments()->first()->status);
    }

    public function test_orders_pages_render_for_owner(): void
    {
        $user = $this->buyer();
        $order = Order::create([
            'order_code' => 'ORD-20261030-0001',
            'user_id' => $user->id,
            'channel' => 'online',
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
            'total_amount' => 50000,
        ]);

        $this->actingAs($user)->get(route('orders.index'))
            ->assertOk()
            ->assertSee('ORD-20261030-0001');

        $this->actingAs($user)->get(route('orders.show', $order->order_code))
            ->assertOk()
            ->assertSee('ORD-20261030-0001');
    }

    public function test_order_detail_is_hidden_from_other_users(): void
    {
        $owner = $this->buyer();
        $stranger = $this->buyer();
        $order = Order::create([
            'order_code' => 'ORD-20261030-0002',
            'user_id' => $owner->id,
            'channel' => 'online',
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
            'total_amount' => 50000,
        ]);

        $this->actingAs($stranger)->get(route('orders.show', $order->order_code))->assertNotFound();
        $this->actingAs($stranger)->get(route('payment.show', $order->order_code))->assertNotFound();
    }

    public function test_payment_page_shows_manual_confirmation(): void
    {
        $user = $this->buyer();
        $order = Order::create([
            'order_code' => 'ORD-20261030-0003',
            'user_id' => $user->id,
            'channel' => 'online',
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
            'total_amount' => 50000,
        ]);
        $order->payments()->create([
            'method' => 'manual',
            'provider' => null,
            'provider_order_id' => 'ORD-20261030-0003-1',
            'status' => Payment::STATUS_PENDING,
            'amount' => 50000,
        ]);

        $this->actingAs($user)->get(route('payment.show', $order->order_code))
            ->assertOk()
            ->assertSee('Saya Sudah Bayar');
    }

    public function test_buyer_can_confirm_payment_manually(): void
    {
        $user = $this->buyer();
        $order = Order::create([
            'order_code' => 'ORD-20261030-0005',
            'user_id' => $user->id,
            'channel' => 'online',
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
            'total_amount' => 50000,
        ]);
        $order->payments()->create([
            'method' => 'manual',
            'provider' => null,
            'provider_order_id' => 'ORD-20261030-0005-1',
            'status' => Payment::STATUS_PENDING,
            'amount' => 50000,
        ]);

        $this->actingAs($user)->post(route('payment.confirm', $order->order_code))
            ->assertRedirect(route('orders.show', $order->order_code));

        $this->assertSame(Payment::STATUS_PAID, $order->payments()->first()->status);
        $this->assertNotNull($order->payments()->first()->paid_at);
        $this->assertSame(Order::STATUS_DIBAYAR, $order->fresh()->status);
    }

    public function test_payment_confirm_is_hidden_from_other_users(): void
    {
        $owner = $this->buyer();
        $stranger = $this->buyer();
        $order = Order::create([
            'order_code' => 'ORD-20261030-0008',
            'user_id' => $owner->id,
            'channel' => 'online',
            'status' => Order::STATUS_MENUNGGU_PEMBAYARAN,
            'total_amount' => 50000,
        ]);
        $order->payments()->create([
            'method' => 'manual',
            'provider' => null,
            'provider_order_id' => 'ORD-20261030-0008-1',
            'status' => Payment::STATUS_PENDING,
            'amount' => 50000,
        ]);

        $this->actingAs($stranger)
            ->post(route('payment.confirm', $order->order_code))
            ->assertNotFound();
    }

    public function test_payment_confirm_rejects_already_processed_payment(): void
    {
        $user = $this->buyer();
        $order = Order::create([
            'order_code' => 'ORD-20261030-0009',
            'user_id' => $user->id,
            'channel' => 'online',
            'status' => Order::STATUS_DIBAYAR,
            'total_amount' => 50000,
        ]);
        $order->payments()->create([
            'method' => 'manual',
            'provider' => null,
            'provider_order_id' => 'ORD-20261030-0009-1',
            'status' => Payment::STATUS_PAID,
            'amount' => 50000,
            'paid_at' => now(),
        ]);

        $this->actingAs($user)->post(route('payment.confirm', $order->order_code))
            ->assertRedirect(route('orders.show', $order->order_code));
    }

    public function test_profile_can_be_updated(): void
    {
        $user = $this->buyer();

        $this->actingAs($user)->patch(route('profile.update'), [
            'name' => 'Budi Santoso Baru',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame('Budi Santoso Baru', $user->fresh()->name);
    }

    public function test_address_crud_and_default_rotation(): void
    {
        $user = $this->buyer();

        $a1 = Address::create([
            'user_id' => $user->id,
            'recipient_name' => 'Budi',
            'phone' => '081234567890',
            'address_detail' => 'Jl. A No. 1',
            'city' => 'Jakarta',
            'postal_code' => '10110',
            'is_default' => true,
        ]);

        $this->actingAs($user)->post(route('addresses.store'), [
            'recipient_name' => 'Sari',
            'phone' => '081298765432',
            'address_detail' => 'Jl. B No. 2',
            'city' => 'Bandung',
            'postal_code' => '40115',
        ])->assertRedirect();

        $this->assertSame(2, $user->addresses()->count());

        $a2 = $user->addresses()->where('city', 'Bandung')->first();
        $this->assertFalse($a2->is_default);

        $this->actingAs($user)->post(route('addresses.setDefault', $a2))->assertRedirect();
        $this->assertTrue($a2->fresh()->is_default);
        $this->assertFalse($a1->fresh()->is_default);

        $this->actingAs($user)->delete(route('addresses.destroy', $a2))->assertRedirect();
        $this->assertTrue($a1->fresh()->is_default, 'Default harus pindah ke alamat tersisa.');
    }

    private function buyer(): User
    {
        return User::factory()->create(['role' => 'buyer', 'onboarding_completed_at' => now()]);
    }

    public function test_seller_cannot_access_buyer_pages(): void
    {
        $seller = User::factory()->create([
            'role' => 'seller',
            'onboarding_completed_at' => now(),
        ]);
        \App\Models\Store::factory()->create([
            'user_id' => $seller->id,
            'verified_at' => now(),
        ]);

        $this->actingAs($seller)->get(route('home'))->assertRedirect(route('seller.dashboard'));
        $this->actingAs($seller)->get(route('products.index'))->assertRedirect(route('seller.dashboard'));
        $this->actingAs($seller)->get(route('cart.index'))->assertRedirect(route('seller.dashboard'));
        $this->actingAs($seller)->get(route('checkout.index'))->assertRedirect(route('seller.dashboard'));
        $this->actingAs($seller)->get(route('orders.index'))->assertRedirect(route('seller.dashboard'));
    }

    public function test_buyer_cannot_access_seller_pages(): void
    {
        $user = $this->buyer();

        $this->actingAs($user)->get(route('seller.dashboard'))->assertForbidden();
        $this->actingAs($user)->get(route('seller.pos'))->assertForbidden();
    }
}
