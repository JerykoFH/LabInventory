<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    /**
     * Test dashboard is protected by api.auth middleware.
     */
    public function test_dashboard_redirects_to_login_if_unauthenticated()
    {
        $response = $this->get('/dashboard');

        // Middleware redirects to login
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('error', 'Silakan login terlebih dahulu.');
    }

    /**
     * Test dashboard is accessible if authenticated.
     */
    public function test_dashboard_accessible_if_authenticated()
    {
        // Set fake session data
        Session::put('api_token', 'fake-token');
        Session::put('api_user', [
            'id' => '123',
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'role' => 'admin'
        ]);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.index'); // Ensure your view name matches
    }
}
