<?php

namespace App\Services;

use App\Models\Coupon;
use Illuminate\Support\Str;

class CouponService
{
    /**
     * Validate a coupon code against the given subtotal.
     *
     * @return array{valid: bool, coupon: Coupon|null, discount: float, message: string}
     */
    public function validate(string $code, float $subtotal): array
    {
        $coupon = Coupon::query()
            ->where('code', Str::upper(trim($code)))
            ->first();

        if ($coupon === null) {
            return ['valid' => false, 'coupon' => null, 'discount' => 0, 'message' => 'Invalid coupon code.'];
        }

        if (! $coupon->is_active) {
            return ['valid' => false, 'coupon' => null, 'discount' => 0, 'message' => 'This coupon is no longer active.'];
        }

        if ($coupon->expires_at !== null && $coupon->expires_at->isPast()) {
            return ['valid' => false, 'coupon' => null, 'discount' => 0, 'message' => 'This coupon has expired.'];
        }

        if ($coupon->usage_limit !== null && $coupon->times_used >= $coupon->usage_limit) {
            return ['valid' => false, 'coupon' => null, 'discount' => 0, 'message' => 'This coupon has reached its usage limit.'];
        }

        if ($subtotal < (float) $coupon->min_order_amount) {
            $min = number_format((float) $coupon->min_order_amount, 0);

            return ['valid' => false, 'coupon' => null, 'discount' => 0, 'message' => "Minimum order amount is ৳{$min}."];
        }

        $discount = $coupon->calculateDiscount($subtotal);

        return ['valid' => true, 'coupon' => $coupon, 'discount' => $discount, 'message' => 'Coupon applied successfully!'];
    }
}
