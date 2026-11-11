<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public const PAYMENT_PENDING = 'pending';

    public const PAYMENT_PAID = 'paid';

    public const PAYMENT_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'total',
        'estado',
        'payment_status',
        'payment_provider',
        'payment_reference',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_street',
        'shipping_exterior_number',
        'shipping_interior_number',
        'shipping_neighborhood',
        'shipping_postal_code',
        'shipping_municipality',
        'shipping_state',
        'shipping_references',
    ];

    public function getShippingAddressAttribute(?string $value): ?string
    {
        if ($value !== null && $value !== '') {
            return $value;
        }

        $parts = array_filter([
            trim(($this->shipping_street ?? '').' '.($this->shipping_exterior_number ?? '')),
            $this->shipping_interior_number
                ? 'Interior '.$this->shipping_interior_number
                : null,
            $this->shipping_neighborhood,
            $this->shipping_municipality,
            $this->shipping_state,
            $this->shipping_postal_code
                ? 'C.P. '.$this->shipping_postal_code
                : null,
            $this->shipping_references
                ? 'Referencias: '.$this->shipping_references
                : null,
        ], fn (?string $part): bool => $part !== null && trim($part) !== '');

        return $parts === [] ? null : implode(', ', $parts);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
}
