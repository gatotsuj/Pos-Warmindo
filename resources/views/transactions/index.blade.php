@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Riwayat Transaksi</h2>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form action="{{ route('transactions.index') }}" method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari invoice..."
                   class="px-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-indomie-green">
            <input type="date" name="date" value="{{ request('date') }}"
                   class="px-4 py-2 border rounded-lg text-sm">
            <select name="payment_method" class="px-4 py-2 border rounded-lg text-sm">
                <option value="">Semua Metode</option>
                <option value="cash"  {{ request('payment_method') === 'cash'  ? 'selected' : '' }}>Cash</option>
                <option value="card"  {{ request('payment_method') === 'card'  ? 'selected' : '' }}>Card</option>
                <option value="qris"  {{ request('payment_method') === 'qris'  ? 'selected' : '' }}>QRIS</option>
            </select>
            <select name="status" class="px-4 py-2 border rounded-lg text-sm">
                <option value="">Semua Status</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                <option value="voided"    {{ request('status') === 'voided'    ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-700 text-white rounded-lg text-sm font-medium hover:bg-slate-800 transition">
                Filter
            </button>
            @if(request()->hasAny(['search', 'date', 'payment_method', 'status']))
                <a href="{{ route('transactions.index') }}"
                   class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm font-medium hover:bg-gray-200 transition">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($transactions->count() > 0)
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pembayaran</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kasir</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transactions as $transaction)
                        <tr class="hover:bg-gray-50 {{ $transaction->isVoided() ? 'opacity-60' : '' }}">
                            <td class="px-6 py-4 font-medium text-blue-600 font-mono text-sm">
                                {{ $transaction->invoice_number }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $transaction->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                {{ $transaction->items->sum('quantity') }} item
                            </td>
                            <td class="px-6 py-4 font-medium text-sm {{ $transaction->isVoided() ? 'line-through text-gray-400' : '' }}">
                                {{ $transaction->formatted_grand_total }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full font-medium
                                    {{ $transaction->payment_method === 'cash' ? 'bg-green-100 text-green-800' :
                                       ($transaction->payment_method === 'card' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ ucfirst($transaction->payment_method) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $transaction->user->name }}</td>
                            <td class="px-6 py-4">
                                @if($transaction->isVoided())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></path></svg>
                                        Dibatalkan
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></path></svg>
                                        Selesai
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button type="button"
                                        x-data
                                        @click="$dispatch('open-modal', 'transaction-detail-{{ $transaction->id }}')"
                                        class="text-blue-600 hover:underline text-sm">
                                    Detail
                                </button>
                                @if(!$transaction->isVoided())
                                    <a href="{{ route('transactions.receipt', $transaction) }}" target="_blank"
                                       class="text-gray-600 hover:underline text-sm">Struk</a>
                                @endif
                                @if(auth()->user()->isAdmin() && !$transaction->isVoided())
                                    <button type="button"
                                            x-data
                                            @click="$dispatch('open-modal', 'void-confirm-{{ $transaction->id }}')"
                                            class="text-red-500 hover:text-red-700 text-sm font-medium">
                                        Batalkan
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Detail & Void Modals --}}
            @foreach($transactions as $transaction)
                {{-- DETAIL MODAL --}}
                <x-modal name="transaction-detail-{{ $transaction->id }}" maxWidth="2xl">
                    <div class="px-6 py-4 border-b flex items-center justify-between">
                        <div>
                            <h2 class="text-lg font-semibold text-gray-800">Detail Transaksi</h2>
                            <p class="text-sm text-gray-500">
                                Invoice {{ $transaction->invoice_number }} · {{ $transaction->formatted_date }}
                            </p>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-600"
                                x-on:click="$dispatch('close-modal', 'transaction-detail-{{ $transaction->id }}')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-6 space-y-5">
                        @if($transaction->isVoided())
                            <div class="p-4 bg-red-50 border border-red-200 rounded-lg text-sm">
                                <p class="font-semibold text-red-700 mb-1">⚠️ Transaksi ini telah dibatalkan</p>
                                <p class="text-red-600">Alasan: {{ $transaction->void_reason }}</p>
                                <p class="text-red-500 text-xs mt-1">
                                    Dibatalkan oleh {{ $transaction->voidedBy?->name }} pada {{ $transaction->voided_at?->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div><div class="text-gray-500">Invoice</div><div class="font-medium">{{ $transaction->invoice_number }}</div></div>
                            <div><div class="text-gray-500">Tanggal</div><div class="font-medium">{{ $transaction->formatted_date }}</div></div>
                            <div><div class="text-gray-500">Metode Bayar</div><div class="font-medium">{{ $transaction->payment_method_label }}</div></div>
                            <div><div class="text-gray-500">Kasir</div><div class="font-medium">{{ $transaction->user->name }}</div></div>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Item</h3>
                            <div class="border rounded-lg overflow-hidden">
                                <table class="min-w-full text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produk</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Qty</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Harga</th>
                                            <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($transaction->items as $item)
                                            <tr>
                                                <td class="px-4 py-2 font-medium">{{ $item->product_name }}</td>
                                                <td class="px-4 py-2 text-right">{{ $item->quantity }}</td>
                                                <td class="px-4 py-2 text-right">{{ $item->formatted_price }}</td>
                                                <td class="px-4 py-2 text-right font-medium">{{ $item->formatted_subtotal }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="space-y-1">
                                <div class="flex justify-between"><span class="text-gray-500">Subtotal</span><span>Rp {{ number_format($transaction->subtotal, 0, ',', '.') }}</span></div>
                                @if($transaction->discount_amount > 0)
                                    <div class="flex justify-between"><span class="text-gray-500">Diskon ({{ $transaction->discount_percent }}%)</span><span class="text-red-600">-Rp {{ number_format($transaction->discount_amount, 0, ',', '.') }}</span></div>
                                @endif
                                @if($transaction->tax_percent > 0)
                                    <div class="flex justify-between"><span class="text-gray-500">Pajak ({{ $transaction->tax_percent }}%)</span><span>Rp {{ number_format($transaction->tax_amount, 0, ',', '.') }}</span></div>
                                @endif
                            </div>
                            <div class="space-y-1">
                                <div class="flex justify-between font-semibold"><span>Grand Total</span><span>{{ $transaction->formatted_grand_total }}</span></div>
                                <div class="flex justify-between"><span class="text-gray-500">Bayar</span><span>Rp {{ number_format($transaction->paid_amount, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span class="text-gray-500">Kembalian</span><span>Rp {{ number_format($transaction->change_amount, 0, ',', '.') }}</span></div>
                            </div>
                        </div>

                        @if($transaction->notes)
                            <div class="pt-2 border-t text-sm text-gray-600">
                                <span class="font-semibold text-gray-700">Catatan: </span>{{ $transaction->notes }}
                            </div>
                        @endif
                    </div>

                    <div class="px-6 py-4 bg-gray-50 flex items-center justify-between">
                        <button type="button"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                                x-on:click="$dispatch('close-modal', 'transaction-detail-{{ $transaction->id }}')">
                            Tutup
                        </button>
                        @if(!$transaction->isVoided())
                            <a href="{{ route('transactions.receipt', $transaction) }}" target="_blank"
                               class="px-4 py-2 text-sm font-medium text-white bg-slate-700 rounded-lg hover:bg-slate-800 transition">
                                🖨️ Cetak Struk
                            </a>
                        @endif
                    </div>
                </x-modal>

                {{-- VOID CONFIRM MODAL --}}
                @if(auth()->user()->isAdmin() && !$transaction->isVoided())
                    <x-modal name="void-confirm-{{ $transaction->id }}" maxWidth="md">
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Batalkan Transaksi?</h3>
                                    <p class="text-sm text-gray-500">{{ $transaction->invoice_number }} · {{ $transaction->formatted_grand_total }}</p>
                                </div>
                            </div>

                            <div class="p-3 bg-amber-50 border border-amber-200 rounded-lg mb-4 text-sm text-amber-700">
                                <p class="font-semibold mb-1">⚠️ Tindakan ini tidak dapat dibatalkan.</p>
                                <p>Stok semua item akan dikembalikan secara otomatis.</p>
                            </div>

                            <form method="POST" action="{{ route('transactions.void', $transaction) }}">
                                @csrf
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                                        Alasan Pembatalan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="void_reason" required
                                           placeholder="Contoh: Salah input, permintaan pelanggan..."
                                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500">
                                </div>

                                <div class="flex gap-3 justify-end">
                                    <button type="button"
                                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition"
                                            x-on:click="$dispatch('close-modal', 'void-confirm-{{ $transaction->id }}')">
                                        Tidak, Kembali
                                    </button>
                                    <button type="submit"
                                            class="px-4 py-2 text-sm font-semibold text-white bg-red-600 rounded-lg hover:bg-red-700 transition">
                                        Ya, Batalkan Transaksi
                                    </button>
                                </div>
                            </form>
                        </div>
                    </x-modal>
                @endif
            @endforeach

            @if($transactions->hasPages())
                <div class="px-6 py-4 border-t">{{ $transactions->links() }}</div>
            @endif
        @else
            <div class="px-6 py-12 text-center text-gray-400">Tidak ada transaksi ditemukan.</div>
        @endif
    </div>
@endsection
