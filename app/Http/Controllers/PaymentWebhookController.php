<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PaymentWebhookController extends Controller
{
    /**
     * Handle incoming webhook requests from Razorpay.
     */
    public function handle(Request $request, PaymentService $paymentService): Response
    {
        $rawPayload = (string) $request->getContent();
        $signature = (string) $request->header('X-Razorpay-Signature', '');

        // 1. Signature validation using the Razorpay Webhook Secret
        if (! $paymentService->verifyWebhookSignature($rawPayload, $signature)) {
            return response()->json(['error' => 'Invalid webhook signature.'], 400);
        }

        // 2. Decode event payload
        $event = json_decode($rawPayload, true);

        if (! is_array($event)) {
            return response()->json(['error' => 'Invalid JSON payload.'], 400);
        }

        // 3. Process event idempotently
        $paymentService->processWebhook($event);

        return response()->json(['status' => 'success']);
    }
}
