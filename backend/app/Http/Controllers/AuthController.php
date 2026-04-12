<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

class AuthController extends Controller
{
    /**
     * Full URL for hash-based Vue routes, e.g. {base}/#/auth/callback?code=...
     */
    private function spaUrl(string $hashPath): string
    {
        $base = rtrim((string) config('app.frontend_url'), '/');
        $hashPath = ltrim($hashPath, '/');

        return "{$base}/#/{$hashPath}";
    }

    public function redirectToGoogle()
    {
        if (!class_exists('Laravel\Socialite\Facades\Socialite')) {
            return response()->json(['message' => 'Socialite package not installed. Run: composer require laravel/socialite'], 500);
        }
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        if (!class_exists('Laravel\Socialite\Facades\Socialite')) {
            return redirect()->away($this->spaUrl('Auth/LogIn?error=socialite_missing'));
        }
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // تحقق من أن الحساب مفعل من قبل جوجل
            if (!($googleUser->user['email_verified'] ?? false)) {
                return redirect()->away($this->spaUrl('Auth/LogIn?error=google_not_verified'));
            }

            $user = User::updateOrCreate([
                'email' => $googleUser->getEmail(),
            ], [
                'name' => $googleUser->getName(),
                'google_id' => $googleUser->getId(),
                'password' => Hash::make(Str::random(16)),
            ]);

            $code = Str::random(64);
            Cache::put(
                'oauth_exchange:'.$code,
                $user->id,
                now()->addMinutes(3)
            );

            return redirect()->away(
                $this->spaUrl('auth/callback?code='.urlencode($code))
            );
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Google OAuth callback failed', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->away($this->spaUrl('Auth/LogIn?error=google_failed'));
        }
    }

    /**
     * Exchange short-lived OAuth redirect code for an API token (avoids token in browser URL).
     */
    public function exchangeOAuthCode(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|size:64',
        ]);

        $userId = Cache::pull('oauth_exchange:'.$data['code']);

        if (! $userId) {
            return response()->json(['message' => 'رمز الدخول غير صالح أو منتهي. أعد المحاولة من جوجل.'], 401);
        }

        $user = User::findOrFail($userId);

        if (! $user->is_active) {
            return response()->json(['message' => 'هذا الحساب موقوف'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
        ]);

        try {
            Mail::to($user->email)->send(new WelcomeEmail($user));
        } catch (\Exception $e) {
            // Log error but don't stop registration
            \Illuminate\Support\Facades\Log::error('Welcome Email failed: ' . $e->getMessage());
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->merge([
            'email' => strtolower(trim($request->email)),
        ]);

        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['البيانات المدخلة غير صحيحة.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json(['message' => 'هذا الحساب موقوف'], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    public function user(Request $request)
    {
        $user = $request->user()->loadCount(['orders', 'favorites']);
        return response()->json([
            'user' => $user
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:1000',
            'avatar' => 'nullable|image|max:2048',
            'password' => 'nullable|string|min:8|confirmed',
            'notifications' => 'nullable|array',
            'address' => 'nullable|string|max:255',
            'preferred_shipping_company' => 'nullable|string|max:100',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->has('username')) {
            $user->username = $request->username;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }
        if ($request->has('notifications')) {
            $user->notifications_prefs = json_encode($request->notifications);
        }
        if ($request->has('address')) {
            $user->address = $request->address;
        }
        if ($request->has('preferred_shipping_company')) {
            $user->preferred_shipping_company = $request->preferred_shipping_company;
        }
        
        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = '/storage/' . $path;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return response()->json([
            'message' => 'تم تحديث البيانات بنجاح',
            'user' => $user
        ]);
    }
}
