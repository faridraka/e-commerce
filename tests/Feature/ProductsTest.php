<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_displays_latest_products(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee($product->name);
    }

    public function test_home_page_shows_not_found_when_no_products(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee('Produk Tidak Ditemukan');
    }

    public function test_catalog_search_only_returns_matching_products(): void
    {
        $kopi = Product::factory()->create(['name' => 'Kopi Arabika Gayo 250g']);
        $sabun = Product::factory()->create(['name' => 'Sabun Cuci Muka Herbal']);

        $response = $this->get(route('products.index', ['q' => 'kopi']));

        $response->assertOk();
        $response->assertSee($kopi->name);
        $response->assertDontSee($sabun->name);
    }

    public function test_catalog_filters_by_category_and_availability(): void
    {
        $minuman = Category::factory()->create(['name' => 'Minuman', 'slug' => 'minuman']);
        $kopi = Product::factory()->create(['category_id' => $minuman->id, 'stock' => 0]);
        $kopiStok = Product::factory()->create(['name' => 'Kopi Robusta Lampung', 'category_id' => $minuman->id, 'stock' => 5]);

        // Filter kategori + hanya tersedia → produk stok 0 disembunyikan
        $response = $this->get(route('products.index', ['kategori' => 'minuman', 'tersedia' => 1]));

        $response->assertOk();
        $response->assertSee($kopiStok->name);
        $response->assertDontSee($kopi->name);
    }

    public function test_catalog_shows_not_found_component_when_search_has_no_result(): void
    {
        Product::factory()->create(['name' => 'Sabun Cuci Muka Herbal']);

        $response = $this->get(route('products.index', ['q' => 'xyzgakadaproduct']));

        $response->assertOk();
        $response->assertSee('Produk Tidak Ditemukan');
    }

    public function test_product_detail_page_can_be_rendered(): void
    {
        $product = Product::factory()->create([
            'name' => 'Sambal Roa Botol 180ml',
            'description' => 'Sambal roa khas Manado.',
        ]);

        $response = $this->get(route('products.show', $product));

        $response->assertOk();
        $response->assertSee('Sambal Roa Botol 180ml');
        $response->assertSee('Sambal roa khas Manado');
    }

    public function test_missing_product_returns_not_found_page(): void
    {
        $response = $this->get('/produk/tidak-ada-slug-ini');

        $response->assertNotFound();
        $response->assertSee('Halaman Tidak Ditemukan');
    }

    public function test_products_api_endpoint_returns_json_collection(): void
    {
        Product::factory()->count(3)->create();

        $response = $this->getJson(route('api.products.index', ['q' => '']));

        $response->assertOk();
        $response->assertJsonCount(3, 'data');
        $response->assertJsonStructure([
            'meta' => ['q', 'total'],
            'data' => [['id', 'name', 'slug', 'price', 'stock_status', 'url']],
        ]);
    }

    public function test_products_api_endpoint_returns_404_for_missing_product(): void
    {
        $response = $this->getJson('/api/products/tidak-ada-slug-ini');

        $response->assertNotFound();
        $response->assertJson(['message' => 'Produk tidak ditemukan.']);
    }

    public function test_inactive_product_is_hidden_from_catalog_and_detail(): void
    {
        $product = Product::factory()->create(['is_active' => false]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertDontSee($product->name);

        $this->get(route('products.show', $product))->assertNotFound();
    }
}
