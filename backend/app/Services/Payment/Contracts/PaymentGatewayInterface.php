<?php

namespace App\Services\Payment\Contracts;

use App\Models\Order;
use Illuminate\Http\Request;

interface PaymentGatewayInterface
{
    /**
     * Process the payment for the given order.
     *
     * @param Order $order
     * @return mixed (usually a redirect URL or a response object)
     */
    public function processPayment(Order $order);

    /**
     * Handle the webhook/callback from the payment provider.
     *
     * @param Request $request
     * @return array ['success' => bool, 'transaction_id' => string, 'payload' => array]
     */
    public function handleWebhook(Request $request);

    /**
     * Verify the transaction status with the provider.
     *
     * @param string $transactionId
     * @return bool
     */
    public function verifyTransaction(string $transactionId): bool;
}
