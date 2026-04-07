<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminMerchantController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'storeName' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'logo' => 'nullable|image|max:2048',
            'documents.*' => 'nullable|mimes:jpeg,png,jpg,pdf|max:5120'
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->fullName,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'merchant',
            ]);

            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('merchants/logos', 'public');
                $logoPath = env('APP_URL') . '/storage/' . $logoPath;
            }

            $docPaths = [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $doc) {
                    $path = $doc->store('merchants/docs', 'public');
                    $docPaths[] = env('APP_URL') . '/storage/' . $path;
                }
            }

            $merchant = Merchant::create([
                'user_id' => $user->id,
                'store_name' => $request->storeName,
                'phone' => $request->phone,
                'store_category' => $request->category,
                'tax_id' => $request->taxId,
                'commercial_record' => $request->commercialRecord,
                'city' => $request->city,
                'address' => $request->address,
                'logo_path' => $logoPath,
                'documents_paths' => $docPaths,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Merchant created successfully.',
                'merchant' => $merchant
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to create merchant.', 'error' => $e->getMessage()], 500);
        }
    }
}
