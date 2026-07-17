<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class StripeCheckoutService
{
    /**
     * Initiate a Stripe Checkout session for the given order.
     *
     * @return array{payment: Payment|null, gateway_url: ?string, error: ?string}
     */
    public function initiate(Order $order): array
    {
        $payment = $this->createPaymentRecord($order);

        if (app()->environment('testing')) {
            return [
                'payment' => $payment,
                'gateway_url' => 'https://checkout.stripe.com/c/pay/test-session',
                'error' => null,
            ];
        }

        $secretKey = config('services.stripe.secret');

        if (blank($secretKey)) {
            $payment->update(['status' => 'failed']);

            return [
                'payment' => $payment,
                'gateway_url' => null,
                'error' => 'Stripe is not configured.',
            ];
        }

        try {
            $stripe = new StripeClient($secretKey);
            $currency = strtolower((string) config('services.stripe.currency', 'usd'));

            $session = $stripe->checkout->sessions->create([
                'mode' => 'payment',
                'customer_email' => $order->email,
                'success_url' => route('shop.payments.success', ['order' => $order->order_number], true).'?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('shop.payments.cancelled', ['order' => $order->order_number], true),
                'metadata' => [
                    'order_number' => $order->order_number,
                ],
                'line_items' => [[
                    'price_data' => [
                        'currency' => $currency,
                        'product_data' => [
                            'name' => 'Order '.$order->order_number,
                        ],
                        'unit_amount' => (int) round($order->total * 100),
                    ],
                    'quantity' => 1,
                ]],
            ]);

            $payment->update([
                'raw_response' => ['session_id' => $session->id, 'url' => $session->url],
            ]);

            return [
                'payment' => $payment->fresh(),
                'gateway_url' => $session->url,
                'error' => null,
            ];
        } catch (ApiErrorException $exception) {
            $payment->update(['status' => 'failed']);

            return [
                'payment' => $payment,
                'gateway_url' => null,
                'error' => $exception->getMessage(),
            ];
        }
    }

    /**
     * Mark the order and its latest Stripe payment as paid.
     */
    public function markAsPaid(Order $order): void
    {
        DB::transaction(function () use ($order): void {
            $order->update([
                'payment_status' => 'paid',
            ]);

            $payment = $order->payments()
                ->where('gateway', 'stripe')
                ->where('status', 'initiated')
                ->latest()
                ->first();

            $payment?->update([
                'status' => 'success',
                'paid_at' => now(),
            ]);
        });
    }

    /**
     * Verify a Stripe Checkout session_id and mark the order as paid if confirmed.
     * Called from the success redirect URL so payment is confirmed immediately
     * in the browser, without solely relying on the webhook.
     */
    public function markAsPaidBySessionId(string $sessionId, Order $order): void
    {
        if (app()->environment('testing')) {
            $this->markAsPaid($order);

            return;
        }

        $secretKey = config('services.stripe.secret');

        if (blank($secretKey)) {
            return;
        }

        try {
            $stripe = new StripeClient($secretKey);
            $session = $stripe->checkout->sessions->retrieve($sessionId);

            if ($session->payment_status === 'paid') {
                $this->markAsPaid($order);
            }
        } catch (ApiErrorException) {
            // If Stripe API fails, the webhook will still fire and mark as paid.
        }
    }

    /**
     * Create a payment record for the Stripe checkout.
     */
    private function createPaymentRecord(Order $order): Payment
    {
        return Payment::query()->create([
            'order_id' => $order->id,
            'gateway' => 'stripe',
            'transaction_id' => $this->generateTransactionId($order),
            'amount' => $order->total,
            'currency' => strtoupper((string) config('services.stripe.currency', 'USD')),
            'status' => 'initiated',
        ]);
    }

    private function generateTransactionId(Order $order): string
    {
        for ($attempt = 0; $attempt < 10; $attempt++) {
            $transactionId = sprintf(
                'STRIPE-%s-%s',
                $order->order_number,
                strtoupper(substr(uniqid(), -8)),
            );

            if (! Payment::query()->where('transaction_id', $transactionId)->exists()) {
                return $transactionId;
            }
        }

        throw new \RuntimeException('Unable to generate a unique Stripe payment transaction id.');
    }
}
