@extends('layouts.app')

@section('content')
    <div class="max-w-2xl">
        {{-- Header --}}
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Receipt Settings</h1>
            <p class="text-sm text-slate-600 mt-1">Customize the header and footer of your receipt.</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <form action="{{ route('admin.receipt-settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Store Logo --}}
                <div class="mb-6">
                    <label for="logo" class="block text-sm font-medium text-slate-700 mb-2">
                        Store Logo
                    </label>

                    <div class="flex items-center gap-6">
                        {{-- Preview --}}
                        <div>
                            @if ($settings->logo)
                                <img id="logo-preview" src="{{ asset('storage/' . $settings->logo) }}" alt="Logo Preview"
                                    class="h-24 w-24 object-contain border border-slate-300 rounded-lg bg-white p-2">
                            @else
                                <div id="logo-preview"
                                    class="h-24 w-24 flex items-center justify-center 
                    bg-indigo-600 text-white text-2xl font-bold 
                    rounded-lg border border-slate-300">
                                    {{ strtoupper(
                                        collect(explode(' ', $settings->store_name))->map(fn($word) => substr($word, 0, 1))->take(2)->implode(''),
                                    ) }}
                                </div>
                            @endif
                        </div>

                        {{-- File Input --}}
                        <div class="flex-1">
                            <input type="file" id="logo" name="logo" accept="image/*"
                                onchange="previewLogo(event)"
                                class="w-full text-sm text-slate-600
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-lg file:border-0
                          file:text-sm file:font-medium
                          file:bg-indigo-50 file:text-indigo-700
                          hover:file:bg-indigo-100">
                            <p class="text-xs text-slate-500 mt-2">
                                Format: JPG, PNG, WEBP. Max 2MB.
                            </p>
                            @error('logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Store Name --}}
                <div class="mb-4">
                    <label for="store_name" class="block text-sm font-medium text-slate-700 mb-1">
                        Store Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="store_name" name="store_name"
                        value="{{ old('store_name', $settings->store_name) }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('store_name') border-red-300 @enderror">
                    @error('store_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Store Address --}}
                <div class="mb-4">
                    <label for="store_address" class="block text-sm font-medium text-slate-700 mb-1">
                        Store Address
                    </label>
                    <textarea id="store_address" name="store_address" rows="2"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('store_address') border-red-300 @enderror"
                        placeholder="Jl. Contoh No. 123">{{ old('store_address', $settings->store_address) }}</textarea>
                    @error('store_address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Store Phone --}}
                <div class="mb-6">
                    <label for="store_phone" class="block text-sm font-medium text-slate-700 mb-1">
                        Store Phone
                    </label>
                    <input type="text" id="store_phone" name="store_phone"
                        value="{{ old('store_phone', $settings->store_phone) }}"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('store_phone') border-red-300 @enderror"
                        placeholder="021-1234567">
                    @error('store_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Header Extra Lines --}}
                <div class="mb-4">
                    <label for="header_line_1" class="block text-sm font-medium text-slate-700 mb-1">
                        Header Line 1
                    </label>
                    <input type="text" id="header_line_1" name="header_line_1"
                        value="{{ old('header_line_1', $settings->header_line_1) }}"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('header_line_1') border-red-300 @enderror">
                    @error('header_line_1')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="header_line_2" class="block text-sm font-medium text-slate-700 mb-1">
                        Header Line 2
                    </label>
                    <input type="text" id="header_line_2" name="header_line_2"
                        value="{{ old('header_line_2', $settings->header_line_2) }}"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('header_line_2') border-red-300 @enderror">
                    @error('header_line_2')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Footer Lines --}}
                <div class="mb-4">
                    <label for="footer_line_1" class="block text-sm font-medium text-slate-700 mb-1">
                        Footer Line 1
                    </label>
                    <input type="text" id="footer_line_1" name="footer_line_1"
                        value="{{ old('footer_line_1', $settings->footer_line_1) }}"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('footer_line_1') border-red-300 @enderror">
                    @error('footer_line_1')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="footer_line_2" class="block text-sm font-medium text-slate-700 mb-1">
                        Footer Line 2
                    </label>
                    <input type="text" id="footer_line_2" name="footer_line_2"
                        value="{{ old('footer_line_2', $settings->footer_line_2) }}"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('footer_line_2') border-red-300 @enderror">
                    @error('footer_line_2')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ===================== PAJAK & DISKON ===================== --}}
                <div class="mb-6 pt-4 border-t border-slate-200">
                    <h2 class="text-base font-semibold text-slate-800 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                        </svg>
                        Pajak &amp; Diskon
                    </h2>

                    {{-- Tax Percent --}}
                    <div class="mb-4">
                        <label for="tax_percent" class="block text-sm font-medium text-slate-700 mb-1">
                            Persentase Pajak (%)
                        </label>
                        <div class="flex items-center gap-2">
                            <input type="number" id="tax_percent" name="tax_percent" min="0" max="100" step="0.01"
                                value="{{ old('tax_percent', $settings->tax_percent ?? 11) }}"
                                class="w-32 px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('tax_percent') border-red-300 @enderror">
                            <span class="text-sm text-slate-500">%</span>
                        </div>
                        @error('tax_percent')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-slate-400 mt-1">Contoh: 11 untuk PPN 11%</p>
                    </div>

                    {{-- Toggle: Pajak Aktif --}}
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200 mb-3">
                        <div>
                            <p class="text-sm font-medium text-slate-700">Aktifkan Pajak</p>
                            <p class="text-xs text-slate-500 mt-0.5">Jika dinonaktifkan, pajak tidak dihitung di kasir dan tidak tampil di struk</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="tax_enabled" name="tax_enabled" value="1"
                                {{ old('tax_enabled', $settings->tax_enabled ?? true) ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer
                                peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px]
                                after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    {{-- Toggle: Diskon Aktif --}}
                    <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg border border-slate-200">
                        <div>
                            <p class="text-sm font-medium text-slate-700">Aktifkan Diskon</p>
                            <p class="text-xs text-slate-500 mt-0.5">Jika dinonaktifkan, input diskon disembunyikan di halaman kasir</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="discount_enabled" name="discount_enabled" value="1"
                                {{ old('discount_enabled', $settings->discount_enabled ?? true) ? 'checked' : '' }}
                                class="sr-only peer">
                            <div class="w-11 h-6 bg-slate-300 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-indigo-500 rounded-full peer
                                peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px]
                                after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5
                                after:transition-all peer-checked:bg-indigo-600"></div>
                        </label>
                    </div>

                    <div class="mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                        <p class="text-xs text-amber-700 flex items-start gap-2">
                            <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pengaturan ini langsung memengaruhi tampilan kasir (POS). Perubahan akan aktif pada transaksi berikutnya.
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Cancel
                    </a>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
<script>
    function previewLogo(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById('logo-preview');

            // Kalau bukan img, ganti jadi img
            if (preview.tagName !== 'IMG') {
                const img = document.createElement('img');
                img.id = 'logo-preview';
                img.className = "h-24 w-24 object-contain border border-slate-300 rounded-lg bg-white p-2";
                preview.parentNode.replaceChild(img, preview);
                preview = img;
            }

            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
</script>
