<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Check active status first — inactive users must be logged out regardless of role
        if ($user && !$user->is_active) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('swal_error', 'Akun Anda dinonaktifkan. Hubungi administrator.');
        }

        if (!$user || !$user->hasAnyRole($roles)) {
            return redirect()->route('dashboard')->with('swal_error', 'Akses ditolak. Anda tidak memiliki izin.');
        }

        return $next($request);
    }
}
