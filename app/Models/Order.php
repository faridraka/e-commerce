<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public const STATUS_MENUNGGU_PEMBAYARAN = 'menunggu_pembayaran';

    public const STATUS_DIBAYAR = 'dibayar';

    public const STATUS_DIPROSES = 'diproses';

    public const STATUS_DIKIRIM = 'dikirim';

    public const STATUS_SELESAI = 'selesai';

    public const STATUS_DIBATALKAN = 'dibatalkan';

    public const STATUS_KEDALUWARSA = 'kedaluwarsa';

    protected $fillable = [
        'order_code',
        'user_id',
        'address_id',
        'channel',
        'status',
        'total_amount',
        'customer_name',
        'shipping_tracking_number',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Pembayaran terakhir untuk pesanan ini.
     */
    public function latestPayment(): ?Payment
    {
        return $this->payments()->orderByDesc('id')->first();
    }

    /**
     * Buat kode pesanan unik berformat ORD-YYYYMMDD-XXXX.
     */
    public static function generateCode(): string
    {
        do {
            $code = 'ORD-' . now()->format('Ymd') . '-' . str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('order_code', $code)->exists());

        return $code;
    }
}
