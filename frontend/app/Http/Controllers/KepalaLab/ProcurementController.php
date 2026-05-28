<?php

namespace App\Http\Controllers\KepalaLab;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;

/**
 * KepalaLab\ProcurementController
 * Membuat dan mengelola draf pengadaan tahunan
 */
class ProcurementController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    /** GET /kepala-lab/procurements */
    public function index()
    {
        $response = $this->api->get('/api/kepala-lab/procurements');
        $drafts = $response->successful() ? $response->json('data') : [];

        return view('kepala_lab.procurements.index', compact('drafts'));
    }

    /** GET /kepala-lab/procurements/create */
    public function create()
    {
        return view('kepala_lab.procurements.create');
    }

    /** POST /kepala-lab/procurements */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'year'  => 'required|integer|min:2020|max:2100',
            'notes' => 'nullable|string',
        ]);

        $response = $this->api->post('/api/kepala-lab/procurements', $validated);

        if ($response->successful()) {
            $draftId = $response->json('data._id');
            return redirect()->route('kepala-lab.procurements.show', $draftId)
                ->with('success', 'Draf pengadaan berhasil dibuat.');
        }

        return back()->withErrors($response->json('message'))->withInput();
    }

    /** GET /kepala-lab/procurements/{id} */
    public function show(string $id)
    {
        $response = $this->api->get("/api/kepala-lab/procurements/{$id}");
        $draft = $response->successful() ? $response->json('data') : null;

        // Ambil daftar aset untuk opsi penggantian
        $assetsResp = $this->api->get('/api/staf-admin/assets');
        $assets = $assetsResp->successful() ? $assetsResp->json('data') : [];

        return view('kepala_lab.procurements.show', compact('draft', 'assets'));
    }

    /** GET /kepala-lab/procurements/{id}/edit */
    public function edit(string $id)
    {
        $response = $this->api->get("/api/kepala-lab/procurements/{$id}");
        $draft = $response->successful() ? $response->json('data') : null;

        if ($draft && $draft['status'] === 'locked') {
            return redirect()->route('kepala-lab.procurements.show', $id)
                ->with('error', 'Draf yang sudah dikunci tidak dapat diubah.');
        }

        return view('kepala_lab.procurements.edit', compact('draft'));
    }

    /** PUT /kepala-lab/procurements/{id} */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:200',
            'year'  => 'required|integer|min:2020|max:2100',
            'notes' => 'nullable|string',
        ]);

        $response = $this->api->put("/api/kepala-lab/procurements/{$id}", $validated);

        if ($response->successful()) {
            return redirect()->route('kepala-lab.procurements.show', $id)
                ->with('success', 'Draf berhasil diperbarui.');
        }

        return back()->withErrors($response->json('message'))->withInput();
    }

    /** DELETE /kepala-lab/procurements/{id} */
    public function destroy(string $id)
    {
        $response = $this->api->delete("/api/kepala-lab/procurements/{$id}");

        if ($response->successful()) {
            return redirect()->route('kepala-lab.procurements.index')
                ->with('success', 'Draf berhasil dihapus.');
        }

        return back()->withErrors($response->json('message'));
    }

    /** POST /kepala-lab/procurements/{id}/submit */
    public function submit(string $id)
    {
        $response = $this->api->post("/api/kepala-lab/procurements/{$id}/submit");

        if ($response->successful()) {
            return redirect()->route('kepala-lab.procurements.index')
                ->with('success', 'Draf berhasil disubmit ke Kaprodi.');
        }

        return back()->withErrors($response->json('message'));
    }

    // ── Item Management ────────────────────────────────────────────────────

    /** POST /kepala-lab/procurements/{id}/items */
    public function addItem(Request $request, string $id)
    {
        $validated = $request->validate([
            'itemType'       => 'required|in:asset,consumable',
            'name'           => 'required|string|max:200',
            'quantity'       => 'required|integer|min:1',
            'unit'           => 'nullable|string|max:50',
            'estimatedPrice' => 'required|numeric|min:0',
            'purchaseLink'   => 'nullable|url',
            'replacedAsset'  => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $response = $this->api->post("/api/kepala-lab/procurements/{$id}/items", $validated);

        if ($response->successful()) {
            return redirect()->route('kepala-lab.procurements.show', $id)
                ->with('success', 'Item berhasil ditambahkan.');
        }

        return back()->withErrors($response->json('message'))->withInput();
    }

    /** PUT /kepala-lab/procurements/{id}/items/{itemId} */
    public function updateItem(Request $request, string $id, string $itemId)
    {
        $validated = $request->validate([
            'itemType'       => 'required|in:asset,consumable',
            'name'           => 'required|string|max:200',
            'quantity'       => 'required|integer|min:1',
            'unit'           => 'nullable|string|max:50',
            'estimatedPrice' => 'required|numeric|min:0',
            'purchaseLink'   => 'nullable|url',
            'replacedAsset'  => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $response = $this->api->put("/api/kepala-lab/procurements/{$id}/items/{$itemId}", $validated);

        if ($response->successful()) {
            return redirect()->route('kepala-lab.procurements.show', $id)
                ->with('success', 'Item berhasil diperbarui.');
        }

        return back()->withErrors($response->json('message'))->withInput();
    }

    /** DELETE /kepala-lab/procurements/{id}/items/{itemId} */
    public function removeItem(string $id, string $itemId)
    {
        $this->api->delete("/api/kepala-lab/procurements/{$id}/items/{$itemId}");
        return redirect()->route('kepala-lab.procurements.show', $id)
            ->with('success', 'Item berhasil dihapus.');
    }
}
