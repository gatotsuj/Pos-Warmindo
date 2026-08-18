@extends('layouts.app')

@section('title', 'Pengaturan Struk & Branding Toko')

@section('content')
<div class="max-w-7xl mx-auto space-y-6" x-data="receiptSettingsApp()">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
        <div>
            <h1 class="text-2xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <span>Pengaturan Struk & Identitas Toko</span>
            </h1>
            <p class="text-xs text-slate-500 mt-1">Kelola informasi header, footer struk termal, warna tema brand aplikasi, pajak, dan metode pembayaran kasir.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="px-4 py-2.5 bg-slate-100 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-200 transition text-center flex items-center justify-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            <span>Kembali ke Dashboard</span>
        </a>
    </div>

    {{-- Main Grid Layout (Form Settings + Live Preview) --}}
    <form action="{{ route('admin.receipt-settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- Form Section (7 Columns) --}}
            <div class="lg:col-span-7 space-y-6">
                
                {{-- Card 1: Identitas & Logo Toko --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-5">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-slate-100">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Identitas Utama & Logo Toko</span>
                    </h3>

                    {{-- Store Logo --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Logo Toko (Cetak Struk & Header)</label>
                        <div class="flex items-center gap-6">
                            <div>
                                @if ($settings->logo)
                                    <img id="logo-preview" src="{{ asset('storage/' . $settings->logo) }}" alt="Logo Preview" class="h-20 w-20 object-contain border border-slate-200 rounded-2xl bg-white p-2 shadow-sm">
                                @else
                                    <div id="logo-preview" class="h-20 w-20 flex items-center justify-center bg-indigo-600 text-white text-xl font-black rounded-2xl border border-slate-200 shadow-sm">
                                        {{ strtoupper(collect(explode(' ', $settings->store_name))->map(fn($w) => substr($w, 0, 1))->take(2)->implode('')) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input type="file" id="logo" name="logo" accept="image/*" onchange="previewLogo(event)" class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="text-[11px] text-slate-400 mt-1.5">Format: JPG, PNG, WEBP. Maksimal 2MB.</p>
                                @error('logo') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Store Name --}}
                    <div>
                        <label for="store_name" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Toko / Warung <span class="text-red-500">*</span></label>
                        <input type="text" id="store_name" name="store_name" x-model="storeName" value="{{ old('store_name', $settings->store_name) }}" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500 @error('store_name') border-red-300 @enderror">
                        @error('store_name') <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p> @enderror
                    </div>

                    {{-- Store Address & Phone --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="store_address" class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Toko</label>
                            <input type="text" id="store_address" name="store_address" x-model="storeAddress" value="{{ old('store_address', $settings->store_address) }}" class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="store_phone" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nomor Telepon / WhatsApp</label>
                            <input type="text" id="store_phone" name="store_phone" x-model="storePhone" value="{{ old('store_phone', $settings->store_phone) }}" class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                {{-- Card 2: Tema Warna Aplikasi (Brand Theme Color) --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-slate-100">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/></svg>
                        <span>Warna Tema Aplikasi (Brand Theme Color)</span>
                    </h3>

                    @php $selectedColor = old('theme_color', $settings->theme_color ?? 'red'); @endphp
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3" x-data="{ color: '{{ $selectedColor }}' }">
                        <label @click="color = 'red'" :class="color === 'red' ? 'border-red-600 bg-red-50/50 ring-2 ring-red-500' : 'border-slate-200 bg-white'" class="relative flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition-all hover:shadow-sm">
                            <input type="radio" name="theme_color" value="red" x-model="color" class="sr-only">
                            <span class="w-6 h-6 rounded-full bg-gradient-to-r from-red-600 to-red-700 shadow-sm shrink-0"></span>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Indomie Red</p>
                                <p class="text-[10px] text-slate-500">Merah Kuliner</p>
                            </div>
                        </label>

                        <label @click="color = 'green'" :class="color === 'green' ? 'border-emerald-600 bg-emerald-50/50 ring-2 ring-emerald-500' : 'border-slate-200 bg-white'" class="relative flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition-all hover:shadow-sm">
                            <input type="radio" name="theme_color" value="green" x-model="color" class="sr-only">
                            <span class="w-6 h-6 rounded-full bg-gradient-to-r from-emerald-600 to-emerald-700 shadow-sm shrink-0"></span>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Emerald Green</p>
                                <p class="text-[10px] text-slate-500">Hijau Eco / Sembako</p>
                            </div>
                        </label>

                        <label @click="color = 'blue'" :class="color === 'blue' ? 'border-blue-600 bg-blue-50/50 ring-2 ring-blue-500' : 'border-slate-200 bg-white'" class="relative flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition-all hover:shadow-sm">
                            <input type="radio" name="theme_color" value="blue" x-model="color" class="sr-only">
                            <span class="w-6 h-6 rounded-full bg-gradient-to-r from-blue-600 to-blue-700 shadow-sm shrink-0"></span>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Royal Blue</p>
                                <p class="text-[10px] text-slate-500">Biru Korporat</p>
                            </div>
                        </label>

                        <label @click="color = 'indigo'" :class="color === 'indigo' ? 'border-indigo-600 bg-indigo-50/50 ring-2 ring-indigo-500' : 'border-slate-200 bg-white'" class="relative flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition-all hover:shadow-sm">
                            <input type="radio" name="theme_color" value="indigo" x-model="color" class="sr-only">
                            <span class="w-6 h-6 rounded-full bg-gradient-to-r from-indigo-600 to-indigo-700 shadow-sm shrink-0"></span>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Deep Indigo</p>
                                <p class="text-[10px] text-slate-500">Nila Premium</p>
                            </div>
                        </label>

                        <label @click="color = 'amber'" :class="color === 'amber' ? 'border-amber-600 bg-amber-50/50 ring-2 ring-amber-500' : 'border-slate-200 bg-white'" class="relative flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition-all hover:shadow-sm">
                            <input type="radio" name="theme_color" value="amber" x-model="color" class="sr-only">
                            <span class="w-6 h-6 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 shadow-sm shrink-0"></span>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Amber Gold</p>
                                <p class="text-[10px] text-slate-500">Kuning Warmindo</p>
                            </div>
                        </label>

                        <label @click="color = 'slate'" :class="color === 'slate' ? 'border-slate-600 bg-slate-100 ring-2 ring-slate-500' : 'border-slate-200 bg-white'" class="relative flex items-center gap-3 p-3 rounded-2xl border cursor-pointer transition-all hover:shadow-sm">
                            <input type="radio" name="theme_color" value="slate" x-model="color" class="sr-only">
                            <span class="w-6 h-6 rounded-full bg-gradient-to-r from-slate-700 to-slate-800 shadow-sm shrink-0"></span>
                            <div>
                                <p class="text-xs font-bold text-slate-800">Dark Slate</p>
                                <p class="text-[10px] text-slate-500">Abu Elegan</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Card 3: Footer Struk, Pajak & Sakelar Pembayaran --}}
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-5">
                    <h3 class="text-sm font-black text-slate-800 uppercase tracking-wider flex items-center gap-2 pb-3 border-b border-slate-100">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Footer Struk, Pajak & Metode Pembayaran</span>
                    </h3>

                    {{-- Footer Lines --}}
                    <div class="space-y-3">
                        <div>
                            <label for="footer_line_1" class="block text-xs font-bold text-slate-700 uppercase mb-1">Pesan Footer Baris 1</label>
                            <input type="text" id="footer_line_1" name="footer_line_1" x-model="footer1" value="{{ old('footer_line_1', $settings->footer_line_1) }}" class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="footer_line_2" class="block text-xs font-bold text-slate-700 uppercase mb-1">Pesan Footer Baris 2</label>
                            <input type="text" id="footer_line_2" name="footer_line_2" x-model="footer2" value="{{ old('footer_line_2', $settings->footer_line_2) }}" class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-xs font-medium text-slate-800 focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>

                    {{-- Tax Settings --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div>
                            <label for="tax_percent" class="block text-xs font-bold text-slate-700 uppercase mb-1">Tarif Pajak PBN (%)</label>
                            <div class="relative">
                                <input type="number" step="any" id="tax_percent" name="tax_percent" x-model="taxPercent" value="{{ old('tax_percent', $settings->tax_percent ?? 11) }}" class="w-full px-4 py-3 pr-8 border border-slate-200 rounded-2xl text-xs font-bold text-slate-800 focus:ring-2 focus:ring-indigo-500">
                                <span class="absolute right-4 top-1/2 -translate-y-1/2 font-bold text-xs text-slate-400">%</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-2xl border border-slate-200">
                            <div>
                                <p class="text-xs font-bold text-slate-800">Aktifkan Pajak</p>
                                <p class="text-[10px] text-slate-500">Hitung pajak di kasir</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="tax_enabled" value="1" {{ old('tax_enabled', $settings->tax_enabled ?? true) ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-9 h-5 bg-slate-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-indigo-600"></div>
                            </label>
                        </div>
                    </div>

                    {{-- Payment Toggles --}}
                    <div class="pt-3 border-t border-slate-100">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-3">Sakelar Metode Pembayaran Kasir POS</label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            {{-- Cash --}}
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-200">
                                <span class="text-xs font-bold text-slate-800">Tunai (Cash)</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_cash_enabled" value="1" {{ old('is_cash_enabled', $settings->is_cash_enabled ?? true) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-8 h-4 bg-slate-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-green-600"></div>
                                </label>
                            </div>
                            {{-- QRIS --}}
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-200">
                                <span class="text-xs font-bold text-slate-800">QRIS / E-Wallet</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_qris_enabled" value="1" {{ old('is_qris_enabled', $settings->is_qris_enabled ?? true) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-8 h-4 bg-slate-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-indigo-600"></div>
                                </label>
                            </div>
                            {{-- Card --}}
                            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-200">
                                <span class="text-xs font-bold text-slate-800">Debit / Card</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_card_enabled" value="1" {{ old('is_card_enabled', $settings->is_card_enabled ?? true) ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-8 h-4 bg-slate-300 rounded-full peer peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-blue-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('dashboard') }}" class="px-6 py-3.5 bg-slate-100 text-slate-700 font-bold text-xs rounded-2xl hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white font-black text-xs rounded-2xl shadow-lg hover:brightness-110 flex items-center justify-center gap-2 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>SIMPAN PENGATURAN STRUK</span>
                    </button>
                </div>

            </div>

            {{-- Right Column (5 Columns): Live Thermal Receipt Preview --}}
            <div class="lg:col-span-5 space-y-4">
                <div class="sticky top-6">
                    <div class="bg-slate-800 rounded-3xl p-5 shadow-xl border border-slate-700 text-white space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-700 pb-3">
                            <span class="text-xs font-black uppercase tracking-wider text-slate-400 flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                <span>Pratinjau Live Struk Termal (58mm/80mm)</span>
                            </span>
                            <span class="text-[10px] font-bold bg-emerald-500/20 text-emerald-400 px-2 py-0.5 rounded-md">LIVE</span>
                        </div>

                        {{-- Thermal Receipt Paper Preview Container --}}
                        <div class="bg-stone-50 rounded-2xl p-5 text-slate-900 font-mono text-xs shadow-inner space-y-3 border border-stone-200">
                            {{-- Header Struk --}}
                            <div class="text-center space-y-1 pb-3 border-b border-dashed border-slate-300">
                                @if ($settings->logo)
                                    <img src="{{ asset('storage/' . $settings->logo) }}" alt="Logo Toko" class="h-12 w-auto mx-auto object-contain mb-1">
                                @endif
                                <h4 class="font-black text-sm text-slate-900 uppercase tracking-tight" x-text="storeName || 'TOKO WARMIDO'"></h4>
                                <p class="text-[10px] text-slate-600 leading-tight" x-text="storeAddress || 'Jl. Contoh No. 123'"></p>
                                <p class="text-[10px] text-slate-600" x-text="'Telp: ' + (storePhone || '0812-3456-7890')"></p>
                            </div>

                            {{-- Info Transaksi Dummy --}}
                            <div class="text-[10px] text-slate-600 space-y-0.5 pb-2 border-b border-dashed border-slate-300">
                                <div class="flex justify-between"><span>No. Struk:</span><span class="font-bold">#TRX-88402</span></div>
                                <div class="flex justify-between"><span>Tanggal:</span><span>{{ date('d/m/Y H:i') }}</span></div>
                                <div class="flex justify-between"><span>Kasir:</span><span>{{ auth()->user()->name }}</span></div>
                            </div>

                            {{-- Items Dummy --}}
                            <div class="space-y-1 py-1 border-b border-dashed border-slate-300 text-[10px]">
                                <div class="flex justify-between font-bold text-slate-800">
                                    <span>2x Indomie Goreng Spesial</span>
                                    <span>Rp 24.000</span>
                                </div>
                                <div class="flex justify-between font-bold text-slate-800">
                                    <span>1x Es Teh Manis</span>
                                    <span>Rp 5.000</span>
                                </div>
                            </div>

                            {{-- Total & Tax --}}
                            <div class="space-y-1 text-[10px] pt-1">
                                <div class="flex justify-between text-slate-600"><span>Subtotal:</span><span>Rp 29.000</span></div>
                                <div class="flex justify-between text-slate-600">
                                    <span x-text="'Pajak (' + (taxPercent || 11) + '%):'"></span>
                                    <span x-text="'Rp ' + Math.round(29000 * ((taxPercent || 11)/100)).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex justify-between font-black text-xs text-slate-900 pt-1 border-t border-slate-300">
                                    <span>TOTAL:</span>
                                    <span x-text="'Rp ' + (29000 + Math.round(29000 * ((taxPercent || 11)/100))).toLocaleString('id-ID')"></span>
                                </div>
                            </div>

                            {{-- Footer Struk --}}
                            <div class="text-center pt-3 border-t border-dashed border-slate-300 text-[10px] text-slate-500 space-y-0.5">
                                <p class="font-bold text-slate-700" x-text="footer1 || 'Terima Kasih Atas Kunjungan Anda'"></p>
                                <p x-text="footer2 || 'Barang yang sudah dibeli tidak dapat ditukar'"></p>
                            </div>
                        </div>

                        <p class="text-[11px] text-slate-400 text-center">Tampilan di atas adalah contoh cetakan pada kertas termal 58mm/80mm saat checkout kasir.</p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function receiptSettingsApp() {
        return {
            storeName: "{{ old('store_name', $settings->store_name) }}",
            storeAddress: "{{ old('store_address', $settings->store_address) }}",
            storePhone: "{{ old('store_phone', $settings->store_phone) }}",
            footer1: "{{ old('footer_line_1', $settings->footer_line_1) }}",
            footer2: "{{ old('footer_line_2', $settings->footer_line_2) }}",
            taxPercent: "{{ old('tax_percent', $settings->tax_percent ?? 11) }}"
        };
    }

    function previewLogo(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById('logo-preview');

            if (preview.tagName !== 'IMG') {
                const img = document.createElement('img');
                img.id = 'logo-preview';
                img.className = "h-20 w-20 object-contain border border-slate-200 rounded-2xl bg-white p-2 shadow-sm";
                preview.parentNode.replaceChild(img, preview);
                preview = img;
            }

            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
</script>
@endsection
