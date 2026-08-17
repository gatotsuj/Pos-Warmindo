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
                    @if($dodGrowth > 0)
                        <p class="text-[11px] font-bold text-green-500 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                            Naik {{ number_format($dodGrowth, 1) }}% dari kemarin
                        </p>
                    @elseif($dodGrowth < 0)
                        <p class="text-[11px] font-bold text-red-500 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                            Turun {{ number_format(abs($dodGrowth), 1) }}% dari kemarin
                        </p>
                    @else
                        <p class="text-[11px] font-bold text-slate-400 mt-1">Sama dengan kemarin</p>
                    @endif
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

        <div class="group relative bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-xl hover:shadow-orange-500/10 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-gradient-to-br from-orange-500/10 to-transparent rounded-full group-hover:scale-150 transition-transform duration-500 ease-out"></div>
            <div class="flex items-center relative z-10">
                <div class="p-3.5 rounded-2xl bg-orange-50 text-orange-600 border border-orange-100 shadow-inner">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-5">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Void / Batal (Hari Ini)</p>
                    <p class="text-xl font-black text-slate-800 mt-1">{{ $voidAnalytics['count'] }} <span class="text-[10px] font-medium text-slate-500">trx</span></p>
                    <p class="text-[11px] font-bold text-red-500 mt-1">Rp {{ number_format($voidAnalytics['amount'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 1 --}}
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

        {{-- Peak Hours Chart --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-slate-800">Analisis Jam Sibuk</h3>
                    <p class="text-sm text-slate-500 mt-1">Distribusi transaksi berdasarkan jam (Hari ini)</p>
                </div>
            </div>
            <div class="relative h-64 w-full">
                <canvas id="peakHoursChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Sales by Category --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div>
                <h3 class="text-lg font-black text-slate-800">Penjualan per Kategori</h3>
                <p class="text-sm text-slate-500 mt-1">Distribusi pendapatan hari ini</p>
            </div>
            <div class="mt-6 flex justify-center items-center h-64 relative">
                @if(count($salesByCategory) > 0)
                    <canvas id="categoryChart"></canvas>
                @else
                    <div class="text-slate-400 font-medium">Belum ada transaksi hari ini</div>
                @endif
            </div>
        </div>

        {{-- Payment Methods Chart --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div>
                <h3 class="text-lg font-black text-slate-800">Metode Pembayaran</h3>
                <p class="text-sm text-slate-500 mt-1">Distribusi pembayaran hari ini</p>
            </div>
            <div class="mt-6 flex justify-center items-center h-64 relative">
                @if(count($paymentBreakdown) > 0)
                    <canvas id="paymentChart"></canvas>
                @else
                    <div class="text-slate-400 font-medium">Belum ada transaksi hari ini</div>
                @endif
            </div>
        </div>
    </div>

    {{-- Bottom Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-8">
        {{-- Top Products (Terlaris) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                <span>Produk Terlaris</span>
            </h3>
            @if(count($topProducts) > 0)
                <div class="space-y-4">
                    @foreach($topProducts as $index => $product)
                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-4">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indomie-yellow to-yellow-600 text-white text-xs flex items-center justify-center font-black shadow-md group-hover:scale-110 transition-transform">
                                    {{ $index + 1 }}
                                </div>
                                <span class="text-sm font-bold text-slate-700 truncate max-w-[120px] group-hover:text-indomie-red transition-colors">{{ $product['product_name'] }}</span>
                            </div>
                            <span class="text-sm font-bold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg">{{ $product['total_qty'] }} <span class="text-xs font-medium text-slate-400">terjual</span></span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-400 text-center py-8 font-medium">Belum ada data</p>
            @endif
        </div>

        {{-- Top Profit Products (Paling Menguntungkan) --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Produk Paling Untung</span>
            </h3>
            @if(count($topProfitProducts) > 0)
                <div class="space-y-4">
                    @foreach($topProfitProducts as $index => $product)
                        <div class="flex items-center justify-between group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 text-white text-xs flex items-center justify-center font-black shadow-md">
                                    {{ $index + 1 }}
                                </div>
                                <span class="text-sm font-bold text-slate-700 truncate max-w-[110px]">{{ $product['product_name'] }}</span>
                            </div>
                            <div class="text-right">
                                <p class="text-xs font-black text-emerald-600">Rp {{ number_format($product['total_profit'], 0, ',', '.') }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">{{ $product['total_qty'] }} terjual</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-400 text-center py-8 font-medium">Belum ada data profit</p>
            @endif
        </div>

        {{-- Cashier Performance --}}
        @if(auth()->user()->isAdmin())
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                <span>Performa Kasir</span>
            </h3>
            @if(count($cashierPerformance) > 0)
                <div class="space-y-4">
                    @foreach($cashierPerformance as $cashier)
                        <div class="flex flex-col p-3 rounded-xl border border-slate-100 bg-slate-50/50 hover:border-indomie-red/30 transition-colors">
                            <span class="text-sm font-bold text-slate-700">{{ $cashier->user->name }}</span>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs font-bold text-slate-500">{{ $cashier->total_trx }} transaksi</span>
                                <span class="text-sm font-black text-indomie-green">Rp {{ number_format($cashier->total_revenue, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-400 text-center py-8 font-medium">Belum ada data</p>
            @endif
        </div>
        @endif

        {{-- Recent Transactions --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 {{ !auth()->user()->isAdmin() ? 'col-span-2' : '' }}">
            <h3 class="text-lg font-black text-slate-800 mb-6 flex items-center gap-2">
                <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                <span>Transaksi Terakhir</span>
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
                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Peringatan Stok</span>
                </h3>
                @if($lowStockProducts->count() > 0)
                    <div class="space-y-4">
                        @foreach($lowStockProducts as $product)
                            <div class="flex items-center justify-between p-3 rounded-xl border {{ $product->stock == 0 ? 'border-red-100 bg-red-50/50' : 'border-yellow-100 bg-yellow-50/50' }}">
                                <span class="text-sm font-bold text-slate-700 truncate max-w-[120px]">{{ $product->name }}</span>
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg {{ $product->stock == 0 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(() => {
    // Shared formatter
    const currencyFormatter = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);

    // 1. Weekly Sales Chart
    const weeklyCtx = document.getElementById('weeklySalesChart');
    if (weeklyCtx) {
        new Chart(weeklyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(collect($weeklySales)->pluck('date')) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode(collect($weeklySales)->pluck('total')) !!},
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
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: (ctx) => currencyFormatter(ctx.raw) } }
                },
                scales: { y: { beginAtZero: true, ticks: { callback: currencyFormatter } } }
            }
        });
    }

    // 2. Peak Hours Chart
    const peakCtx = document.getElementById('peakHoursChart');
    if (peakCtx) {
        new Chart(peakCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($peakHours)) !!},
                datasets: [{
                    label: 'Transaksi',
                    data: {!! json_encode(array_values($peakHours)) !!},
                    backgroundColor: '#FFD100',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    }

    // 3. Category Chart
    const catCtx = document.getElementById('categoryChart');
    if (catCtx) {
        new Chart(catCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($salesByCategory->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($salesByCategory->pluck('total_revenue')) !!},
                    backgroundColor: ['#E11B22', '#00A651', '#FFD100', '#3B82F6', '#8B5CF6'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '70%',
                plugins: {
                    legend: { position: 'right' },
                    tooltip: { callbacks: { label: (ctx) => ' ' + currencyFormatter(ctx.raw) } }
                }
            }
        });
    }

    // 4. Payment Chart
    const payCtx = document.getElementById('paymentChart');
    if (payCtx) {
        new Chart(payCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(collect($paymentBreakdown)->pluck('payment_method')->map(fn($v) => ucfirst($v))) !!},
                datasets: [{
                    data: {!! json_encode(collect($paymentBreakdown)->pluck('total')) !!},
                    backgroundColor: ['#3B82F6', '#F59E0B', '#10B981'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '70%',
                plugins: {
                    legend: { position: 'right' },
                    tooltip: { callbacks: { label: (ctx) => ' ' + currencyFormatter(ctx.raw) } }
                }
            }
        });
    }
})();
</script>
@endpush