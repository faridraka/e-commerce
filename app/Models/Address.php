<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'recipient_name',
        'phone',
        'address_detail',
        'city',
        'postal_code',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Jadikan alamat ini satu-satunya alamat default milik user.
     */
    public function markAsDefault(): void
    {
        $this->user->addresses()->update(['is_default' => false]);
        $this->forceFill(['is_default' => true])->save();
    }
}
