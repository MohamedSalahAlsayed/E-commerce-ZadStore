<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminFinancialController extends Controller
{
    public function getFinancials(Request $request)
    {
        $days = $request->query('days', 30);
        $since = Carbon::now()->subDays($days);

        // إجمالي الإيرادات (من الطلبات المكتملة أو المشحونة)
        $totalRevenue = Order::whereIn('status', ['completed', 'shipped'])
            ->where('created_at', '>=', $since)
            ->sum('total');
            
        // المبالغ المسترجعة (الطلبات الملغاة أو المستردة)
        $totalRefunded = Order::whereIn('status', ['returned', 'rejected'])
            ->where('created_at', '>=', $since)
            ->sum('total');

        // إجمالي التكاليف (COGS)
        $totalCosts = OrderItem::whereHas('order', function($q) use ($since) {
            $q->whereIn('status', ['completed', 'shipped'])->where('created_at', '>=', $since);
        })->sum(DB::raw('quantity * purchase_price'));

        $netProfit = $totalRevenue - $totalRefunded - $totalCosts;

        // مبيعات الفترة (للرسم البياني)
        $salesHistory = Order::whereIn('status', ['completed', 'shipped'])
            ->where('created_at', '>=', $since)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as daily_revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // أفضل 5 منتجات ربحية
        $topProfitableProducts = OrderItem::whereHas('order', function($q) use ($since) {
                $q->whereIn('status', ['completed', 'shipped'])->where('created_at', '>=', $since);
            })
            ->select(
                'product_id',
                'name',
                DB::raw('SUM(quantity * (price - COALESCE(purchase_price, 0))) as total_profit'),
                DB::raw('SUM(quantity) as total_sold')
            )
            ->groupBy('product_id', 'name')
            ->orderBy('total_profit', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'summary' => [
                'total_revenue' => (float)$totalRevenue,
                'total_refunded' => (float)$totalRefunded,
                'total_costs' => (float)$totalCosts,
                'net_profit' => (float)$netProfit,
                'profit_margin' => ($totalRevenue - $totalRefunded) > 0 ? round(($netProfit / ($totalRevenue - $totalRefunded)) * 100, 2) : 0,
            ],
            'charts' => [
                'sales_history' => $salesHistory
            ],
            'top_products' => $topProfitableProducts
        ]);
    }
}
