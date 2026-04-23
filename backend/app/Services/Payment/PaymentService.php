<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Gateways\PaymobGateway;
use App\Services\Payment\Gateways\TapGateway;
use App\Services\Payment\Gateways\MyFatoorahGateway;
use Exception;
use Illuminate\Http\Request;
use App\Models\PaymentTransaction;

class PaymentService
{
    /**
     * Get the gateway implementation based on the payment method.
     */
    public function getGateway(string $method): PaymentGatewayInterface
    {
        switch ($method) {
            case 'paymob':
                return new PaymobGateway(config('services.paymob') ?? []);
            case 'tap':
                return new TapGateway(config('services.tap') ?? []);
            case 'myfatoorah':
                return new MyFatoorahGateway(config('services.myfatoorah') ?? []);
            default:
                throw new Exception("Payment method [{$method}] not supported.");
        }
    }

    /**
     * Process order payment.
     */
    public function process(Order $order)
    {
        $gateway = $this->getGateway($order->payment_method);
        return $gateway->processPayment($order);
    }

    /**
     * Handle payment provider webhook.
     */
    public function handleWebhook(Request $request, string $method)
    {
        $gateway = $this->getGateway($method);
        $result = $gateway->handleWebhook($request);

        if ($result['success']) {
            $this->finalizePayment($result['order_id'], $result['transaction_id'], $method, $result['payload']);
        } else {
            $this->failPayment($result['order_id'], $result['error'] ?? 'Unknown error', $method, $result['payload']);
        }

        return $result;
    }

    /**
     * Mark order as paid and update lifecycle.
     */
    protected function finalizePayment($orderId, $transactionId, $method, $payload = null)
    {
        $order = Order::findOrFail($orderId);
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);

        PaymentTransaction::create([
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'payment_method' => $method,
            'amount' => $order->total,
            'status' => 'success',
            'payload' => $payload,
        ]);
    }

    /**
     * Mark payment as failed.
     */
    protected function failPayment($orderId, $error, $method, $payload = null)
    {
        $order = Order::findOrFail($orderId);
        $order->update([
            'payment_status' => 'failed',
        ]);

        PaymentTransaction::create([
            'order_id' => $order->id,
            'payment_method' => $method,
            'amount' => $order->total,
            'status' => 'failed',
            'error_message' => $error,
            'payload' => $payload,
        ]);
    }
}
