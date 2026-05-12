@extends('layouts.app')

@section('title', 'Laporan Keuangan (Semua Tenant)')

@section('content')
    <div class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Laporan keuangan per tenant</h1>
            <p class="text-sm text-slate-600 mt-1">Ringkasan transaksi lintas tenant. Filter berdasarkan acara dan rentang tanggal.</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
            <form method="GET" action="{{ route('superadmin.financial.index') }}" class="flex flex-wrap gap-4 items-end">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Acara</label>
                    <select name="event_id"
                        class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 min-w-[200px]">
                        <option value="">Semua acara</option>
                        @foreach ($events as $ev)
                            <option value="{{ $ev->id }}" @selected(request('event_id') == $ev->id)>{{ $ev->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Dari</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                        class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Sampai</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                        class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="flex-1 min-w-[160px]">
                    <label class="block text-xs font-medium text-slate-600 mb-1">Cari tenant</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama tenant..."
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                </div>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">Terapkan</button>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white rounded-lg border border-slate-200 p-4">
                <p class="text-xs font-medium text-slate-500 uppercase">Total pendapatan (filter)</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">Rp {{ number_format($grandTotals['revenue'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-lg border border-slate-200 p-4">
                <p class="text-xs font-medium text-slate-500 uppercase">Total transaksi (filter)</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($grandTotals['transactions'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tenant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Acara</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Transaksi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Pendapatan</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($tenants as $tenant)
                            @php
                                $s = $stats->get($tenant->id);
                                $cnt = $s->transaction_count ?? 0;
                                $rev = $s->total_revenue ?? 0;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $tenant->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $tenant->event?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-slate-800 text-right">{{ number_format($cnt, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-slate-900 text-right">Rp {{ number_format($rev, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('superadmin.financial.tenant', ['tenant' => $tenant, 'month' => \Carbon\Carbon::parse($dateTo)->format('Y-m')]) }}"
                                        class="text-sm text-indigo-600 hover:text-indigo-800">Bulanan</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">Tidak ada tenant pada filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($tenants->hasPages())
                <div class="px-6 py-3 border-t border-slate-200">{{ $tenants->links() }}</div>
            @endif
        </div>
    </div>
@endsection
