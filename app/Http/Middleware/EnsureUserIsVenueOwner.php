<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsVenueOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, ['VENUE_OWNER', 'SUPER_ADMIN'], true)) {
            abort(403, 'Akses khusus Admin Lapangan / Pengelola Arena (Venue Owner).');
        }

        if (! $user->is_active) {
            abort(403, 'Akun Anda sedang dinonaktifkan / disuspend oleh pihak administrator.');
        }

        return $next($request);
    }
}

