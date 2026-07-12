<?php

namespace App\Models;

use Database\Factories\CouponFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'code',
    'discount_type',
    'discount_value',
    'min_order_amount',
    'usage_limit',
    'times_used',
    'expires_at',
    'is_active',
])]
class Coupon extends Model
{
    /** @use HasFactory<CouponFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'usage_limit' => 'integer',
            'times_used' => 'integer',
            'expires_at' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected $attributes = [
        'min_order_amount' => 0,
        'times_used' => 0,
        'is_active' => true,
    ];

    /**
     * Check if the coupon is valid for the given order subtotal.
     */
    public function isValid(float $subtotal): bool
    {
        if (! $this->is_active) {
            return false;
        }

        if ($this->expires_at !== null && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->times_used >= $this->usage_limit) {
            return false;
        }

        if ($subtotal < (float) $this->min_order_amount) {
            return false;
        }

        return true;
    }

    /**
     * Calculate the discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->discount_type === 'percentage') {
            return round($subtotal * (float) $this->discount_value / 100, 2);
        }

        return min((float) $this->discount_value, $subtotal);
    }

    /**
     * Increment the usage counter.
     */
    public function markAsUsed(): void
    {
        $this->increment('times_used');
    }
}
