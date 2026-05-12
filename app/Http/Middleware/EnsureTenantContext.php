<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use App\Support\CurrentTenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenantContext
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->isSuperAdmin()) {
            if ($request->routeIs('superadmin.*')) {
                CurrentTenant::set(null);
                View::share('currentTenant', null);

                return $next($request);
            }

            if ($request->routeIs('profile.*')) {
                $tenantId = session('current_tenant_id');
                if ($tenantId) {
                    CurrentTenant::set((int) $tenantId);
                    View::share('currentTenant', Tenant::find($tenantId));
                } else {
                    CurrentTenant::set(null);
                }

                return $next($request);
            }

            $tenantId = session('current_tenant_id');

            if (! $tenantId) {
                return redirect()
                    ->route('superadmin.tenants.index')
                    ->with('warning', 'Pilih tenant terlebih dahulu untuk mengakses aplikasi.');
            }

            CurrentTenant::set((int) $tenantId);
            View::share('currentTenant', Tenant::find($tenantId));

            return $next($request);
        }

        if (! $user->tenant_id) {
            abort(403, 'Akun tidak terhubung ke tenant.');
        }

        CurrentTenant::set((int) $user->tenant_id);
        View::share('currentTenant', $user->tenant);

        return $next($request);
    }
}
