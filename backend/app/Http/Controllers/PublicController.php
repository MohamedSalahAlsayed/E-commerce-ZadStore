<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function getCategories()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    public function getBrands()
    {
        $brands = Brand::all();
        return response()->json($brands);
    }

    public function getProducts(Request $request)
    {
        $query = Product::with(['category', 'brand', 'images'])->orderBy('created_at', 'desc');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('brand_id')) {
            $query->where('brand_id', $request->brand_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
        }

        $products = $query->paginate(12);

        return response()->json($products);
    }

    public function getFlashDeals()
    {
        $products = Product::with(['category', 'brand', 'images'])
                           ->where('is_flash_deal', true)
                           ->take(10)
                           ->get();

        return response()->json($products);
    }

    public function getProductDetails($id)
    {
        $product = Product::with(['category', 'brand', 'images', 'reviews' => function($q) {
            $q->where('is_approved', true)->with('user')->orderBy('created_at', 'desc');
        }])->findOrFail($id);
        return response()->json($product);
    }

    public function getBanners()
    {
        $banners = \App\Models\Banner::where('is_active', true)->get();
        return response()->json($banners);
    }

    public function getSettings()
    {
        $settings = \App\Models\StoreSetting::pluck('value', 'key');
        return response()->json($settings);
    }

    public function contactUs(Request $request) 
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'message' => 'required|string'
        ]);

        $message = \App\Models\ContactMessage::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'message' => $data['message'],
            'is_read' => false
        ]);

        return response()->json(['message' => 'تم إرسال رسالتك بنجاح', 'contact_message' => $message], 201);
    }

    public function getShippingZones()
    {
        $zones = \App\Models\ShippingZone::where('is_active', true)->get();
        return response()->json($zones);
    }

    public function applyCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'subtotal' => 'required|numeric'
        ]);

        $coupon = \App\Models\Coupon::where('code', $request->code)->where('is_active', true)->first();
        if (!$coupon) {
            return response()->json(['message' => 'عفواً، هذا الكوبون غير صالح أو غير مفعل'], 400);
        }

        if ($coupon->expires_at && \Carbon\Carbon::now()->gt($coupon->expires_at)) {
             return response()->json(['message' => 'الكوبون منتهي الصلاحية'], 400);
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
             return response()->json(['message' => 'لقد تم تجاوز الحد الأقصى لاستخدام هذا الكوبون'], 400);
        }

        if ($coupon->min_order_amount && $request->subtotal < $coupon->min_order_amount) {
             return response()->json(['message' => 'قيمة الطلب أقل من الحد الأدنى (' . $coupon->min_order_amount . ') لاستخدام هذا الكوبون'], 400);
        }

        $discount_amount = 0;
        if ($coupon->type === 'percentage') {
             $discount_amount = ($request->subtotal * $coupon->value) / 100;
        } else {
             $discount_amount = $coupon->value;
        }

        return response()->json([
             'message' => 'تم تطبيق الخصم بنجاح', 
             'coupon' => $coupon, 
             'discount_amount' => $discount_amount,
             'new_subtotal' => max(0, $request->subtotal - $discount_amount)
        ]);
    }

    // --- Blog ---
    public function getBlogPosts()
    {
        $posts = \App\Models\BlogPost::with('author:id,name,avatar')->where('is_published', true)->orderBy('created_at', 'desc')->get();
        return response()->json($posts);
    }

    public function getBlogPost($slug)
    {
        $post = \App\Models\BlogPost::with('author:id,name,avatar')->where('slug', $slug)->where('is_published', true)->firstOrFail();
        $post->increment('views');
        return response()->json($post);
    }

    public function getBestSellers()
    {
        $products = Product::with(['category', 'brand', 'images'])
                           ->where('is_best_seller', true)
                           ->take(10)
                           ->get();

        return response()->json($products);
    }
}
