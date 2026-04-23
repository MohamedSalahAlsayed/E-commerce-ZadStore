<?php

namespace App\Services\Payment\Gateways;

use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Models\PaymentTransaction;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

abstract class BaseGateway implements PaymentGatewayInterface
{
    protected $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Record a transaction attempt in the database.
     */
    protected function recordTransaction(Order $order, array $data)
    {
        return PaymentTransaction::create([
            'order_id' => $order->id,
            'transaction_id' => $data['transaction_id'] ?? null,
            'payment_method' => $data['method'],
            'amount' => $order->total,
            'currency' => $data['currency'] ?? 'EGP',
            'status' => $data['status'] ?? 'pending',
            'payload' => $data['payload'] ?? null,
            'error_message' => $data['error'] ?? null,
        ]);
    }

    /**
     * Log payment events for debugging.
     */
    protected function log(string $message, array $context = [], string $level = 'info')
    {
        Log::channel('payment')->$level("[Payment] " . $message, $context);
    }
}
