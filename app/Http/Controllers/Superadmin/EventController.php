<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EventController extends Controller
{
    protected EventRepositoryInterface $eventRepo;

    public function __construct(EventRepositoryInterface $eventRepo)
    {
        $this->eventRepo = $eventRepo;
    }

    public function index(): View
    {
        $events = $this->eventRepo->paginateWithTenantsCount(15);

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

        $this->eventRepo->create([
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

        $this->eventRepo->update($event, [
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
        if ($this->eventRepo->hasTenants($event)) {
            return redirect()
                ->route('superadmin.events.index')
                ->with('error', 'Acara masih memiliki tenant. Pindahkan tenant ke acara lain terlebih dahulu.');
        }

        $this->eventRepo->delete($event);

        return redirect()
            ->route('superadmin.events.index')
            ->with('success', 'Acara dihapus.');
    }
}
