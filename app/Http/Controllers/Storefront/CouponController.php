<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Services\CouponService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Validate and return discount info for a coupon code.
     */
    public function apply(Request $request, CouponService $couponService): JsonResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'max:50'],
            'subtotal' => ['required', 'numeric', 'min:0'],
        ]);

        $result = $couponService->validate(
            (string) $request->input('code'),
            (float) $request->input('subtotal'),
        );

        if (! $result['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $result['message'],
            ], 422);
        }

        $coupon = $result['coupon'];

        return response()->json([
            'valid' => true,
            'message' => $result['message'],
            'coupon_code' => $coupon->code,
            'discount_type' => $coupon->discount_type,
            'discount_value' => (float) $coupon->discount_value,
            'discount_amount' => $result['discount'],
        ]);
    }
}
