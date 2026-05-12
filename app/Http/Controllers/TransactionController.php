<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\ReceiptSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::with(['user', 'items'])
            ->when(!auth()->user()->isAdmin(), function ($q) {
                $q->where('user_id', auth()->id());
            })
            ->when($request->search, function ($q, $search) {
                $q->where('invoice_number', 'like', "%{$search}%");
            })
            ->when($request->date, function ($q, $date) {
                $q->whereDate('created_at', $date);
            })
            ->when($request->payment_method, function ($q, $method) {
                $q->where('payment_method', $method);
            })
            ->when($request->status, function ($q, $status) {
                $q->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('transactions.index', ['transactions' => $query]);
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

        $receiptSettings = ReceiptSetting::first();

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

        DB::transaction(function () use ($transaction, $request) {
            // Kembalikan stok tiap item
            foreach ($transaction->items as $item) {
                $product = Product::find($item->product_id);

                if ($product) {
                    $stockBefore = $product->stock;
                    $product->increment('stock', $item->quantity);
                    $stockAfter = $product->fresh()->stock;

                    StockMovement::create([
                        'product_id'   => $product->id,
                        'user_id'      => auth()->id(),
                        'type'         => 'void',
                        'quantity'     => $item->quantity,
                        'stock_before' => $stockBefore,
                        'stock_after'  => $stockAfter,
                        'reference'    => $transaction->invoice_number,
                        'notes'        => 'Void: ' . $request->void_reason,
                    ]);
                }
            }

            // Update status transaksi
            $transaction->update([
                'status'      => 'voided',
                'voided_at'   => now(),
                'voided_by'   => auth()->id(),
                'void_reason' => $request->void_reason,
            ]);
        });

        return back()->with('success', "Transaksi {$transaction->invoice_number} berhasil dibatalkan. Stok telah dikembalikan.");
    }
}
