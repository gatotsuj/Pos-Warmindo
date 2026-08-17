<?php

namespace App\Http\Controllers;

use App\Models\CashierShift;
use App\Models\Transaction;
use App\Models\Akuntansi\Pengeluaran;
use App\Services\AkuntansiService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashierShiftController extends Controller
{
    /**
     * Tampilkan riwayat audit shift kasir (khusus Admin & Superadmin).
     */
    public function index(Request $request): View
    {
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;

        $query = CashierShift::where('tenant_id', $tenantId)
            ->with('user')
            ->orderBy('opened_at', 'desc');

        if ($request->filled('date')) {
            $query->whereDate('opened_at', $request->date);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $shifts = $query->paginate(15)->withQueryString();
        $cashiers = \App\Models\User::where('tenant_id', $tenantId)->get();

        return view('admin.shifts.index', compact('shifts', 'cashiers'));
    }

    /**
     * Buka Shift Kasir Baru (Buka Laci Kas).
     */
    public function open(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $tenantId = session('current_tenant_id') ?? $user->tenant_id;

        // Cek apakah kasir sudah memiliki shift yang masih terbuka
        $activeShift = CashierShift::where('tenant_id', $tenantId)
            ->where('user_id', $user->id)
            ->where('status', 'open')
            ->first();

        if ($activeShift) {
            return back()->with('error', 'Anda sudah memiliki shift kasir yang aktif.');
        }

        $request->validate([
            'starting_cash' => ['required', 'numeric', 'min:0'],
        ], [
            'starting_cash.required' => 'Nominal Modal Kas Awal wajib diisi.',
            'starting_cash.numeric'  => 'Modal Kas Awal harus berupa angka.',
            'starting_cash.min'      => 'Modal Kas Awal tidak boleh negatif.',
        ]);

        CashierShift::create([
            'tenant_id'     => $tenantId,
            'user_id'       => $user->id,
            'starting_cash' => $request->starting_cash,
            'cash_sales'    => 0,
            'non_cash_sales'=> 0,
            'cash_expenses'  => 0,
            'expected_cash' => $request->starting_cash,
            'opened_at'     => now(),
            'status'        => 'open',
        ]);

        return back()->with('success', 'Shift kasir berhasil dibuka! Modal Kas Awal: Rp ' . number_format($request->starting_cash, 0, ',', '.'));
    }

    /**
     * Tutup Shift Kasir (Rekap & Cek Selisih Uang Laci Kas).
     */
    public function close(Request $request, CashierShift $shift): RedirectResponse
    {
        if ($shift->status === 'closed') {
            return back()->with('error', 'Shift kasir ini sudah ditutup sebelumnya.');
        }

        $request->validate([
            'actual_cash' => ['required', 'numeric', 'min:0'],
            'notes'       => ['nullable', 'string', 'max:500'],
        ], [
            'actual_cash.required' => 'Jumlah uang fisik di laci kas wajib diisi.',
            'actual_cash.numeric'  => 'Jumlah uang fisik harus berupa angka.',
            'actual_cash.min'      => 'Jumlah uang fisik tidak boleh negatif.',
        ]);

        // Hitung akumulasi penjualan selama shift ini berlangsung
        $cashSales = Transaction::where('tenant_id', $shift->tenant_id)
            ->where('payment_method', 'cash')
            ->where('status', '!=', 'voided')
            ->whereBetween('created_at', [$shift->opened_at, now()])
            ->sum('grand_total');

        $nonCashSales = Transaction::where('tenant_id', $shift->tenant_id)
            ->whereIn('payment_method', ['card', 'qris'])
            ->where('status', '!=', 'voided')
            ->whereBetween('created_at', [$shift->opened_at, now()])
            ->sum('grand_total');

        $cashExpenses = Pengeluaran::where('tenant_id', $shift->tenant_id)
            ->whereBetween('tanggal', [$shift->opened_at->format('Y-m-d'), now()->format('Y-m-d')])
            ->sum('jumlah');

        $expectedCash = $shift->starting_cash + $cashSales - $cashExpenses;
        $actualCash = $request->actual_cash;
        $cashDifference = $actualCash - $expectedCash;

        $shift->update([
            'cash_sales'      => $cashSales,
            'non_cash_sales'  => $nonCashSales,
            'cash_expenses'   => $cashExpenses,
            'expected_cash'   => $expectedCash,
            'actual_cash'     => $actualCash,
            'cash_difference' => $cashDifference,
            'closed_at'       => now(),
            'status'          => 'closed',
            'notes'           => $request->notes,
        ]);

        $message = 'Shift kasir telah ditutup. ';
        if (abs($cashDifference) < 1) {
            $message .= 'Status Kas: SEIMBANG (Tidak ada selisih).';
        } elseif ($cashDifference > 0) {
            $message .= 'Status Kas: SURPLUS/LEBIH Rp ' . number_format($cashDifference, 0, ',', '.');
        } else {
            $message .= 'Status Kas: DEFISIT/KURANG Rp ' . number_format(abs($cashDifference), 0, ',', '.');
        }

        return redirect()->route('pos.index')->with('success', $message);
    }
}
