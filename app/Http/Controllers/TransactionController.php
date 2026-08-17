<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Repositories\Contracts\ReceiptSettingRepositoryInterface;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    protected TransactionRepositoryInterface $transactionRepo;
    protected ReceiptSettingRepositoryInterface $receiptSettingRepo;

    public function __construct(
        TransactionRepositoryInterface $transactionRepo,
        ReceiptSettingRepositoryInterface $receiptSettingRepo
    ) {
        $this->transactionRepo = $transactionRepo;
        $this->receiptSettingRepo = $receiptSettingRepo;
    }

    public function index(Request $request): View
    {
        $filters = $request->all();
        if (!auth()->user()->isAdmin()) {
            $filters['user_id'] = auth()->id();
        }

        $transactions = $this->transactionRepo->paginateFiltered($filters, 10)->withQueryString();

        return view('transactions.index', ['transactions' => $transactions]);
    }

    public function show(Transaction $transaction): View
    {
        if (!auth()->user()->isAdmin() && $transaction->user_id !== auth()->id()) {
            abort(403);
        }

        $transaction->load(['user', 'items', 'voidedBy']);

        return view('transactions.show', compact('transaction'));
    }

    public function receipt(Transaction $transaction): View
    {
        $transaction->load(['user', 'items']);

        $receiptSettings = $this->receiptSettingRepo->getSettingsOrNew();

        return view('transactions.receipt', [
            'transaction'     => $transaction,
            'receiptSettings' => $receiptSettings,
        ]);
    }

    /**
     * Batalkan (void) transaksi — hanya admin.
     */
    public function void(Request $request, Transaction $transaction): RedirectResponse
    {
        // Hanya admin
        if (!auth()->user()->isAdmin()) {
            abort(403);
        }

        // Sudah di-void
        if ($transaction->isVoided()) {
            return back()->with('error', 'Transaksi sudah pernah dibatalkan.');
        }

        $request->validate([
            'void_reason' => ['required', 'string', 'max:255'],
        ]);

        $success = $this->transactionRepo->void($transaction, $request->void_reason, auth()->id());

        if (!$success) {
            return back()->with('error', 'Transaksi gagal dibatalkan.');
        }

        // Auto-Journaling Reversal / Void Akuntansi SAK
        try {
            app(\App\Services\AkuntansiService::class)->catatJurnalVoid($transaction);
        } catch (\Exception $accErr) {
            \Illuminate\Support\Facades\Log::warning('Akuntansi Void Auto-Journal error: ' . $accErr->getMessage());
        }

        return back()->with('success', "Transaksi {$transaction->invoice_number} berhasil dibatalkan. Stok telah dikembalikan.");
    }
}
