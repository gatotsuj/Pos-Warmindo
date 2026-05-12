<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    //
    public function index()
    {
        $isAdmin = auth()->user()->isAdmin();
        $userId = auth()->id();

        // Today's stats
        $todayStats = $this->getTodayStats($isAdmin, $userId);

        // Weekly sales (last 7 days)
        //$weeklySales = $this->getWeeklySales($isAdmin, $userId);

        // Payment method breakdown (today)
        $paymentBreakdown = $this->getPaymentBreakdown($isAdmin, $userId);

        // Top products (this month)
        $topProducts = $this->getTopProducts();

        // Recent transactions
        $recentTransactions = Transaction::with('user')
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $userId))
            ->latest()
            ->take(5)
            ->get();

        // Low stock products (admin only)
        $lowStockProducts = $isAdmin ? Product::lowStock(10)->take(5)->get() : collect();

        $weeklySales = Transaction::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(grand_total) as total')
        )
        ->whereDate('created_at', '>=', now()->subDays(6))
        ->groupBy(DB::raw('DATE(created_at)'))
        ->orderBy('date')
        ->get()
        ->map(function ($item) {
            return [
                'date' => Carbon::parse($item->date)->format('d M'),
                'total' => (int) $item->total,
            ];
        });

        return view('dashboard', compact(
            'todayStats',
            'weeklySales',
            'paymentBreakdown',
            'topProducts',
            'recentTransactions',
            'lowStockProducts'
        ));
    }

    private function getTodayStats(bool $isAdmin, int $userId): array
    {
        $query = Transaction::whereDate('created_at', today());

        if (!$isAdmin) {
            $query->where('user_id', $userId);
        }

        return [
            'revenue' => $query->sum('grand_total'),
            'transactions' => $query->count(),
            'items_sold' => TransactionItem::whereHas('transaction', function ($q) use ($isAdmin, $userId) {
                $q->whereDate('created_at', today());
                if (!$isAdmin) $q->where('user_id', $userId);
            })->sum('quantity'),
        ];
    }

    private function getWeeklySales(bool $isAdmin, int $userId): array
    {
        $sales = Transaction::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(grand_total) as total'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $userId))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Fill missing dates with 0
        $result = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $result[] = [
                'date' => now()->subDays($i)->format('D'),
                'total' => $sales[$date]->total ?? 0,
                'count' => $sales[$date]->count ?? 0,
            ];
        }

        return $result;
    }

    private function getPaymentBreakdown(bool $isAdmin, int $userId): array
    {
        return Transaction::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(grand_total) as total'))
            ->whereDate('created_at', today())
            ->when(!$isAdmin, fn($q) => $q->where('user_id', $userId))
            ->groupBy('payment_method')
            ->get()
            ->toArray();
    }

    private function getTopProducts(): array
    {
        return TransactionItem::select(
                'product_name',
                DB::raw('SUM(quantity) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue')
            )
            ->whereHas('transaction', fn($q) => $q->whereMonth('created_at', now()->month))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get()
            ->toArray();
    }
}
