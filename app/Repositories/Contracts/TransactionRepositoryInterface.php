<?php

namespace App\Repositories\Contracts;

use App\Models\Transaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Paginate transactions with filters: search, date, payment_method, status, user_id.
     */
    public function paginateFiltered(array $filters, int $perPage = 10): LengthAwarePaginator;

    /**
     * Perform transaction void (cancellation) operation, reverting stock and creating log entries.
     */
    public function void(Transaction $transaction, string $reason, int $userId): bool;

    /**
     * Create a transaction checkout from cart items, updating inventory levels and creating logs.
     */
    public function checkout(array $cart, array $checkoutData, int $userId): Transaction;

    /**
     * Dashboard: Today's statistics.
     */
    public function getTodayStats(bool $isAdmin, int $userId): array;

    /**
     * Dashboard: Revenue for a specific date.
     */
    public function getRevenueForDate(string $date, bool $isAdmin, int $userId): float;

    /**
     * Dashboard: Void transactions count & total amount for a specific date.
     */
    public function getVoidAnalyticsForDate(string $date, bool $isAdmin, int $userId): array;

    /**
     * Dashboard: Sales category distribution for today.
     */
    public function getSalesByCategoryForDate(string $date, bool $isAdmin, int $userId): Collection;

    /**
     * Dashboard: Cashier performance.
     */
    public function getCashierPerformanceForDate(string $date): Collection;

    /**
     * Dashboard: Get transactions for peak hour counting.
     */
    public function getTransactionsForDate(string $date, bool $isAdmin, int $userId): EloquentCollection;

    /**
     * Dashboard: Get last 7 days of sales.
     */
    public function getWeeklySalesSummary(bool $isAdmin, int $userId): Collection;

    /**
     * Dashboard: Payment method count and revenue breakdown.
     */
    public function getPaymentBreakdownForDate(string $date, bool $isAdmin, int $userId): array;

    /**
     * Dashboard: Top 5 products for the current month.
     */
    public function getTopProductsForMonth(int $year, int $month): array;

    /**
     * Dashboard: Top 5 profit generating products for the current month.
     */
    public function getTopProfitProductsForMonth(int $year, int $month): array;

    /**
     * Dashboard & List: Get recent transactions.
     */
    public function getRecentTransactions(bool $isAdmin, int $userId, int $limit = 5): EloquentCollection;

    /**
     * Reports: Get daily transaction list.
     */
    public function getDailyTransactions(string $date): EloquentCollection;

    /**
     * Reports: Get monthly transaction list.
     */
    public function getMonthlyTransactions(int $year, int $month): EloquentCollection;

    /**
     * Superadmin Finance: Get financial sum by tenant list.
     */
    public function getTenantsFinancialSummary(Collection $tenantIds, string $dateFrom, string $dateTo): Collection;

    /**
     * Superadmin Finance: Get a single tenant's financial summary for a month.
     */
    public function getTenantFinancialSummaryForMonth(int $tenantId, int $year, int $month): object;

    /**
     * Superadmin Finance: Get a single tenant's payment breakdown for a month.
     */
    public function getTenantPaymentBreakdownForMonth(int $tenantId, int $year, int $month): EloquentCollection;

    /**
     * Superadmin Finance: Get a single tenant's daily sales graph data for a month.
     */
    public function getTenantDailyFinancialsForMonth(int $tenantId, int $year, int $month, string $driverName): EloquentCollection;

    /**
     * Superadmin Finance: Paginate transactions for a single tenant in a month.
     */
    public function getTenantTransactionsForMonth(int $tenantId, int $year, int $month, int $perPage = 15): LengthAwarePaginator;
}
