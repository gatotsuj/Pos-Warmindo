@extends('layouts.app')

@section('title', 'Daftar Tenant Baru')

@section('content')
    <div class="max-w-2xl">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">Daftar tenant untuk acara</h1>
            <p class="text-sm text-slate-600 mt-1">Pilih acara, isi data tenant. Opsional: buat akun admin pertama untuk tenant ini.</p>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-6">
            <form action="{{ route('superadmin.tenants.store') }}" method="POST" class="space-y-8">
                @csrf

                <div>
                    <h2 class="text-sm font-semibold text-slate-800 mb-4">Acara & tenant</h2>
                    <div class="space-y-4">
                        <div>
                            <label for="event_id" class="block text-sm font-medium text-slate-700 mb-2">Acara <span class="text-red-500">*</span></label>
                            <select id="event_id" name="event_id" required
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('event_id') border-red-300 @enderror">
                                <option value="">— Pilih acara —</option>
                                @foreach ($events as $ev)
                                    <option value="{{ $ev->id }}" @selected(old('event_id') == $ev->id)>{{ $ev->name }}</option>
                                @endforeach
                            </select>
                            @error('event_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-slate-500">Belum ada acara? <a href="{{ route('superadmin.events.create') }}" class="text-indigo-600 hover:underline">Buat acara</a> dulu.</p>
                        </div>

                        <div>
                            <label for="name" class="block text-sm font-medium text-slate-700 mb-2">Nama tenant <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('name') border-red-300 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="slug" class="block text-sm font-medium text-slate-700 mb-2">Slug (opsional)</label>
                            <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 font-mono text-sm @error('slug') border-red-300 @enderror">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-2">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="is_active" class="text-sm text-slate-700">Tenant aktif</label>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-200 pt-6">
                    <h2 class="text-sm font-semibold text-slate-800 mb-1">Admin tenant (opsional)</h2>
                    <p class="text-xs text-slate-500 mb-4">Jika diisi, sistem membuat satu user dengan role admin untuk tenant ini.</p>
                    <div class="space-y-4">
                        <div>
                            <label for="admin_name" class="block text-sm font-medium text-slate-700 mb-2">Nama admin</label>
                            <input type="text" id="admin_name" name="admin_name" value="{{ old('admin_name') }}"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('admin_name') border-red-300 @enderror">
                            @error('admin_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="admin_email" class="block text-sm font-medium text-slate-700 mb-2">Email admin</label>
                            <input type="email" id="admin_email" name="admin_email" value="{{ old('admin_email') }}"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('admin_email') border-red-300 @enderror">
                            @error('admin_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="admin_password" class="block text-sm font-medium text-slate-700 mb-2">Password</label>
                            <input type="password" id="admin_password" name="admin_password"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('admin_password') border-red-300 @enderror">
                            @error('admin_password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="admin_password_confirmation" class="block text-sm font-medium text-slate-700 mb-2">Konfirmasi password</label>
                            <input type="password" id="admin_password_confirmation" name="admin_password_confirmation"
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-200">
                    <a href="{{ route('superadmin.tenants.index') }}"
                        class="px-4 py-2 text-sm text-slate-700 border border-slate-300 rounded-lg hover:bg-slate-50">Batal</a>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
