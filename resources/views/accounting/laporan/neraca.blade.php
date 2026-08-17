@extends('layouts.app')

@section('title', 'Laporan Posisi Keuangan (Neraca)')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l9-4 9 4v12l-9 4-9-4V6zm9-1v16"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Laporan Posisi Keuangan (Neraca)</h2>
                <p class="text-sm text-slate-500 mt-0.5">Standar Akuntansi Keuangan SAK EMKM / SAK ETAP (Aset = Kewajiban + Ekuitas)</p>
            </div>
        </div>
        <button onclick="window.print()" class="px-4 py-2 bg-slate-800 text-white font-bold text-xs rounded-xl flex items-center gap-1.5 shadow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            <span>Cetak Neraca</span>
        </button>
    </div>

    {{-- Filter Per Tanggal --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100 print:hidden">
        <form action="{{ route('akuntansi.laporan.neraca') }}" method="GET" class="flex items-center gap-3">
            <label class="text-xs font-bold text-slate-600">Per Tanggal:</label>
            <input type="date" name="per_tanggal" value="{{ $perTanggal }}" class="px-3 py-2 border rounded-xl text-xs font-bold">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-bold text-xs rounded-xl shadow">Tampilkan Neraca</button>
        </form>
    </div>

    {{-- Sheet Neraca 2 Kolom (Aset vs Kewajiban & Ekuitas) --}}
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
            <h4 class="text-base font-black text-blue-600 uppercase tracking-wider mt-1">LAPORAN POSISI KEUANGAN (NERACA)</h4>
            <p class="text-xs text-slate-500 font-medium">Per Tanggal: {{ \Carbon\Carbon::parse($perTanggal)->format('d F Y') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- AKTIVA / ASET --}}
            <div class="space-y-4">
                <div class="bg-slate-100 p-2.5 rounded-xl text-center">
                    <h5 class="text-xs font-black text-slate-800 uppercase tracking-wider">AKTIVA / ASET</h5>
                </div>

                <div class="space-y-2">
                    <h6 class="text-[11px] font-bold text-slate-400 uppercase">ASET LANCAR & TETAP</h6>
                    @foreach($akunAset as $akun)
                        <div class="flex justify-between text-xs py-1 border-b border-slate-50">
                            <span class="text-slate-700 font-medium">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</span>
                            <span class="font-mono font-bold text-slate-800">Rp {{ number_format($akun->saldo, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-xl border border-blue-100 mt-4">
                    <span class="text-xs font-black text-blue-900 uppercase">TOTAL ASET (AKTIVA)</span>
                    <span class="font-mono text-base font-black text-blue-700">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- PASIVA / KEWAJIBAN & EKUITAS --}}
            <div class="space-y-4">
                <div class="bg-slate-100 p-2.5 rounded-xl text-center">
                    <h5 class="text-xs font-black text-slate-800 uppercase tracking-wider">PASIVA (KEWAJIBAN & EKUITAS)</h5>
                </div>

                {{-- Kewajiban --}}
                <div class="space-y-2">
                    <h6 class="text-[11px] font-bold text-slate-400 uppercase">KEWAJIBAN / UTANG</h6>
                    @foreach($akunKewajiban as $akun)
                        <div class="flex justify-between text-xs py-1 border-b border-slate-50">
                            <span class="text-slate-700 font-medium">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</span>
                            <span class="font-mono font-bold text-slate-800">Rp {{ number_format($akun->saldo, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between text-xs font-bold text-slate-700 pt-1">
                        <span>Total Kewajiban</span>
                        <span class="font-mono">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Ekuitas --}}
                <div class="space-y-2 pt-2 border-t border-slate-200">
                    <h6 class="text-[11px] font-bold text-slate-400 uppercase">EKUITAS / MODAL</h6>
                    @foreach($akunEkuitas as $akun)
                        <div class="flex justify-between text-xs py-1 border-b border-slate-50">
                            <span class="text-slate-700 font-medium">{{ $akun->kode_akun }} - {{ $akun->nama_akun }}</span>
                            <span class="font-mono font-bold text-slate-800">Rp {{ number_format($akun->saldo, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="flex justify-between text-xs py-1 text-indomie-green font-bold">
                        <span>Laba Berjalan Tahun Ini</span>
                        <span class="font-mono">Rp {{ number_format($labaBerjalan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-xs font-bold text-slate-700 pt-1">
                        <span>Total Ekuitas</span>
                        <span class="font-mono">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-xl border border-blue-100 mt-4">
                    <span class="text-xs font-black text-blue-900 uppercase">TOTAL PASIVA (KEWAJIBAN & EKUITAS)</span>
                    <span class="font-mono text-base font-black text-blue-700">Rp {{ number_format($totalPasiva, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Verification Balance Alert --}}
        <div class="p-4 rounded-xl text-center text-xs font-bold flex items-center justify-center gap-2 {{ abs($totalAset - $totalPasiva) < 1 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            @if(abs($totalAset - $totalPasiva) < 1)
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Status Neraca: SEIMBANG / BALANCED (Aset = Kewajiban + Ekuitas)</span>
            @else
                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>Status Neraca: UNBALANCED (Selisih: Rp {{ number_format(abs($totalAset - $totalPasiva), 0, ',', '.') }})</span>
            @endif
        </div>
    </div>
</div>
@endsection
