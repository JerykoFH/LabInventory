<?php

namespace App\Http\Controllers\StafAdmin;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;

/**
 * StafAdmin\InventoryController
 * Mengelola inventaris: label, QR/barcode, dan tanggal penerimaan barang
 */
class InventoryController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    /** GET /staf-admin/procurements — Lihat draf locked */
    public function procurements()
    {
        $response = $this->api->get('/api/staf-admin/procurements');
        $drafts = $response->successful() ? $response->json('data') : [];

        return view('staf_admin.procurements.index', compact('drafts'));
    }

    /** GET /staf-admin/procurements/{id} — Detail draf locked */
    public function procurementDetail(string $id)
    {
        $response = $this->api->get("/api/staf-admin/procurements/{$id}");
        $draft = $response->successful() ? $response->json('data') : null;

        return view('staf_admin.procurements.show', compact('draft'));
    }

    /** GET /staf-admin/assets — Daftar inventaris */
    public function assets()
    {
        $response = $this->api->get('/api/staf-admin/assets');
        $assets = $response->successful() ? $response->json('data') : [];

        return view('staf_admin.assets.index', compact('assets'));
    }

    /** PATCH /staf-admin/assets/{id}/label — Update label/QR */
    public function updateLabel(Request $request, string $id)
    {
        $validated = $request->validate([
            'assetCode'  => 'required|string|max:50',
            'labelPhoto' => 'nullable|string',
            'qrCode'     => 'nullable|string',
        ]);

        $response = $this->api->patch("/api/staf-admin/assets/{$id}/label", $validated);

        if ($response->successful()) {
            return redirect()->route('staf-admin.assets')->with('success', 'Label aset berhasil diperbarui.');
        }

        return back()->withErrors($response->json('message'));
    }

    /** PATCH /staf-admin/assets/{id}/receive — Input tanggal penerimaan */
    public function setReceived(Request $request, string $id)
    {
        $validated = $request->validate([
            'receivedDate' => 'required|date',
        ]);

        $response = $this->api->patch("/api/staf-admin/assets/{$id}/receive", $validated);

        if ($response->successful()) {
            return redirect()->route('staf-admin.assets')->with('success', 'Tanggal penerimaan berhasil disimpan.');
        }

        return back()->withErrors($response->json('message'));
    }
}
