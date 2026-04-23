<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use Illuminate\Http\Request;

class TapGateway extends BaseGateway
{
    /**
     * Tap Payments Workflow:
     * 1. Create a Charge request.
     * 2. Receive a redirect URL from Tap.
     * 3. Redirect user.
     */
    public function processPayment(Order $order)
    {
        $this->log("Initiating Tap payment for Order #{$order->order_number}");

        // Placeholder for Tap Charge API
        /*
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->config['secret_key']
        ])->post('https://api.tap.company/v2/charges', [
            'amount' => $order->total,
            'currency' => 'SAR', // or KWD, AED
            'customer' => [
                'first_name' => $order->customer_name,
                'email' => $order->user->email ?? 'guest@example.com',
            ],
            'redirect' => [ 'url' => route('payment.callback', ['method' => 'tap']) ]
        ]);
        */

        $this->recordTransaction($order, [
            'method' => 'tap',
            'status' => 'pending',
        ]);

        // Mock redirect
        return "https://checkout.tap.company/sample_checkout_page";
    }

    public function handleWebhook(Request $request)
    {
        $this->log("Received Tap Webhook", $request->all());

        // Logic to parse Tap response
        return [
            'success' => $request->status === 'CAPTURED',
            'order_id' => $request->metadata['order_id'] ?? null,
            'transaction_id' => $request->id,
            'payload' => $request->all(),
        ];
    }

    public function verifyTransaction(string $transactionId): bool
    {
        return true;
    }
}
