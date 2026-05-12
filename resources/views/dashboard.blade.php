
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="group relative bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:shadow-indomie-red/10 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-indomie-red/10 to-transparent rounded-full group-hover:scale-150 transition-transform duration-500 ease-out"></div>
            <div class="flex items-center relative z-10">
                <div class="p-3.5 rounded-2xl bg-indomie-red/10 text-indomie-red border border-indomie-red/20 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Revenue Hari Ini</p>
                    <p class="text-2xl font-black text-slate-800 mt-1">Rp {{ number_format($todayStats['revenue'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="group relative bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:shadow-indomie-green/10 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-indomie-green/10 to-transparent rounded-full group-hover:scale-150 transition-transform duration-500 ease-out"></div>
            <div class="flex items-center relative z-10">
                <div class="p-3.5 rounded-2xl bg-indomie-green/10 text-indomie-green border border-indomie-green/20 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Transaksi Hari Ini</p>
                    <p class="text-2xl font-black text-slate-800 mt-1">{{ $todayStats['transactions'] }}</p>
                </div>
            </div>
        </div>

        <div class="group relative bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:shadow-indomie-yellow/10 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-indomie-yellow/20 to-transparent rounded-full group-hover:scale-150 transition-transform duration-500 ease-out"></div>
            <div class="flex items-center relative z-10">
                <div class="p-3.5 rounded-2xl bg-indomie-yellow/20 text-yellow-700 border border-indomie-yellow/30 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Item Terjual</p>
                    <p class="text-2xl font-black text-slate-800 mt-1">{{ $todayStats['items_sold'] }}</p>
                </div>
            </div>
        </div>

        <div class="group relative bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:shadow-indigo-500/10 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-indigo-500/10 to-transparent rounded-full group-hover:scale-150 transition-transform duration-500 ease-out"></div>
            <div class="flex items-center relative z-10">
                <div class="p-3.5 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Products</p>
                    <p class="text-2xl font-black text-slate-800 mt-1">{{ \App\Models\Product::count() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Weekly Sales Chart --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-slate-800">Penjualan 7 Hari</h3>
                    <p class="text-sm text-slate-500 mt-1">Tren revenue mingguan</p>
                </div>
                <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold tracking-wider">
                    {{ now()->subDays(6)->format('d M') }} – {{ now()->format('d M Y') }}
                </span>
            </div>

            <div class="relative h-64 w-full">
                <canvas id="weeklySalesChart"></canvas>
            </div>

            @if(collect($weeklySales)->sum('total') == 0)
                <div class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm font-medium">
                    Belum ada transaksi dalam 7 hari terakhir
                </div>
            @endif
        </div>

        {{-- Payment Methods Chart --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div>
                <h3 class="text-lg font-black text-slate-800">Metode Pembayaran</h3>
                <p class="text-sm text-slate-500 mt-1">Distribusi pembayaran hari ini</p>
            </div>
            <div class="mt-6 flex justify-center items-center h-64">
                @if(count($paymentBreakdown) > 0)
                    <canvas id="paymentChart"></canvas>
                @else
                    <div class="text-slate-400 font-medium">
                        Belum ada transaksi hari ini
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Top Products --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                <span class="text-xl">🏆</span> Produk Terlaris
            </h3>
            @if(count($topProducts) > 0)
                <div class="space-y-4">
                    @foreach($topProducts as $index => $product)
                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indomie-yellow to-yellow-600 text-white text-xs flex items-center justify-center font-black shadow-md group-hover:scale-110 transition-transform">
                                    {{ $index + 1 }}
                                </div>
                                <span class="text-sm font-bold text-slate-700 truncate max-w-[150px] group-hover:text-indomie-red transition-colors">{{ $product['product_name'] }}</span>
                            </div>
                            <span class="text-sm font-bold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg">{{ $product['total_qty'] }} <span class="text-xs font-medium text-slate-400">terjual</span></span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-400 text-center py-8 font-medium">Belum ada data</p>
            @endif
        </div>

        {{-- Recent Transactions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                <span class="text-xl">⚡</span> Transaksi Terakhir
            </h3>
            @if($recentTransactions->count() > 0)
                <div class="space-y-4">
                    @foreach($recentTransactions as $trx)
                        <div class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-indomie-red/30 hover:bg-indomie-red/5 transition-colors group">
                            <div>
                                <p class="font-bold text-slate-800 group-hover:text-indomie-red transition-colors">{{ $trx->invoice_number }}</p>
                                <p class="text-xs font-medium text-slate-400 mt-0.5 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $trx->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <span class="font-black text-slate-700">{{ $trx->formatted_grand_total }}</span>
                        </div>
                    @endforeach
                </div>
                <a href="{{ route('transactions.index') }}" class="mt-6 flex items-center justify-center gap-2 w-full py-2.5 text-sm font-bold text-indomie-red bg-indomie-red/5 hover:bg-indomie-red/10 rounded-xl transition-colors">
                    Lihat Semua
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            @else
                <p class="text-slate-400 text-center py-8 font-medium">Belum ada transaksi</p>
            @endif
        </div>

        {{-- Low Stock Alert (Admin Only) --}}
        @if(auth()->user()->isAdmin())
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                    <span class="text-xl">⚠️</span> Peringatan Stok
                </h3>
                @if($lowStockProducts->count() > 0)
                    <div class="space-y-4">
                        @foreach($lowStockProducts as $product)
                            <div class="flex items-center justify-between p-3 rounded-xl border {{ $product->stock == 0 ? 'border-red-100 bg-red-50/50' : 'border-yellow-100 bg-yellow-50/50' }}">
                                <span class="text-sm font-bold text-slate-700 truncate max-w-[150px]">{{ $product->name }}</span>
                                <span class="px-2.5 py-1 text-xs font-bold rounded-lg {{ $product->stock == 0 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    Sisa {{ $product->stock }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <a href="{{ route('admin.products.index', ['stock' => 'low']) }}" class="mt-6 flex items-center justify-center gap-2 w-full py-2.5 text-sm font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        Kelola Stok
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @else
                    <div class="flex flex-col items-center justify-center py-8 text-center">
                        <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="font-bold text-slate-700">Stok Aman</p>
                        <p class="text-xs text-slate-500 mt-1">Tidak ada produk yang menipis</p>
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
    const ctx = document.getElementById('weeklySalesChart');
    if (!ctx) return;

    const labels = {!! json_encode(collect($weeklySales)->pluck('date')) !!};
    const data = {!! json_encode(collect($weeklySales)->pluck('total')) !!};

    new Chart(ctx, {
        type: 'line',
        data: {
            labels,
            datasets: [{
                label: 'Revenue',
                data,
                borderWidth: 2,
                tension: 0.35,
                fill: true,
                borderColor: '#E11B22',
                backgroundColor: 'rgba(225, 27, 34, 0.12)',
                pointRadius: 4,
                pointBackgroundColor: '#E11B22'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) =>
                            'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw)
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: value =>
                            'Rp ' + new Intl.NumberFormat('id-ID').format(value)
                    }
                }
            }
        }
    });
})();
</script>
@endpush