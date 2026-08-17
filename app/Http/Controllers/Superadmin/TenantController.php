<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Repositories\Contracts\EventRepositoryInterface;
use App\Repositories\Contracts\TenantRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class TenantController extends Controller
{
    protected TenantRepositoryInterface $tenantRepo;
    protected EventRepositoryInterface $eventRepo;
    protected UserRepositoryInterface $userRepo;

    public function __construct(
        TenantRepositoryInterface $tenantRepo,
        EventRepositoryInterface $eventRepo,
        UserRepositoryInterface $userRepo
    ) {
        $this->tenantRepo = $tenantRepo;
        $this->eventRepo = $eventRepo;
        $this->userRepo = $userRepo;
    }

    public function index(Request $request): View
    {
        $tenants = $this->tenantRepo->paginateFiltered($request->all(), 15)->withQueryString();
        $events = $this->eventRepo->allOrderedByName();

        return view('superadmin.tenants.index', compact('tenants', 'events'));
    }

    public function create(): View
    {
        $events = $this->eventRepo->getActiveOrderedByName();

        return view('superadmin.tenants.create', compact('events'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'event_id' => ['nullable', 'exists:events,id'],
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:tenants,slug'],
            'is_active' => ['boolean'],
            'admin_name' => ['nullable', 'required_with:admin_email', 'string', 'max:255'],
            'admin_email' => ['nullable', 'email', 'max:255', 'unique:users,email'],
            'admin_password' => ['nullable', 'required_with:admin_email', 'string', 'min:8', 'confirmed'],
        ]);

        $slug = $validated['slug'] ?? Tenant::generateUniqueSlug($validated['name']);
        $eventId = $validated['event_id'] ?? (\App\Models\Event::first()?->id ?? 1);

        DB::transaction(function () use ($request, $validated, $slug, $eventId) {
            $tenant = $this->tenantRepo->create([
                'event_id' => $eventId,
                'name' => $validated['name'],
                'slug' => $slug,
                'is_active' => $request->boolean('is_active', true),
            ]);

            if (! empty($validated['admin_email'])) {
                $this->userRepo->create([
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
        $events = $this->eventRepo->getActiveOrSpecificOrderedByName($tenant->event_id);

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

        $this->tenantRepo->update($tenant, [
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
        if ($this->tenantRepo->hasUsers($tenant)) {
            return redirect()
                ->route('superadmin.tenants.index')
                ->with('error', 'Tenant masih memiliki pengguna. Hapus atau pindahkan pengguna terlebih dahulu.');
        }

        if ($tenant->id === 1) {
            return redirect()
                ->route('superadmin.tenants.index')
                ->with('error', 'Tenant default tidak dapat dihapus.');
        }

        $this->tenantRepo->delete($tenant);

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
