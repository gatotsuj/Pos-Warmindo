<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request. Role parameter may be a single role or pipe-separated (e.g. admin|superadmin).
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $allowed = array_map('trim', explode('|', $roles));

        if (! in_array($request->user()->role, $allowed, true)) {
            abort(403, 'Unauthorized. You do not have permission to access this page.');
        }

        return $next($request);
    }
}
