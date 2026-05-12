@extends('layouts.app')

@section('title', 'POS / Cashier')

@section('content')
<div x-data="posApp()" x-init="init()" class="h-[calc(100vh-10rem)]">
    <div class="flex gap-6 h-full">

        {{-- LEFT: Products Section --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Search & Filter --}}
            <div class="bg-white rounded-lg shadow p-4 mb-4">
                <div class="flex gap-4">
                    <div class="flex-1">
                        <input
                            type="text"
                            x-model="searchQuery"
                            @input.debounce.300ms="filterProducts()"
                            placeholder="Cari produk..."
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                    <select
                        x-model="selectedCategory"
                        @change="filterProducts()"
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Products Grid --}}
            <div class="flex-1 overflow-y-auto bg-white rounded-lg shadow p-4">
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach($products as $product)
                        <div
                            class="product-card cursor-pointer bg-white border border-slate-100 rounded-2xl p-3 shadow-sm hover:shadow-xl hover:shadow-blue-500/10 hover:-translate-y-1 hover:border-blue-200 transition-all duration-300 group"
                            data-category="{{ $product->category_id }}"
                            data-name="{{ strtolower($product->name) }}"
                            data-sku="{{ strtolower($product->sku) }}"
                            @click="addToCart({{ $product->id }})"
                        >
                            <div class="aspect-square bg-slate-50 rounded-xl mb-3 overflow-hidden relative">
                                <img
                                    src="{{ $product->image_url }}"
                                    alt="{{ $product->name }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                    loading="lazy"
                                >
                                @if($product->stock <= 10)
                                    <span class="absolute top-2 right-2 px-2 py-0.5 bg-red-500 text-white text-[10px] font-bold rounded-lg shadow-sm">
                                        Sisa {{ $product->stock }}
                                    </span>
                                @endif
                            </div>
                            <h4 class="font-bold text-slate-800 text-sm line-clamp-2 mb-1 group-hover:text-blue-600 transition-colors">{{ $product->name }}</h4>
                            <div class="flex items-center justify-between mt-2">
                                <p class="text-blue-600 font-black text-sm">{{ $product->formatted_price }}</p>
                                <span class="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Empty State --}}
                @if($products->isEmpty())
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="mt-4 text-gray-500">Tidak ada produk tersedia</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT: Cart Section --}}
        <div class="w-96 flex flex-col bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
            {{-- Cart Header --}}
            <div class="p-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-black text-slate-800 flex items-center gap-1.5">
                        <span class="text-lg">🛒</span> Order Saat Ini
                    </h3>
                    <span class="px-2 py-0.5 bg-indomie-red/10 text-indomie-red text-[10px] font-bold rounded-lg" x-text="totals.items_count + ' item'"></span>
                </div>
                <button
                    @click="clearCart()"
                    :disabled="Object.keys(cart).length === 0"
                    class="text-slate-400 hover:text-indomie-red transition-colors disabled:opacity-50"
                    title="Kosongkan Cart"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto p-4">
                <template x-if="Object.keys(cart).length === 0">
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <p class="mt-2 text-gray-500">Cart kosong</p>
                        <p class="text-sm text-gray-400">Klik produk untuk menambahkan</p>
                    </div>
                </template>

                <template x-for="item in Object.values(cart)" :key="item.id">
                    <div class="flex items-center gap-3 py-3 border-b border-gray-100">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-medium text-gray-800 text-sm truncate" x-text="item.name"></h4>
                            <p class="text-sm text-gray-500" x-text="formatRupiah(item.price) + ' × ' + item.quantity"></p>
                        </div>

                        {{-- Quantity Controls --}}
                        <div class="flex items-center gap-1">
                            <button
                                @click="updateQuantity(item.id, item.quantity - 1)"
                                class="w-7 h-7 flex items-center justify-center bg-gray-200 rounded hover:bg-gray-300 text-sm font-bold"
                            >−</button>
                            <span class="w-8 text-center text-sm font-medium" x-text="item.quantity"></span>
                            <button
                                @click="updateQuantity(item.id, item.quantity + 1)"
                                class="w-7 h-7 flex items-center justify-center bg-gray-200 rounded hover:bg-gray-300 text-sm font-bold"
                            >+</button>
                        </div>

                        {{-- Subtotal & Remove --}}
                        <div class="text-right">
                            <p class="font-bold text-gray-800 text-sm" x-text="formatRupiah(item.subtotal)"></p>
                            <button
                                @click="removeItem(item.id)"
                                class="text-xs text-red-500 hover:text-red-700"
                            >Hapus</button>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Cart Footer: Totals --}}
            <div class="border-t border-slate-100 p-3 bg-slate-50/30">
                <div class="space-y-1.5 mb-3">
                    {{-- Subtotal --}}
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500 font-bold">Subtotal</span>
                        <span class="font-bold text-slate-700" x-text="formatRupiah(totals.subtotal)"></span>
                    </div>

                    {{-- Discount --}}
                    @if(!$receiptSetting || $receiptSetting->discount_enabled)
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-slate-500 font-bold">Diskon</span>
                        <div class="flex items-center gap-1.5">
                            <input
                                type="number"
                                x-model="discountPercent"
                                @input="calculateTotals()"
                                min="0"
                                max="100"
                                placeholder="0"
                                class="w-12 px-1.5 py-0.5 text-right border border-slate-200 rounded text-xs font-bold focus:ring-indomie-red focus:border-indomie-red"
                            >
                            <span class="text-slate-400 font-bold">%</span>
                            <span class="text-indomie-red font-bold w-20 text-right" x-text="'-' + formatRupiah(totals.discount_amount)"></span>
                        </div>
                    </div>
                    @endif

                    {{-- Tax --}}
                    @if(!$receiptSetting || $receiptSetting->tax_enabled)
                    <div class="flex justify-between text-xs">
                        <span class="text-slate-500 font-bold">Pajak ({{ $receiptSetting ? $receiptSetting->tax_percent : 11 }}%)</span>
                        <span class="font-bold text-slate-700" x-text="formatRupiah(totals.tax_amount)"></span>
                    </div>
                    @endif
                </div>

                {{-- Grand Total & Action --}}
                <div class="border-t border-slate-200 pt-2.5">
                    <div class="flex justify-between items-end mb-3">
                        <span class="text-xs font-black text-slate-400 uppercase tracking-wider">Total Tagihan</span>
                        <span class="text-xl font-black text-indomie-red" x-text="formatRupiah(totals.grand_total)"></span>
                    </div>

                    <button
                        @click="openPaymentModal()"
                        :disabled="Object.keys(cart).length === 0"
                        class="w-full px-4 py-3 bg-gradient-to-r from-green-500 to-green-600 shadow-lg shadow-green-500/20 text-white text-sm font-black tracking-wide rounded-xl hover:from-green-600 hover:to-green-700 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-y-0 flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        PROSES PEMBAYARAN
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Payment Modal --}}
    <div
        x-show="showPaymentModal"
        x-transition
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        style="display: none;"
    >
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm" @click="showPaymentModal = false"></div>

        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-10 overflow-hidden">
            {{-- Modal Header --}}
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-xl font-black text-slate-800">Selesaikan Pembayaran</h3>
                <p class="text-sm text-slate-500 mt-1">Total Tagihan: <span class="font-black text-indomie-red text-lg" x-text="formatRupiah(totals.grand_total)"></span></p>
            </div>

            {{-- Modal Body --}}
            <div class="p-6 space-y-5">
                {{-- Payment Method --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-3">Pilih Metode</label>
                    <div class="grid grid-cols-3 gap-3">
                        <button
                            @click="paymentMethod = 'cash'"
                            :class="paymentMethod === 'cash' ? 'ring-2 ring-indomie-red bg-indomie-red/5' : 'bg-slate-50 hover:bg-slate-100 border border-slate-100'"
                            class="p-4 rounded-xl text-center transition-all duration-200"
                        >
                            <span class="text-3xl">💵</span>
                            <p class="text-sm font-bold mt-2 text-slate-700">Cash</p>
                        </button>
                        <button
                            @click="paymentMethod = 'card'; paidAmount = totals.grand_total"
                            :class="paymentMethod === 'card' ? 'ring-2 ring-indomie-red bg-indomie-red/5' : 'bg-slate-50 hover:bg-slate-100 border border-slate-100'"
                            class="p-4 rounded-xl text-center transition-all duration-200"
                        >
                            <span class="text-3xl">💳</span>
                            <p class="text-sm font-bold mt-2 text-slate-700">Card</p>
                        </button>
                        <button
                            @click="paymentMethod = 'qris'; paidAmount = totals.grand_total"
                            :class="paymentMethod === 'qris' ? 'ring-2 ring-indomie-red bg-indomie-red/5' : 'bg-slate-50 hover:bg-slate-100 border border-slate-100'"
                            class="p-4 rounded-xl text-center transition-all duration-200"
                        >
                            <span class="text-3xl">📱</span>
                            <p class="text-sm font-bold mt-2 text-slate-700">QRIS</p>
                        </button>
                    </div>
                </div>

                {{-- Paid Amount (only for cash) --}}
                <div x-show="paymentMethod === 'cash'">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jumlah Dibayar</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                        <input
                            type="number"
                            x-model.number="paidAmount"
                            class="w-full pl-12 pr-4 py-3 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                            placeholder="0"
                        >
                    </div>

                    {{-- Quick Amount Buttons --}}
                    <div class="flex flex-wrap gap-2 mt-2">
                        <button @click="paidAmount = totals.grand_total" class="px-3 py-1 bg-gray-200 rounded text-sm hover:bg-gray-300">Uang Pas</button>
                        <button @click="paidAmount = Math.ceil(totals.grand_total / 10000) * 10000" class="px-3 py-1 bg-gray-200 rounded text-sm hover:bg-gray-300" x-text="formatRupiah(Math.ceil(totals.grand_total / 10000) * 10000)"></button>
                        <button @click="paidAmount = Math.ceil(totals.grand_total / 50000) * 50000" class="px-3 py-1 bg-gray-200 rounded text-sm hover:bg-gray-300" x-text="formatRupiah(Math.ceil(totals.grand_total / 50000) * 50000)"></button>
                        <button @click="paidAmount = Math.ceil(totals.grand_total / 100000) * 100000" class="px-3 py-1 bg-gray-200 rounded text-sm hover:bg-gray-300" x-text="formatRupiah(Math.ceil(totals.grand_total / 100000) * 100000)"></button>
                    </div>
                </div>

                {{-- Change --}}
                <div x-show="paymentMethod === 'cash' && paidAmount >= totals.grand_total" class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex justify-between items-center">
                        <span class="text-green-800 font-medium">Kembalian</span>
                        <span class="text-2xl font-bold text-green-600" x-text="formatRupiah(paidAmount - totals.grand_total)"></span>
                    </div>
                </div>

                {{-- Insufficient --}}
                <div x-show="paymentMethod === 'cash' && paidAmount > 0 && paidAmount < totals.grand_total" class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <p class="text-red-600 text-sm">Jumlah pembayaran kurang <span class="font-bold" x-text="formatRupiah(totals.grand_total - paidAmount)"></span></p>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (opsional)</label>
                    <input
                        type="text"
                        x-model="paymentNotes"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                        placeholder="Catatan tambahan..."
                    >
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="p-6 border-t border-slate-100 flex gap-3 bg-slate-50">
                <button
                    @click="showPaymentModal = false"
                    class="flex-1 px-4 py-3 text-slate-600 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 hover:text-slate-800 transition-all duration-200 font-bold"
                >
                    Kembali
                </button>
                <button
                    @click="processPayment()"
                    :disabled="processing || (paymentMethod === 'cash' && paidAmount < totals.grand_total)"
                    class="flex-1 px-4 py-3 bg-indomie-red text-white font-black tracking-wide rounded-xl shadow-lg shadow-indomie-red/30 hover:bg-red-700 hover:shadow-xl hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2"
                >
                    <span x-show="!processing">BAYAR SEKARANG</span>
                    <span x-show="processing" class="flex items-center justify-center">
                        <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
function posApp() {
    return {
        // State
        cart: @json($cart),
        taxPercent: {{ $receiptSetting ? $receiptSetting->tax_percent : 11 }},
        taxEnabled: {{ ($receiptSetting && !$receiptSetting->tax_enabled) ? 'false' : 'true' }},
        discountEnabled: {{ ($receiptSetting && !$receiptSetting->discount_enabled) ? 'false' : 'true' }},
        totals: {
            subtotal: 0,
            discount_percent: 0,
            discount_amount: 0,
            tax_percent: 11,
            tax_amount: 0,
            grand_total: 0,
            items_count: 0,
        },
        discountPercent: 0,
        searchQuery: '',
        selectedCategory: '',

        // Payment Modal
        showPaymentModal: false,
        paymentMethod: 'cash',
        paidAmount: 0,
        paymentNotes: '',
        processing: false,

        // Toast state removed

        // Initialize
        init() {
            this.calculateTotals();
        },

        // Format currency
        formatRupiah(amount) {
            const num = parseFloat(amount);
            if (isNaN(num)) return 'Rp 0';
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(num);
        },

        // Show toast (menggunakan Toast SweetAlert2 agar tidak terlalu besar)
        showToast(message, type = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 1500,
                timerProgressBar: true,
                customClass: { popup: 'rounded-xl shadow-md border border-slate-100' }
            });

            Toast.fire({
                icon: type,
                title: message
            });
        },

        // Calculate totals
        calculateTotals() {
            const subtotal = Object.values(this.cart).reduce((sum, item) => sum + item.subtotal, 0);
            const rawDiscount = parseFloat(this.discountPercent);
            const discountPercent = isNaN(rawDiscount) || rawDiscount < 0 ? 0 : Math.min(rawDiscount, 100);
            this.discountPercent = discountPercent;
            const discountAmount = this.discountEnabled ? subtotal * (discountPercent / 100) : 0;
            const afterDiscount = subtotal - discountAmount;
            const taxAmount = this.taxEnabled ? afterDiscount * (this.taxPercent / 100) : 0;
            const grandTotal = afterDiscount + taxAmount;
            const itemsCount = Object.values(this.cart).reduce((sum, item) => sum + item.quantity, 0);

            this.totals = {
                subtotal: Math.round(subtotal),
                discount_percent: discountPercent,
                discount_amount: Math.round(discountAmount),
                tax_percent: this.taxEnabled ? this.taxPercent : 0,
                tax_amount: Math.round(taxAmount),
                grand_total: Math.round(grandTotal),
                items_count: itemsCount,
            };
        },

        // Filter products (client-side)
        filterProducts() {
            const search = this.searchQuery.toLowerCase();
            const category = this.selectedCategory;

            document.querySelectorAll('.product-card').forEach(card => {
                const name = card.dataset.name;
                const sku = card.dataset.sku;
                const cat = card.dataset.category;

                const matchSearch = !search || name.includes(search) || sku.includes(search);
                const matchCategory = !category || cat === category;

                card.style.display = matchSearch && matchCategory ? 'block' : 'none';
            });
        },

        // Add to cart
        async addToCart(productId) {
            try {
                const response = await fetch('{{ route("pos.cart.add") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ product_id: productId }),
                });

                const data = await response.json();

                if (data.success) {
                    this.cart = data.cart;
                    this.totals = data.totals;
                    this.showToast(data.message, 'success');
                } else {
                    this.showToast(data.message, 'error');
                }
            } catch (error) {
                this.showToast('Terjadi kesalahan', 'error');
            }
        },

        // Update quantity
        async updateQuantity(productId, quantity) {
            try {
                const response = await fetch('{{ route("pos.cart.update") }}', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ product_id: productId, quantity }),
                });

                const data = await response.json();

                if (data.success) {
                    this.cart = data.cart;
                    this.calculateTotals();
                } else {
                    this.showToast(data.message, 'error');
                }
            } catch (error) {
                this.showToast('Terjadi kesalahan', 'error');
            }
        },

        // Remove item
        async removeItem(productId) {
            try {
                const response = await fetch('{{ route("pos.cart.remove") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ product_id: productId }),
                });

                const data = await response.json();

                if (data.success) {
                    this.cart = data.cart;
                    this.calculateTotals();
                    this.showToast(data.message, 'success');
                }
            } catch (error) {
                this.showToast('Terjadi kesalahan', 'error');
            }
        },

        // Clear cart
        async clearCart() {
            const result = await Swal.fire({
                title: 'Kosongkan Cart?',
                text: 'Semua item di cart akan dihapus.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#E11B22',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Ya, Kosongkan!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'rounded-2xl' }
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch('{{ route("pos.cart.clear") }}', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    this.cart = {};
                    this.discountPercent = 0;
                    this.calculateTotals();
                    this.showToast(data.message, 'success');
                }
            } catch (error) {
                this.showToast('Terjadi kesalahan', 'error');
            }
        },

        // Open payment modal
        openPaymentModal() {
            this.showPaymentModal = true;
            this.paymentMethod = 'cash';
            this.paidAmount = 0;
            this.paymentNotes = '';
        },

        // Process payment
        async processPayment() {
            if (this.processing) return;
            this.processing = true;

            try {
                const response = await fetch('{{ route("pos.checkout") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        payment_method: this.paymentMethod,
                        paid_amount: this.paidAmount,
                        discount_percent: this.discountPercent,
                        notes: this.paymentNotes,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    this.showPaymentModal = false;
                    this.cart = {};
                    this.discountPercent = 0;
                    this.calculateTotals();
                    this.showToast('Transaksi berhasil! Invoice: ' + data.transaction.invoice_number, 'success');

                    // Open receipt in new tab
                    if (data.receipt_url) {
                        window.open(data.receipt_url, '_blank');
                    }
                } else {
                    this.showToast(data.message, 'error');
                }
            } catch (error) {
                this.showToast('Terjadi kesalahan saat proses pembayaran', 'error');
            } finally {
                this.processing = false;
            }
        },
    };
}
</script>
@endpush
@endsection