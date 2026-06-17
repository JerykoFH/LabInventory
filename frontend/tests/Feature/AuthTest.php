<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Test the login page is accessible.
     */
    public function test_login_page_is_accessible()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * Test successful login redirects to the correct dashboard.
     */
    public function test_successful_login_redirects_by_role()
    {
        // Mock the HTTP request to the Node.js API
        Http::fake([
            '*/api/auth/login' => Http::response([
                'success' => true,
                'token' => 'fake-jwt-token',
                'user' => [
                    'id' => '123',
                    'name' => 'Staf Lab User',
                    'email' => 'staf@example.com',
                    'role' => 'staf_lab'
                ]
            ], 200)
        ]);

        $response = $this->post('/login', [
            'email' => 'staf@example.com',
            'password' => 'password123'
        ]);

        // Staf lab should redirect to staf-lab.consumables.index
        $response->assertRedirect(route('staf-lab.consumables.index'));
        
        // Assert session has token and user
        $this->assertEquals('fake-jwt-token', Session::get('api_token'));
        $this->assertEquals('staf_lab', Session::get('api_user')['role']);
    }

    /**
     * Test failed login redirects back with errors.
     */
    public function test_failed_login_redirects_back_with_errors()
    {
        // Mock failed API response
        Http::fake([
            '*/api/auth/login' => Http::response([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401)
        ]);

        $response = $this->post('/login', [
            'email' => 'wrong@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertSessionHasErrors('email');
        $response->assertRedirect();
        
        $this->assertNull(Session::get('api_token'));
    }

    /**
     * Test logout clears session and redirects to login.
     */
    public function test_logout_clears_session()
    {
        // Set session manually
        Session::put('api_token', 'token');
        Session::put('api_user', ['name' => 'Test']);

        $response = $this->post('/logout');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('success', 'Anda berhasil logout.');
        
        $this->assertNull(Session::get('api_token'));
        $this->assertNull(Session::get('api_user'));
    }
}
