<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

// Middleware ini ngecek apakah user sudah login lewat session
// Kalau belum ada token atau data user di session, langsung redirect ke login
class CheckApiAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Session::has('api_token') || !Session::has('api_user')) {
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu.');
        }

        // Bagikan data user ke semua view biar bisa langsung pakai $authUser
        $user = Session::get('api_user');
        view()->share('authUser', $user);

        $response = $next($request);

        // Pasang header no-cache biar back button di browser nggak bypass ke halaman yang sudah logout
        return $response->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }
}
