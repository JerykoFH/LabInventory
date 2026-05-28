<?php

namespace App\Http\Controllers\StafLab;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;

// Controller staf lab — kelola stok barang habis pakai (BHP)
class ConsumableController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    // Tampilkan semua item BHP beserta stok saat ini
    public function index()
    {
        $response = $this->api->get('/api/staf-lab/consumables');
        $items = $response->successful() ? $response->json('data') : [];

        return view('staf_lab.consumables.index', compact('items'));
    }

    // Tampilkan form tambah item BHP
    public function create()
    {
        return view('staf_lab.consumables.create');
    }

    // Simpan item BHP baru ke database
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

    // Tambah atau kurangi stok BHP (adjustment positif = tambah, negatif = kurangi)
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
