@extends('layouts.app')

@section('title', 'Jurnal Umum (General Journal)')

@section('content')
<div class="space-y-6" x-data="{ showManualModal: false }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center font-black text-2xl shadow-md flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            </div>
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Jurnal Umum (General Journal)</h2>
                <p class="text-xs text-slate-500 mt-0.5">SaaS Accounting - Histori pencatatan transaksi finansial berpasangan SAK</p>
            </div>
        </div>
        <button @click="showManualModal = true" class="px-5 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-bold text-sm rounded-xl shadow-md hover:brightness-110 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            + Entry Jurnal Manual
        </button>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-2xl p-4 shadow-sm border border-slate-100">
        <form action="{{ route('akuntansi.jurnal.index') }}" method="GET" class="flex flex-wrap gap-3 items-center">
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="px-3 py-2 border border-slate-200 rounded-xl text-xs font-bold">
            <span class="text-xs text-slate-400">s/d</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="px-3 py-2 border border-slate-200 rounded-xl text-xs font-bold">
            <select name="sumber" class="px-3 py-2 border border-slate-200 rounded-xl text-xs font-bold">
                <option value="">Semua Sumber</option>
                <option value="pos" {{ request('sumber') === 'pos' ? 'selected' : '' }}>Penjualan POS</option>
                <option value="pengeluaran" {{ request('sumber') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran Kas</option>
                <option value="manual" {{ request('sumber') === 'manual' ? 'selected' : '' }}>Entry Manual</option>
                <option value="void" {{ request('sumber') === 'void' ? 'selected' : '' }}>Pembatalan (Void)</option>
            </select>
            <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-xs font-bold">Filter</button>
        </form>
    </div>

    {{-- Daftar Jurnal --}}
    <div class="space-y-4">
        @forelse($jurnals as $jurnal)
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-slate-100">
                <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-slate-100 mb-3">
                    <div class="flex items-center gap-3">
                        <span class="font-mono font-bold text-sm text-blue-600">{{ $jurnal->nomor_jurnal }}</span>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                            {{ $jurnal->sumber_transaksi === 'pos' ? 'bg-green-100 text-green-800' :
                               ($jurnal->sumber_transaksi === 'pengeluaran' ? 'bg-orange-100 text-orange-800' :
                               ($jurnal->sumber_transaksi === 'void' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800')) }}">
                            {{ $jurnal->sumber_transaksi }}
                        </span>
                    </div>
                    <div class="text-xs text-slate-500 font-medium flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>{{ $jurnal->tanggal->format('d F Y') }} oleh <strong class="text-slate-700">{{ $jurnal->user->name ?? 'Sistem' }}</strong></span>
                    </div>
                </div>
                <p class="text-xs font-bold text-slate-700 mb-3">Keterangan: <span class="font-normal text-slate-600">{{ $jurnal->keterangan }}</span></p>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-slate-400 font-bold uppercase text-[10px] border-b border-slate-100">
                                <th class="p-2">Kode & Nama Akun</th>
                                <th class="p-2 text-right">Debit (Rp)</th>
                                <th class="p-2 text-right">Kredit (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($jurnal->details as $item)
                                <tr>
                                    <td class="p-2 font-medium text-slate-800 {{ $item->jenis === 'kredit' ? 'pl-8 text-slate-600' : 'font-bold' }}">
                                        {{ $item->akun->kode_akun }} - {{ $item->akun->nama_akun }}
                                    </td>
                                    <td class="p-2 text-right font-mono font-bold text-slate-800">
                                        {{ $item->jenis === 'debit' ? number_format($item->jumlah, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="p-2 text-right font-mono font-bold text-slate-800">
                                        {{ $item->jenis === 'kredit' ? number_format($item->jumlah, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl p-12 text-center text-slate-400 border border-slate-100">
                <p class="text-sm">Belum ada jurnal transaksi tercatat.</p>
            </div>
        @endforelse

        <div class="mt-4">
            {{ $jurnals->links() }}
        </div>
    </div>

    {{-- Modal Entry Jurnal Manual --}}
    <div x-show="showManualModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showManualModal = false"></div>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl relative z-10 p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-black text-slate-800 mb-4">Entry Jurnal Manual (Debit = Kredit)</h3>
            <form action="{{ route('akuntansi.jurnal.manual') }}" method="POST" x-data="{
                rows: [
                    { akun_id: '', jenis: 'debit', jumlah: 0 },
                    { akun_id: '', jenis: 'kredit', jumlah: 0 }
                ],
                addRow() { this.rows.push({ akun_id: '', jenis: 'debit', jumlah: 0 }) },
                removeRow(i) { if(this.rows.length > 2) this.rows.splice(i, 1) }
            }">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal</label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 border rounded-xl text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Keterangan Jurnal</label>
                        <input type="text" name="keterangan" placeholder="Keterangan penyesuaian..." required class="w-full px-3 py-2 border rounded-xl text-sm font-medium">
                    </div>
                </div>

                <div class="space-y-3 mb-4">
                    <template x-for="(row, index) in rows" :key="index">
                        <div class="flex gap-2 items-center bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                            <select :name="'items['+index+'][akun_id]'" required class="flex-1 px-2.5 py-1.5 border rounded-lg text-xs font-medium">
                                <option value="">-- Pilih Akun --</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->kode_akun }} - {{ $acc->nama_akun }}</option>
                                @endforeach
                            </select>
                            <select :name="'items['+index+'][jenis]'" x-model="row.jenis" required class="w-24 px-2 py-1.5 border rounded-lg text-xs font-bold">
                                <option value="debit">DEBIT</option>
                                <option value="kredit">KREDIT</option>
                            </select>
                            <input type="number" :name="'items['+index+'][jumlah]'" x-model="row.jumlah" min="1" placeholder="Nominal" required class="w-32 px-2.5 py-1.5 border rounded-lg text-xs font-bold text-right">
                            <button type="button" @click="removeRow(index)" class="text-red-500 font-bold text-xs p-1 hover:text-red-700" title="Hapus Baris">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </template>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-slate-100">
                    <button type="button" @click="addRow()" class="text-xs font-bold text-blue-600 hover:underline">+ Tambah Baris Akun</button>
                    <div class="flex gap-2">
                        <button type="button" @click="showManualModal = false" class="px-4 py-2 bg-slate-200 text-slate-700 font-bold text-xs rounded-xl">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-blue-600 text-white font-bold text-xs rounded-xl shadow">Simpan Jurnal</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
