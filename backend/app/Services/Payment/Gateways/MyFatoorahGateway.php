<?php

namespace App\Services\Payment\Gateways;

use App\Models\Order;
use Illuminate\Http\Request;

class MyFatoorahGateway extends BaseGateway
{
    public function processPayment(Order $order)
    {
        $this->log("Initiating MyFatoorah payment for Order #{$order->order_number}");

        $this->recordTransaction($order, [
            'method' => 'myfatoorah',
            'status' => 'pending',
        ]);

        return "https://demo.myfatoorah.com/En/KNET/PayEntity/002";
    }

    public function handleWebhook(Request $request)
    {
        $this->log("Received MyFatoorah Webhook", $request->all());

        return [
            'success' => true, // logic here
            'order_id' => null, 
            'transaction_id' => null,
            'payload' => $request->all(),
        ];
    }

    public function verifyTransaction(string $transactionId): bool
    {
        return true;
    }
}
