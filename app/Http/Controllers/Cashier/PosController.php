<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ReceiptSetting;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PosController extends Controller
{
    //
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->active()
            ->where('stock', '>', 0)
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('sku', 'like', "%{$search}%");
                });
            })
            ->when($request->category, function ($query, $categoryId) {
                $query->where('category_id', $categoryId);
            })
            // ->orderBy('name')
            ->get();

        $categories = Category::whereHas('products', function ($query) {
            $query->active()->where('stock', '>', 0);
        })->orderBy('name')->get();

        $cart = session('pos_cart', []);

        $receiptSetting = ReceiptSetting::first();

        return view('cashier.pos', compact('products', 'categories', 'cart', 'receiptSetting'));
    }

    /**
     * Add product to cart
     */
    public function addToCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => [
                'required',
                Rule::exists('products', 'id')->where(fn ($q) => $q->where('tenant_id', CurrentTenant::id())),
            ],
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check if product is available
        if (! $product->is_active || $product->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Product tidak tersedia.',
            ], 400);
        }

        $cart = session('pos_cart', []);
        $productId = $product->id;

        if (isset($cart[$productId])) {
            // Check stock
            $newQty = $cart[$productId]['quantity'] + 1;
            if ($newQty > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock tidak mencukupi. Tersedia: '.$product->stock,
                ], 400);
            }
            $cart[$productId]['quantity'] = $newQty;
            $cart[$productId]['subtotal'] = $newQty * $product->price;
        } else {
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => 1,
                'subtotal' => $product->price,
                'stock' => $product->stock,
            ];
        }

        session(['pos_cart' => $cart]);

        return response()->json([
            'success' => true,
            'message' => $product->name.' ditambahkan ke cart.',
            'cart' => $cart,
            'totals' => $this->calculateTotals($cart),
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function updateCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => [
                'required',
                Rule::exists('products', 'id')->where(fn ($q) => $q->where('tenant_id', CurrentTenant::id())),
            ],
            'quantity' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($request->product_id);
        $cart = session('pos_cart', []);
        $productId = $product->id;

        if (! isset($cart[$productId])) {
            return response()->json([
                'success' => false,
                'message' => 'Product tidak ada di cart.',
            ], 400);
        }

        if ($request->quantity <= 0) {
            unset($cart[$productId]);
        } else {
            if ($request->quantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock tidak mencukupi. Tersedia: '.$product->stock,
                ], 400);
            }
            $cart[$productId]['quantity'] = $request->quantity;
            $cart[$productId]['subtotal'] = $request->quantity * $product->price;
        }

        session(['pos_cart' => $cart]);

        return response()->json([
            'success' => true,
            'cart' => $cart,
            'totals' => $this->calculateTotals($cart),
        ]);
    }

    /**
     * Remove item from cart
     */
    public function removeFromCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required',
        ]);

        $cart = session('pos_cart', []);
        $productId = $request->product_id;

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session(['pos_cart' => $cart]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item dihapus dari cart.',
            'cart' => $cart,
            'totals' => $this->calculateTotals($cart),
        ]);
    }

    /**
     * Clear entire cart
     */
    public function clearCart(): JsonResponse
    {
        session()->forget('pos_cart');

        return response()->json([
            'success' => true,
            'message' => 'Cart dikosongkan.',
            'cart' => [],
            'totals' => $this->calculateTotals([]),
        ]);
    }

    /**
     * Calculate cart totals
     */
    private function calculateTotals(array $cart, float $discountPercent = 0): array
    {
        $setting = ReceiptSetting::first();
        $taxPercent = $setting ? (float) $setting->tax_percent : 11;
        $taxEnabled = $setting ? (bool) $setting->tax_enabled : true;

        $subtotal = collect($cart)->sum('subtotal');
        $discountAmount = $subtotal * ($discountPercent / 100);
        $afterDiscount = $subtotal - $discountAmount;
        $taxAmount = $taxEnabled ? $afterDiscount * ($taxPercent / 100) : 0;
        $grandTotal = $afterDiscount + $taxAmount;

        return [
            'subtotal' => round($subtotal),
            'discount_percent' => $discountPercent,
            'discount_amount' => round($discountAmount),
            'tax_percent' => $taxEnabled ? $taxPercent : 0,
            'tax_amount' => round($taxAmount),
            'grand_total' => round($grandTotal),
            'items_count' => collect($cart)->sum('quantity'),
        ];
    }

    public function checkout(Request $request): JsonResponse
    {
        $request->validate([
            'payment_method' => 'required|in:cash,card,qris',
            'paid_amount' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:500',
        ]);

        $cart = session('pos_cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart kosong.',
            ], 400);
        }

        // Load receipt settings
        $setting = ReceiptSetting::first();
        $taxPercent = $setting ? (float) $setting->tax_percent : 11;
        $taxEnabled = $setting ? (bool) $setting->tax_enabled : true;
        $discountAllowed = $setting ? (bool) $setting->discount_enabled : true;

        // Calculate totals
        $subtotal = collect($cart)->sum('subtotal');
        $discountPercent = $discountAllowed ? ($request->discount_percent ?? 0) : 0;
        $discountAmount = $subtotal * ($discountPercent / 100);
        $afterDiscount = $subtotal - $discountAmount;
        $taxAmount = $taxEnabled ? $afterDiscount * ($taxPercent / 100) : 0;
        $taxPercentSaved = $taxEnabled ? $taxPercent : 0;
        $grandTotal = round($afterDiscount + $taxAmount);

        // Validate payment amount
        if ($request->payment_method === 'cash' && $request->paid_amount < $grandTotal) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah pembayaran kurang.',
            ], 400);
        }

        // For card/qris, paid amount equals grand total
        $paidAmount = $request->payment_method === 'cash'
            ? $request->paid_amount
            : $grandTotal;

        try {
            DB::beginTransaction();

            // Create transaction
            $transaction = Transaction::create([
                'invoice_number' => Transaction::generateInvoiceNumber(),
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'discount_percent' => $discountPercent,
                'discount_amount' => round($discountAmount),
                'tax_percent' => $taxPercentSaved,
                'tax_amount' => round($taxAmount),
                'grand_total' => $grandTotal,
                'payment_method' => $request->payment_method,
                'paid_amount' => $paidAmount,
                'change_amount' => max(0, $paidAmount - $grandTotal),
                'notes' => $request->notes,
                'status' => 'completed',
            ]);

            // Create transaction items & decrease stock
            foreach ($cart as $item) {
                $product = Product::find($item['id']);

                if (! $product || ! $product->hasEnoughStock($item['quantity'])) {
                    throw new \Exception("Stock {$item['name']} tidak mencukupi.");
                }

                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_price' => $product->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);

                $stockBefore = $product->stock;
                $product->decreaseStock($item['quantity']);
                $stockAfter = $product->fresh()->stock;

                // Catat pergerakan stok
                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => auth()->id(),
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference' => $transaction->invoice_number,
                    'notes' => 'Penjualan: '.$transaction->invoice_number,
                ]);
            }

            // Clear cart
            session()->forget('pos_cart');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'transaction' => $transaction->load('items'),
                'receipt_url' => route('transactions.receipt', $transaction),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal: '.$e->getMessage(),
            ], 500);
        }
    }
}
