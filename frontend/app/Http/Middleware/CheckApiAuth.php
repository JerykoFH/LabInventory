<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckApiAuth
 * Middleware ini buat ngecek user udah login atau belum.
 * Login dianggap valid kalau api_token dan api_user ada di session.
 * Kalau belum login, user bakal dilempar balik ke halaman login.
 */
class CheckApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('api_token') || !Session::has('api_user')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Sediakan data user ke semua view via View::share
        $user = Session::get('api_user');
        view()->share('authUser', $user);

        return $next($request);
    }
}
