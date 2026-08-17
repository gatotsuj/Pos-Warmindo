<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected TransactionRepositoryInterface $transactionRepo;
    protected ProductRepositoryInterface $productRepo;

    public function __construct(
        TransactionRepositoryInterface $transactionRepo,
        ProductRepositoryInterface $productRepo
    ) {
        $this->transactionRepo = $transactionRepo;
        $this->productRepo = $productRepo;
    }

    public function index()
    {
        $isAdmin = auth()->user()->isAdmin();
        $userId = auth()->id();

        // Today's stats (excluding voided)
        $todayStats = $this->transactionRepo->getTodayStats($isAdmin, $userId);

        // DoD Growth (Day over Day vs Yesterday)
        $yesterdayRevenue = $this->transactionRepo->getRevenueForDate(
            now()->subDay()->format('Y-m-d'),
            $isAdmin,
            $userId
        );

        $dodGrowth = 0;
        if ($yesterdayRevenue > 0) {
            $dodGrowth = (($todayStats['revenue'] - $yesterdayRevenue) / $yesterdayRevenue) * 100;
        } elseif ($todayStats['revenue'] > 0) {
            $dodGrowth = 100;
        }

        // Void Analytics today
        $voidAnalytics = $this->transactionRepo->getVoidAnalyticsForDate(
            today()->format('Y-m-d'),
            $isAdmin,
            $userId
        );

        // Sales by Category (Today)
        $salesByCategory = $this->transactionRepo->getSalesByCategoryForDate(
            today()->format('Y-m-d'),
            $isAdmin,
            $userId
        );

        // Cashier Performance (Admin Only - Today)
        $cashierPerformance = collect();
        if ($isAdmin) {
            $cashierPerformance = $this->transactionRepo->getCashierPerformanceForDate(
                today()->format('Y-m-d')
            );
        }

        // Peak Hours (Today)
        $todayTransactions = $this->transactionRepo->getTransactionsForDate(
            today()->format('Y-m-d'),
            $isAdmin,
            $userId
        );
            
        $peakHours = [];
        // Only generate hours from 06:00 to 23:00 for simplicity
        for ($i=6; $i<=23; $i++) {
            $peakHours[sprintf('%02d:00', $i)] = 0;
        }
        foreach ($todayTransactions as $trx) {
            $hour = $trx->created_at->format('H:00');
            if (isset($peakHours[$hour])) {
                $peakHours[$hour]++;
            }
        }

        // Weekly sales
        $weeklySales = $this->transactionRepo->getWeeklySalesSummary($isAdmin, $userId);

        // Payment method breakdown (today)
        $paymentBreakdown = $this->transactionRepo->getPaymentBreakdownForDate(
            today()->format('Y-m-d'),
            $isAdmin,
            $userId
        );

        // Top products (this month)
        $topProducts = $this->transactionRepo->getTopProductsForMonth(
            now()->year,
            now()->month
        );

        // Top profit products (this month)
        $topProfitProducts = $this->transactionRepo->getTopProfitProductsForMonth(
            now()->year,
            now()->month
        );

        // Recent transactions
        $recentTransactions = $this->transactionRepo->getRecentTransactions($isAdmin, $userId, 5);

        // Low stock products (admin only)
        $lowStockProducts = $isAdmin
            ? $this->productRepo->getLowStock(5, 10)
            : collect();

        return view('dashboard', compact(
            'todayStats',
            'dodGrowth',
            'voidAnalytics',
            'salesByCategory',
            'cashierPerformance',
            'peakHours',
            'weeklySales',
            'paymentBreakdown',
            'topProducts',
            'topProfitProducts',
            'recentTransactions',
            'lowStockProducts'
        ));
    }
}
