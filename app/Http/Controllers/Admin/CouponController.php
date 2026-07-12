<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCouponRequest;
use App\Http\Requests\Admin\UpdateCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class CouponController extends Controller
{
    /**
     * Display a listing of the coupons.
     */
    public function index(): Response
    {
        $coupons = Coupon::query()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Coupon $coupon): array => $this->couponPayload($coupon));

        return Inertia::render('admin/coupons/Index', [
            'coupons' => $coupons,
        ]);
    }

    /**
     * Show the form for creating a new coupon.
     */
    public function create(): Response
    {
        return Inertia::render('admin/coupons/Create');
    }

    /**
     * Store a newly created coupon in storage.
     */
    public function store(StoreCouponRequest $request): RedirectResponse
    {
        Coupon::query()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Coupon created.')]);

        return to_route('admin.coupons.index');
    }

    /**
     * Show the form for editing the specified coupon.
     */
    public function edit(Coupon $coupon): Response
    {
        return Inertia::render('admin/coupons/Edit', [
            'coupon' => $this->couponPayload($coupon),
        ]);
    }

    /**
     * Update the specified coupon in storage.
     */
    public function update(UpdateCouponRequest $request, Coupon $coupon): RedirectResponse
    {
        $coupon->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Coupon updated.')]);

        return to_route('admin.coupons.index');
    }

    /**
     * Remove the specified coupon from storage.
     */
    public function destroy(Coupon $coupon): RedirectResponse
    {
        $coupon->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Coupon deleted.')]);

        return to_route('admin.coupons.index');
    }

    /**
     * @return array{
     *     id: int,
     *     code: string,
     *     discount_type: string,
     *     discount_value: float,
     *     min_order_amount: float,
     *     usage_limit: int|null,
     *     times_used: int,
     *     expires_at: string|null,
     *     is_active: bool,
     *     created_at: string,
     *     updated_at: string
     * }
     */
    private function couponPayload(Coupon $coupon): array
    {
        return [
            'id' => $coupon->id,
            'code' => $coupon->code,
            'discount_type' => $coupon->discount_type,
            'discount_value' => (float) $coupon->discount_value,
            'min_order_amount' => (float) $coupon->min_order_amount,
            'usage_limit' => $coupon->usage_limit,
            'times_used' => $coupon->times_used,
            'expires_at' => $coupon->expires_at?->toDateString(),
            'is_active' => $coupon->is_active,
            'created_at' => $coupon->created_at?->toIso8601String() ?? '',
            'updated_at' => $coupon->updated_at?->toIso8601String() ?? '',
        ];
    }
}
