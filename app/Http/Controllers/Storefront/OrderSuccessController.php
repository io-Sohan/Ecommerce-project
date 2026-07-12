<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Inertia\Inertia;
use Inertia\Response;

class OrderSuccessController extends Controller
{
    /**
     * Display the order success page.
     */
    public function __invoke(): Response
    {
        $orderData = session('order');

        $order = null;

        if ($orderData !== null && isset($orderData['orderNumber'])) {
            $order = $this->buildFullOrderPayload($orderData['orderNumber']);
        }

        return Inertia::render('shop/OrderSuccess', [
            'order' => $order,
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildFullOrderPayload(string $orderNumber): ?array
    {
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
