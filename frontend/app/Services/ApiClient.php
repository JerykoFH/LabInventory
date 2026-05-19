<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

/**
 * ApiClient — Service untuk berkomunikasi dengan Node.js backend
 *
 * Setiap method otomatis menyertakan Bearer token dari session.
 */
class ApiClient
{
    protected string $baseUrl;
    protected string $internalKey;

    public function __construct()
    {
        $this->baseUrl     = config('app.api_url', env('API_URL', 'http://localhost:3000'));
        $this->internalKey = env('INTERNAL_API_KEY', '');
    }

    /**
     * Buat HTTP request dengan auth token dari session
     */
    private function http()
    {
        $token = Session::get('api_token');

        return Http::withHeaders([
            'Authorization'   => "Bearer {$token}",
            'X-Internal-Key'  => $this->internalKey,
            'Accept'          => 'application/json',
        ])->baseUrl($this->baseUrl);
    }

    public function get(string $endpoint, array $query = [])
    {
        return $this->http()->get($endpoint, $query);
    }

    public function post(string $endpoint, array $data = [])
    {
        return $this->http()->post($endpoint, $data);
    }

    public function put(string $endpoint, array $data = [])
    {
        return $this->http()->put($endpoint, $data);
    }

    public function patch(string $endpoint, array $data = [])
    {
        return $this->http()->patch($endpoint, $data);
    }

    public function delete(string $endpoint)
    {
        return $this->http()->delete($endpoint);
    }

    /**
     * Login ke API dan simpan token ke session
     */
    public function login(string $email, string $password)
    {
        $response = Http::baseUrl($this->baseUrl)
            ->post('/api/auth/login', compact('email', 'password'));

        if ($response->successful()) {
            $data = $response->json();
            Session::put('api_token', $data['token']);
            Session::put('api_user', $data['user']);
            return $data['user'];
        }

        return null;
    }

    /**
     * Logout — hapus token dari session
     */
    public function logout(): void
    {
        Session::forget(['api_token', 'api_user']);
    }
}
