<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Repositories\Contracts\EventRepositoryInterface;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FinancialReportController extends Controller
{
    protected TenantRepositoryInterface $tenantRepo;
    protected EventRepositoryInterface $eventRepo;
    protected TransactionRepositoryInterface $transactionRepo;

    public function __construct(
        TenantRepositoryInterface $tenantRepo,
        EventRepositoryInterface $eventRepo,
        TransactionRepositoryInterface $transactionRepo
    ) {
        $this->tenantRepo = $tenantRepo;
        $this->eventRepo = $eventRepo;
        $this->transactionRepo = $transactionRepo;
    }

    public function index(Request $request): View
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $events = $this->eventRepo->allOrderedByStartsAtAndName();

        $tenantIds = $this->tenantRepo->getFilteredTenantIds($request->all());

        $stats = collect();
        $grandTotals = ['revenue' => 0, 'transactions' => 0];

        if ($tenantIds->isNotEmpty()) {
            $stats = $this->transactionRepo->getTenantsFinancialSummary($tenantIds, $dateFrom, $dateTo);

            $grandTotals = [
                'revenue' => (int) $stats->sum('total_revenue'),
                'transactions' => (int) $stats->sum('transaction_count'),
            ];
        }

        $tenants = $this->tenantRepo->paginateFiltered($request->all(), 20)->withQueryString();

        return view('superadmin.financial.index', compact(
            'tenants',
            'stats',
            'events',
            'dateFrom',
            'dateTo',
            'grandTotals'
        ));
    }

    public function showTenant(Request $request, Tenant $tenant): View
    {
        $monthInput = $request->input('month', now()->format('Y-m'));
        try {
            $month = Carbon::createFromFormat('Y-m', $monthInput);
        } catch (\Throwable) {
            $month = now();
            $monthInput = $month->format('Y-m');
        }

        $summary = $this->transactionRepo->getTenantFinancialSummaryForMonth(
            $tenant->id,
            $month->year,
            $month->month
        );

        $byPayment = $this->transactionRepo->getTenantPaymentBreakdownForMonth(
            $tenant->id,
            $month->year,
            $month->month
        );

        $daily = $this->transactionRepo->getTenantDailyFinancialsForMonth(
            $tenant->id,
            $month->year,
            $month->month,
            DB::getDriverName()
        );

        $transactions = $this->transactionRepo->getTenantTransactionsForMonth(
            $tenant->id,
            $month->year,
            $month->month,
            15
        )->withQueryString();

        $tenant->load('event');

        return view('superadmin.financial.tenant', compact(
            'tenant',
            'month',
            'monthInput',
            'summary',
            'byPayment',
            'daily',
            'transactions'
        ));
    }
}
