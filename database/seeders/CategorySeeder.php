<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Kategori etalase Lokalku.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Makanan', 'slug' => 'makanan'],
            ['name' => 'Minuman', 'slug' => 'minuman'],
            ['name' => 'Kerajinan', 'slug' => 'kerajinan'],
            ['name' => 'Fashion', 'slug' => 'fashion'],
            ['name' => 'Perawatan', 'slug' => 'perawatan'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}
