<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * AuthController
 * Menangani login/logout Laravel session-based.
 * Token JWT disimpan di session dan diteruskan ke Node.js backend via ApiClient.
 */
class AuthController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    /** GET /login */
    public function showLogin()
    {
        if (Session::has('api_user')) {
            return $this->redirectByRole(Session::get('api_user')['role']);
        }

        // Pastikan halaman login tidak di-cache browser
        return response()
            ->view('auth.login')
            ->withHeaders([
                'Cache-Control' => 'no-store, no-cache, must-revalidate, private',
                'Pragma'        => 'no-cache',
                'Expires'       => '0',
            ]);
    }

    /** POST /login */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = $this->api->login($request->email, $request->password);

        if (!$user) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
        }

        return $this->redirectByRole($user['role']);
    }

    /** POST /logout */
    public function logout()
    {
        $this->api->logout();
        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }

    /**
     * Redirect ke halaman utama sesuai role setelah login
     */
    private function redirectByRole(string $role)
    {
        return match($role) {
            'admin'      => redirect()->route('admin.users.index'),
            'kepala_lab' => redirect()->route('kepala-lab.procurements.index'),
            'kaprodi'    => redirect()->route('kaprodi.procurements.index'),
            'staf_admin' => redirect()->route('staf-admin.procurements.index'),
            'staf_lab'   => redirect()->route('staf-lab.consumables.index'),
            default      => redirect()->route('dashboard'),
        };
    }
}
