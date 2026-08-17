<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Models\ReceiptSetting;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TransactionRepository extends BaseRepository implements TransactionRepositoryInterface
{
    public function __construct(Transaction $model)
    {
        parent::__construct($model);
    }

    public function paginateFiltered(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['user', 'items']);

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('invoice_number', 'like', "%{$filters['search']}%");
        }

        if (!empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        }

        if (!empty($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function void(Transaction $transaction, string $reason, int $userId): bool
    {
        if ($transaction->isVoided()) {
            return false;
        }

        return DB::transaction(function () use ($transaction, $reason, $userId) {
            foreach ($transaction->items as $item) {
                $product = Product::find($item->product_id);

                if ($product) {
                    $stockBefore = $product->stock;
                    $product->increment('stock', $item->quantity);
                    $stockAfter = $product->fresh()->stock;

                    StockMovement::create([
                        'product_id'   => $product->id,
                        'user_id'      => $userId,
                        'type'         => 'void',
                        'quantity'     => $item->quantity,
                        'stock_before' => $stockBefore,
                        'stock_after'  => $stockAfter,
                        'reference'    => $transaction->invoice_number,
                        'notes'        => 'Void: ' . $reason,
                    ]);
                }
            }

            return $transaction->update([
                'status'      => 'voided',
                'voided_at'   => now(),
                'voided_by'   => $userId,
                'void_reason' => $reason,
            ]);
        });
    }

    public function checkout(array $cart, array $checkoutData, int $userId): Transaction
    {
        // Load receipt settings for tax & discount parameters
        $setting = ReceiptSetting::first();
        $taxPercent = $setting ? (float) $setting->tax_percent : 11;
        $taxEnabled = $setting ? (bool) $setting->tax_enabled : true;
        $discountAllowed = $setting ? (bool) $setting->discount_enabled : true;

        // Calculate totals
        $subtotal = collect($cart)->sum('subtotal');
        $discountPercent = $discountAllowed ? ($checkoutData['discount_percent'] ?? 0) : 0;
        $discountAmount = $subtotal * ($discountPercent / 100);
        $afterDiscount = $subtotal - $discountAmount;
        $taxAmount = $taxEnabled ? $afterDiscount * ($taxPercent / 100) : 0;
        $taxPercentSaved = $taxEnabled ? $taxPercent : 0;
        $grandTotal = round($afterDiscount + $taxAmount);

        // Calculate paid and change amounts
        $paymentMethod = $checkoutData['payment_method'];
        $paidAmount = $paymentMethod === 'cash'
            ? $checkoutData['paid_amount']
            : $grandTotal;

        return DB::transaction(function () use ($cart, $checkoutData, $userId, $subtotal, $discountPercent, $discountAmount, $taxPercentSaved, $taxAmount, $grandTotal, $paidAmount, $paymentMethod) {
            $transaction = $this->create([
                'invoice_number' => Transaction::generateInvoiceNumber(),
                'user_id' => $userId,
                'subtotal' => $subtotal,
                'discount_percent' => $discountPercent,
                'discount_amount' => round($discountAmount),
                'tax_percent' => $taxPercentSaved,
                'tax_amount' => round($taxAmount),
                'grand_total' => $grandTotal,
                'payment_method' => $paymentMethod,
                'paid_amount' => $paidAmount,
                'change_amount' => max(0, $paidAmount - $grandTotal),
                'notes' => $checkoutData['notes'] ?? null,
                'status' => 'completed',
            ]);

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

                StockMovement::create([
                    'product_id' => $product->id,
                    'user_id' => $userId,
                    'type' => 'out',
                    'quantity' => $item['quantity'],
                    'stock_before' => $stockBefore,
                    'stock_after' => $stockAfter,
                    'reference' => $transaction->invoice_number,
                    'notes' => 'Penjualan: '.$transaction->invoice_number,
                ]);
            }

            return $transaction;
        });
    }

    public function getTodayStats(bool $isAdmin, int $userId): array
    {
        $query = $this->model->newQuery()->whereDate('created_at', today())->where('status', '!=', 'voided');

        if (!$isAdmin) {
            $query->where('user_id', $userId);
        }

        return [
            'revenue' => $query->sum('grand_total'),
            'transactions' => $query->count(),
            'items_sold' => TransactionItem::whereHas('transaction', function ($q) use ($isAdmin, $userId) {
                $q->whereDate('created_at', today())->where('status', '!=', 'voided');
                if (!$isAdmin) $q->where('user_id', $userId);
            })->sum('quantity'),
        ];
    }

    public function getRevenueForDate(string $date, bool $isAdmin, int $userId): float
    {
        return $this->model->newQuery()
            ->whereDate('created_at', $date)
            ->where('status', '!=', 'voided')
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $userId))
            ->sum('grand_total');
    }

    public function getVoidAnalyticsForDate(string $date, bool $isAdmin, int $userId): array
    {
        $query = $this->model->newQuery()
            ->whereDate('created_at', $date)
            ->where('status', 'voided')
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $userId));

        return [
            'count' => $query->count(),
            'amount' => $query->sum('grand_total'),
        ];
    }

    public function getSalesByCategoryForDate(string $date, bool $isAdmin, int $userId): Collection
    {
        return DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_id', '=', 'transactions.id')
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(transaction_items.quantity) as total_qty'), DB::raw('SUM(transaction_items.subtotal) as total_revenue'))
            ->whereDate('transactions.created_at', $date)
            ->where('transactions.status', '!=', 'voided')
            ->when(!$isAdmin, fn($q) => $q->where('transactions.user_id', $userId))
            ->groupBy('categories.name')
            ->get();
    }

    public function getCashierPerformanceForDate(string $date): Collection
    {
        return $this->model->newQuery()
            ->with('user')
            ->select('user_id', DB::raw('COUNT(*) as total_trx'), DB::raw('SUM(grand_total) as total_revenue'))
            ->whereDate('created_at', $date)
            ->where('status', '!=', 'voided')
            ->groupBy('user_id')
            ->orderByDesc('total_revenue')
            ->get();
    }

    public function getTransactionsForDate(string $date, bool $isAdmin, int $userId): EloquentCollection
    {
        return $this->model->newQuery()
            ->whereDate('created_at', $date)
            ->where('status', '!=', 'voided')
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $userId))
            ->get();
    }

    public function getWeeklySalesSummary(bool $isAdmin, int $userId): Collection
    {
        return $this->model->newQuery()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(grand_total) as total')
            )
            ->where('status', '!=', 'voided')
            ->whereDate('created_at', '>=', now()->subDays(6))
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $userId))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(function ($item) {
                return [
                    'date' => Carbon::parse($item->date)->format('d M'),
                    'total' => (int) $item->total,
                ];
            });
    }

    public function getPaymentBreakdownForDate(string $date, bool $isAdmin, int $userId): array
    {
        return $this->model->newQuery()
            ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(grand_total) as total'))
            ->whereDate('created_at', $date)
            ->where('status', '!=', 'voided')
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $userId))
            ->groupBy('payment_method')
            ->get()
            ->toArray();
    }

    public function getTopProductsForMonth(int $year, int $month): array
    {
        return TransactionItem::select(
                'product_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('transaction', function($q) use ($year, $month) {
                $q->whereYear('created_at', $year)
                  ->whereMonth('created_at', $month)
                  ->where('status', '!=', 'voided');
            })
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get()
            ->toArray();
    }

    public function getTopProfitProductsForMonth(int $year, int $month): array
    {
        return TransactionItem::select(
                'transaction_items.product_name',
                DB::raw('SUM(transaction_items.quantity) as total_qty'),
                DB::raw('SUM(transaction_items.subtotal) as total_revenue'),
                DB::raw('SUM(transaction_items.subtotal - (transaction_items.quantity * COALESCE(products.cost_price, 0))) as total_profit')
            )
            ->leftJoin('products', 'products.id', '=', 'transaction_items.product_id')
            ->whereHas('transaction', function($q) use ($year, $month) {
                $q->whereYear('transactions.created_at', $year)
                  ->whereMonth('transactions.created_at', $month)
                  ->where('transactions.status', '!=', 'voided');
            })
            ->groupBy('transaction_items.product_id', 'transaction_items.product_name')
            ->orderByDesc('total_profit')
            ->take(5)
            ->get()
            ->toArray();
    }

    public function getRecentTransactions(bool $isAdmin, int $userId, int $limit = 5): EloquentCollection
    {
        return $this->model->newQuery()
            ->with('user')
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $userId))
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getDailyTransactions(string $date): EloquentCollection
    {
        return $this->model->newQuery()
            ->with(['user', 'items'])
            ->whereDate('created_at', $date)
            ->latest()
            ->get();
    }

    public function getMonthlyTransactions(int $year, int $month): EloquentCollection
    {
        return $this->model->newQuery()
            ->with(['user', 'items'])
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->latest()
            ->get();
    }

    public function getTenantsFinancialSummary(Collection $tenantIds, string $dateFrom, string $dateTo): Collection
    {
        return $this->model->newQuery()
            ->whereIn('tenant_id', $tenantIds)
            ->whereDate('created_at', '>=', $dateFrom)
            ->whereDate('created_at', '<=', $dateTo)
            ->selectRaw('tenant_id, COUNT(*) as transaction_count, COALESCE(SUM(grand_total), 0) as total_revenue')
            ->groupBy('tenant_id')
            ->get()
            ->keyBy('tenant_id');
    }

    public function getTenantFinancialSummaryForMonth(int $tenantId, int $year, int $month): object
    {
        $row = $this->model->newQuery()
            ->where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(grand_total), 0) as total_revenue')
            ->first();

        return (object) [
            'transaction_count' => (int) ($row->transaction_count ?? 0),
            'total_revenue' => (float) ($row->total_revenue ?? 0),
        ];
    }

    public function getTenantPaymentBreakdownForMonth(int $tenantId, int $year, int $month): EloquentCollection
    {
        return $this->model->newQuery()
            ->where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->select('payment_method', DB::raw('COUNT(*) as cnt'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('payment_method')
            ->get();
    }

    public function getTenantDailyFinancialsForMonth(int $tenantId, int $year, int $month, string $driverName): EloquentCollection
    {
        $dayExpr = $driverName === 'sqlite'
            ? 'date(created_at)'
            : 'DATE(created_at)';

        return $this->model->newQuery()
            ->where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->selectRaw("{$dayExpr} as day, COUNT(*) as cnt, COALESCE(SUM(grand_total), 0) as revenue")
            ->groupByRaw($dayExpr)
            ->orderBy('day')
            ->get();
    }

    public function getTenantTransactionsForMonth(int $tenantId, int $year, int $month, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->where('tenant_id', $tenantId)
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->with('user')
            ->latest()
            ->paginate($perPage);
    }
}
