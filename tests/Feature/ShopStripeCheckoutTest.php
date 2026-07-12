<?php

use App\Models\Order;
use App\Models\Product;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->withoutMiddleware(PreventRequestForgery::class);
});

/**
 * @return array<string, mixed>
 */
function stripeCheckoutPayload(array $overrides = []): array
{
    return array_merge([
        'customer_name' => 'Rina Akter',
        'phone' => '01712345678',
        'email' => 'rina@example.com',
        'district' => 'Dhaka',
        'area' => 'Dhanmondi',
        'address' => 'House 12, Road 5, Dhanmondi',
        'notes' => 'Call before delivery',
        'payment_method' => 'stripe',
    ], $overrides);
}

test('stripe checkout places order and redirects to stripe checkout', function () {
    $this->seed(DatabaseSeeder::class);

    $product = Product::where('is_active', true)
        ->where('stock_status', 'in_stock')
        ->first();

    $this->post(route('shop.cart.store'), ['product_id' => $product->id, 'qty' => 1]);

    $response = $this->post(route('shop.checkout.store'), stripeCheckoutPayload());

    $response->assertRedirect('https://checkout.stripe.com/c/pay/test-session');

    $order = Order::first();

    expect($order)->not->toBeNull()
        ->and($order->payment_method)->toBe('stripe')
        ->and($order->payment_status)->toBe('pending');

    $this->assertDatabaseHas('payments', [
        'order_id' => $order->id,
        'gateway' => 'stripe',
        'status' => 'initiated',
    ]);
});

test('payment success page renders with stripe order details', function () {
    $order = Order::factory()->create([
        'payment_method' => 'stripe',
        'payment_status' => 'paid',
    ]);

    $this->get(route('shop.payments.success', ['order' => $order->order_number]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('shop/PaymentSuccess')
            ->where('order.orderNumber', $order->order_number)
            ->where('order.paymentLabel', 'Stripe')
        );
});

test('stripe webhook marks order as paid and updates payment record', function () {
    $order = Order::factory()->create([
        'payment_method' => 'stripe',
        'payment_status' => 'pending',
    ]);

    $order->payments()->create([
        'gateway' => 'stripe',
        'transaction_id' => 'STRIPE-'.$order->order_number.'-TESTID01',
        'amount' => $order->total,
        'currency' => 'USD',
        'status' => 'initiated',
    ]);

    // Simulate the webhook by calling the service directly (webhook signature verification makes
    // direct POST testing impractical without a real Stripe secret).
    app(\App\Services\StripeCheckoutService::class)->markAsPaid($order);

    $order->refresh();

    expect($order->payment_status)->toBe('paid');

    $payment = $order->payments()->where('gateway', 'stripe')->first();

    expect($payment->status)->toBe('success')
        ->and($payment->paid_at)->not->toBeNull();
});
