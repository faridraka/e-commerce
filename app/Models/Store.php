<?php

namespace App\Models;

use Database\Factories\StoreFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Store extends Model
{
    /** @use HasFactory<StoreFactory> */
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'activation_code',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Menandai toko ini sebagai terverifikasi dan diklaim oleh user tertentu.
     */
    public function claimBy(User $user): void
    {
        $this->forceFill([
            'user_id' => $user->id,
            'verified_at' => now(),
        ])->save();
    }
}
