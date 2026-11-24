<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Jika user belum login, lempar kembali ke halaman login
        if (! Auth::check()) {
            return redirect('/login');
        }

        // Jika role user tidak sesuai dengan role halaman yang dituju, tolak aksesnya
        if (Auth::user()->role !== $role) {
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin untuk membuka halaman ini.');
        }

        return $next($request);
    }
}
