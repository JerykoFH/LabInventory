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

        return view('auth.login');
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
     * Redirect ke dashboard setelah login
     */
    private function redirectByRole(string $role)
    {
        return redirect()->route('dashboard');
    }
}
