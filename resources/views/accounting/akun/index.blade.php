@extends('layouts.app')

@section('title', 'Bagan Akun (Chart of Accounts)')

@section('content')
<div class="space-y-6" x-data="{ showModal: false, editMode: false, form: { id: null, kode_akun: '', nama_akun: '', kategori: 'aset_lancar', posisi_normal: 'debit' } }">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-600 to-blue-800 text-white flex items-center justify-center font-black text-2xl shadow-md flex-shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">Bagan Akun (Chart of Accounts / COA)</h2>
                    @if(isset($currentTenant))
                        <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 border border-blue-200 rounded-full font-bold text-xs flex items-center gap-1 shadow-sm">
                            <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            <span>{{ $currentTenant->name }}</span>
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 mt-0.5">SaaS Multi-Tenant Accounting - Master akun standar SAK Indonesia 4-Digit per Warung/Tenant</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <form action="{{ route('akuntansi.akun.reset') }}" method="POST" onsubmit="return confirm('Sinkronkan & pastikan seluruh akun standar SAK SaaS tersedia untuk tenant ini?')">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-200 transition-colors flex items-center gap-1.5 shadow-sm">
                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Reset Templat SAK SaaS</span>
                </button>
            </form>
            <button @click="showModal = true; editMode = false; form = { id: null, kode_akun: '', nama_akun: '', kategori: 'aset_lancar', posisi_normal: 'debit' }" class="px-5 py-2.5 bg-gradient-to-r from-indomie-red to-red-600 text-white font-bold text-sm rounded-xl shadow-md hover:brightness-110 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                + Tambah Akun Baru
            </button>
        </div>
    </div>

    {{-- Tabel Bagan Akun --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h3 class="font-bold text-slate-800 text-sm">Daftar Akun Terdaftar</h3>
            <span class="text-xs text-slate-500 font-medium">Total: {{ $accounts->count() }} akun</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-bold uppercase text-[10px] border-b border-slate-100">
                        <th class="p-3.5">Kode Akun</th>
                        <th class="p-3.5">Nama Akun SAK</th>
                        <th class="p-3.5">Kategori</th>
                        <th class="p-3.5">Posisi Normal</th>
                        <th class="p-3.5">Tipe</th>
                        <th class="p-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($accounts as $acc)
                        <tr class="hover:bg-slate-50/50">
                            <td class="p-3.5 font-mono font-bold text-blue-600 text-xs">{{ $acc->kode_akun }}</td>
                            <td class="p-3.5 font-bold text-slate-800 text-sm">{{ $acc->nama_akun }}</td>
                            <td class="p-3.5">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                    {{ $acc->kategori_label }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                <span class="uppercase font-bold text-xs {{ $acc->posisi_normal === 'debit' ? 'text-green-600' : 'text-blue-600' }}">
                                    {{ $acc->posisi_normal }}
                                </span>
                            </td>
                            <td class="p-3.5">
                                @if($acc->is_system)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">Sistem Bawaan</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">Kustom</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center space-x-2">
                                @if(!$acc->is_system)
                                    <button @click="showModal = true; editMode = true; form = { id: {{ $acc->id }}, kode_akun: '{{ $acc->kode_akun }}', nama_akun: '{{ $acc->nama_akun }}', kategori: '{{ $acc->kategori }}', posisi_normal: '{{ $acc->posisi_normal }}' }" class="text-blue-600 font-bold hover:underline">Edit</button>
                                    <form action="{{ route('akuntansi.akun.destroy', $acc) }}" method="POST" class="inline" onsubmit="return confirm('Hapus akun ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 font-bold hover:underline">Hapus</button>
                                    </form>
                                @else
                                    <span class="text-slate-300 italic text-[10px]">Terkunci</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Tambah / Edit --}}
    <div x-show="showModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative z-10 p-6">
            <h3 class="text-lg font-black text-slate-800 mb-4" x-text="editMode ? 'Edit Akun COA' : 'Tambah Akun COA Baru'"></h3>
            <form :action="editMode ? '/akuntansi/akun/' + form.id : '{{ route('akuntansi.akun.store') }}'" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="_method" value="PUT">
                </template>
                <div class="space-y-4 mb-6">
                    <div x-show="!editMode">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kode Akun SAK (4-Digit)</label>
                        <input type="text" name="kode_akun" x-model="form.kode_akun" placeholder="Contoh: 6-1007" required class="w-full px-3.5 py-2 border rounded-xl text-sm font-mono font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Akun</label>
                        <input type="text" name="nama_akun" x-model="form.nama_akun" placeholder="Contoh: Beban Kemasan Kebersihan" required class="w-full px-3.5 py-2 border rounded-xl text-sm font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Kategori Akun</label>
                        <select name="kategori" x-model="form.kategori" required class="w-full px-3.5 py-2 border rounded-xl text-sm font-bold">
                            <option value="aset_lancar">Aset Lancar</option>
                            <option value="aset_tetap">Aset Tetap</option>
                            <option value="kewajiban">Kewajiban / Utang</option>
                            <option value="ekuitas">Ekuitas / Modal</option>
                            <option value="pendapatan">Pendapatan Usaha</option>
                            <option value="hpp">Beban Pokok Penjualan (HPP)</option>
                            <option value="beban_operasional">Beban Operasional</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Posisi Normal</label>
                        <select name="posisi_normal" x-model="form.posisi_normal" required class="w-full px-3.5 py-2 border rounded-xl text-sm font-bold">
                            <option value="debit">DEBIT</option>
                            <option value="kredit">KREDIT</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="showModal = false" class="px-4 py-2 bg-slate-200 text-slate-700 font-bold text-xs rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2 bg-indomie-red text-white font-bold text-xs rounded-xl shadow" x-text="editMode ? 'Perbarui Akun' : 'Simpan Akun'"></button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
