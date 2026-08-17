<?php

namespace App\Http\Controllers\Accounting;

use App\Http\Controllers\Controller;
use App\Models\Akuntansi\Akun;
use App\Models\Akuntansi\Pengeluaran;
use App\Services\AkuntansiService;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    protected AkuntansiService $akuntansiService;

    public function __construct(AkuntansiService $akuntansiService)
    {
        $this->akuntansiService = $akuntansiService;
    }

    public function index(Request $request)
    {
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
        AkuntansiService::pastikanAkunDefault($tenantId);

        $query = Pengeluaran::where('tenant_id', $tenantId)->with(['akunBeban', 'akunKas', 'user']);

        if ($request->filled('start_date')) {
            $query->whereDate('tanggal', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('tanggal', '<=', $request->end_date);
        }

        $pengeluaranList = $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc')->paginate(15);

        // Options Akun
        $akunBeban = Akun::where('tenant_id', $tenantId)->whereIn('kategori', ['beban_operasional', 'hpp'])->orderBy('kode_akun')->get();
        $akunKas = Akun::where('tenant_id', $tenantId)->whereIn('kategori', ['aset_lancar'])->orderBy('kode_akun')->get();

        return view('accounting.pengeluaran.index', compact('pengeluaranList', 'akunBeban', 'akunKas'));
    }

    public function store(Request $request)
    {
        $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;

        $request->validate([
            'tanggal' => 'required|date',
            'akun_beban_id' => 'required|exists:akuntansi_akun,id',
            'akun_kas_id' => 'required|exists:akuntansi_akun,id',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string|max:255',
            'bukti_foto' => 'nullable|image|max:2048',
        ]);

        $buktiPath = null;
        if ($request->hasFile('bukti_foto')) {
            $buktiPath = $request->file('bukti_foto')->store('pengeluaran', 'public');
        }

        $todayStr = now()->format('Ymd');
        $countToday = Pengeluaran::where('tenant_id', $tenantId)->whereDate('tanggal', now()->toDateString())->count() + 1;
        $nomorPengeluaran = 'EXP-' . $todayStr . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

        $pengeluaran = Pengeluaran::create([
            'tenant_id' => $tenantId,
            'nomor_pengeluaran' => $nomorPengeluaran,
            'tanggal' => $request->tanggal,
            'akun_beban_id' => $request->akun_beban_id,
            'akun_kas_id' => $request->akun_kas_id,
            'jumlah' => $request->jumlah,
            'keterangan' => $request->keterangan,
            'bukti_foto' => $buktiPath,
            'user_id' => auth()->id(),
        ]);

        // Jurnal Otomatis berpasangan
        $this->akuntansiService->catatJurnalPengeluaran($pengeluaran);

        return back()->with('success', 'Pengeluaran Rp ' . number_format($request->jumlah, 0, ',', '.') . ' berhasil dicatat dan dijurnal.');
    }

    public function destroy(Pengeluaran $pengeluaran)
    {
        if ($pengeluaran->jurnal) {
            $pengeluaran->jurnal->details()->delete();
            $pengeluaran->jurnal->delete();
        }

        $pengeluaran->delete();
        return back()->with('success', 'Catatan pengeluaran dan jurnal terkait berhasil dihapus.');
    }
}
