<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Coupon;
use App\Models\ContactMessage;
use App\Models\Review;
use App\Models\ShippingZone;
use App\Models\Governorate;
use App\Models\ShippingMethod;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactReplyMail;

class AdminSupplementalController extends Controller
{
    // --- Banners ---
    public function getBanners() { return response()->json(Banner::all()); }
    public function createBanner(Request $request) {
        $data = $request->validate([
            'title' => 'nullable|string',
            'image' => 'required|image|max:2048',
            'link' => 'nullable|string',
            'is_active' => 'boolean',
            'target' => 'nullable|in:guest,user,both'
        ]);
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $data['image'] = url('storage/' . $path);
        }
        return response()->json(Banner::create($data), 201);
    }
    public function updateBanner(Request $request, $id) {
        $banner = Banner::findOrFail($id);
        $data = $request->validate([
            'title' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'link' => 'nullable|string',
            'is_active' => 'boolean',
            'target' => 'nullable|in:guest,user,both'
        ]);
        $updateData = $request->except('image');
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $updateData['image'] = url('storage/' . $path);
        }
        $banner->update($updateData);
        return response()->json($banner);
    }
    public function deleteBanner($id) {
        Banner::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }
    public function batchDeleteBanners(Request $request) {
        $request->validate(['ids' => 'required|array', 'ids.*' => 'exists:banners,id']);
        Banner::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'Banners deleted']);
    }

    // --- Coupons ---
    public function getCoupons() { return response()->json(Coupon::all()); }
    public function createCoupon(Request $request) {
        $data = $request->validate(['code' => 'required|string|unique:coupons', 'type' => 'required|string', 'value' => 'required|numeric', 'min_order_amount' => 'nullable|numeric', 'expires_at' => 'nullable|date', 'is_active' => 'boolean']);
        return response()->json(Coupon::create($data), 201);
    }
    public function updateCoupon(Request $request, $id) {
        $coupon = Coupon::findOrFail($id);
        $data = $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $id,
            'type' => 'required|string',
            'value' => 'required|numeric',
            'min_order_amount' => 'nullable|numeric',
            'expires_at' => 'nullable|date',
            'is_active' => 'boolean'
        ]);
        $coupon->update($data);
        return response()->json($coupon);
    }
    public function deleteCoupon($id) {
        Coupon::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // --- Contact Messages ---
    public function getMessages() { 
        return response()->json(
            ContactMessage::with(['user.orders' => function($q) {
                $q->latest()->limit(1);
            }])->orderBy('created_at', 'desc')->get()
        ); 
    }
    /** Admin opened the message — mark as read (idempotent, no toggle). */
    public function markMessageAsRead($id)
    {
        $msg = ContactMessage::findOrFail($id);
        $msg->is_read = true;
        $msg->save();

        return response()->json($msg);
    }
    public function replyToMessage(Request $request, $id) {
        $msg = ContactMessage::findOrFail($id);
        $data = $request->validate([
            'reply_text' => 'required|string',
        ]);
        $msg->reply_text = $data['reply_text'];
        $msg->replied_at = now();
        $msg->is_read = true;
        $msg->is_user_read = false;
        $msg->save();

        // Send email to the sender (guest or registered user)
        try {
            $storeName = StoreSetting::where('key', 'storeName')->value('value') ?? 'ZadStore';
            Mail::to($msg->email)->send(new ContactReplyMail($msg, $data['reply_text'], $storeName));
        } catch (\Exception $e) {
            \Log::warning('Failed to send reply email: ' . $e->getMessage());
        }

        if ($msg->user_id) {
            \App\Models\UserNotification::create([
                'user_id' => $msg->user_id,
                'title' => 'رد جديد على رسالتك',
                'description' => "رد الدعم الفني: " . \Illuminate\Support\Str::limit($data['reply_text'], 50),
                'icon' => 'mdi-message-reply-text',
                'color' => 'info',
                'type' => 'system'
            ]);
        }

        return response()->json(['message' => 'تم الرد بنجاح', 'msg' => $msg]);
    }
    public function deleteMessage($id) {
        ContactMessage::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // --- Reviews ---
    public function getReviews() { return response()->json(Review::with(['user', 'product'])->orderBy('created_at', 'desc')->get()); }
    public function toggleReviewStatus($id) {
        $review = Review::findOrFail($id);
        $review->is_approved = !$review->is_approved;
        $review->save();

        if ($review->is_approved && $review->user_id) {
            \App\Models\UserNotification::create([
                'user_id' => $review->user_id,
                'title' => 'تم الموافقة على تقييمك',
                'description' => "شكراً لتقييمك! تم نشر تقييمك للمنتج.",
                'icon' => 'mdi-star-check',
                'color' => 'orange',
                'type' => 'review'
            ]);
        }

        return response()->json($review);
    }

    public function replyToReview(Request $request, $id)
    {
        $request->validate(['reply' => 'required|string']);
        $review = Review::findOrFail($id);
        $review->admin_reply = $request->reply;
        $review->replied_at = now();
        $review->save();

        if ($review->user_id) {
            \App\Models\UserNotification::create([
                'user_id' => $review->user_id,
                'title' => 'رد جديد على تقييمك',
                'description' => "رد المتجر: " . \Illuminate\Support\Str::limit($request->reply, 50),
                'icon' => 'mdi-comment-account-outline',
                'color' => 'primary',
                'type' => 'review_reply'
            ]);
        }

        return response()->json([
            'message' => 'تم الرد بنجاح',
            'admin_reply' => $review->admin_reply,
            'replied_at' => $review->replied_at
        ]);
    }
    public function deleteReview($id) {
        Review::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    // --- Shipping Zones ---
    public function getShippingZones() { 
        return response()->json(ShippingZone::with(['governorates', 'methods'])->get()); 
    }

    public function getGovernorates() {
        return response()->json(Governorate::all());
    }

    public function createShippingZone(Request $request) {
        $data = $request->validate(['name' => 'required|string', 'is_active' => 'boolean']);
        return response()->json(ShippingZone::create($data), 201);
    }

    public function updateShippingZone(Request $request, $id) {
        $zone = ShippingZone::findOrFail($id);
        $data = $request->validate(['name' => 'required|string', 'is_active' => 'boolean']);
        $zone->update($data);

        // Optional: Sync governorates if provided
        if ($request->has('governorate_ids')) {
            $request->validate(['governorate_ids' => 'array']);
            // First, remove this zone from all governorates currently assigned to it
            Governorate::where('shipping_zone_id', $zone->id)->update(['shipping_zone_id' => null]);
            // Then, assign this zone to the selected governorates
            Governorate::whereIn('id', $request->governorate_ids)->update(['shipping_zone_id' => $zone->id]);
        }

        return response()->json($zone->load(['governorates', 'methods']));
    }

    public function deleteShippingZone($id) {
        ShippingZone::findOrFail($id)->delete();
        return response()->json(['message' => 'Deleted']);
    }

    public function assignGovernorateToZone(Request $request) {
        $data = $request->validate([
            'governorate_id' => 'required|exists:governorates,id',
            'shipping_zone_id' => 'nullable|exists:shipping_zones,id'
        ]);

        $gov = Governorate::findOrFail($data['governorate_id']);
        $gov->shipping_zone_id = $data['shipping_zone_id'];
        $gov->save();

        return response()->json($gov);
    }

    public function manageShippingMethod(Request $request, $zoneId) {
        $zone = ShippingZone::findOrFail($zoneId);
        $data = $request->validate([
            'id' => 'nullable|exists:shipping_methods,id',
            'name_ar' => 'required|string',
            'name_en' => 'required|string',
            'fee' => 'required|numeric',
            'delivery_time' => 'nullable|string',
            'is_active' => 'boolean',
            'driver' => 'string'
        ]);

        if (isset($data['id'])) {
            $method = ShippingMethod::findOrFail($data['id']);
            $method->update($data);
        } else {
            $data['shipping_zone_id'] = $zoneId;
            $method = ShippingMethod::create($data);
        }

        return response()->json($method);
    }

    public function deleteShippingMethod($id) {
        ShippingMethod::findOrFail($id)->delete();
        return response()->json(['message' => 'Method deleted']);
    }

    // --- Store Settings ---
    public function getSettings() { 
        $settings = StoreSetting::all()->pluck('value', 'key');
        return response()->json($settings);
    }
    public function updateSettings(Request $request) {
        $settings = $request->except(['logo_file', 'promo_small1_img', 'promo_small2_img', 'promo_small3_img', 'promo_large1_img', 'promo_large2_img']);
        
        if ($request->hasFile('logo_file')) {
            $path = $request->file('logo_file')->store('settings', 'public');
            $settings['logoPreview'] = url('storage/' . $path);
        }

        // Handle Promo Offers distinct image uploads
        if (isset($settings['promo_offers']) && is_string($settings['promo_offers'])) {
            $promoOffers = json_decode($settings['promo_offers'], true);
            $imgKeys = [
                'promo_small1_img' => 'small1',
                'promo_small2_img' => 'small2',
                'promo_small3_img' => 'small3',
                'promo_large1_img' => 'large1',
                'promo_large2_img' => 'large2'
            ];
            foreach ($imgKeys as $fileKey => $jsonKey) {
                if ($request->hasFile($fileKey)) {
                    $path = $request->file($fileKey)->store('promos', 'public');
                    if (isset($promoOffers[$jsonKey])) {
                        $promoOffers[$jsonKey]['image'] = url('storage/' . $path);
                    }
                }
            }
            $settings['promo_offers'] = json_encode($promoOffers);
        }

        foreach ($settings as $key => $value) {
            StoreSetting::updateOrCreate(['key' => $key], ['value' => is_array($value) ? json_encode($value) : $value]);
        }
        return response()->json(['message' => 'Settings updated']);
    }

    public function updateBulkSettings(Request $request) {
        $data = $request->validate([
            'settings' => 'required|array',
            'settings.*.key' => 'required|string',
            'settings.*.value' => 'nullable|string'
        ]);

        foreach ($data['settings'] as $item) {
            StoreSetting::updateOrCreate(['key' => $item['key']], ['value' => $item['value']]);
        }
        return response()->json(['message' => 'Bulk settings updated']);
    }

    // --- Header Data ---
    public function getHeaderData(Request $request) {
        $user = $request->user();
        
        // جلب الرسائل غير المقروءة أحدث 5 رسائل
        $unreadMessages = ContactMessage::where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // جلب عدد التنبيهات (طلبات جديدة + مراجعات معلقة + طلبات إرجاع معلقة)
        $pendingOrdersCount = \App\Models\Order::whereIn('status', ['pending', 'processing'])->count();
        $pendingReturnsCount = \App\Models\Order::where('return_request_status', 'pending')->count();
        $pendingReviewsCount = \App\Models\Review::where('is_approved', false)->count();

        $notificationsCount = $pendingOrdersCount + $pendingReturnsCount + $pendingReviewsCount;

        // مصفوفة تفصيلية للإشعارات
        $recentNotifications = [];
        if ($pendingOrdersCount > 0) {
            $recentNotifications[] = [
                'id' => 'orders_pending',
                'title' => 'طلبات جديدة',
                'description' => "هناك {$pendingOrdersCount} طلب بانتظار المراجعة",
                'icon' => 'mdi-cart-outline',
                'color' => 'success',
                'to' => '/Dashboard/MangOrder'
            ];
        }
        if ($pendingReturnsCount > 0) {
            $recentNotifications[] = [
                'id' => 'returns_pending',
                'title' => 'طلبات إرجاع',
                'description' => "هناك {$pendingReturnsCount} طلب إرجاع يحتاج لموافقة",
                'icon' => 'mdi-receipt-refund-outline',
                'color' => 'warning',
                'to' => '/Dashboard/MangReturns'
            ];
        }
        if ($pendingReviewsCount > 0) {
            $recentNotifications[] = [
                'id' => 'reviews_pending',
                'title' => 'مراجعات معلقة',
                'description' => "هناك {$pendingReviewsCount} تقييم جديد بانتظار النشر",
                'icon' => 'mdi-star-outline',
                'color' => 'info',
                'to' => '/Dashboard/RatingsReviews'
            ];
        }

        return response()->json([
            'user' => $user,
            'unreadMessages' => $unreadMessages,
            'notificationsCount' => $notificationsCount,
            'recentNotifications' => $recentNotifications
        ]);
    }
}
