<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Jika user belum login, redirect ke login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // Cek apakah user memiliki salah satu role yang diizinkan
        foreach ($roles as $role) {
            if ($user->role === $role) {
                return $next($request);
            }
        }

        // Jika user tidak punya akses, redirect ke dashboard mereka
        // Admin → dashboard admin, CS → dashboard CS
        if ($user->role === 'admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        } elseif ($user->role === 'cs') {
            return redirect()->route('cs.dashboard')->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        // Fallback: logout jika role tidak dikenali
        Auth::logout();
        return redirect()->route('login')->with('error', 'Role tidak valid.');
    }
}