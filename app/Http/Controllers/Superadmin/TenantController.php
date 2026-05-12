<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(Request $request): View
    {
        $tenants = Tenant::query()
            ->with('event')
            ->withCount('users')
            ->when($request->filled('event_id'), fn ($q) => $q->where('event_id', $request->integer('event_id')))
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $events = Event::orderBy('name')->get();

        return view('superadmin.tenants.index', compact('tenants', 'events'));
    }

    public function create(): View
    {
        $events = Event::where('is_active', true)->orderBy('name')->get();

        return view('superadmin.tenants.create', compact('events'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:tenants,slug'],
            'is_active' => ['boolean'],
            'admin_name' => ['nullable', 'required_with:admin_email', 'string', 'max:255'],
            'admin_email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['nullable', 'required_with:admin_email', 'string', 'min:8', 'confirmed'],
        ]);

        $slug = $validated['slug'] ?? Tenant::generateUniqueSlug($validated['name']);

        DB::transaction(function () use ($request, $validated, $slug) {
            $tenant = Tenant::create([
                'event_id' => $validated['event_id'],
                'name' => $validated['name'],
                'slug' => $slug,
                'is_active' => $request->boolean('is_active', true),
            ]);

            if (! empty($validated['admin_email'])) {
                User::create([
                    'tenant_id' => $tenant->id,
                    'name' => $validated['admin_name'],
                    'email' => $validated['admin_email'],
                    'password' => Hash::make($validated['admin_password']),
                    'role' => User::ROLE_ADMIN,
                    'email_verified_at' => now(),
                ]);
            }
        });

        return redirect()
            ->route('superadmin.tenants.index')
            ->with('success', 'Tenant berhasil didaftarkan untuk acara.');
    }

    public function edit(Tenant $tenant): View
    {
        $events = Event::query()
            ->where(function ($q) use ($tenant) {
                $q->where('is_active', true)->orWhere('id', $tenant->event_id);
            })
            ->orderBy('name')
            ->get();

        return view('superadmin.tenants.edit', compact('tenant', 'events'));
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:tenants,slug,'.$tenant->id],
            'is_active' => ['boolean'],
        ]);

        $slug = $validated['slug'] ?? Tenant::generateUniqueSlug($validated['name'], $tenant->id);

        $tenant->update([
            'event_id' => $validated['event_id'],
            'name' => $validated['name'],
            'slug' => $slug,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('superadmin.tenants.index')
            ->with('success', 'Tenant diperbarui.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        if ($tenant->users()->exists()) {
            return redirect()
                ->route('superadmin.tenants.index')
                ->with('error', 'Tenant masih memiliki pengguna. Hapus atau pindahkan pengguna terlebih dahulu.');
        }

        if ($tenant->id === 1) {
            return redirect()
                ->route('superadmin.tenants.index')
                ->with('error', 'Tenant default tidak dapat dihapus.');
        }

        $tenant->delete();

        return redirect()
            ->route('superadmin.tenants.index')
            ->with('success', 'Tenant dihapus.');
    }

    public function enter(Tenant $tenant): RedirectResponse
    {
        if (! $tenant->is_active) {
            return redirect()
                ->route('superadmin.tenants.index')
                ->with('error', 'Tenant tidak aktif.');
        }

        session([
            'current_tenant_id' => $tenant->id,
        ]);
        session()->forget('pos_cart');

        return redirect()
            ->route('dashboard')
            ->with('success', 'Beroperasi sebagai: '.$tenant->name);
    }

    public function leave(): RedirectResponse
    {
        session()->forget(['current_tenant_id', 'pos_cart']);

        return redirect()
            ->route('superadmin.tenants.index')
            ->with('success', 'Keluar dari konteks tenant.');
    }
}
