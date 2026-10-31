<?php

namespace Database\Seeders;

use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class StoreSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Toko demo untuk katalog/produk contoh. Di production, penjual
     * mendaftarkan tokonya sendiri lewat onboarding (tanpa seeder).
     */
    public function run(): void
    {
        $stores = [
            ['name' => 'Toko Kopi Gayo Aceh', 'activation_code' => 'LKL-7294-9102'],
            ['name' => 'Madu Hutan Sumbawa', 'activation_code' => 'LKL-8821-4902'],
            ['name' => 'Tenun Sasak Lombok', 'activation_code' => 'LKL-3567-1208'],
        ];

        foreach ($stores as $store) {
            Store::create([
                'name' => $store['name'],
                'slug' => Str::slug($store['name']),
                'activation_code' => $store['activation_code'],
            ]);
        }
    }
}
