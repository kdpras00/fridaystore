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

        if (!$user || !$user->hasAnyRole($roles)) {
            return redirect()->route('dashboard')->with('swal_error', 'Akses ditolak. Anda tidak memiliki izin.');
        }

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('swal_error', 'Akun Anda dinonaktifkan. Hubungi administrator.');
        }

        return $next($request);
    }
}
