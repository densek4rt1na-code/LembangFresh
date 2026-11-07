<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

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

