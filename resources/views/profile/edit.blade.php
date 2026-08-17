@extends('layouts.app')

@section('title', 'Profil Pengguna')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Header Banner Profil --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-bl from-blue-500/10 to-indigo-500/5 rounded-full blur-2xl pointer-events-none"></div>
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 relative z-10">
            {{-- Avatar Initials --}}
            <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-slate-800 to-slate-900 text-white font-black text-3xl flex items-center justify-center shadow-lg flex-shrink-0 border-2 border-white">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>

            <div class="flex-1 text-center sm:text-left">
                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight">{{ $user->name }}</h2>
                    <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $user->isSuperAdmin() ? 'bg-purple-100 text-purple-800 border border-purple-200' : ($user->isAdmin() ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-green-100 text-green-800 border border-green-200') }}">
                        {{ $user->role }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 font-medium flex items-center justify-center sm:justify-start gap-2">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span>{{ $user->email }}</span>
                </p>
                <div class="mt-3 flex flex-wrap items-center justify-center sm:justify-start gap-4 text-xs font-bold text-slate-600">
                    <span class="flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>Tenant: {{ $user->tenant->name ?? 'Platform Superadmin' }}</span>
                    </span>
                    <span class="flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-100">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Terdaftar: {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Alert Success Status --}}
    @if (session('status') === 'profile-updated')
        <div class="p-4 bg-green-50 border border-green-200 rounded-2xl text-xs font-bold text-green-800 flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Informasi profil Anda berhasil diperbarui!</span>
        </div>
    @elseif (session('status') === 'password-updated')
        <div class="p-4 bg-green-50 border border-green-200 rounded-2xl text-xs font-bold text-green-800 flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span>Password akun Anda berhasil diperbarui!</span>
        </div>
    @endif

    {{-- Card 1: Form Update Informasi Profil --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
        <div class="border-b border-slate-100 pb-3 flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-black text-slate-800">Informasi Akun</h3>
                <p class="text-xs text-slate-500">Perbarui nama pengguna dan alamat email akun Anda</p>
            </div>
        </div>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-4 max-w-xl">
            @csrf
            @method('patch')

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase mb-1">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-blue-500 focus:border-blue-500">
                @error('name')
                    <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase mb-1">Alamat Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium text-slate-800 focus:ring-blue-500 focus:border-blue-500">
                @error('email')
                    <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="px-5 py-2.5 bg-blue-600 text-white font-bold text-xs rounded-xl shadow hover:bg-blue-700 transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Card 2: Form Update Password --}}
    <div class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 space-y-4">
        <div class="border-b border-slate-100 pb-3 flex items-center gap-2">
            <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <div>
                <h3 class="text-base font-black text-slate-800">Keamanan & Password</h3>
                <p class="text-xs text-slate-500">Pastikan akun Anda menggunakan kata sandi yang kuat dan aman</p>
            </div>
        </div>

        <form method="post" action="{{ route('password.update') }}" class="space-y-4 max-w-xl">
            @csrf
            @method('put')

            <div>
                <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase mb-1">Password Saat Ini</label>
                <input type="password" id="current_password" name="current_password" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:ring-indigo-500">
                @error('current_password', 'updatePassword')
                    <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase mb-1">Password Baru</label>
                <input type="password" id="password" name="password" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:ring-indigo-500">
                @error('password', 'updatePassword')
                    <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase mb-1">Konfirmasi Password Baru</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required class="w-full px-4 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:ring-indigo-500">
                @error('password_confirmation', 'updatePassword')
                    <p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white font-bold text-xs rounded-xl shadow hover:bg-indigo-700 transition-colors flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 0121 9z"/></svg>
                    <span>Perbarui Password</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
