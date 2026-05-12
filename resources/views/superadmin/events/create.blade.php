@extends('layouts.app')

@section('title', 'Acara Baru')

@section('content')
    <div class="max-w-xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Acara Baru</h1>
            <p class="text-sm text-slate-600 mt-1">Setelah acara dibuat, daftarkan tenant dan hubungkan ke acara ini.</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <form action="{{ route('superadmin.events.store') }}" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nama acara <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('name') border-red-300 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
                    <textarea id="description" name="description" rows="3"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('description') border-red-300 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="starts_at" class="block text-sm font-medium text-slate-700 mb-2">Mulai</label>
                        <input type="datetime-local" id="starts_at" name="starts_at" value="{{ old('starts_at') }}"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('starts_at') border-red-300 @enderror">
                        @error('starts_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="ends_at" class="block text-sm font-medium text-slate-700 mb-2">Selesai</label>
                        <input type="datetime-local" id="ends_at" name="ends_at" value="{{ old('ends_at') }}"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('ends_at') border-red-300 @enderror">
                        @error('ends_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6 flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="text-sm text-slate-700">Acara aktif (bisa dipilih saat daftar tenant)</label>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('superadmin.events.index') }}"
                        class="px-4 py-2 text-sm text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50">Batal</a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
