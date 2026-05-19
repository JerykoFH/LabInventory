<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;

/**
 * Admin\UserController
 * Mengelola data pengguna sistem
 */
class UserController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    /** GET /admin/users */
    public function index()
    {
        $response = $this->api->get('/api/admin/users');
        $users = $response->successful() ? $response->json('data') : [];

        return view('admin.users.index', compact('users'));
    }

    /** GET /admin/users/create */
    public function create()
    {
        $roles = ['admin', 'kepala_lab', 'kaprodi', 'staf_admin', 'staf_lab'];
        return view('admin.users.create', compact('roles'));
    }

    /** POST /admin/users */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email',
            'password' => 'required|min:8',
            'role'     => 'required|in:admin,kepala_lab,kaprodi,staf_admin,staf_lab',
        ]);

        $response = $this->api->post('/api/admin/users', $validated);

        if ($response->successful()) {
            return redirect()->route('admin.users.index')->with('success', 'User berhasil dibuat.');
        }

        return back()->withErrors($response->json('message'))->withInput();
    }

    /** GET /admin/users/{id} */
    public function show(string $id)
    {
        $response = $this->api->get("/api/admin/users/{$id}");
        $user = $response->successful() ? $response->json('data') : null;

        return view('admin.users.show', compact('user'));
    }

    /** GET /admin/users/{id}/edit */
    public function edit(string $id)
    {
        $response = $this->api->get("/api/admin/users/{$id}");
        $user  = $response->successful() ? $response->json('data') : null;
        $roles = ['admin', 'kepala_lab', 'kaprodi', 'staf_admin', 'staf_lab'];

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /** PUT /admin/users/{id} */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email',
            'role'     => 'required|in:admin,kepala_lab,kaprodi,staf_admin,staf_lab',
            'isActive' => 'boolean',
        ]);

        $response = $this->api->put("/api/admin/users/{$id}", $validated);

        if ($response->successful()) {
            return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
        }

        return back()->withErrors($response->json('message'))->withInput();
    }

    /** DELETE /admin/users/{id} */
    public function destroy(string $id)
    {
        $this->api->delete("/api/admin/users/{$id}");
        return redirect()->route('admin.users.index')->with('success', 'User berhasil dinonaktifkan.');
    }
}
