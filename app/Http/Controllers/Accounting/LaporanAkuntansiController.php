<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Akuntansi\Akun;
use App\Models\Akuntansi\JurnalDetail;
use App\Services\AkuntansiService;
use Illuminate\Http\Request;

class LaporanAkuntansiController extends Controller
{
    /**
     * Laporan Laba Rugi SAK Indonesia
     */
    public function labaRugi(Request $request)
    {
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
        AkuntansiService::pastikanAkunDefault($tenantId);

        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        // Pendapatan (Normal Kredit)
        $akunPendapatan = Akun::where('tenant_id', $tenantId)->where('kategori', 'pendapatan')->get();
        $totalPendapatan = 0;
        foreach ($akunPendapatan as $akun) {
            $kredit = JurnalDetail::where('akun_id', $akun->id)
                ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]))
                ->where('jenis', 'kredit')->sum('jumlah');
            $debit = JurnalDetail::where('akun_id', $akun->id)
                ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]))
                ->where('jenis', 'debit')->sum('jumlah');
            $akun->saldo = $kredit - $debit;
            $totalPendapatan += $akun->saldo;
        }

        // HPP (Normal Debit)
        $akunHpp = Akun::where('tenant_id', $tenantId)->where('kategori', 'hpp')->get();
        $totalHpp = 0;
        foreach ($akunHpp as $akun) {
            $debit = JurnalDetail::where('akun_id', $akun->id)
                ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]))
                ->where('jenis', 'debit')->sum('jumlah');
            $kredit = JurnalDetail::where('akun_id', $akun->id)
                ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]))
                ->where('jenis', 'kredit')->sum('jumlah');
            $akun->saldo = $debit - $kredit;
            $totalHpp += $akun->saldo;
        }

        $labaKotor = $totalPendapatan - $totalHpp;

        // Beban Operasional (Normal Debit)
        $akunBeban = Akun::where('tenant_id', $tenantId)->where('kategori', 'beban_operasional')->get();
        $totalBebanOperasional = 0;
        foreach ($akunBeban as $akun) {
            $debit = JurnalDetail::where('akun_id', $akun->id)
                ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]))
                ->where('jenis', 'debit')->sum('jumlah');
            $kredit = JurnalDetail::where('akun_id', $akun->id)
                ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]))
                ->where('jenis', 'kredit')->sum('jumlah');
            $akun->saldo = $debit - $kredit;
            $totalBebanOperasional += $akun->saldo;
        }

        $labaBersih = $labaKotor - $totalBebanOperasional;

        return view('accounting.laporan.laba_rugi', compact(
            'startDate', 'endDate',
            'akunPendapatan', 'totalPendapatan',
            'akunHpp', 'totalHpp',
            'labaKotor',
            'akunBeban', 'totalBebanOperasional',
            'labaBersih'
        ));
    }

    /**
     * Laporan Posisi Keuangan (Neraca) SAK Indonesia
     */
    public function neraca(Request $request)
    {
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
        AkuntansiService::pastikanAkunDefault($tenantId);

        $perTanggal = $request->input('per_tanggal', now()->toDateString());

        // Aset (Normal Debit)
        $akunAset = Akun::where('tenant_id', $tenantId)->whereIn('kategori', ['aset_lancar', 'aset_tetap'])->get();
        $totalAset = 0;
        foreach ($akunAset as $akun) {
            $debit = JurnalDetail::where('akun_id', $akun->id)->whereHas('jurnal', fn($q) => $q->where('tanggal', '<=', $perTanggal))->where('jenis', 'debit')->sum('jumlah');
            $kredit = JurnalDetail::where('akun_id', $akun->id)->whereHas('jurnal', fn($q) => $q->where('tanggal', '<=', $perTanggal))->where('jenis', 'kredit')->sum('jumlah');
            $akun->saldo = ($akun->posisi_normal === 'debit') ? ($debit - $kredit) : ($kredit - $debit);
            $totalAset += $akun->saldo;
        }

        // Kewajiban (Normal Kredit)
        $akunKewajiban = Akun::where('tenant_id', $tenantId)->where('kategori', 'kewajiban')->get();
        $totalKewajiban = 0;
        foreach ($akunKewajiban as $akun) {
            $debit = JurnalDetail::where('akun_id', $akun->id)->whereHas('jurnal', fn($q) => $q->where('tanggal', '<=', $perTanggal))->where('jenis', 'debit')->sum('jumlah');
            $kredit = JurnalDetail::where('akun_id', $akun->id)->whereHas('jurnal', fn($q) => $q->where('tanggal', '<=', $perTanggal))->where('jenis', 'kredit')->sum('jumlah');
            $akun->saldo = $kredit - $debit;
            $totalKewajiban += $akun->saldo;
        }

        // Ekuitas (Normal Kredit)
        $akunEkuitas = Akun::where('tenant_id', $tenantId)->where('kategori', 'ekuitas')->get();
        $totalEkuitas = 0;
        foreach ($akunEkuitas as $akun) {
            $debit = JurnalDetail::where('akun_id', $akun->id)->whereHas('jurnal', fn($q) => $q->where('tanggal', '<=', $perTanggal))->where('jenis', 'debit')->sum('jumlah');
            $kredit = JurnalDetail::where('akun_id', $akun->id)->whereHas('jurnal', fn($q) => $q->where('tanggal', '<=', $perTanggal))->where('jenis', 'kredit')->sum('jumlah');
            $akun->saldo = $kredit - $debit;
            $totalEkuitas += $akun->saldo;
        }

        // Hitung Laba Berjalan s/d perTanggal
        $totPendapatan = JurnalDetail::whereHas('akun', fn($q) => $q->where('tenant_id', $tenantId)->where('kategori', 'pendapatan'))
            ->whereHas('jurnal', fn($q) => $q->where('tanggal', '<=', $perTanggal))
            ->selectRaw('SUM(CASE WHEN jenis="kredit" THEN jumlah ELSE -jumlah END) as total')->value('total') ?? 0;

        $totHpp = JurnalDetail::whereHas('akun', fn($q) => $q->where('tenant_id', $tenantId)->where('kategori', 'hpp'))
            ->whereHas('jurnal', fn($q) => $q->where('tanggal', '<=', $perTanggal))
            ->selectRaw('SUM(CASE WHEN jenis="debit" THEN jumlah ELSE -jumlah END) as total')->value('total') ?? 0;

        $totBeban = JurnalDetail::whereHas('akun', fn($q) => $q->where('tenant_id', $tenantId)->where('kategori', 'beban_operasional'))
            ->whereHas('jurnal', fn($q) => $q->where('tanggal', '<=', $perTanggal))
            ->selectRaw('SUM(CASE WHEN jenis="debit" THEN jumlah ELSE -jumlah END) as total')->value('total') ?? 0;

        $labaBerjalan = ($totPendapatan - $totHpp) - $totBeban;
        $totalEkuitas += $labaBerjalan;

        $totalPasiva = $totalKewajiban + $totalEkuitas;

        return view('accounting.laporan.neraca', compact(
            'perTanggal',
            'akunAset', 'totalAset',
            'akunKewajiban', 'totalKewajiban',
            'akunEkuitas', 'labaBerjalan', 'totalEkuitas',
            'totalPasiva'
        ));
    }

    /**
     * Buku Besar per Akun COA (General Ledger)
     */
    public function bukuBesar(Request $request)
    {
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
        AkuntansiService::pastikanAkunDefault($tenantId);

        $accounts = Akun::where('tenant_id', $tenantId)->orderBy('kode_akun')->get();
        $selectedAkunId = $request->input('akun_id', $accounts->first()?->id);
        $startDate = $request->input('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->input('end_date', now()->endOfMonth()->toDateString());

        $selectedAkun = Akun::find($selectedAkunId);
        $details = [];
        $saldoAwal = 0;

        if ($selectedAkun) {
            // Saldo Awal sebelum startDate
            $debitAwal = JurnalDetail::where('akun_id', $selectedAkun->id)
                ->whereHas('jurnal', fn($q) => $q->where('tanggal', '<', $startDate))->where('jenis', 'debit')->sum('jumlah');
            $kreditAwal = JurnalDetail::where('akun_id', $selectedAkun->id)
                ->whereHas('jurnal', fn($q) => $q->where('tanggal', '<', $startDate))->where('jenis', 'kredit')->sum('jumlah');

            $saldoAwal = ($selectedAkun->posisi_normal === 'debit') ? ($debitAwal - $kreditAwal) : ($kreditAwal - $debitAwal);

            // Mutasi dalam periode
            $details = JurnalDetail::where('akun_id', $selectedAkun->id)
                ->whereHas('jurnal', fn($q) => $q->whereBetween('tanggal', [$startDate, $endDate]))
                ->with(['jurnal.user'])
                ->get()
                ->sortBy(fn($d) => $d->jurnal->tanggal->format('Y-m-d') . '-' . $d->jurnal->id);
        }

        return view('accounting.laporan.buku_besar', compact('accounts', 'selectedAkun', 'startDate', 'endDate', 'saldoAwal', 'details'));
    }
}
