<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Tenant;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FinancialReportController extends Controller
{
    public function index(Request $request): View
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->format('Y-m-d'));

        $events = Event::query()->orderByDesc('starts_at')->orderBy('name')->get();

        $tenantBaseQuery = Tenant::query()
            ->with('event')
            ->withCount('users')
            ->when($request->filled('event_id'), fn ($q) => $q->where('event_id', $request->integer('event_id')))
            ->when($request->filled('search'), fn ($q) => $q->where('name', 'like', '%'.$request->search.'%'));

        $tenantIds = (clone $tenantBaseQuery)->pluck('id');

        $stats = collect();
        $grandTotals = ['revenue' => 0, 'transactions' => 0];

        if ($tenantIds->isNotEmpty()) {
            $stats = Transaction::query()
                ->whereIn('tenant_id', $tenantIds)
                ->whereDate('created_at', '>=', $dateFrom)
                ->whereDate('created_at', '<=', $dateTo)
                ->selectRaw('tenant_id, COUNT(*) as transaction_count, COALESCE(SUM(grand_total), 0) as total_revenue')
                ->groupBy('tenant_id')
                ->get()
                ->keyBy('tenant_id');

            $grandTotals = [
                'revenue' => (int) $stats->sum('total_revenue'),
                'transactions' => (int) $stats->sum('transaction_count'),
            ];
        }

        $tenants = $tenantBaseQuery->orderBy('name')->paginate(20)->withQueryString();

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

        $base = Transaction::query()->where('tenant_id', $tenant->id);

        $summaryRow = (clone $base)
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(grand_total), 0) as total_revenue')
            ->first();

        $summary = (object) [
            'transaction_count' => (int) ($summaryRow->transaction_count ?? 0),
            'total_revenue' => (float) ($summaryRow->total_revenue ?? 0),
        ];

        $byPayment = (clone $base)
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->select('payment_method', DB::raw('COUNT(*) as cnt'), DB::raw('SUM(grand_total) as total'))
            ->groupBy('payment_method')
            ->get();

        $dayExpr = DB::getDriverName() === 'sqlite'
            ? 'date(created_at)'
            : 'DATE(created_at)';

        $daily = (clone $base)
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->selectRaw("{$dayExpr} as day, COUNT(*) as cnt, COALESCE(SUM(grand_total), 0) as revenue")
            ->groupByRaw($dayExpr)
            ->orderBy('day')
            ->get();

        $transactions = (clone $base)
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->with('user')
            ->latest()
            ->paginate(15)
            ->withQueryString();

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
