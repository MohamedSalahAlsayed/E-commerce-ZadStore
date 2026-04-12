<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Get overview statistics for the main dashboard and analytics page.
     */
    public function getOverview(Request $request)
    {
        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);

        // 1. Sales Summary
        $stats = Order::where('status', 'completed')
            ->select(
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(total) as total_revenue'),
                DB::raw('SUM(shipping_fee) as total_shipping')
            )
            ->first();

        // 2. Profit Calculation (Revenue - Cost of goods sold)
        // Profit = SUM((item_price - purchase_price) * quantity)
        $profit = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', 'completed')
            ->select(DB::raw('SUM((order_items.price - IFNULL(order_items.purchase_price, 0)) * order_items.quantity) as total_profit'))
            ->first();

        // 3. Sales Trends (Daily)
        $trends = Order::where('status', 'completed')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as revenue'),
                DB::raw('COUNT(*) as orders')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // 4. Category Performance
        $categoryPerformance = Category::withCount(['products as sales_count' => function($query) {
            $query->join('order_items', 'products.id', '=', 'order_items.product_id')
                  ->join('orders', 'order_items.order_id', '=', 'orders.id')
                  ->where('orders.status', 'completed');
        }])
        ->get(['id', 'name'])
        ->map(function($cat) {
            return [
                'name' => $cat->name,
                'value' => (int) $cat->sales_count
            ];
        });

        // 5. Low Stock Alert
        $lowStock = Product::where('stock', '<=', 5)
            ->where('is_active', true)
            ->limit(5)
            ->get(['id', 'title', 'stock', 'thumbnail']);

        return response()->json([
            'summary' => [
                'revenue' => (float) ($stats->total_revenue ?? 0),
                'orders' => (int) ($stats->total_orders ?? 0),
                'profit' => (float) ($profit->total_profit ?? 0),
                'avg_order_value' => $stats->total_orders > 0 ? (float) ($stats->total_revenue / $stats->total_orders) : 0,
            ],
            'trends' => $trends,
            'category_performance' => $categoryPerformance,
            'low_stock' => $lowStock
        ]);
    }

    /**
     * Product-specific analytics (Top sellers, worst performers).
     */
    public function getProductAnalytics()
    {
        $topSellers = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'completed')
            ->select(
                'products.id',
                'products.title',
                'products.thumbnail',
                DB::raw('SUM(order_items.quantity) as sold_count'),
                DB::raw('SUM(order_items.price * order_items.quantity) as revenue')
            )
            ->groupBy('products.id', 'products.title', 'products.thumbnail')
            ->orderByDesc('sold_count')
            ->limit(10)
            ->get();

        return response()->json([
            'top_sellers' => $topSellers
        ]);
    }
}
