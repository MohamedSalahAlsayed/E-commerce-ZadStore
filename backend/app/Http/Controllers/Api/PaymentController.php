<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Exception;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Initiate payment for an order.
     */
    public function checkout(Request $request, $orderId)
    {
        try {
            $order = Order::findOrFail($orderId);
            
            // Logic: Ensure order belongs to user or is valid for guest
            
            $redirectUrl = $this->paymentService->process($order);

            return response()->json([
                'success' => true,
                'redirect_url' => $redirectUrl
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Handle the return URL from the payment gateway (User redirected back).
     */
    public function callback(Request $request, $method)
    {
        // This is usually where the user lands after payment
        // We verify the status and show a Success/Failure page
        
        $result = $this->paymentService->handleWebhook($request, $method);

        if ($result['success']) {
            return redirect()->away(config('app.frontend_url') . "/payment/success?order_id=" . $result['order_id']);
        }

        return redirect()->away(config('app.frontend_url') . "/payment/failed?order_id=" . $result['order_id']);
    }

    /**
     * Handle background server-to-server notifications (Webhooks).
     */
    public function webhook(Request $request, $method)
    {
        try {
            $this->paymentService->handleWebhook($request, $method);
            return response()->json(['status' => 'received']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
