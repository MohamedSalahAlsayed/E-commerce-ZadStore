<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\Order;
use App\Models\Governorate;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectCycleTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->user = User::factory()->create(['role' => 'user']);
        
        $cat = Category::create(['name' => 'Test Category', 'slug' => 'test-category', 'is_active' => true]);
        $brand = Brand::create(['name' => 'Test Brand', 'slug' => 'test-brand', 'is_active' => true]);
        
        $this->product = Product::create([
            'title' => 'Test Product',
            'price' => 100,
            'purchase_price' => 50,
            'category_id' => $cat->id,
            'brand_id' => $brand->id,
            'stock' => 0,
            'is_active' => true
        ]);

        // Setup shipping
        $zone = ShippingZone::create(['name' => 'Zone 1', 'is_active' => true]);
        Governorate::create(['name_en' => 'Cairo', 'name_ar' => 'القاهرة', 'shipping_zone_id' => $zone->id]);
        ShippingMethod::create([
            'name_ar' => 'شحن قياسي',
            'name_en' => 'Standard',
            'fee' => 50,
            'shipping_zone_id' => $zone->id,
            'is_active' => true
        ]);
    }

    /** @test */
    public function full_inventory_and_sales_cycle_test()
    {
        // 1. Inventory In (Purchase)
        $supplier = Supplier::create(['name' => 'Test Supplier']);
        $purchaseResponse = $this->actingAs($this->admin)->postJson('/api/admin/purchases', [
            'supplier_id' => $supplier->id,
            'invoice_number' => 'INV-001',
            'purchase_date' => now()->toDateString(),
            'status' => 'received',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10,
                    'unit_price' => 60 // Update purchase price
                ]
            ]
        ]);

        $purchaseResponse->assertStatus(200);
        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock);
        $this->assertEquals(60, $this->product->purchase_price);

        // Check Inventory Logs
        $this->assertDatabaseHas('inventory_logs', [
            'product_id' => $this->product->id,
            'type' => 'purchase',
            'new_stock' => 10
        ]);

        // 2. Sales Cycle (Place Order)
        $orderResponse = $this->actingAs($this->user)->postJson('/api/user/orders', [
            'customer_name' => 'John Doe',
            'phone' => '0123456789',
            'address' => 'Test St',
            'governorate_id' => 1,
            'shipping_method_id' => 1,
            'payment_method' => 'cod',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $orderResponse->assertStatus(201);
        $this->product->refresh();
        $this->assertEquals(8, $this->product->stock); // 10 - 2

        // Check Sale Log
        $this->assertDatabaseHas('inventory_logs', [
            'product_id' => $this->product->id,
            'type' => 'sale',
            'new_stock' => 8
        ]);

        // 3. Financial Cycle (Complete Order)
        $order = Order::first();
        $this->actingAs($this->admin)->putJson("/api/admin/orders/{$order->id}/status", [
            'status' => 'completed'
        ]);

        // Check Financials API
        $order->refresh();
        $this->assertEquals('completed', $order->status);

        $financeResponse = $this->actingAs($this->admin)->getJson('/api/admin/financials?days=30');
        $financeResponse->assertStatus(200);

        $this->assertEquals(250, $financeResponse->json('summary.total_revenue'));
        
        // Cost should be (purchase_price * quantity) = 60 * 2 = 120
        $this->assertEquals(120, $financeResponse->json('summary.total_costs'));

        // 4. Return Cycle (Request & Approve Return)
        $this->actingAs($this->user)->postJson("/api/user/orders/{$order->id}/return", [
            'reason_code' => 'damaged',
            'return_type' => 'full'
        ]);

        $this->actingAs($this->admin)->putJson("/api/admin/orders/{$order->id}/handle-return", [
            'action' => 'approve'
        ]);

        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock); // Restored to 10

        // Check Return Log
        $this->assertDatabaseHas('inventory_logs', [
            'product_id' => $this->product->id,
            'type' => 'order_return',
            'new_stock' => 10
        ]);
    }
}
