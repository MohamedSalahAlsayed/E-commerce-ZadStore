<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymobGateway extends BaseGateway
{
    /**
     * Paymob Workflow:
     * 1. Authentication Request -> Token
     * 2. Order Registration -> Order ID
     * 3. Payment Key Generation -> Payment Token
     * 4. Redirect user to iframe with Payment Token
     */
    public function processPayment(Order $order)
    {
        $this->log("Initiating Paymob payment for Order #{$order->order_number}");

        // Placeholder for real API calls
        // $token = $this->authenticate();
        // $paymobOrderId = $this->registerOrder($token, $order);
        // $paymentToken = $this->getPaymentToken($token, $paymobOrderId, $order);

        // Record the transaction attempt
        $this->recordTransaction($order, [
            'method' => 'paymob',
            'status' => 'pending',
            'payload' => ['step' => 'initiation'],
        ]);

        // Return the redirect URL (Mocking Paymob iframe URL)
        $iframeId = $this->config['iframe_id'] ?? '12345';
        $paymentToken = "SAMPLE_TOKEN_" . uniqid();
        
        return "https://portal.paymob.com/api/acceptance/iframes/{$iframeId}?payment_token={$paymentToken}";
    }

    public function handleWebhook(Request $request)
    {
        $this->log("Received Paymob Webhook", $request->all());

        $success = $request->input('obj.success') === true;
        $orderId = $request->input('obj.order.shipping_data.extra_description'); // We usually store our Order ID here
        $transactionId = $request->input('obj.id');

        return [
            'success' => $success,
            'order_id' => $orderId,
            'transaction_id' => $transactionId,
            'payload' => $request->all(),
            'error' => $success ? null : 'Transaction failed via Paymob',
        ];
    }

    public function verifyTransaction(string $transactionId): bool
    {
        // Call Paymob API to verify the status of a transaction ID
        return true; 
    }
}
