@extends('layouts.app')

@section('title', 'Acara')

@section('content')
    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Acara / Event</h1>
                <p class="text-sm text-slate-600 mt-1">Satu acara dapat berisi banyak tenant (stand, outlet, dll.).</p>
            </div>
            <a href="{{ route('superadmin.events.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Acara Baru
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Nama</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Tenant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse ($events as $event)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-slate-900">{{ $event->name }}</div>
                                    @if ($event->description)
                                        <div class="text-xs text-slate-500 mt-1">{{ \Illuminate\Support\Str::limit($event->description, 80) }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 whitespace-nowrap">
                                    @if ($event->starts_at || $event->ends_at)
                                        {{ optional($event->starts_at)->format('d M Y H:i') ?? '—' }}
                                        —
                                        {{ optional($event->ends_at)->format('d M Y H:i') ?? '—' }}
                                    @else
                                        <span class="text-slate-400">Belum diatur</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600">{{ $event->tenants_count }}</td>
                                <td class="px-6 py-4">
                                    @if ($event->is_active)
                                        <span class="text-xs px-2 py-1 rounded-full bg-green-100 text-green-800">Aktif</span>
                                    @else
                                        <span class="text-xs px-2 py-1 rounded-full bg-slate-100 text-slate-600">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                    <a href="{{ route('superadmin.tenants.index', ['event_id' => $event->id]) }}"
                                        class="text-sm text-indigo-600 hover:text-indigo-800">Tenant</a>
                                    <a href="{{ route('superadmin.events.edit', $event) }}"
                                        class="text-sm text-slate-600 hover:text-slate-900">Edit</a>
                                    <form action="{{ route('superadmin.events.destroy', $event) }}" method="POST" class="inline"
                                        onsubmit="return confirm('Hapus acara ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">Belum ada acara.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($events->hasPages())
                <div class="px-6 py-3 border-t border-slate-200">{{ $events->links() }}</div>
            @endif
        </div>
    </div>
@endsection
