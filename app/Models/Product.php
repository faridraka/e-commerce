<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;
    protected $fillable = [
        'category_id',
        'store_id',
        'name',
        'slug',
        'description',
        'price',
        'original_price',
        'stock',
        'image',
        'is_active',
        'sold_count',
        'rating',
        'origin',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'original_price' => 'decimal:2',
            'rating' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class)->latest();
    }

    /**
     * Persentase diskon dibanding harga awal, null bila tidak ada promo.
     */
    public function discountPercent(): ?int
    {
        if (! $this->original_price || (float) $this->original_price <= (float) $this->price) {
            return null;
        }

        return (int) round((1 - ((float) $this->price / (float) $this->original_price)) * 100);
    }

    /**
     * Status ketersediaan untuk badge stok.
     */
    public function stockStatus(): string
    {
        if ($this->stock <= 0) {
            return 'habis';
        }

        return $this->stock <= 5 ? 'menipis' : 'tersedia';
    }

    /**
     * Scope pencarian sederhana pada nama dan deskripsi produk.
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        $term = trim($term);

        return $query->where(function (Builder $w) use ($term) {
            $w->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%");
        });
    }
}
