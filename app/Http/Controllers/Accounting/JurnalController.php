<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Akuntansi\Akun;
use App\Models\Akuntansi\Jurnal;
use App\Models\Akuntansi\JurnalDetail;
use App\Services\AkuntansiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JurnalController extends Controller
{
    public function index(Request $request)
    {
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
        AkuntansiService::pastikanAkunDefault($tenantId);

        $query = Jurnal::where('tenant_id', $tenantId)->with(['details.akun', 'user']);

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }
        if ($request->filled('sumber')) {
            $query->where('sumber_transaksi', $request->sumber);
        }

        $jurnals = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(15);
        $accounts = Akun::where('tenant_id', $tenantId)->orderBy('kode_akun')->get();

        return view('accounting.jurnal.index', compact('jurnals', 'accounts'));
    }

    public function storeManual(Request $request)
    {
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;

        $request->validate([
            'tanggal' => 'required|date',
            'keterangan' => 'required|string|max:255',
            'items' => 'required|array|min:2',
            'items.*.akun_id' => 'required|exists:akuntansi_akun,id',
            'items.*.jenis' => 'required|in:debit,kredit',
            'items.*.jumlah' => 'required|numeric|min:1',
        ]);

        $totalDebit = collect($request->items)->where('jenis', 'debit')->sum('jumlah');
        $totalKredit = collect($request->items)->where('jenis', 'kredit')->sum('jumlah');

        if (abs($totalDebit - $totalKredit) > 0.01) {
            return back()->with('error', 'Gagal: Total Debit (Rp ' . number_format($totalDebit, 0, ',', '.') . ') dan Kredit (Rp ' . number_format($totalKredit, 0, ',', '.') . ') tidak seimbang (Unbalanced)!');
        }

        DB::transaction(function () use ($request, $tenantId) {
            $nomorJurnal = AkuntansiService::generateNomorJurnal($tenantId);

            $jurnal = Jurnal::create([
                'tenant_id' => $tenantId,
                'nomor_jurnal' => $nomorJurnal,
                'tanggal' => $request->tanggal,
                'sumber_transaksi' => 'manual',
                'keterangan' => $request->keterangan,
                'user_id' => auth()->id(),
            ]);

            foreach ($request->items as $item) {
                JurnalDetail::create([
                    'jurnal_id' => $jurnal->id,
                    'akun_id' => $item['akun_id'],
                    'jenis' => $item['jenis'],
                    'jumlah' => $item['jumlah'],
                    'catatan' => $item['catatan'] ?? null,
                ]);
            }
        });

        return back()->with('success', 'Jurnal manual berhasil disimpan.');
    }
}
