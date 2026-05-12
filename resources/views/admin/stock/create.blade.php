@extends('layouts.app')
@section('title', 'Tambah Stok')

@section('content')
<div class="max-w-2xl">
    {{-- Header --}}
    <div class="mb-6 flex items-center gap-3">
        <a href="{{ route('admin.stock.index') }}"
           class="p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Tambah Stok Masuk</h1>
            <p class="text-sm text-slate-500 mt-0.5">Catat penambahan stok produk</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-6">
        <form method="POST" action="{{ route('admin.stock.store') }}" x-data="stockForm()">
            @csrf

            {{-- Product Select --}}
            <div class="mb-5">
                <label for="product_id" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Produk <span class="text-red-500">*</span>
                </label>
                <select id="product_id" name="product_id" required
                        @change="updateStock($event.target)"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indomie-green focus:border-indomie-green @error('product_id') border-red-400 @enderror">
                    <option value="">— Pilih Produk —</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}"
                                data-stock="{{ $product->stock }}"
                                data-sku="{{ $product->sku }}"
                                {{ (old('product_id', $selected?->id) == $product->id) ? 'selected' : '' }}>
                            {{ $product->name }} (Stok: {{ $product->stock }})
                        </option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Current Stock Info --}}
            <div class="mb-5 p-4 bg-slate-50 rounded-lg border border-slate-200" x-show="selectedProduct">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-slate-500">SKU:</span>
                    <span class="font-mono font-medium text-slate-700" x-text="selectedProduct.sku"></span>
                </div>
                <div class="flex items-center justify-between text-sm mt-2">
                    <span class="text-slate-500">Stok Saat Ini:</span>
                    <span class="font-bold text-lg" :class="selectedProduct.stock <= 10 ? 'text-red-600' : 'text-slate-800'" x-text="selectedProduct.stock + ' unit'"></span>
                </div>
                <div class="flex items-center justify-between text-sm mt-2" x-show="quantity > 0">
                    <span class="text-slate-500">Stok Setelah Tambah:</span>
                    <span class="font-bold text-lg text-indomie-green" x-text="(selectedProduct.stock + quantity) + ' unit'"></span>
                </div>
            </div>

            {{-- Quantity --}}
            <div class="mb-5">
                <label for="quantity" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Jumlah Tambah <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-3">
                    <button type="button" @click="quantity = Math.max(1, quantity - 1)"
                            class="w-10 h-10 rounded-lg border border-slate-300 flex items-center justify-center text-slate-700 hover:bg-slate-100 transition font-bold text-lg">
                        −
                    </button>
                    <input type="number" id="quantity" name="quantity" min="1"
                           x-model.number="quantity"
                           value="{{ old('quantity', 1) }}"
                           class="w-28 text-center px-4 py-2.5 border border-slate-300 rounded-lg text-sm font-bold text-lg focus:ring-2 focus:ring-indomie-green focus:border-indomie-green @error('quantity') border-red-400 @enderror">
                    <button type="button" @click="quantity++"
                            class="w-10 h-10 rounded-lg border border-slate-300 flex items-center justify-center text-slate-700 hover:bg-slate-100 transition font-bold text-lg">
                        +
                    </button>

                    {{-- Quick amounts --}}
                    <div class="flex gap-2 ml-2">
                        @foreach([10, 24, 50, 100] as $qty)
                            <button type="button" @click="quantity = {{ $qty }}"
                                    class="px-3 py-2 text-xs font-semibold rounded-lg border border-slate-200 hover:bg-indomie-green/10 hover:border-indomie-green hover:text-indomie-green transition">
                                +{{ $qty }}
                            </button>
                        @endforeach
                    </div>
                </div>
                @error('quantity')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Notes --}}
            <div class="mb-6">
                <label for="notes" class="block text-sm font-semibold text-slate-700 mb-1.5">
                    Keterangan
                </label>
                <input type="text" id="notes" name="notes"
                       value="{{ old('notes') }}"
                       placeholder="Contoh: Pembelian dari supplier X, restock mingguan..."
                       class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-indomie-green focus:border-indomie-green @error('notes') border-red-400 @enderror">
                @error('notes')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="{{ route('admin.stock.index') }}"
                   class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit"
                        class="px-5 py-2.5 text-sm font-semibold text-white bg-indomie-green rounded-lg hover:bg-indomie-green/90 transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Stok Masuk
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function stockForm() {
    return {
        quantity: {{ old('quantity', 1) }},
        selectedProduct: null,

        updateStock(select) {
            const opt = select.options[select.selectedIndex];
            if (opt.value) {
                this.selectedProduct = {
                    stock: parseInt(opt.dataset.stock),
                    sku: opt.dataset.sku,
                };
            } else {
                this.selectedProduct = null;
            }
        },

        init() {
            // Pre-select if there is a selected product
            const sel = document.getElementById('product_id');
            if (sel && sel.value) {
                this.updateStock(sel);
            }
        },
    };
}
</script>
@endpush
@endsection
