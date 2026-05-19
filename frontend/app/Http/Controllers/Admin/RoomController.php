<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;

/**
 * Admin\RoomController
 * Mengelola data ruangan laboratorium
 */
class RoomController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    /** GET /admin/rooms */
    public function index()
    {
        $response = $this->api->get('/api/admin/rooms');
        $rooms = $response->successful() ? $response->json('data') : [];

        return view('admin.rooms.index', compact('rooms'));
    }

    /** GET /admin/rooms/create */
    public function create()
    {
        return view('admin.rooms.create');
    }

    /** POST /admin/rooms */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:20',
            'location'    => 'nullable|string|max:200',
            'capacity'    => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $response = $this->api->post('/api/admin/rooms', $validated);

        if ($response->successful()) {
            return redirect()->route('admin.rooms.index')->with('success', 'Ruangan berhasil ditambahkan.');
        }

        return back()->withErrors($response->json('message'))->withInput();
    }

    /** GET /admin/rooms/{id}/edit */
    public function edit(string $id)
    {
        $response = $this->api->get("/api/admin/rooms/{$id}");
        $room = $response->successful() ? $response->json('data') : null;

        return view('admin.rooms.edit', compact('room'));
    }

    /** PUT /admin/rooms/{id} */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:20',
            'location'    => 'nullable|string|max:200',
            'capacity'    => 'nullable|integer|min:0',
            'description' => 'nullable|string',
        ]);

        $response = $this->api->put("/api/admin/rooms/{$id}", $validated);

        if ($response->successful()) {
            return redirect()->route('admin.rooms.index')->with('success', 'Ruangan berhasil diperbarui.');
        }

        return back()->withErrors($response->json('message'))->withInput();
    }

    /** DELETE /admin/rooms/{id} */
    public function destroy(string $id)
    {
        $this->api->delete("/api/admin/rooms/{$id}");
        return redirect()->route('admin.rooms.index')->with('success', 'Ruangan berhasil dinonaktifkan.');
    }
}
