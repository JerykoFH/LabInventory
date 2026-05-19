<?php

namespace App\Http\Controllers\StafLab;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;

/**
 * StafLab\ConsumableController
 * Mengelola stok BHP (Barang Habis Pakai)
 */
class ConsumableController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    /** GET /staf-lab/consumables */
    public function index()
    {
        $response = $this->api->get('/api/staf-lab/consumables');
        $items = $response->successful() ? $response->json('data') : [];

        return view('staf_lab.consumables.index', compact('items'));
    }

    /** GET /staf-lab/consumables/create */
    public function create()
    {
        return view('staf_lab.consumables.create');
    }

    /** POST /staf-lab/consumables */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:200',
            'category'     => 'nullable|string|max:100',
            'unit'         => 'required|string|max:50',
            'currentStock' => 'required|integer|min:0',
            'minimumStock' => 'nullable|integer|min:0',
            'location'     => 'nullable|string|max:200',
            'notes'        => 'nullable|string',
        ]);

        $response = $this->api->post('/api/staf-lab/consumables', $validated);

        if ($response->successful()) {
            return redirect()->route('staf-lab.consumables.index')
                ->with('success', 'Item BHP berhasil ditambahkan.');
        }

        return back()->withErrors($response->json('message'))->withInput();
    }

    /** PATCH /staf-lab/consumables/{id}/stock — Adjust stok */
    public function adjustStock(Request $request, string $id)
    {
        $validated = $request->validate([
            'adjustment' => 'required|numeric',
            'reason'     => 'nullable|string',
        ]);

        $response = $this->api->patch("/api/staf-lab/consumables/{$id}/stock", $validated);

        if ($response->successful()) {
            return redirect()->route('staf-lab.consumables.index')
                ->with('success', 'Stok berhasil diperbarui.');
        }

        return back()->withErrors($response->json('message'));
    }
}
