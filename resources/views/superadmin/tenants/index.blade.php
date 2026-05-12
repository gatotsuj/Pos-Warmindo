@extends('layouts.app')

@section('title', 'Tenant')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Kelola Tenant</h1>
                <p class="text-sm text-slate-600 mt-1">Daftarkan tenant per acara; superadmin dapat membuat admin tenant sekaligus.</p>
            </div>
            <a href="{{ route('superadmin.tenants.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Daftar tenant baru
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-4">
            <form method="GET" action="{{ route('superadmin.tenants.index') }}" class="flex flex-wrap gap-3 items-end">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Acara</label>
                    <select name="event_id" class="px-3 py-2 border border-slate-300 rounded-lg text-sm min-w-[200px]">
                        <option value="">Semua</option>
                        @foreach ($events as $ev)
                            <option value="{{ $ev->id }}" @selected(request('event_id') == $ev->id)>{{ $ev->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="px-4 py-2 bg-slate-100 text-slate-800 text-sm rounded-lg hover:bg-slate-200">Filter</button>
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Acara</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Pengguna</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($tenants as $tenant)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4 text-sm font-medium text-slate-900">{{ $tenant->name }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $tenant->event?->name ?? '—' }}</td>
                                <td class="px-6 py-4 text-xs font-mono text-slate-600">{{ $tenant->slug }}</td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $tenant->users_count }}</td>
                                <td class="px-6 py-4">
                                    @if ($tenant->is_active)
                                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">Aktif</span>
                                    @else
                                        <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    @if ($tenant->is_active)
                                        <form action="{{ route('superadmin.tenants.enter', $tenant) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Masuk</button>
                                        </form>
                                    @endif
                                    <a href="{{ route('superadmin.financial.tenant', $tenant) }}"
                                        class="text-sm text-emerald-600 hover:text-emerald-800">Laporan</a>
                                    <a href="{{ route('superadmin.tenants.edit', $tenant) }}"
                                        class="text-sm text-slate-600 hover:text-slate-900">Edit</a>
                                    @if ($tenant->id !== 1)
                                        <form action="{{ route('superadmin.tenants.destroy', $tenant) }}" method="POST"
                                            class="inline"
                                            onsubmit="return confirm('Hapus tenant ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">Belum ada tenant.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($tenants->hasPages())
                <div class="px-6 py-3 border-t border-slate-200">
                    {{ $tenants->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
