@extends('layouts.app')
@section('title', 'Manajemen Stok')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">📦 Manajemen Stok</h1>
        <p class="text-sm text-slate-500 mt-1">Riwayat pergerakan stok semua produk</p>
    </div>
    <a href="{{ route('admin.stock.create') }}"
       class="inline-flex items-center gap-2 px-5 py-2.5 bg-indomie-green text-white text-sm font-semibold rounded-xl hover:bg-indomie-green/90 transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Stok
    </a>
</div>

{{-- Filter --}}
<div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 mb-6">
    <form method="GET" action="{{ route('admin.stock.index') }}" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Produk</label>
            <select name="product_id" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indomie-green focus:border-indomie-green">
                <option value="">Semua Produk</option>
                @foreach($products as $product)
                    <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                        {{ $product->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Tipe</label>
            <select name="type" class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indomie-green focus:border-indomie-green">
                <option value="">Semua Tipe</option>
                <option value="in"   {{ request('type') === 'in'   ? 'selected' : '' }}>Stok Masuk</option>
                <option value="out"  {{ request('type') === 'out'  ? 'selected' : '' }}>Penjualan</option>
                <option value="void" {{ request('type') === 'void' ? 'selected' : '' }}>Void Transaksi</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Tanggal</label>
            <input type="date" name="date" value="{{ request('date') }}"
                   class="px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indomie-green focus:border-indomie-green">
        </div>
        <button type="submit"
                class="px-4 py-2 bg-slate-700 text-white text-sm font-medium rounded-lg hover:bg-slate-800 transition">
            Filter
        </button>
        @if(request()->hasAny(['product_id','type','date']))
            <a href="{{ route('admin.stock.index') }}"
               class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-medium rounded-lg hover:bg-slate-200 transition">
                Reset
            </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    @if($movements->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Waktu</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Produk</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok Sebelum</th>
                        <th class="px-5 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider">Stok Sesudah</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Referensi</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Oleh</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($movements as $movement)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3 text-sm text-slate-500 whitespace-nowrap">
                                {{ $movement->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-5 py-3">
                                <div class="text-sm font-medium text-slate-800">{{ $movement->product->name }}</div>
                                <div class="text-xs text-slate-400">SKU: {{ $movement->product->sku }}</div>
                            </td>
                            <td class="px-5 py-3">
                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $movement->type_badge_class }}">
                                    {{ $movement->type_label }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <span class="font-bold text-sm {{ $movement->type === 'in' || $movement->type === 'void' ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $movement->type === 'out' ? '-' : '+' }}{{ $movement->quantity }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-center text-sm text-slate-600">{{ $movement->stock_before }}</td>
                            <td class="px-5 py-3 text-center">
                                <span class="font-semibold text-sm text-slate-800">{{ $movement->stock_after }}</span>
                            </td>
                            <td class="px-5 py-3 text-sm text-slate-500 font-mono">{{ $movement->reference ?? '—' }}</td>
                            <td class="px-5 py-3 text-sm text-slate-600">{{ $movement->user->name }}</td>
                            <td class="px-5 py-3 text-sm text-slate-500 max-w-[200px] truncate">
                                {{ $movement->notes ?? '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($movements->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">
                {{ $movements->links() }}
            </div>
        @endif
    @else
        <div class="py-16 text-center">
            <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <p class="mt-4 text-slate-400 font-medium">Belum ada pergerakan stok</p>
            <a href="{{ route('admin.stock.create') }}" class="mt-3 inline-block text-sm text-indomie-green font-semibold hover:underline">
                Tambah stok pertama →
            </a>
        </div>
    @endif
</div>
@endsection
