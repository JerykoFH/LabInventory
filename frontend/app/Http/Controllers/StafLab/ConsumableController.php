<?php

namespace App\Http\Controllers\StafLab;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;

// Kelola stok barang habis pakai (BHP) untuk keperluan laboratorium
class ConsumableController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    // Tampilkan semua item barang habis pakai beserta stok terkini
    public function index()
    {
        $response = $this->api->get('/api/staf-lab/consumables');
        $items = $response->successful() ? $response->json('data') : [];

        return view('staf_lab.consumables.index', compact('items'));
    }

    // Tampilkan form untuk menambah item barang habis pakai baru
    public function create()
    {
        abort(403, 'Hanya Administrator yang dapat menambah item BHP baru.');
    }

    // Daftarkan item barang habis pakai baru ke database
    public function store(Request $request)
    {
        abort(403, 'Hanya Administrator yang dapat menambah item BHP baru.');
    }

    // Sesuaikan stok barang habis pakai (bisa menambah atau mengurangi)
    // Gunakan nilai positif untuk menambah, negatif untuk mengurangi
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

        return back()->with('error', $response->json('message') ?? 'Gagal memperbarui stok.');
    }
}
