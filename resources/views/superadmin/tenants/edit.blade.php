@extends('layouts.app')

@section('title', 'Edit Tenant')

@section('content')
    <div class="max-w-xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Edit Tenant</h1>
            <p class="text-sm text-slate-600 mt-1">{{ $tenant->name }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <form action="{{ route('superadmin.tenants.update', $tenant) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="event_id" class="block text-sm font-medium text-slate-700 mb-2">Acara <span class="text-red-500">*</span></label>
                    <select id="event_id" name="event_id" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('event_id') border-red-300 @enderror">
                        @foreach ($events as $ev)
                            <option value="{{ $ev->id }}" @selected(old('event_id', $tenant->event_id) == $ev->id)>{{ $ev->name }}</option>
                        @endforeach
                    </select>
                    @error('event_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nama <span class="text-red-500">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $tenant->name) }}" required
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('name') border-red-300 @enderror">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="slug" class="block text-sm font-medium text-slate-700 mb-2">Slug</label>
                    <input type="text" id="slug" name="slug" value="{{ old('slug', $tenant->slug) }}"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 font-mono text-sm @error('slug') border-red-300 @enderror">
                    @error('slug')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6 flex items-center gap-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" id="is_active" name="is_active" value="1"
                        {{ old('is_active', $tenant->is_active) ? 'checked' : '' }}
                        class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <label for="is_active" class="text-sm text-slate-700">Aktif</label>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('superadmin.tenants.index') }}"
                        class="px-4 py-2 text-sm text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50">Batal</a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
