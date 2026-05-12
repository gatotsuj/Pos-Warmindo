<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::query()
            ->withCount('tenants')
            ->orderByDesc('starts_at')
            ->orderBy('name')
            ->paginate(15);

        return view('superadmin.events.index', compact('events'));
    }

    public function create(): View
    {
        return view('superadmin.events.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['boolean'],
        ]);

        Event::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('superadmin.events.index')
            ->with('success', 'Acara berhasil dibuat.');
    }

    public function edit(Event $event): View
    {
        return view('superadmin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'is_active' => ['boolean'],
        ]);

        $event->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'starts_at' => $validated['starts_at'] ?? null,
            'ends_at' => $validated['ends_at'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('superadmin.events.index')
            ->with('success', 'Acara diperbarui.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        if ($event->tenants()->exists()) {
            return redirect()
                ->route('superadmin.events.index')
                ->with('error', 'Acara masih memiliki tenant. Pindahkan tenant ke acara lain terlebih dahulu.');
        }

        $event->delete();

        return redirect()
            ->route('superadmin.events.index')
            ->with('success', 'Acara dihapus.');
    }
}
