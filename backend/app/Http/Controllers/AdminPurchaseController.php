<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Product;
use App\Traits\LogsInventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminPurchaseController extends Controller
{
    use LogsInventory;

    public function index()
    {
        $purchases = Purchase::with(['supplier', 'items.product'])->orderBy('purchase_date', 'desc')->get();
        return response()->json($purchases);
    }

    public function statistics()
    {
        $totalPurchases = Purchase::where('status', '!=', 'cancelled')->sum('total_amount');
        $monthlyPurchases = Purchase::where('status', '!=', 'cancelled')
            ->whereMonth('purchase_date', now()->month)
            ->sum('total_amount');
        $activeSuppliers = Supplier::count();
        $pendingBills = Purchase::where('status', 'pending')->count();

        return response()->json([
            'totalPurchases' => (float)$totalPurchases,
            'monthlyPurchases' => (float)$monthlyPurchases,
            'activeSuppliers' => $activeSuppliers,
            'pendingBills' => $pendingBills,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|unique:purchases,invoice_number',
            'purchase_date' => 'required|date',
            'status' => 'required|in:pending,received,cancelled',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $totalAmount = collect($request->items)->sum(function($item) {
                return $item['quantity'] * $item['unit_price'];
            });

            $purchase = Purchase::create([
                'supplier_id' => $request->supplier_id,
                'invoice_number' => $request->invoice_number,
                'purchase_date' => $request->purchase_date,
                'total_amount' => $totalAmount,
                'status' => $request->status,
                'notes' => $request->notes,
            ]);

            foreach ($request->items as $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);

                // If received, update stock
                if ($purchase->status === 'received') {
                    $product = Product::find($item['product_id']);
                    $oldS = $product->stock;
                    $product->increment('stock', $item['quantity']);

                    $this->logInventoryChange(
                        $product->id, 
                        $oldS, 
                        $oldS + $item['quantity'], 
                        'purchase', 
                        "استلام مشتريات من فاتورة رقم #{$purchase->invoice_number}",
                        ['purchase_id' => $purchase->id]
                    );

                    // Update purchase_price in product too
                    $product->update(['purchase_price' => $item['unit_price']]);
                }
            }

            return response()->json(['message' => 'تم تسجيل الفاتورة بنجاح', 'purchase' => $purchase]);
        });
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|in:pending,received,cancelled']);
        $purchase = Purchase::with('items')->findOrFail($id);

        if ($purchase->status === $request->status) {
            return response()->json(['message' => 'لم يتغير شيء']);
        }

        return DB::transaction(function () use ($purchase, $request) {
            $oldStatus = $purchase->status;
            $newStatus = $request->status;

            // If changing FROM anything TO received
            if ($newStatus === 'received') {
                foreach ($purchase->items as $item) {
                    $p = Product::find($item->product_id);
                    if (!$p) continue;
                    $oldS = $p->stock;
                    $p->increment('stock', $item->quantity);

                    $this->logInventoryChange(
                        $p->id, 
                        $oldS, 
                        $oldS + $item->quantity, 
                        'purchase_received', 
                        "تغيير حالة الفاتورة #{$purchase->invoice_number} إلى مستلم",
                        ['purchase_id' => $purchase->id]
                    );
                    // Update purchase_price in product too
                    $p->update(['purchase_price' => $item->unit_price]);
                }
            } 
            // If changing FROM received TO cancelled or pending (Reverse stock)
            elseif ($oldStatus === 'received' && $newStatus !== 'received') {
                foreach ($purchase->items as $item) {
                    $p = Product::find($item->product_id);
                    if (!$p) continue;
                    $oldS = $p->stock;
                    $p->decrement('stock', $item->quantity);

                    $this->logInventoryChange(
                        $p->id, 
                        $oldS, 
                        $oldS - $item->quantity, 
                        'purchase_cancelled', 
                        "تغيير حالة الفاتورة #{$purchase->invoice_number} من مستلم إلى {$newStatus}",
                        ['purchase_id' => $purchase->id]
                    );
                }
            }

            $purchase->update(['status' => $newStatus]);
            return response()->json(['message' => 'تم تحديث حالة الفاتورة بنجاح']);
        });
    }

    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);
        
        return DB::transaction(function () use ($purchase) {
            if ($purchase->status === 'received') {
                foreach ($purchase->items as $item) {
                    $p = Product::find($item->product_id);
                    if (!$p) continue;
                    $oldS = $p->stock;
                    $p->decrement('stock', $item->quantity);

                    $this->logInventoryChange(
                        $p->id, 
                        $oldS, 
                        $oldS - $item->quantity, 
                        'purchase_deletion', 
                        "حذف فاتورة مشتريات رقم #{$purchase->invoice_number}",
                        ['purchase_id' => $purchase->id]
                    );
                }
            }
            $purchase->delete();
            return response()->json(['message' => 'تم حذف الفاتورة بنجاح']);
        });
    }

    public function getSuppliers()
    {
        return response()->json(Supplier::all());
    }

    public function storeSupplier(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $supplier = Supplier::create($request->all());
        return response()->json(['message' => 'تم إضافة المورد بنجاح', 'supplier' => $supplier]);
    }
}
