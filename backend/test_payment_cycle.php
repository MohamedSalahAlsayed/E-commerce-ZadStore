<?php

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🚀 Starting Payment Cycle Test...\n";

// 1. Get or Create a test user
$user = User::first() ?: User::factory()->create(['name' => 'Test User', 'email' => 'test@example.com']);
echo "✅ Using User: {$user->name}\n";

// 2. Get a product
$product = Product::first();
if (!$product) {
    echo "❌ No products found. Please seed the database first.\n";
    exit;
}

// 3. Create a Mock Order
DB::beginTransaction();
try {
    $order = Order::create([
        'user_id' => $user->id,
        'order_number' => 'TEST-' . time(),
        'customer_name' => $user->name,
        'phone' => '01000000000',
        'address' => 'Test Address, Egypt',
        'status' => 'pending',
        'payment_status' => 'unpaid',
        'payment_method' => 'paymob', // Testing with Paymob
        'subtotal' => $product->price,
        'shipping_fee' => 50,
        'total' => $product->price + 50,
        'governorate_id' => 1,
        'shipping_method_id' => 1,
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'name' => $product->title,
        'price' => $product->price,
        'quantity' => 1,
    ]);

    DB::commit();
    echo "✅ Step 1: Order Created (#{$order->order_number}). Payment Status: {$order->payment_status}\n";

    // 4. Simulate Webhook (The Logic part)
    echo "⏳ Step 2: Simulating Success Webhook from Paymob...\n";
    
    $paymentService = app(PaymentService::class);
    
    // Mock Request Object
    $mockRequest = new Request([
        'obj' => [
            'success' => true,
            'id' => 'TXN_PAYMOB_' . time(),
            'order' => [
                'shipping_data' => [
                    'extra_description' => $order->id // Our logic uses this as Order ID
                ]
            ]
        ]
    ]);

    $result = $paymentService->handleWebhook($mockRequest, 'paymob');

    // 5. Verify Results
    $order->refresh();
    echo "✅ Step 3: Webhook Handled.\n";
    echo "📊 Results:\n";
    echo "   - Order Status: {$order->status} (Expected: processing)\n";
    echo "   - Payment Status: {$order->payment_status} (Expected: paid)\n";
    
    $transaction = $order->paymentTransactions()->first();
    if ($transaction) {
        echo "   - Transaction Logged: YES (#{$transaction->transaction_id})\n";
        echo "   - Logged Amount: {$transaction->amount} {$transaction->currency}\n";
    } else {
        echo "   - Transaction Logged: NO ❌\n";
    }

    if ($order->payment_status === 'paid' && $order->status === 'processing') {
        echo "\n🎉 TEST SUCCESSFUL! The cycle is working perfectly.\n";
    } else {
        echo "\n⚠️ TEST INCOMPLETE. Check the logic.\n";
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ TEST FAILED: " . $e->getMessage() . "\n";
}
