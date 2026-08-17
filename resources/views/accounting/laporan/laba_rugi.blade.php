@extends('layouts.app')

@section('title', 'Laporan Laba Rugi')

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Laporan Laba Rugi (Income Statement)</h2>
                <p class="text-sm text-slate-500 mt-0.5">Standar Akuntansi Keuangan (SAK EMKM / SAK ETAP)</p>
            </div>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 text-white font-bold text-xs rounded-xl flex items-center gap-1.5 shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak Laporan</span>
        </button>
    </div>

    {{-- Filter Periode --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 print:hidden">
        <form action="{{ route('akuntansi.laporan.laba-rugi') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <label class="text-xs font-bold text-slate-600">Periode:</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 border rounded-xl text-xs font-bold">
            <span class="text-xs text-slate-400">s/d</span>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 border rounded-xl text-xs font-bold">
            <button type="submit" class="px-4 py-2 bg-indomie-red text-white font-bold text-xs rounded-xl shadow">Tampilkan</button>
        </form>
    </div>

    {{-- Sheet Laporan Laba Rugi --}}
    <div class="bg-white rounded-2xl p-8 shadow-sm border border-slate-100 space-y-6">
        @php
            $tenantId = session('current_tenant_id') ?? auth()->user()->tenant_id;
            $receiptSetting = \App\Models\ReceiptSetting::where('tenant_id', $tenantId)->first();
            $storeLogo = $receiptSetting?->store_logo ?? $receiptSetting?->logo ?? null;
            $storeDisplayName = $receiptSetting?->store_name ?? ($currentTenant->name ?? 'POS UMKM');
        @endphp
        <div class="text-center border-b border-slate-200 pb-4">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-gradient-to-r from-slate-900 to-slate-800 text-white rounded-2xl font-black text-base tracking-widest shadow-md mb-2">
                @if($storeLogo && \Illuminate\Support\Facades\Storage::disk('public')->exists($storeLogo))
                    <img src="{{ asset('storage/' . $storeLogo) }}" alt="Logo" class="w-6 h-6 object-contain bg-white rounded p-0.5">
                @else
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                @endif
                <span>{{ strtoupper($storeDisplayName) }}</span>
            </div>
            <h4 class="text-base font-black text-slate-800 uppercase tracking-wider mt-1">LAPORAN LABA RUGI</h4>
            <p class="text-xs text-slate-500 font-medium">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} – {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</p>
        </div>

        {{-- Section 1: PENDAPATAN USAHA --}}
        <div>
            <h5 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-2">I. PENDAPATAN USAHA</h5>
            <div class="space-y-2 border-b border-slate-100 pb-3">
                @foreach($akunPendapatan as $akun)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-700 font-medium pl-4">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</span>
                        <span class="font-mono font-bold text-slate-800">Rp {{ number_format($akun->saldo, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between items-center py-2 font-bold text-sm bg-slate-50 px-3 rounded-lg mt-2">
                <span class="text-slate-800 uppercase">TOTAL PENDAPATAN USAHA</span>
                <span class="font-mono text-indomie-green text-base font-black">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Section 2: BEBAN POKOK PENJUALAN (HPP) --}}
        <div>
            <h5 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-2">II. BEBAN POKOK PENJUALAN (HPP)</h5>
            <div class="space-y-2 border-b border-slate-100 pb-3">
                @foreach($akunHpp as $akun)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-700 font-medium pl-4">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</span>
                        <span class="font-mono font-bold text-slate-800">Rp {{ number_format($akun->saldo, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between items-center py-2 font-bold text-sm bg-slate-50 px-3 rounded-lg mt-2">
                <span class="text-slate-800 uppercase">TOTAL BEBAN POKOK PENJUALAN (HPP)</span>
                <span class="font-mono text-slate-800 text-sm font-bold">Rp {{ number_format($totalHpp, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- LABA KOTOR --}}
        <div class="flex justify-between items-center p-3 bg-blue-50/60 rounded-xl border border-blue-100">
            <span class="text-sm font-black text-blue-900 uppercase">LABA KOTOR (GROSS PROFIT)</span>
            <span class="font-mono text-lg font-black text-blue-700">Rp {{ number_format($labaKotor, 0, ',', '.') }}</span>
        </div>

        {{-- Section 3: BEBAN OPERASIONAL & ADMINISTRASI --}}
        <div>
            <h5 class="text-xs font-black text-slate-400 uppercase tracking-wider mb-2">III. BEBAN OPERASIONAL & ADMINISTRASI</h5>
            <div class="space-y-2 border-b border-slate-100 pb-3">
                @foreach($akunBeban as $akun)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-700 font-medium pl-4">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</span>
                        <span class="font-mono font-bold text-slate-800">Rp {{ number_format($akun->saldo, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between items-center py-2 font-bold text-sm bg-slate-50 px-3 rounded-lg mt-2">
                <span class="text-slate-800 uppercase">TOTAL BEBAN OPERASIONAL</span>
                <span class="font-mono text-indomie-red text-sm font-bold">Rp {{ number_format($totalBebanOperasional, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- LABA BERSIH USAHA --}}
        <div class="flex justify-between items-center p-4 bg-gradient-to-r {{ $labaBersih >= 0 ? 'from-green-500 to-green-600' : 'from-red-500 to-red-600' }} text-white rounded-2xl shadow-lg">
            <div>
                <span class="text-xs uppercase font-bold tracking-wider opacity-80">LABA BERSIH USAHA (NET PROFIT)</span>
                <p class="text-xs opacity-90 font-medium">Setelah dikurangi HPP & seluruh beban operasional</p>
            </div>
            <span class="font-mono text-2xl font-black">Rp {{ number_format($labaBersih, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
@endsection
