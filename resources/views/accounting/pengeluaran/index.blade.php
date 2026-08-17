@extends('layouts.app')

@section('title', 'Pengeluaran Kas Operasional')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center font-black text-2xl shadow-md flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Pengeluaran Kas (Petty Cash)</h2>
                <p class="text-xs text-slate-500 mt-0.5">SaaS Accounting - Catat belanja operasional harian toko UMKM</p>
            </div>
        </div>
    </div>

    {{-- Form Tambah Pengeluaran Cepat --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
        <h3 class="text-base font-bold text-slate-800 mb-4 flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Input Pengeluaran Baru</span>
        </h3>
        <form action="{{ route('akuntansi.pengeluaran.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:ring-indomie-red focus:border-indomie-red">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Beban / Kategori</label>
                    <select name="akun_beban_id" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:ring-indomie-red focus:border-indomie-red">
                        <option value="">-- Pilih Akun Beban --</option>
                        @foreach($akunBeban as $akun)
                            <option value="{{ $akun->id }}">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Sumber Kas / Bank</label>
                    <select name="akun_kas_id" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:ring-indomie-red focus:border-indomie-red">
                        @foreach($akunKas as $kas)
                            <option value="{{ $kas->id }}">{{ $kas->kode_akun }} - {{ $kas->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jumlah Nominal (Rp)</label>
                    <input type="number" name="jumlah" min="1" step="100" placeholder="Contoh: 25000" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-bold focus:ring-indomie-red focus:border-indomie-red">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Keterangan / Keperluan</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Beli Es Batu 5 Plastik & Gas 3kg" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:ring-indomie-red focus:border-indomie-red">
                </div>
            </div>
            <div class="flex justify-end">
                <button type="submit" class="px-6 py-3 bg-gradient-to-r from-indomie-red to-red-600 text-white font-bold text-sm rounded-xl shadow-md hover:brightness-110 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Simpan & Jurnal Otomatis
                </button>
            </div>
        </form>
    </div>

    {{-- Tabel Riwayat Pengeluaran --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Riwayat Pengeluaran Kas</h3>
            <span class="text-xs text-slate-500">Total: {{ $pengeluaranList->total() }} catatan</span>
        </div>

        @if($pengeluaranList->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="p-4">No. Pengeluaran</th>
                            <th class="p-4">Tanggal</th>
                            <th class="p-4">Kategori Beban</th>
                            <th class="p-4">Sumber Pembayaran</th>
                            <th class="p-4">Keterangan</th>
                            <th class="p-4 text-right">Nominal</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @foreach($pengeluaranList as $item)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4 font-mono font-bold text-blue-600 text-xs">{{ $item->nomor_pengeluaran }}</td>
                                <td class="p-4 text-slate-600 text-xs">{{ $item->tanggal->format('d/m/Y') }}</td>
                                <td class="p-4 font-bold text-slate-800 text-xs">{{ $item->akunBeban->nama_akun ?? '-' }}</td>
                                <td class="p-4 text-slate-600 text-xs">{{ $item->akunKas->nama_akun ?? '-' }}</td>
                                <td class="p-4 text-slate-700 text-xs">{{ $item->keterangan }}</td>
                                <td class="p-4 text-right font-black text-indomie-red text-sm">{{ $item->formatted_jumlah }}</td>
                                <td class="p-4 text-center">
                                    <form action="{{ route('akuntansi.pengeluaran.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus transaksi pengeluaran dan batal jurnal ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-bold">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100">
                {{ $pengeluaranList->links() }}
            </div>
        @else
            <div class="text-center py-12 text-slate-400">
                <p class="text-sm">Belum ada data pengeluaran kas tercatat.</p>
            </div>
        @endif
    </div>
</div>
@endsection
