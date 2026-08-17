<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Akuntansi\Akun;
use App\Services\AkuntansiService;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    public function index()
    {
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
        AkuntansiService::pastikanAkunDefault($tenantId);

        $currentTenant = \App\Models\Tenant::find($tenantId);
        $accounts = Akun::where('tenant_id', $tenantId)
            ->orderBy('kode_akun')
            ->get();

        return view('accounting.akun.index', compact('accounts', 'currentTenant'));
    }

    public function resetDefault()
    {
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
        AkuntansiService::pastikanAkunDefault($tenantId);

        return back()->with('success', 'Bagan Akun COA berhasil disinkronkan ke Templat Standar SAK SaaS.');
    }

    public function store(Request $request)
    {
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;

        $request->validate([
            'kode_akun' => 'required|string|max:20|unique:akuntansi_akun,kode_akun,NULL,id,tenant_id,' . $tenantId,
            'nama_akun' => 'required|string|max:150',
            'kategori' => 'required|in:aset_lancar,aset_tetap,kewajiban,ekuitas,pendapatan,hpp,beban_operasional',
            'posisi_normal' => 'required|in:debit,kredit',
        ]);

        Akun::create([
            'tenant_id' => $tenantId,
            'kode_akun' => $request->kode_akun,
            'nama_akun' => $request->nama_akun,
            'kategori' => $request->kategori,
            'posisi_normal' => $request->posisi_normal,
            'is_system' => false,
        ]);

        return back()->with('success', 'Akun ' . $request->nama_akun . ' berhasil ditambahkan.');
    }

    public function update(Request $request, Akun $akun)
    {
        if ($akun->is_system) {
            return back()->with('error', 'Akun sistem bawaan tidak dapat diubah kodenya.');
        }

        $request->validate([
            'nama_akun' => 'required|string|max:150',
            'kategori' => 'required|in:aset_lancar,aset_tetap,kewajiban,ekuitas,pendapatan,hpp,beban_operasional',
            'posisi_normal' => 'required|in:debit,kredit',
        ]);

        $akun->update([
            'nama_akun' => $request->nama_akun,
            'kategori' => $request->kategori,
            'posisi_normal' => $request->posisi_normal,
        ]);

        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    public function destroy(Akun $akun)
    {
        if ($akun->is_system) {
            return back()->with('error', 'Akun bawaan sistem tidak dapat dihapus.');
        }

        if ($akun->jurnalDetails()->exists()) {
            return back()->with('error', 'Akun tidak dapat dihapus karena sudah memiliki histori jurnal transaksi.');
        }

        $akun->delete();
        return back()->with('success', 'Akun berhasil dihapus.');
    }
}
