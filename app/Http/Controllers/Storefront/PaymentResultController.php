<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PaymentResultController extends Controller
{
    /**
     * Display the payment success page.
     */
    public function success(Request $request): Response
    {
        return Inertia::render('shop/PaymentSuccess', [
            'order' => $this->buildFullOrderPayload($request->query('order')),
        ]);
    }

    /**
     * Display the payment failed page.
     */
    public function failed(Request $request): Response
    {
        return Inertia::render('shop/PaymentFailed', [
            'order' => $this->orderSummary($request->query('order')),
        ]);
    }

    /**
     * Display the payment cancelled page.
     */
    public function cancelled(Request $request): Response
    {
        return Inertia::render('shop/PaymentCancelled', [
            'order' => $this->orderSummary($request->query('order')),
        ]);
    }

    /**
     * @return array{orderNumber: string, total: float, paymentLabel: string}|null
     */
    private function orderSummary(?string $orderNumber): ?array
    {
        if (! filled($orderNumber)) {
            return null;
        }

        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->first();

        if ($order === null) {
            return null;
        }

        return [
            'orderNumber' => $order->order_number,
            'total' => (float) $order->total,
            'paymentLabel' => match ($order->payment_method) {
                'stripe' => 'Stripe',
                'sslcommerz' => 'SSLCommerz',
                default => 'Cash on Delivery',
            },
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildFullOrderPayload(?string $orderNumber): ?array
    {
        if (! filled($orderNumber)) {
            return null;
        }

        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->with('items')
            ->first();

        if ($order === null) {
            return null;
        }

        return [
            'orderNumber' => $order->order_number,
            'total' => (float) $order->total,
            'subtotal' => (float) $order->subtotal,
            'discountAmount' => (float) $order->discount_amount,
            'couponCode' => $order->coupon_code,
            'deliveryCharge' => (float) $order->delivery_charge,
            'paymentLabel' => match ($order->payment_method) {
                'stripe' => 'Stripe',
                'sslcommerz' => 'SSLCommerz',
                default => 'Cash on Delivery',
            },
            'paymentMethod' => $order->payment_method,
            'paymentStatus' => $order->payment_status,
            'status' => $order->status,
            'placedAt' => $order->placed_at?->toIso8601String(),
            'customerName' => $order->customer_name,
            'phone' => $order->phone,
            'email' => $order->email,
            'district' => $order->district,
            'area' => $order->area,
            'address' => $order->address,
            'notes' => $order->notes,
            'items' => $order->items
                ->map(fn ($item): array => [
                    'id' => $item->id,
                    'productName' => $item->product_name,
                    'unitPrice' => (float) $item->unit_price,
                    'quantity' => (int) $item->quantity,
                    'lineTotal' => (float) $item->line_total,
                ])
                ->values()
                ->all(),
        ];
    }
}
