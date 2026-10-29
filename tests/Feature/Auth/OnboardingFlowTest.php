<?php

namespace Tests\Feature\Auth;

use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnboardingFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee('Buat Akun Baru');
    }

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertSee('Masuk ke Lokalku');
    }

    public function test_new_users_can_register_and_are_redirected_to_role_onboarding(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@nusantara.id',
            'password' => 'RahasiaLokal2025!',
            'password_confirmation' => 'RahasiaLokal2025!',
        ]);

        $response->assertRedirect(route('onboarding.role'));
        $this->assertAuthenticated();

        $user = User::whereEmail('budi.santoso@nusantara.id')->first();
        $this->assertNotNull($user);
        $this->assertNull($user->role);
    }

    public function test_registration_requires_unique_email_and_matching_password(): void
    {
        User::factory()->create(['email' => 'budi.santoso@nusantara.id']);

        $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@nusantara.id',
            'password' => 'RahasiaLokal2025!',
            'password_confirmation' => 'RahasiaLokal2025!',
        ])->assertSessionHasErrors('email');

        $this->from(route('register'))->post(route('register.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi.lain@nusantara.id',
            'password' => 'RahasiaLokal2025!',
            'password_confirmation' => 'BedaSandi123',
        ])->assertSessionHasErrors('password');
    }

    public function test_buyer_can_choose_role_and_complete_onboarding(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('onboarding.role.store'), ['role' => 'buyer'])
            ->assertRedirect(route('home'));

        $this->assertSame('buyer', $user->fresh()->role);
        $this->assertNotNull($user->fresh()->onboarding_completed_at);
    }

    public function test_seller_is_redirected_to_verification_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('onboarding.role.store'), ['role' => 'seller'])
            ->assertRedirect(route('onboarding.verification'));

        $this->assertSame('seller', $user->fresh()->role);
        $this->assertNull($user->fresh()->onboarding_completed_at);

        $this->actingAs($user)
            ->get(route('onboarding.verification'))
            ->assertOk()
            ->assertSee('Verifikasi Akun Penjual');
    }

    public function test_seller_verification_rejects_invalid_code(): void
    {
        $user = User::factory()->create(['role' => 'seller']);
        Store::factory()->create(['activation_code' => 'LKL-7294-9102']);

        $this->actingAs($user)
            ->post(route('onboarding.verification.store'), ['activation_code' => 'LKL-0000-FAIL'])
            ->assertSessionHasErrors('activation_code');

        $this->assertNull($user->fresh()->onboarding_completed_at);
    }

    public function test_seller_can_verify_with_valid_activation_code(): void
    {
        $user = User::factory()->create(['role' => 'seller']);
        $store = Store::factory()->create(['activation_code' => 'LKL-7294-9102']);

        $this->actingAs($user)
            ->post(route('onboarding.verification.store'), ['activation_code' => 'lkl-7294-9102'])
            ->assertRedirect(route('home'));

        $this->assertNotNull($store->fresh()->verified_at);
        $this->assertSame($user->id, $store->fresh()->user_id);
        $this->assertNotNull($user->fresh()->onboarding_completed_at);
    }

    public function test_seller_can_skip_verification_and_become_buyer(): void
    {
        $user = User::factory()->create(['role' => 'seller']);

        $this->actingAs($user)
            ->post(route('onboarding.verification.skip'))
            ->assertRedirect(route('home'));

        $this->assertSame('buyer', $user->fresh()->role);
        $this->assertNotNull($user->fresh()->onboarding_completed_at);
    }

    public function test_users_can_log_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect(route('home'));

        $this->assertGuest();
    }

    public function test_login_redirects_unfinished_onboarding_to_role_selection(): void
    {
        $user = User::factory()->create();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('onboarding.role'));
    }
}
