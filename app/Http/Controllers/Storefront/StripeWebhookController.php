<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\StripeCheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __construct(private readonly StripeCheckoutService $checkoutService) {}

    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = config('services.stripe.webhook_secret');

        if (blank($payload) || blank($signature) || blank($secret)) {
            return response()->json(['error' => 'Invalid Stripe webhook request.'], 400);
        }

        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
        } catch (\UnexpectedValueException|SignatureVerificationException $exception) {
            return response()->json(['error' => 'Invalid Stripe signature.'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $orderNumber = $session->metadata->order_number ?? null;

            if (filled($orderNumber)) {
                $order = Order::query()->where('order_number', $orderNumber)->first();

                if ($order !== null) {
                    $this->checkoutService->markAsPaid($order);
                }
            }
        }

        return response()->json(['received' => true]);
    }
}
