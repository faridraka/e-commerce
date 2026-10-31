<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public const TYPE_SALE = 'penjualan';

    public const TYPE_RETURN = 'pengembalian';

    public const TYPE_ADJUSTMENT = 'penyesuaian';

    protected $fillable = [
        'product_id',
        'order_id',
        'type',
        'quantity',
        'note',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
