<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        // If multiple roles passed, check if user's role matches any
        $userRole = $request->user()->role;
        $allowedRoles = [];

        foreach ($roles as $roleGroup) {
            $split = explode(',', $roleGroup);
            foreach ($split as $r) {
                $allowedRoles[] = trim($r);
            }
        }

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Akses ditolak. Peran pengguna (' . ucfirst($userRole) . ') tidak memiliki izin mengakses modul ini.');
        }

        return $next($request);
    }
}

