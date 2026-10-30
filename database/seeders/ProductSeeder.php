<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Produk demo sesuai desain etalase & katalog Lokalku.
     */
    public function run(): void
    {
        $storeId = fn (string $name) => Store::where('name', $name)->value('id');

        $products = [
            // --- Kopi (hasil pencarian "kopi") ---
            [
                'name' => 'Kopi Arabika Gayo 250g Single Origin',
                'category' => 'minuman', 'store' => 'Toko Kopi Gayo Aceh',
                'price' => 65000, 'stock' => 24, 'sold_count' => 128, 'rating' => 4.9,
                'origin' => 'Aceh Gayo',
                'description' => 'Biji kopi arabika single origin dari dataran tinggi Gayo, Aceh. Dipetik dan disangrai oleh roastery mikro binaan lokal dengan profil medium roast: aroma floral, kadar asam seimbang, dan aftertaste cokelat mani. Cocok untuk manual brew maupun espresso.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAb8vNmBhkQliatuOhn_dSpgaIcq1kR34wH62U2gs_64JjO7boYYIJGAxJ3IaQVlZyd6loWtwv7p4n7Mh7xZBEG1kvN1Gj6wh2903u1o1dyotdcg_dIULXMDZj91yhG2ySPWL-q-blMP3h8ygOnDxVgBEoPY4mGzvNmB2zQUoQ4emPdLNNuXPrKfNQ2jrid-0qfvHaiADidf0Zpag281sOCqIsPR_oMTdQoD1Dl8ipb',
            ],
            [
                'name' => 'Kopi Robusta Lampung 500g Sangrai Tradisional',
                'category' => 'minuman', 'store' => 'Toko Kopi Gayo Aceh',
                'price' => 72000, 'stock' => 18, 'sold_count' => 94, 'rating' => 4.8,
                'origin' => 'Lampung Barat',
                'description' => 'Kopi robusta lampung dengan proses sangrai tradisional. Body tebal, pahit kokoh, dan aroma kayu manis khas kopi robusta asli. Pilihan tepat untuk kopi tubruk dan kopi susu gula aren.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDrr04kGoVNdNLYrhEBkHjWmZdS3-ZnV7IaasNGY_bE21gptbcVmHpUS_8UCNcxAYPdRVWQkYs0T_YUT8ZSmFCGTNQrJI7OKj7aVQ9KuOG5292IAs0c15xxT_PHcJ05jIueH3i-0GcKDWLw0cSwTDNd8uRDYVnHiv-FNW-oODImKIMqWhOGWo0iJqUwQevzEroipO-cdZQ2ERlN8B4xLkX60mphgcxXZc8-aU614xvT',
            ],
            [
                'name' => 'Kopi Susu Aren Literan 1L Racikan Segar',
                'category' => 'minuman', 'store' => 'Toko Kopi Gayo Aceh',
                'price' => 45000, 'stock' => 0, 'sold_count' => 210, 'rating' => 4.7,
                'origin' => 'Siap Minum',
                'description' => 'Kopi susu gula aren literan siap minum, racikan segar setiap pagi dari espresso house blend dan santan gula aren asli. Simpan dingin, nikmati maksimal 3 hari.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCNGDjP8RFs0kZQ4rMmt3lWs50_3K9u2mo0QAveG4qpzUP6E_xUQQ_lV-cvSusd6ykFq_kOZDCkC3bxlazKbH_t9clgH871w1QcaLG4Oi2ddHEVfDTjzZtRgxEZfRPLsy_wPi7TvMjkz1AnQLG1jBZ28SKJQsucq0bjwvqc80S73WxLizWbJ1lIHyyHl2V5g377epnGdjrR-GyBgX3PIl6oectcCzZc_wxFMXDsVdfL',
            ],
            [
                'name' => 'Kopi Luwak Liar Sachet Praktis Isi 10pcs',
                'category' => 'minuman', 'store' => 'Toko Kopi Gayo Aceh',
                'price' => 89000, 'stock' => 9, 'sold_count' => 42, 'rating' => 5.0,
                'origin' => 'Premium Sachet',
                'description' => 'Kopi luwak liar asli Sumatra dalam kemasan drip sachet praktis isi 10. Fermentasi alami menghasilkan karakter earthy, manis lembut, dan aroma buah tropis.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCmVaWQsTBkPYFpBNq81DDkbyr43IuUmY5t2Nb-heCh8dBkmaYgB92Re35I_hXE9hN-ZZ0-oklZYAN1l902MAlmh1-dCpo55XIMcCbehB3DvjlrannWzujF39gVHevhzPqdQ-4KDlBJyJxvheI_L7XV6Ef9dWVOGBYtoC-9r3Bru1QRFUiR_RUMKQxUASxU_V47ua3ziyph10zetzKxpXhBS89LmihHU4XBc7oIygTa',
            ],
            [
                'name' => 'Bubuk Kopi Toraja Kalosi Asli 200g Medium',
                'category' => 'minuman', 'store' => 'Toko Kopi Gayo Aceh',
                'price' => 58000, 'stock' => 15, 'sold_count' => 67, 'rating' => 4.9,
                'origin' => 'Toraja Kalosi',
                'description' => 'Bubuk kopi Toraja Kalosi giling halus dari Sulawesi Selatan. Rasa earthy dengan sentuhan rempah dan kekacangan, cocok diseduh panas maupun cold brew.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuD0WpG9YYz7ZdOK5nZpRTfPPLs9COafhLYYndpwyYtigS4BSqDncNZ6VcgYTcdtb9kzYVakcdXEEH0YneYvWZ6PdlH2nrB4jyRqJP-Q4IX7fBR-Sz8H6sJTNV6fHyZJpKicqs80sok0XaZFQL3dOLH-IULZZhjQR5cudW5WdDRmRtZiazK7jy7sAhLrOCLsezwDxESqeEtKOIIVN7ff37titGoDe73vB5IN9RVW2_KF',
            ],
            [
                'name' => 'Kopi Cascara Teh Kulit Buah Kopi Alami 100g',
                'category' => 'minuman', 'store' => 'Toko Kopi Gayo Aceh',
                'price' => 30000, 'stock' => 30, 'sold_count' => 23, 'rating' => 4.7,
                'origin' => 'Teh Cascara',
                'description' => 'Teh herbal dari kulit buah kopi (cascara) yang dikeringkan sinar matahari. Rasa manis-asam segar seperti tamarin dan madu, kaya antioksidan, kafein rendah.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCQcZoKndsIugZEZG9ngGUTLp7kNXs9drO0CD95PWbjITzS5yBPHM-_Bwi8-2XqCdpQmC9LR_WN09DMlam6TarG8peGcFDeAh8mH6APKI3HY37mVkOx0QK2Wd1LHrgnBpHO57iD5EFyDm-X2YhSIGQgq3IblCNnQQO147_0w3UyTUMbUk0UZfuSNpt0HVxKL2hZu-hS9UEC7UDP9QLdpP5TpnApDpKZpVUyc24Rz9jx',
            ],
            [
                'name' => 'Cold Brew Flores Bajawa 250ml Siap Minum',
                'category' => 'minuman', 'store' => 'Toko Kopi Gayo Aceh',
                'price' => 38000, 'stock' => 3, 'sold_count' => 88, 'rating' => 4.9,
                'origin' => 'Siap Minum',
                'description' => 'Cold brew single origin Flores Bajawa, diseduh dingin 16 jam untuk rasa manis alami, rendah asam, dan body silky. Sajikan dingin langsung dari kulkas.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAu-Mp8u9L3ZVGsu8uBCiKsKvSZyCDogmr6M4cJFPi_HgI80k4kSMMoCgGYYUOLxXaY1NiSCKV6hh9nYAqoybwpUtkUsPdIFfm4mm5-MgftvMSHq8zHefczmWy2ecEXIjPnR5fDscNmW848_j1p5Kizxc1zeiZ8z8q5ulkqXjiUEvhmKLDcDUFyKvhTXKBruOa7CbjMF1x1nTFKn6XjjlJMSdfbskIA7QhmSYq71SGR',
            ],
            [
                'name' => 'Kopi Biji Bali Kintamani 250g Whole Bean',
                'category' => 'minuman', 'store' => 'Toko Kopi Gayo Aceh',
                'price' => 68000, 'original_price' => 75000, 'stock' => 12, 'sold_count' => 151, 'rating' => 4.8,
                'origin' => 'Citrusy Notes',
                'description' => 'Biji kopi arabika Kintamani, Bali dengan proses honey. Notes jeruk, gula merah, dan floral yang khas dataran tinggi Kintamani.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCXujKeJEtT55aOP5GbiqeWMDcDcqgAerAgw5B4WR0w1e9-ebtvaTYFps3DkVOB7Rm5AFX9eJPtjUWTNq20Ie9L6xnuL1SY-3ysgJ1HIWaWFhJbigbP0XxsO8XzZI_Q1Od0VfsvowRIb5mAdzL1_30t-qUpwnihKsTqlzWKfRdMiq5kjeA0SpY-leN-tFBAjmAiIN5Nl1hQQ4xWnF-vAfME0mqJid9fd1mJ35tMltp6',
            ],

            // --- Produk non-kopi (etalase beranda) ---
            [
                'name' => 'Keripik Tempe Original',
                'category' => 'makanan', 'store' => 'Toko Kopi Gayo Aceh',
                'price' => 18000, 'stock' => 60, 'sold_count' => 340, 'rating' => 4.8,
                'origin' => 'Jawa Tengah',
                'description' => 'Keripik tempe renyah khas Jawa Tengah, digoreng ganda dengan bumbu rahasia keluarga. Tanpa MSG, renyah tahan lama hingga 3 bulan.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCPHfVKqr7OR2K2lgdCBOu-z2LLWBHWyGVZiZ9cBIxtoRo0ukqFKuFUtYwwBhCZQkdV1IKtAbstKs1vQxcFNrvChRcKxc19yokeE5zEV2mFUapBI6pU5KpFnrWoejBg_qGTWNHCiCNNtpG6DlTEpJXK4Zlyd8rwQ8wugO3jGbssQQCrAoEXjfkQ6f-eOGpYjGcIzE7Hv2cuYvAtMv7zY3159tVV5Fa90HghyzwHgHx1',
            ],
            [
                'name' => 'Sambal Roa Botol 180ml',
                'category' => 'makanan', 'store' => 'Toko Kopi Gayo Aceh',
                'price' => 35000, 'original_price' => 42000, 'stock' => 4, 'sold_count' => 150, 'rating' => 4.9,
                'origin' => 'Kota Manado',
                'description' => 'Sambal Roa khas Manado dibuat dari ikan roa asap pilihan yang ditumbuk halus bersama cabai rawit segar dan rempah nusantara. Aroma asap menggugah selera dengan cita rasa pedas gurih autentik, tanpa bahan pengawet kimiawi buatan.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBG21F5Rq6r02qtpezo6wG7PzeNnTT7FfBciF3oQRJqmOYUZRj7lrTZRqWqPp7McCDvEl2MzG0K5MUyTAAAedWrivd_Go4-F9ZAtVmiZPKsV4Cj_WhE0-mClM3tqmCOlpoliGhR5w-PHIQpG3EpFHFKjhO4aHGdhQfY7RsDT8ZS1aPCcM1HBo_JbqJ0NPRQ27shGmPDfpktHbLmsE22qFZAkTS86eu4kOomwzfWGQRT',
            ],
            [
                'name' => 'Madu Hutan Sumbawa 500ml',
                'category' => 'minuman', 'store' => 'Madu Hutan Sumbawa',
                'price' => 98000, 'stock' => 15, 'sold_count' => 76, 'rating' => 4.9,
                'origin' => 'Sumbawa',
                'description' => 'Madu hutan murni dari lebah apis dorsata di hutan Sumbawa. Dipanen berkelanjutan tanpa pakan buatan, kaya enzim alami dengan rasa manis smoky khas.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuDmfPmMNz4DMDn0PPNEEZrbvSI0QY5CahgVOqkO_I2jWK6MZ3T6f43r_Phx5cl_k999cUQiyHk8JgvLw6mUrysikjzvCjZsF2qTqkyYIXqbQHvgBmoK2HabO0okxesYeFbUTT2FZgbRcmTh7pCX8exEZ5ie5rU4zhLmN5XOD7RGB3F7AYPkifQk92NMdCt1BJVVsYG5LuU4OJIh1kIUfXjZNZj53UwNrLoq_kCnW-5B',
            ],
            [
                'name' => 'Tas Anyaman Bambu',
                'category' => 'kerajinan', 'store' => 'Tenun Sasak Lombok',
                'price' => 125000, 'stock' => 8, 'sold_count' => 54, 'rating' => 4.9,
                'origin' => 'Lombok',
                'description' => 'Tas tangan anyaman bambu buatan pengrajin Lombok. Tenun tradisional rapat, finishing halus anti jamur, dan jahitan kuat tahan pakai bertahun-tahun.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuACyLzNLuUSE_Js8Jd-fu1kz_5JxzXYZKu9Cmyr9VRw65EBdWaOZInvSUaQJZR2hBzOJnFbPa429FIcd66VMlL0Lyxne2VSQ_cTCtpu1kgBJs5egWJkAMZMcZwnOODToc5puiItKrzRfORvADlTLC3R_UJe__0r5aQrAlp_xaEJk0snOChhIBXQ2a1bCRbLFYZtCQAj6gDGvlSzYSl4kUt_JZgHFC8G7SCYJUV_pqvQ',
            ],
            [
                'name' => 'Batik Tulis Kain 2m',
                'category' => 'fashion', 'store' => 'Tenun Sasak Lombok',
                'price' => 275000, 'original_price' => 310000, 'stock' => 5, 'sold_count' => 38, 'rating' => 5.0,
                'origin' => 'Pekalongan',
                'description' => 'Kain batik tulis premium 2 meter dengan motif parang klasik. Digambar tangan oleh perajin Pekalongan menggunakan malam alami dan pewarna indigosol.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuBFYgkP9FjEMix3IacCEz9OJdklWw74fvSTMBm1rD-CMWKF3-X85xQHUZu5_FxGN0UEJLu2pwGTyXFbajz4XXNXh2XI8Hye0YYSl8P9u1ZHO9_H6E84uIDPEkUFEjy7LNS2s-pI7mJ1ESDusqh7AqBCcQHnJ0o0kXMOhPVHuRB_dGDMNH_tyj03ttk8VyFtHcDtBS2XzBxHqUHvG5CNkjrf_ViQuCVKi6EWgphx8vMD',
            ],
            [
                'name' => 'Teh Rempah Jahe Merah',
                'category' => 'minuman', 'store' => 'Madu Hutan Sumbawa',
                'price' => 22000, 'stock' => 32, 'sold_count' => 265, 'rating' => 4.8,
                'origin' => 'Jawa Barat',
                'description' => 'Campuran teh herbal jahe merah, kayu manis, dan kapulaga kemasan premium. Melimpah manis-warm, cocok dinikmati hangat di pagi hari.',
                'image' => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAqSSphLrGhp6lIZhCK0epRoS04S1ogAdB6IO9eVE5lP2fptrwnwcPIQpmI9Pex2AKnXyR_PGaKqDDLl0SnZJBZDafyLCYh6NgL0hDltceGb82oWy52wIUvQgydfE_wEFGKwOMtIxOYOpR6ff3pnxDyo-0f446picbODU97Zp5S8mln8Tu5C1L7nlYjI_xXV9Kg6zLomNGFehML4IH49ltajRqzSq2uIxdl_VohCDb6',
            ],
        ];

        foreach ($products as $item) {
            $category = Category::where('slug', $item['category'])->first();
            if (! $category) {
                continue;
            }

            Product::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($item['name'])],
                [
                    'name' => $item['name'],
                    'category_id' => $category->id,
                    'store_id' => $storeId($item['store']),
                    'description' => $item['description'],
                    'price' => $item['price'],
                    'original_price' => $item['original_price'] ?? null,
                    'stock' => $item['stock'],
                    'sold_count' => $item['sold_count'],
                    'rating' => $item['rating'],
                    'origin' => $item['origin'],
                    'image' => $item['image'],
                    'is_active' => true,
                ]
            );
        }
    }
}
