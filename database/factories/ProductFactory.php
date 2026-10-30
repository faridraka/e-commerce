<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(asText: true);

        return [
            'category_id' => Category::factory(),
            'name' => ucwords($name),
            'slug' => \Illuminate\Support\Str::slug($name),
            'description' => fake()->paragraph(),
            'price' => fake()->numberBetween(10, 300) * 1000,
            'original_price' => null,
            'stock' => fake()->numberBetween(0, 50),
            'image' => 'https://lh3.googleusercontent.com/aida-public/demo-' . fake()->uuid(),
            'is_active' => true,
            'sold_count' => fake()->numberBetween(0, 300),
            'rating' => fake()->randomFloat(1, 3.5, 5),
            'origin' => fake()->city(),
        ];
    }
}
