<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Cek apakah pengguna sudah login dan memiliki salah satu peran yang diizinkan
        if (!Auth::check() || !in_array(Auth::user()->role, $roles)) {
            // Jika tidak, kirim response "Forbidden"
            return response()->json(['message' => 'Anda tidak memiliki akses untuk tindakan ini.'], 403);
        }

        // Jika peran sesuai, lanjutkan request
        return $next($request);
    }
}
