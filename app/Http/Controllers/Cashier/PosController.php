<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ReceiptSettingRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PosController extends Controller
{
    protected ProductRepositoryInterface $productRepo;
    protected CategoryRepositoryInterface $categoryRepo;
    protected ReceiptSettingRepositoryInterface $receiptSettingRepo;
    protected TransactionRepositoryInterface $transactionRepo;

    public function __construct(
        ProductRepositoryInterface $productRepo,
        CategoryRepositoryInterface $categoryRepo,
        ReceiptSettingRepositoryInterface $receiptSettingRepo,
        TransactionRepositoryInterface $transactionRepo
    ) {
        $this->productRepo = $productRepo;
        $this->categoryRepo = $categoryRepo;
        $this->receiptSettingRepo = $receiptSettingRepo;
        $this->transactionRepo = $transactionRepo;
    }

    public function index(Request $request): View
    {
        $products = $this->productRepo->getActiveInStock($request->all());
        $categories = $this->categoryRepo->getActiveInStockOrderedByName();
        $cart = session('pos_cart', []);
        $receiptSetting = $this->receiptSettingRepo->getSettingsOrNew();

        $user = auth()->user();
        $tenantId = session('current_tenant_id') ?? $user->tenant_id;

        $activeShift = \App\Models\CashierShift::where('tenant_id', $tenantId)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($activeShift) {
            $activeShift->current_cash_sales = Transaction::where('tenant_id', $tenantId)
                ->where('payment_method', 'cash')
                ->where('status', '!=', 'voided')
                ->whereBetween('created_at', [$activeShift->opened_at, now()])
                ->sum('grand_total');

            $activeShift->current_non_cash_sales = Transaction::where('tenant_id', $tenantId)
                ->whereIn('payment_method', ['card', 'qris'])
                ->where('status', '!=', 'voided')
                ->whereBetween('created_at', [$activeShift->opened_at, now()])
                ->sum('grand_total');

            $activeShift->current_cash_expenses = \App\Models\Akuntansi\Pengeluaran::where('tenant_id', $tenantId)
                ->whereBetween('tanggal', [$activeShift->opened_at->format('Y-m-d'), now()->format('Y-m-d')])
                ->sum('jumlah');

            $activeShift->calculated_expected_cash = $activeShift->starting_cash + $activeShift->current_cash_sales - $activeShift->current_cash_expenses;
        }

        return view('cashier.pos', compact('products', 'categories', 'cart', 'receiptSetting', 'activeShift'));
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

        $product = $this->productRepo->findOrFail($request->product_id);

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

        $product = $this->productRepo->findOrFail($request->product_id);
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
        $setting = $this->receiptSettingRepo->getSettingsOrNew();
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

        try {
            $transaction = $this->transactionRepo->checkout($cart, $request->all(), auth()->id());

            // Auto-Journaling Akuntansi SAK
            try {
                app(\App\Services\AkuntansiService::class)->catatJurnalPos($transaction);
            } catch (\Exception $accErr) {
                \Illuminate\Support\Facades\Log::warning('Akuntansi Auto-Journal error: ' . $accErr->getMessage());
            }

            // Clear cart
            session()->forget('pos_cart');

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'transaction' => $transaction->load('items'),
                'receipt_url' => route('transactions.receipt', $transaction),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal: '.$e->getMessage(),
            ], 500);
        }
    }
}
