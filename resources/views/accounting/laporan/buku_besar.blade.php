@extends('layouts.app')

@section('title', 'Buku Besar (General Ledger)')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center font-black text-2xl shadow-md flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Buku Besar (General Ledger)</h2>
                <p class="text-xs text-slate-500 mt-0.5">SaaS Accounting - Rincian mutasi transaksi & saldo berjalan per akun COA</p>
            </div>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 text-white font-bold text-xs rounded-xl flex items-center gap-1.5 shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak Buku Besar</span>
        </button>
    </div>

    {{-- Filter Akun & Tanggal --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 print:hidden">
        <form action="{{ route('akuntansi.laporan.buku-besar') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Pilih Akun</label>
                <select name="akun_id" class="px-3.5 py-2 border border-slate-200 rounded-xl text-xs font-bold focus:ring-indomie-red">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}" {{ $selectedAkun && $selectedAkun->id == $acc->id ? 'selected' : '' }}>
                            {{ $acc->kode_akun }} - {{ $acc->nama_akun }} ({{ $acc->kategori_label }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 border border-slate-200 rounded-xl text-xs font-bold">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 border border-slate-200 rounded-xl text-xs font-bold">
            </div>
            <div class="pt-5">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-bold text-xs rounded-xl shadow">Tampilkan Mutasi</button>
            </div>
        </form>
    </div>

    {{-- Sheet Mutasi Buku Besar --}}
    @if($selectedAkun)
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-100 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-black text-slate-800 font-mono">{{ $selectedAkun->kode_akun }} - {{ $selectedAkun->nama_akun }}</h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Kategori: <span class="font-bold text-slate-700">{{ $selectedAkun->kategori_label }}</span> | Posisi Normal: <span class="font-bold uppercase text-indomie-red">{{ $selectedAkun->posisi_normal }}</span></p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-slate-400 font-bold uppercase">Saldo Awal (per {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }})</p>
                    <p class="text-sm font-black text-slate-800 font-mono">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-bold uppercase text-[10px] border-b border-slate-100">
                            <th class="p-3">Tanggal</th>
                            <th class="p-3">No. Jurnal</th>
                            <th class="p-3">Keterangan</th>
                            <th class="p-3 text-right">Debit (Rp)</th>
                            <th class="p-3 text-right">Kredit (Rp)</th>
                            <th class="p-3 text-right">Saldo Berjalan (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        {{-- Baris Saldo Awal --}}
                        <tr class="bg-slate-50/50 font-bold">
                            <td class="p-3 text-slate-500">{{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }}</td>
                            <td class="p-3 text-slate-400">-</td>
                            <td class="p-3 text-slate-700 italic">SALDO AWAL PERIODE</td>
                            <td class="p-3 text-right font-mono">-</td>
                            <td class="p-3 text-right font-mono">-</td>
                            <td class="p-3 text-right font-mono text-blue-600">Rp {{ number_format($saldoAwal, 0, ',', '.') }}</td>
                        </tr>

                        @php $runningBalance = $saldoAwal; @endphp
                        @foreach($details as $detail)
                            @php
                                if ($selectedAkun->posisi_normal === 'debit') {
                                    $runningBalance += ($detail->jenis === 'debit' ? $detail->jumlah : -$detail->jumlah);
                                } else {
                                    $runningBalance += ($detail->jenis === 'kredit' ? $detail->jumlah : -$detail->jumlah);
                                }
                            @endphp
                            <tr class="hover:bg-slate-50/50">
                                <td class="p-3 text-slate-600 font-medium">{{ $detail->jurnal->tanggal->format('d/m/Y') }}</td>
                                <td class="p-3 font-mono font-bold text-blue-600">{{ $detail->jurnal->nomor_jurnal }}</td>
                                <td class="p-3 text-slate-700">{{ $detail->catatan ?? $detail->jurnal->keterangan }}</td>
                                <td class="p-3 text-right font-mono font-bold text-slate-800">
                                    {{ $detail->jenis === 'debit' ? number_format($detail->jumlah, 0, ',', '.') : '-' }}
                                </td>
                                <td class="p-3 text-right font-mono font-bold text-slate-800">
                                    {{ $detail->jenis === 'kredit' ? number_format($detail->jumlah, 0, ',', '.') : '-' }}
                                </td>
                                <td class="p-3 text-right font-mono font-black text-slate-900">
                                    Rp {{ number_format($runningBalance, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
