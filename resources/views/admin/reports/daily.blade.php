@extends('layouts.app')

@section('title', 'Daily Report')

@section('content')
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold">Laporan Harian</h2>
        <div class="flex flex-wrap gap-2 items-center">
            <form class="flex gap-2" method="GET" action="{{ route('admin.reports.daily') }}">
                <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" class="px-4 py-2 border rounded-lg">
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg">Tampilkan</button>
            </form>
            <a href="{{ route('admin.reports.daily.export', ['date' => $date->format('Y-m-d')]) }}"
               class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm">
                Export Excel
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Total Revenue</p>
            <p class="text-xl font-bold">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Transaksi</p>
            <p class="text-xl font-bold">{{ $summary['total_transactions'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Items Sold</p>
            <p class="text-xl font-bold">{{ $summary['total_items'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Rata-rata/Transaksi</p>
            <p class="text-xl font-bold">Rp {{ number_format($summary['avg_transaction'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Payment Breakdown --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <h3 class="font-semibold mb-3">Berdasarkan Metode Pembayaran</h3>
        <div class="flex gap-6">
            @foreach($summary['by_payment'] as $method => $data)
                <div>
                    <span class="text-sm text-gray-500">{{ ucfirst($method) }}:</span>
                    <span class="font-medium">{{ $data['count'] }} transaksi</span>
                    <span class="text-gray-400">(Rp {{ number_format($data['total'], 0, ',', '.') }})</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Transactions Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cashier</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($transactions as $trx)
                    <tr>
                        <td class="px-4 py-3 text-sm text-blue-600">{{ $trx->invoice_number }}</td>
                        <td class="px-4 py-3 text-sm">{{ $trx->created_at->format('H:i') }}</td>
                        <td class="px-4 py-3 text-sm">{{ $trx->items->sum('quantity') }}</td>
                        <td class="px-4 py-3 text-sm font-medium">{{ $trx->formatted_grand_total }}</td>
                        <td class="px-4 py-3 text-sm">{{ ucfirst($trx->payment_method) }}</td>
                        <td class="px-4 py-3 text-sm">{{ $trx->user->name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">Tidak ada transaksi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
