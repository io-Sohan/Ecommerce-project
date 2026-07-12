<?php

namespace App\Http\Middleware;

use App\Models\Coupon;
use App\Services\CartService;
use App\Services\WishlistService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    public function __construct(
        private readonly CartService $cartService,
        private readonly WishlistService $wishlistService,
    ) {}

    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'cart' => fn () => [
                'qty' => $this->cartService->totalQty(),
                'items' => $this->cartService->totalQty() > 0
                    ? $this->cartService->items()
                    : [],
            ],
            'wishlist' => fn () => [
                'count' => $this->wishlistService->count(),
                'productIds' => $this->wishlistService->productIds(),
                'items' => $this->wishlistService->count() > 0
                    ? $this->wishlistService->items()
                    : [],
            ],
            'activeCoupons' => fn () => Coupon::query()
                ->where('is_active', true)
                ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', now()->toDateString()))
                ->where(fn ($q) => $q->whereNull('usage_limit')->orWhereColumn('times_used', '<', 'usage_limit'))
                ->orderByDesc('created_at')
                ->get(['code', 'discount_type', 'discount_value', 'min_order_amount', 'expires_at'])
                ->map(fn (Coupon $coupon): array => [
                    'code' => $coupon->code,
                    'discount_type' => $coupon->discount_type,
                    'discount_value' => (float) $coupon->discount_value,
                    'min_order_amount' => (float) $coupon->min_order_amount,
                    'expires_at' => $coupon->expires_at?->toDateString(),
                ])
                ->all(),
        ];
    }
}
