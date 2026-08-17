@extends('layouts.app')

@section('title', 'Audit Shift Kasir')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center font-black text-2xl shadow-md flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Audit Shift Kasir & Rekap Laci Kas</h2>
                <p class="text-xs text-slate-500 mt-0.5">Monitoring modal kas awal, hasil penjualan tunai, dan evaluasi selisih kas fisik</p>
            </div>
        </div>
    </div>

    {{-- Filter Date & Kasir --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
        <form action="{{ route('admin.shifts.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}" class="px-3.5 py-2 border border-slate-200 rounded-xl text-xs font-bold">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase mb-1">Pilih Kasir</label>
                <select name="user_id" class="px-3.5 py-2 border border-slate-200 rounded-xl text-xs font-bold">
                    <option value="">— Semua Kasir —</option>
                    @foreach($cashiers as $cashier)
                        <option value="{{ $cashier->id }}" {{ request('user_id') == $cashier->id ? 'selected' : '' }}>
                            {{ $cashier->name }} ({{ $cashier->email }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="pt-5 flex items-center gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-bold text-xs rounded-xl shadow">Filter Audit</button>
                <a href="{{ route('admin.shifts.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 font-bold text-xs rounded-xl">Reset</a>
            </div>
        </form>
    </div>

    {{-- Tabel Riwayat Shift --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Riwayat Rekap Shift Kasir</h3>
            <span class="text-xs text-slate-500 font-medium">Total: {{ $shifts->total() }} shift</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-bold uppercase text-[10px] border-b border-slate-100">
                        <th class="p-3.5">Waktu Shift</th>
                        <th class="p-3.5">Kasir</th>
                        <th class="p-3.5 text-right">Modal Kas Awal</th>
                        <th class="p-3.5 text-right">Penjualan Cash</th>
                        <th class="p-3.5 text-right">Pengeluaran</th>
                        <th class="p-3.5 text-right">Estimasi Kas</th>
                        <th class="p-3.5 text-right">Uang Fisik Laci</th>
                        <th class="p-3.5 text-right">Selisih Kas</th>
                        <th class="p-3.5 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($shifts as $s)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3.5 text-slate-600 font-medium">
                                <div>{{ $s->opened_at->format('d/m/Y H:i') }}</div>
                                <div class="text-[10px] text-slate-400">
                                    {{ $s->closed_at ? 's/d ' . $s->closed_at->format('H:i') : 'Masih Buka' }}
                                </div>
                            </td>
                            <td class="p-3.5 font-bold text-slate-800">{{ $s->user->name ?? 'Kasir Deleted' }}</td>
                            <td class="p-3.5 text-right font-mono font-bold text-slate-700">Rp {{ number_format($s->starting_cash, 0, ',', '.') }}</td>
                            <td class="p-3.5 text-right font-mono font-bold text-green-600">Rp {{ number_format($s->cash_sales, 0, ',', '.') }}</td>
                            <td class="p-3.5 text-right font-mono font-bold text-red-500">Rp {{ number_format($s->cash_expenses, 0, ',', '.') }}</td>
                            <td class="p-3.5 text-right font-mono font-black text-slate-900">Rp {{ number_format($s->expected_cash, 0, ',', '.') }}</td>
                            <td class="p-3.5 text-right font-mono font-black text-blue-600">
                                {{ $s->actual_cash !== null ? 'Rp ' . number_format($s->actual_cash, 0, ',', '.') : '-' }}
                            </td>
                            <td class="p-3.5 text-right font-mono font-bold">
                                @if($s->status === 'closed')
                                    @if(abs($s->cash_difference) < 1)
                                        <span class="text-green-600">0 (Match)</span>
                                    @elseif($s->cash_difference > 0)
                                        <span class="text-blue-600">+Rp {{ number_format($s->cash_difference, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-red-600">-Rp {{ number_format(abs($s->cash_difference), 0, ',', '.') }}</span>
                                    @endif
                                @else
                                    <span class="text-slate-300">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center">
                                @if($s->status === 'open')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-800 animate-pulse">Shift Aktif</span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">Tutup Shift</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-slate-400 font-medium">Belum ada data riwayat shift kasir.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($shifts->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $shifts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
