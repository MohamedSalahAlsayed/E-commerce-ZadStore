<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function getOrders(Request $request)
    {
        $query = Order::with('items.product')->where('user_id', $request->user()->id);

        if ($request->has('period')) {
            $now = \Carbon\Carbon::now();
            if ($request->period === 'last_30_days') {
                $query->where('created_at', '>=', $now->subDays(30));
            } elseif ($request->period === 'last_3_months') {
                $query->where('created_at', '>=', $now->subMonths(3));
            } elseif (is_numeric($request->period)) {
                $query->whereYear('created_at', $request->period);
            }
        }

        $orders = $query->orderBy('created_at', 'desc')->get();
        return response()->json($orders);
    }

    public function getOrderDetails(Request $request, $id)
    {
        $order = Order::with('items.product.images')
                      ->where('user_id', $request->user()->id)
                      ->findOrFail($id);

        return response()->json($order);
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'governorate_id' => 'required|exists:governorates,id',
            'shipping_method_id' => 'required|exists:shipping_methods,id',
            'coupon_code' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $subtotal = 0;
            $itemsData = [];
            $total_weight = 0;

            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stock < $item['quantity']) {
                    return response()->json(['message' => "نفدت كمية المنتج: {$product->title}"], 400);
                }
                
                $price = $product->price;
                if ($product->discount_percentage > 0) {
                    $price = $price - ($price * ($product->discount_percentage / 100));
                }

                $subtotal += $price * $item['quantity'];
                $total_weight += ($product->weight_kg ?? 0) * $item['quantity'];

                $itemsData[] = [
                    'product_id' => $product->id,
                    'name' => $product->title,
                    'price' => $price,
                    'quantity' => $item['quantity'],
                    'image' => $product->thumbnail,
                    'purchase_price' => $product->purchase_price,
                ];

                $product->decrement('stock', $item['quantity']);
            }

            // Secure Shipping Evaluation
            $governorate = \App\Models\Governorate::with('zone')->findOrFail($request->governorate_id);
            $method = \App\Models\ShippingMethod::findOrFail($request->shipping_method_id);
            
            // Re-calculate shipping fee to prevent frontend manipulation
            $settings = \App\Models\StoreSetting::all()->pluck('value', 'key');
            $free_shipping_threshold = $settings['free_shipping_threshold'] ?? 1000;
            
            $shipping_fee = 0;
            if ($subtotal < $free_shipping_threshold) {
                $shipping_fee = $method->fee;
                if ($method->is_weight_aware) {
                    $extra_weight = max(0, $total_weight - $method->base_weight);
                    $shipping_fee += $extra_weight * $method->extra_weight_fee;
                }
            }

            // Secure Coupon Evaluation
            $discount_amount = 0;
            $applied_coupon = null;
            if ($request->coupon_code) {
                $coupon = \App\Models\Coupon::where('code', $request->coupon_code)->where('is_active', true)->first();
                if ($coupon && (!$coupon->expires_at || \Carbon\Carbon::now()->lt($coupon->expires_at))) {
                    if (!$coupon->usage_limit || $coupon->used_count < $coupon->usage_limit) {
                        if (!$coupon->min_order_amount || $subtotal >= $coupon->min_order_amount) {
                            if ($coupon->type === 'percentage') {
                                $discount_amount = ($subtotal * $coupon->value) / 100;
                            } else {
                                $discount_amount = $coupon->value;
                            }
                            $applied_coupon = $coupon;
                        }
                    }
                }
            }

            $total = max(0, $subtotal - $discount_amount) + $shipping_fee;

            $order = Order::create([
                'user_id' => $request->user()->id,
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'customer_name' => $request->customer_name,
                'phone' => $request->phone,
                'address' => $request->address . ' - ' . $governorate->name_ar,
                'governorate_id' => $governorate->id,
                'shipping_method_id' => $method->id,
                'status' => 'pending',
                'subtotal' => $subtotal - $discount_amount,
                'shipping_fee' => $shipping_fee,
                'total' => $total,
                'total_weight' => $total_weight,
            ]);

            foreach ($itemsData as $data) {
                $data['order_id'] = $order->id;
                OrderItem::create($data);
            }

            if ($applied_coupon) {
                $applied_coupon->increment('used_count');
            }

            // Clear the cart
            Cart::where('user_id', $request->user()->id)->delete();

            DB::commit();

            return response()->json(['message' => 'تم إنشاء الطلب بنجاح', 'order' => $order], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('createOrder failed', [
                'user_id' => $request->user()->id,
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['message' => 'حدث خطأ أثناء إنشاء الطلب. حاول مرة أخرى.'], 500);
        }
    }
    // ==========================================
    // CART
    // ==========================================
    public function getCart(Request $request)
    {
        $cart = Cart::with('product')->where('user_id', $request->user()->id)->get();
        return response()->json($cart);
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer'
        ]);

        $cartItem = Cart::where('user_id', $request->user()->id)
                        ->where('product_id', $request->product_id)
                        ->first();

        $increment = $request->input('quantity', 1);

        if ($cartItem) {
            $cartItem->quantity += $increment;
            if ($cartItem->quantity <= 0) {
                $cartItem->delete();
                return response()->json(['message' => 'تم الحذف من السلة']);
            }
            $cartItem->save();
        } else {
            if ($increment <= 0) {
                return response()->json(['message' => 'كمية غير صالحة'], 400);
            }
            $cartItem = Cart::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity' => $increment,
            ]);
        }

        return response()->json(['message' => 'تم الإضافة للسلة', 'cart' => $cartItem], 200);
    }

    public function removeFromCart(Request $request, $product_id)
    {
        Cart::where('user_id', $request->user()->id)->where('product_id', $product_id)->delete();
        return response()->json(['message' => 'تم الحذف من السلة']);
    }

    public function clearCart(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();
        return response()->json(['message' => 'تم تفريغ السلة']);
    }

    // ==========================================
    // FAVORITES
    // ==========================================
    public function getFavorites(Request $request)
    {
        $favorites = Favorite::with('product')->where('user_id', $request->user()->id)->get();
        return response()->json($favorites);
    }

    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $fav = Favorite::where('user_id', $request->user()->id)
                       ->where('product_id', $request->product_id)
                       ->first();

        if ($fav) {
            $fav->delete();
            return response()->json(['message' => 'تم الحذف من المفضلة', 'added' => false]);
        } else {
            Favorite::create([
                'user_id' => $request->user()->id,
                'product_id' => $request->product_id,
            ]);
            return response()->json(['message' => 'تمت الإضافة للمفضلة', 'added' => true]);
        }
    }

    // ==========================================
    // REVIEWS
    // ==========================================
    public function postReview(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|numeric|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $product = Product::findOrFail($id);
        
        $alreadyReviewed = \App\Models\Review::where('user_id', $request->user()->id)
                                             ->where('product_id', $id)
                                             ->first();
        if ($alreadyReviewed) {
             return response()->json(['message' => 'لقد قمت بتقييم هذا المنتج مسبقاً'], 400);   
        }

        $review = \App\Models\Review::create([
            'user_id' => $request->user()->id,
            'product_id' => $id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'is_approved' => false // Admin needs to approve
        ]);

        return response()->json(['message' => 'تم استلام تقييمك بنجاح، بانتظار موافقة الإدارة.', 'review' => $review], 201);
    }
    
    // ==========================================
    // USER STATISTICS AND ACTIVITIES
    // ==========================================
    public function getUserHeaderData(Request $request)
    {
        $user = $request->user();
        
        $unreadNotificationsCount = \App\Models\UserNotification::where('user_id', $user->id)
                                                               ->where('is_read', false)
                                                               ->count();
                                                               
        $unreadMessagesCount = \App\Models\ContactMessage::where('user_id', $user->id)
                                                         ->whereNotNull('reply_text')
                                                         ->where('is_user_read', false)
                                                         ->count();
                                                         
        $recentNotifications = \App\Models\UserNotification::where('user_id', $user->id)
                                                           ->orderBy('created_at', 'desc')
                                                           ->take(5)
                                                           ->get();

        return response()->json([
            'unreadNotificationsCount' => $unreadNotificationsCount,
            'unreadMessagesCount' => $unreadMessagesCount,
            'recentNotifications' => $recentNotifications
        ]);
    }

    public function getUserStatistics(Request $request)
    {
        $user = $request->user();
        
        $completed_orders = Order::where('user_id', $user->id)
                                 ->whereIn('status', ['delivered', 'completed'])
                                 ->count();
                                 
        $recent_activities = [];
        
        $orders = Order::where('user_id', $user->id)
                       ->orderBy('created_at', 'desc')
                       ->take(5)
                       ->get();
                       
        foreach ($orders as $order) {
            $recent_activities[] = [
                'type' => 'order',
                'title' => 'طلب جديد: ' . $order->order_number,
                'subtitle' => 'حالة الطلب: ' . $order->status . ' - بقيمة ' . number_format($order->total, 2) . ' ج.م',
                'created_at' => $order->created_at,
                'status' => $order->status
            ];
        }
        
        // Add reviews to activities
        $reviews = \App\Models\Review::where('user_id', $user->id)
                                     ->orderBy('created_at', 'desc')
                                     ->take(3)
                                     ->get();
                                     
        foreach ($reviews as $review) {
            $recent_activities[] = [
                'type' => 'review',
                'title' => 'تم تقييم منتج',
                'subtitle' => 'التقييم: ' . $review->rating . ' نجوم',
                'created_at' => $review->created_at,
                'status' => $review->is_approved ? 'approved' : 'pending'
            ];
        }
        
        // Sort combined activities by created_at desc
        usort($recent_activities, function($a, $b) {
            return $b['created_at'] <=> $a['created_at'];
        });
        
        return response()->json([
            'completed_orders' => $completed_orders,
            'wallet_balance' => (float) ($user->wallet_balance ?? 0),
            'total_earnings' => (float) ($user->total_earnings ?? 0),
            'recent_activities' => array_slice($recent_activities, 0, 5)
        ]);
    }
    
    // ==========================================
    // NOTIFICATIONS
    // ==========================================
    public function getNotifications(Request $request)
    {
        $notifications = \App\Models\UserNotification::where('user_id', $request->user()->id)
                                                     ->orderBy('created_at', 'desc')
                                                     ->get();
        return response()->json($notifications);
    }
    
    public function markNotificationsAsRead(Request $request)
    {
        \App\Models\UserNotification::where('user_id', $request->user()->id)
                                    ->where('is_read', false)
                                    ->update(['is_read' => true]);
                                    
        return response()->json(['message' => 'تم تحديد الكل كمقروء']);
    }
    
    public function markNotificationAsRead(Request $request, $id)
    {
        $notification = \App\Models\UserNotification::where('user_id', $request->user()->id)
                                                    ->findOrFail($id);
        $notification->is_read = true;
        $notification->save();
        
        return response()->json(['message' => 'تم القراءة']);
    }
    
    // ==========================================
    // MESSAGES (TICKETS)
    // ==========================================
    public function getUserMessages(Request $request)
    {
        $messages = \App\Models\ContactMessage::where('user_id', $request->user()->id)
                                              ->orWhere('email', $request->user()->email)
                                              ->orderBy('created_at', 'desc')
                                              ->get();
        
        // بمجرد جلب الرسائل، نعتبرها "مقروءة" للمستخدم إذا كان هناك رد
        \App\Models\ContactMessage::where('user_id', $request->user()->id)
                                  ->whereNotNull('reply_text')
                                  ->update(['is_user_read' => true]);

        return response()->json($messages);
    }
    
    public function sendUserMessage(Request $request)
    {
        $request->validate([
            'subject' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000'
        ]);
        
        $msg = \App\Models\ContactMessage::create([
            'user_id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'subject' => $request->subject,
            'message' => $request->message,
            'is_read' => false
        ]);
        
        return response()->json(['message' => 'تم الإرسال بنجاح', 'contact_message' => $msg]);
    }

    // ==========================================
    // ORDER ACTIONS (Expedite, Return)
    // ==========================================
    public function expediteOrder(Request $request, $id)
    {
        $order = Order::where('user_id', $request->user()->id)->findOrFail($id);
        $order->is_urgent = true;
        $order->save();

        return response()->json(['message' => 'تم استلام طلب الاستعجال، سنعمل على شحن طلبك بأسرع وقت!']);
    }

    public function cancelOrder(Request $request, $id)
    {
        $order = Order::with('items')->where('user_id', $request->user()->id)->findOrFail($id);

        if ($order->status !== 'pending') {
            return response()->json(['message' => 'لا يمكن إلغاء الطلب بعد البدء في تجهيزه. يرجى التواصل مع الدعم.'], 400);
        }

        DB::beginTransaction();
        try {
            $order->status = 'cancelled';
            $order->save();

            // Restore stock
            foreach ($order->items as $item) {
                if ($item->product_id) {
                    Product::where('id', $item->product_id)->increment('stock', $item->quantity);
                }
            }

            DB::commit();
            return response()->json(['message' => 'تم إلغاء الطلب بنجاح وتم استرجاع الكميات للمخزون.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'حدث خطأ أثناء إلغاء الطلب'], 500);
        }
    }

    public function requestReturn(Request $request, $id)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);
        $order = Order::where('user_id', $request->user()->id)->findOrFail($id);
        
        $order->return_request_status = 'pending';
        // Note: we DO NOT change the actual $order->status here. We wait for Admin approval.
        $order->save();

        return response()->json(['message' => 'تم تسجيل طلب الإرجاع بنجاح، سيقوم الدعم الفني بمراجعته في أقرب وقت.']);
    }
}
