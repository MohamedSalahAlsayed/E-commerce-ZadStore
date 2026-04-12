<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\InventoryLog;
use App\Traits\LogsInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    use LogsInventory;

    public function statistics()
    {
        $now = \Carbon\Carbon::now();
        $startOfThisMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Total Counts
        $totalUsers = User::where('role', 'user')->count();
        $totalOrders = Order::count();
        $totalProducts = Product::count();

        // Financials (Revenue & Profit)
        $completedOrders = Order::whereIn('status', ['completed', 'shipped'])->get();
        $totalRevenue = $completedOrders->sum('total');
        
        // Profit Calculation Helper
        $calculateProfit = function($orders) {
            $profit = 0;
            foreach ($orders as $order) {
                $order->load(['items.product']);
                foreach ($order->items as $item) {
                    $buyPrice = $item->purchase_price ?? ($item->product->purchase_price ?? 0);
                    $sellPrice = $item->price;
                    $profit += ($sellPrice - $buyPrice) * $item->quantity;
                }
            }
            return $profit;
        };

        $totalProfit = $calculateProfit($completedOrders);

        // Daily Sales (Last 7 Days) for Chart
        $dailySales = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = $now->copy()->subDays($i);
            $revenue = Order::whereIn('status', ['completed', 'shipped'])
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->sum('total');
            $dailySales[] = [
                'day' => $date->format('Y-m-d'),
                'label' => $date->translatedFormat('D'),
                'revenue' => (float)$revenue
            ];
        }

        // Top Selling Products
        $topProducts = DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select('products.id', 'products.title', 'products.thumbnail', DB::raw('SUM(order_items.quantity) as sold_count'))
            ->groupBy('products.id', 'products.title', 'products.thumbnail')
            ->orderBy('sold_count', 'desc')
            ->take(5)
            ->get();

        // Growth Calculations (Revenue)
        $thisMonthRevenue = Order::whereIn('status', ['completed', 'shipped'])
            ->whereBetween('created_at', [$startOfThisMonth, $now])
            ->sum('total');
        $lastMonthRevenue = Order::whereIn('status', ['completed', 'shipped'])
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('total');
        $revenueGrowth = $lastMonthRevenue > 0 
            ? round((($thisMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) 
            : ($thisMonthRevenue > 0 ? 100 : 0);

        // Growth Calculations (Orders)
        $thisMonthOrders = Order::whereBetween('created_at', [$startOfThisMonth, $now])->count();
        $lastMonthOrders = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $ordersGrowth = $lastMonthOrders > 0 
            ? round((($thisMonthOrders - $lastMonthOrders) / $lastMonthOrders) * 100, 1) 
            : ($thisMonthOrders > 0 ? 100 : 0);

        // Growth Calculations (Profit)
        $thisMonthProfit = $calculateProfit(Order::whereIn('status', ['completed', 'shipped'])
            ->whereBetween('created_at', [$startOfThisMonth, $now])->get());
        $lastMonthProfit = $calculateProfit(Order::whereIn('status', ['completed', 'shipped'])
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->get());
        $profitGrowth = $lastMonthProfit > 0 
            ? round((($thisMonthProfit - $lastMonthProfit) / $lastMonthProfit) * 100, 1) 
            : ($thisMonthProfit > 0 ? 100 : 0);

        // Growth Calculations (Users)
        $thisMonthUsers = User::where('role', 'user')->whereBetween('created_at', [$startOfThisMonth, $now])->count();
        $lastMonthUsers = User::where('role', 'user')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
        $usersGrowth = $lastMonthUsers > 0 
            ? round((($thisMonthUsers - $lastMonthUsers) / $lastMonthUsers) * 100, 1) 
            : ($thisMonthUsers > 0 ? 100 : 0);

        // Category Distribution (for charts)
        $categoryDistribution = Category::withCount('products')
            ->orderBy('products_count', 'desc')
            ->take(5)
            ->get()
            ->map(function($cat) {
                return [
                    'name' => $cat->name,
                    'count' => $cat->products_count
                ];
            });

        $recentOrders = Order::with('user')->orderBy('created_at', 'desc')->take(5)->get();

        return response()->json([
            'users' => $totalUsers,
            'orders' => $totalOrders,
            'revenue' => $totalRevenue,
            'profit' => $totalProfit,
            'products' => $totalProducts,
            'revenue_growth' => $revenueGrowth,
            'orders_growth' => $ordersGrowth,
            'profit_growth' => $profitGrowth,
            'users_growth' => $usersGrowth,
            'daily_sales' => $dailySales,
            'top_products' => $topProducts,
            'category_distribution' => $categoryDistribution,
            'recent_orders' => $recentOrders,
        ]);
    }

    // --- Users Management ---
    public function getUsers()
    {
        return response()->json(User::withCount('orders')->withSum('orders', 'total')->where('role', 'user')->orderBy('created_at', 'desc')->get());
    }

    public function toggleUserStatus($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();
        return response()->json(['message' => 'تم تحديث حالة المستخدم', 'user' => $user]);
    }

    public function deleteUser($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'تم حذف المستخدم بنجاح']);
    }

    public function batchDeleteUsers(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:users,id']);
        User::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'تم حذف المستخدمين بنجاح']);
    }

    public function getStaff()
    {
        return response()->json(User::whereIn('role', ['admin', 'moderator'])->orderBy('created_at', 'desc')->get());
    }

    public function createStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,moderator',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return response()->json(['message' => 'تم إنشاء حساب الموظف بنجاح', 'user' => $user], 201);
    }

    public function updateStaffRole(Request $request, $id)
    {
        $request->validate(['role' => 'required|in:admin,moderator,user']);
        $user = User::findOrFail($id);
        $user->role = $request->role;
        $user->save();
        return response()->json(['message' => 'تم تحديث صلاحيات المستخدم بنجاح', 'user' => $user]);
    }

    public function updateStaffPermissions(Request $request, $id)
    {
        $request->validate(['permissions' => 'nullable|array']);
        $user = User::findOrFail($id);
        $user->permissions = $request->permissions;
        $user->save();
        return response()->json(['message' => 'تم تحديث أذونات الموظف بنجاح', 'user' => $user]);
    }

    // --- Categories Management ---
    public function getCategories()
    {
        return response()->json(Category::withCount('products')->get());
    }

    public function toggleCategoryStatus($id)
    {
        $cat = Category::findOrFail($id);
        $cat->is_active = !$cat->is_active;
        $cat->save();
        return response()->json(['message' => 'تم تحديث حالة القسم بالنجاح', 'category' => $cat]);
    }

    public function createCategory(Request $request)
    {
        $request->validate(['name' => 'required|string', 'is_active' => 'boolean', 'thumbnail' => 'nullable|image|max:2048']);
        
        $data = $request->except('thumbnail');
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('categories', 'public');
            $data['thumbnail'] = url('storage/' . $path);
        }

        $cat = Category::create($data);
        return response()->json(['message' => 'تم إنشاء القسم بنجاح', 'category' => $cat], 201);
    }

    public function updateCategory(Request $request, $id)
    {
        $cat = Category::findOrFail($id);
        
        $request->validate(['name' => 'required|string', 'is_active' => 'boolean', 'thumbnail' => 'nullable|image|max:2048']);
        
        $data = $request->except('thumbnail');
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('categories', 'public');
            $data['thumbnail'] = url('storage/' . $path);
        }

        $cat->update($data);
        return response()->json(['message' => 'تم تحديث القسم بنجاح', 'category' => $cat]);
    }

    public function deleteCategory($id)
    {
        Category::findOrFail($id)->delete();
        return response()->json(['message' => 'تم حذف القسم بنجاح']);
    }

    public function batchDeleteCategories(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:categories,id']);
        Category::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'تم حذف الأقسام بنجاح']);
    }

    // --- Brands Management ---
    public function getBrands()
    {
        return response()->json(Brand::withCount('products')->get());
    }

    public function toggleBrandStatus($id)
    {
        $brand = Brand::findOrFail($id);
        $brand->is_active = !$brand->is_active;
        $brand->save();
        return response()->json(['message' => 'تم تحديث حالة الماركة بالنجاح', 'brand' => $brand]);
    }

    public function createBrand(Request $request)
    {
        $request->validate(['name' => 'required|string', 'logo' => 'nullable|image|max:2048']);
        
        $data = $request->except('logo');
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brands', 'public');
            $data['logo'] = url('storage/' . $path);
        }

        $brand = Brand::create($data);
        return response()->json(['message' => 'تم إنشاء الماركة بنجاح', 'brand' => $brand], 201);
    }

    public function updateBrand(Request $request, $id)
    {
        $brand = Brand::findOrFail($id);
        
        $request->validate(['name' => 'required|string', 'logo' => 'nullable|image|max:2048']);
        
        $data = $request->except('logo');
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('brands', 'public');
            $data['logo'] = url('storage/' . $path);
        }

        $brand->update($data);
        return response()->json(['message' => 'تم تحديث الماركة بنجاح', 'brand' => $brand]);
    }

    public function deleteBrand($id)
    {
        Brand::findOrFail($id)->delete();
        return response()->json(['message' => 'تم حذف الماركة بنجاح']);
    }

    public function batchDeleteBrands(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:brands,id']);
        Brand::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'تم حذف الماركات بنجاح']);
    }

    // --- Products Management ---
    public function getProducts()
    {
        return response()->json(Product::with(['category', 'brand'])->orderBy('created_at', 'desc')->get());
    }

    public function createProduct(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'thumbnail' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'rating' => 'nullable|numeric|min:1|max:5',
            'purchase_price' => 'nullable|numeric|min:0',
            'is_flash_deal' => 'nullable|boolean',
            'is_best_seller' => 'nullable|boolean',
        ]);
        
        $data = $request->except('thumbnail');
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('products', 'public');
            $data['thumbnail'] = url('storage/' . $path);
        }

        $product = Product::create($data);
        return response()->json(['message' => 'تم إنشاء المنتج بنجاح', 'product' => $product], 201);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        
        $request->validate([
            'title' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
            'thumbnail' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'rating' => 'nullable|numeric|min:1|max:5',
            'purchase_price' => 'nullable|numeric|min:0',
            'is_flash_deal' => 'nullable|boolean',
            'is_best_seller' => 'nullable|boolean',
        ]);

        $data = $request->except('thumbnail');
        
        if ($request->hasFile('thumbnail')) {
            $path = $request->file('thumbnail')->store('products', 'public');
            $data['thumbnail'] = url('storage/' . $path);
        }

        $product->update($data);
        return response()->json(['message' => 'تم تحديث المنتج بنجاح', 'product' => $product]);
    }

    public function toggleProductStatus($id)
    {
        $product = Product::findOrFail($id);
        $product->is_active = !$product->is_active;
        $product->save();
        return response()->json(['message' => 'تم تحديث حالة المنتج بالنجاح', 'product' => $product]);
    }

    public function batchDeleteProducts(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:products,id']);
        Product::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'تم حذف المنتجات بنجاح']);
    }

    public function updateProductStock(Request $request, $id)
    {
        $request->validate([
            'stock' => 'required|numeric|min:0',
            'notes' => 'nullable|string'
        ]);
        
        $product = Product::findOrFail($id);
        $oldStock = $product->stock;
        $newStock = $request->stock;
        
        $product->stock = $newStock;
        $product->save();

        $this->logInventoryChange(
            $product->id, 
            $oldStock, 
            $newStock, 
            'manual', 
            $request->notes ?? 'تعديل يدوي من لوحة التحكم'
        );
        
        return response()->json(['message' => 'تم تحديث المخزون بنجاح']);
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        // Optional: delete image file from storage here
        $product->delete();
        return response()->json(['message' => 'تم حذف المنتج بنجاح']);
    }

    public function getInventoryLogs()
    {
        return response()->json(
            InventoryLog::with(['product:id,title,thumbnail', 'user:id,name'])
                ->orderBy('created_at', 'desc')
                ->take(500)
                ->get()
        );
    }

    // --- Orders Management ---
    public function getOrders(Request $request)
    {
        $query = Order::with(['user', 'items.product']);

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        return response()->json($query->orderBy('created_at', 'desc')->get());
    }

    public function getReturnRequests()
    {
        $orders = Order::with(['user', 'items.product'])
            ->whereNotNull('return_request_status')
            ->orderBy('updated_at', 'desc')
            ->get();
            
        return response()->json($orders);
    }

    public function getOrderDetails($id)
    {
        return response()->json(Order::with(['items.product', 'user'])->findOrFail($id));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,processing,shipped,completed,cancelled,returned']);
        $order = Order::findOrFail($id);

        if (in_array($request->status, ['cancelled', 'returned']) && 
            !in_array($order->status, ['cancelled', 'returned'])) {
            
            $order->load('items');
            foreach ($order->items as $item) {
                $p = \App\Models\Product::findOrFail($item->product_id);
                $oldS = $p->stock;
                $p->increment('stock', $item->quantity);
                
                $this->logInventoryChange(
                    $p->id, 
                    $oldS, 
                    $oldS + $item->quantity, 
                    'order_cancellation', 
                    "إلغاء الطلب رقم #{$order->order_number}",
                    ['order_id' => $order->id]
                );
            }
        } 
        elseif (in_array($order->status, ['cancelled', 'returned']) && 
                !in_array($request->status, ['cancelled', 'returned'])) {
            
            $order->load('items');
            foreach ($order->items as $item) {
                $p = \App\Models\Product::findOrFail($item->product_id);
                $oldS = $p->stock;
                $p->decrement('stock', $item->quantity);

                $this->logInventoryChange(
                    $p->id, 
                    $oldS, 
                    $oldS - $item->quantity, 
                    'order_restoration', 
                    "إعادة تفعيل الطلب رقم #{$order->order_number}",
                    ['order_id' => $order->id]
                );
            }
        }

        $order->status = $request->status;
        $order->save();

        // Create localized status mappings for the notification
        $statusAr = [
            'pending' => 'قيد الانتظار',
            'processing' => 'قيد التجهيز',
            'shipped' => 'تم الشحن',
            'completed' => 'مكتمل',
            'cancelled' => 'ملغي',
            'returned' => 'مرتجع'
        ];
        
        $color = 'primary';
        $icon = 'mdi-package-variant';
        
        switch($request->status) {
            case 'shipped': $color = 'info'; $icon = 'mdi-truck-delivery'; break;
            case 'completed': $color = 'success'; $icon = 'mdi-check-circle'; break;
            case 'cancelled': 
            case 'returned': $color = 'error'; $icon = 'mdi-close-circle'; break;
            case 'processing': $color = 'warning'; $icon = 'mdi-progress-clock'; break;
        }

        \App\Models\UserNotification::create([
            'user_id' => $order->user_id,
            'title' => 'تحديث حالة الطلب',
            'description' => "تم تحديث حالة طلبك رقم {$order->order_number} إلى: " . ($statusAr[$request->status] ?? $request->status),
            'icon' => $icon,
            'color' => $color,
            'type' => 'order'
        ]);

        return response()->json(['message' => 'تم التحديث', 'order' => $order]);
    }

    public function handleReturnRequest(Request $request, $id)
    {
        $request->validate(['action' => 'required|in:approve,reject']);
        $order = Order::findOrFail($id);

        if ($request->action === 'approve') {
            $order->return_request_status = 'approved';
            
            // Only increment stock if not already returned
            if ($order->status !== 'returned') {
                $order->status = 'returned';
                $order->load('items');
                foreach ($order->items as $item) {
                    $p = \App\Models\Product::findOrFail($item->product_id);
                    $oldS = $p->stock;
                    $p->increment('stock', $item->quantity);

                    $this->logInventoryChange(
                        $p->id, 
                        $oldS, 
                        $oldS + $item->quantity, 
                        'order_return', 
                        "مرتجع الطلب رقم #{$order->order_number}",
                        ['order_id' => $order->id]
                    );
                }
            }
        } else {
            $order->return_request_status = 'rejected';
        }

        $order->save();

        // Notify user
        $message = $request->action === 'approve' 
            ? "تمت الموافقة على طلب إرجاع الطلب #{$order->order_number}" 
            : "نعتذر، تم رفض طلب استرجاع الطلب #{$order->order_number}";

        \App\Models\UserNotification::create([
            'user_id' => $order->user_id,
            'title' => "رد على طلب استرجاع",
            'description' => $message,
            'icon' => $request->action === 'approve' ? 'mdi-check-circle' : 'mdi-close-circle',
            'color' => $request->action === 'approve' ? 'success' : 'error',
            'type' => 'order'
        ]);

        return response()->json(['message' => 'تم معالجة طلب الإرجاع بنجاح', 'order' => $order]);
    }

    public function deleteOrder($id)
    {
        $order = Order::findOrFail($id);
        $order->delete();
        return response()->json(['message' => 'تم حذف الطلب بنجاح']);
    }

    public function batchDeleteOrders(Request $request)
    {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:orders,id']);
        Order::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'تم حذف الطلبات بنجاح']);
    }
}
