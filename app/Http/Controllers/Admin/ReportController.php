<?php

namespace App\Http\Controllers\Admin;

use App\Exports\DailyTransactionExport;
use App\Exports\MonthlyTransactionExport;
use App\Http\Controllers\Controller;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    protected Excel $excel;
    protected TransactionRepositoryInterface $transactionRepo;

    public function __construct(Excel $excel, TransactionRepositoryInterface $transactionRepo)
    {
        $this->excel = $excel;
        $this->transactionRepo = $transactionRepo;
    }

    public function index()
    {
        $today = today();
        $month = now();

        $todayTransactions = $this->transactionRepo->getDailyTransactions($today->format('Y-m-d'));
        $monthTransactions = $this->transactionRepo->getMonthlyTransactions($month->year, $month->month);

        return view('admin.reports.index', [
            'today' => [
                'revenue' => $todayTransactions->sum('grand_total'),
                'transactions' => $todayTransactions->count(),
                'items' => $todayTransactions->sum(fn ($t) => $t->items->sum('quantity')),
            ],
            'month' => [
                'revenue' => $monthTransactions->sum('grand_total'),
                'transactions' => $monthTransactions->count(),
                'items' => $monthTransactions->sum(fn ($t) => $t->items->sum('quantity')),
            ],
        ]);
    }

    public function daily(Request $request)
    {
        $date = $request->date ? \Carbon\Carbon::parse($request->date) : today();

        $transactions = $this->transactionRepo->getDailyTransactions($date->format('Y-m-d'));

        $summary = [
            'total_revenue' => $transactions->sum('grand_total'),
            'total_transactions' => $transactions->count(),
            'total_items' => $transactions->sum(fn ($t) => $t->items->sum('quantity')),
            'avg_transaction' => $transactions->count() > 0 ? $transactions->avg('grand_total') : 0,
            'by_payment' => $transactions->groupBy('payment_method')->map(fn ($g) => [
                'count' => $g->count(),
                'total' => $g->sum('grand_total'),
            ]),
        ];

        return view('admin.reports.daily', compact('date', 'transactions', 'summary'));
    }

    public function monthly(Request $request)
    {
        $month = $request->month
            ? Carbon::createFromFormat('Y-m', $request->month)
            : now();

        $transactions = $this->transactionRepo->getMonthlyTransactions($month->year, $month->month);

        $summary = [
            'total_revenue' => $transactions->sum('grand_total'),
            'total_transactions' => $transactions->count(),
            'total_items' => $transactions->sum(fn ($t) => $t->items->sum('quantity')),
            'avg_transaction' => $transactions->count() > 0
                ? $transactions->avg('grand_total')
                : 0,
            'by_payment' => $transactions
                ->groupBy('payment_method')
                ->map(fn ($g) => [
                    'count' => $g->count(),
                    'total' => $g->sum('grand_total'),
                ]),
        ];

        return view('admin.reports.monthly', compact(
            'month',
            'transactions',
            'summary'
        ));
    }

    public function exportDaily(Request $request): BinaryFileResponse
    {
        $date = $request->date ? Carbon::parse($request->date) : today();

        $transactions = $this->transactionRepo->getDailyTransactions($date->format('Y-m-d'));

        $fileName = 'daily-report-'.$date->format('Y-m-d').'.xlsx';

        return $this->excel->download(
            new DailyTransactionExport($transactions, $date),
            $fileName
        );
    }

    public function exportMonthly(Request $request): BinaryFileResponse
    {
        $month = $request->month
            ? Carbon::createFromFormat('Y-m', $request->month)
            : now();

        $transactions = $this->transactionRepo->getMonthlyTransactions($month->year, $month->month);

        $fileName = 'monthly-report-'.$month->format('Y-m').'.xlsx';

        return $this->excel->download(
            new MonthlyTransactionExport($transactions, $month),
            $fileName
        );
    }
}
