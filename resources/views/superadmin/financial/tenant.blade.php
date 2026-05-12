@extends('layouts.app')

@section('title', 'Laporan: ' . $tenant->name)

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('superadmin.financial.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800 mb-2 inline-block">← Kembali ke ringkasan</a>
                <h1 class="text-2xl font-bold text-slate-800">{{ $tenant->name }}</h1>
                <p class="text-sm text-slate-600 mt-1">
                    Acara: {{ $tenant->event?->name ?? '—' }} · Bulan {{ $month->translatedFormat('F Y') }}
                </p>
            </div>
            <form method="GET" action="{{ route('superadmin.financial.tenant', $tenant) }}" class="flex items-center gap-2">
                <label class="text-sm text-slate-600">Periode</label>
                <input type="month" name="month" value="{{ $monthInput }}"
                    class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500">
                <button type="submit" class="px-3 py-2 bg-slate-100 text-slate-800 text-sm rounded-lg hover:bg-slate-200">Tampilkan</button>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white rounded-lg border border-slate-200 p-4">
                <p class="text-xs font-medium text-slate-500 uppercase">Pendapatan bulan ini</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">Rp {{ number_format($summary->total_revenue ?? 0, 0, ',', '.') }}</p>
            </div>
            <div class="bg-white rounded-lg border border-slate-200 p-4">
                <p class="text-xs font-medium text-slate-500 uppercase">Jumlah transaksi</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ number_format($summary->transaction_count ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>

        @if ($byPayment->isNotEmpty())
            <div class="bg-white rounded-lg border border-slate-200 p-4">
                <h2 class="text-sm font-semibold text-slate-800 mb-3">Metode pembayaran</h2>
                <div class="space-y-2">
                    @foreach ($byPayment as $row)
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600 capitalize">{{ $row->payment_method }}</span>
                            <span class="text-slate-900">{{ $row->cnt }} trx · Rp {{ number_format($row->total, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        @if ($daily->isNotEmpty())
            <div class="bg-white rounded-lg border border-slate-200 p-4">
                <h2 class="text-sm font-semibold text-slate-800 mb-3">Per hari</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                            <tr class="text-left text-slate-500 border-b">
                                <th class="py-2 pr-4">Tanggal</th>
                                <th class="py-2 pr-4">Transaksi</th>
                                <th class="py-2">Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($daily as $d)
                                <tr class="border-b border-slate-100">
                                    <td class="py-2 pr-4">{{ \Carbon\Carbon::parse($d->day)->format('d M Y') }}</td>
                                    <td class="py-2 pr-4">{{ $d->cnt }}</td>
                                    <td class="py-2">Rp {{ number_format($d->revenue, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-200">
                <h2 class="text-sm font-semibold text-slate-800">Daftar transaksi</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Invoice</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Waktu</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-slate-500 uppercase">Kasir</th>
                            <th class="px-4 py-2 text-right text-xs font-medium text-slate-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($transactions as $tx)
                            <tr>
                                <td class="px-4 py-2 font-mono text-xs">{{ $tx->invoice_number }}</td>
                                <td class="px-4 py-2 text-slate-600">{{ $tx->created_at->format('d M Y H:i') }}</td>
                                <td class="px-4 py-2">{{ $tx->user?->name ?? '—' }}</td>
                                <td class="px-4 py-2 text-right font-medium">Rp {{ number_format($tx->grand_total, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-slate-500">Belum ada transaksi di bulan ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($transactions->hasPages())
                <div class="px-4 py-3 border-t border-slate-200">{{ $transactions->links() }}</div>
            @endif
        </div>
    </div>
@endsection
