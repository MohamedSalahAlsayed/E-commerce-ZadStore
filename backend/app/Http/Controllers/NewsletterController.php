<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Public: Subscribe to newsletter
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletters,email',
        ], [
            'email.unique' => 'هذا البريد مشترك بالفعل لدينا.',
            'email.required' => 'يرجى إدخال البريد الإلكتروني.',
            'email.email' => 'البريد الإلكتروني غير صالح.',
        ]);

        Newsletter::create([
            'email' => $request->email,
        ]);

        return response()->json([
            'message' => 'تم الاشتراك بنجاح في القائمة البريدية!',
        ]);
    }

    /**
     * Admin: Get all subscribers
     */
    public function index()
    {
        $subscribers = Newsletter::orderBy('created_at', 'desc')->get();
        return response()->json($subscribers);
    }

    /**
     * Admin: Remove a subscriber
     */
    public function destroy($id)
    {
        $subscriber = Newsletter::findOrFail($id);
        $subscriber->delete();

        return response()->json([
            'message' => 'تم حذف المشترك بنجاح',
        ]);
    }
}
