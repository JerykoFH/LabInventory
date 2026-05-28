<?php

namespace App\Http\Controllers\StafAdmin;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;

// Controller untuk staf administrasi — kelola label aset & penerimaan barang
class InventoryController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    // Tampilkan daftar pengadaan yang sudah final (status locked)
    public function procurements()
    {
        $response = $this->api->get('/api/staf-admin/procurements');
        $drafts = $response->successful() ? $response->json('data') : [];

        return view('staf_admin.procurements.index', compact('drafts'));
    }

    // Tampilkan detail satu pengadaan beserta item yang disetujui
    public function procurementDetail(string $id)
    {
        $response = $this->api->get("/api/staf-admin/procurements/{$id}");
        $draft = $response->successful() ? $response->json('data') : null;

        return view('staf_admin.procurements.show', compact('draft'));
    }

    // Tampilkan semua aset inventaris laboratorium
    public function assets()
    {
        $response = $this->api->get('/api/staf-admin/assets');
        $assets = $response->successful() ? $response->json('data') : [];

        return view('staf_admin.assets.index', compact('assets'));
    }

    // Simpan kode aset / label / QR yang diinput staf
    public function updateLabel(Request $request, string $id)
    {
        $validated = $request->validate([
            'assetCode'  => 'required|string|max:50',
            'labelPhoto' => 'nullable|string',
            'qrCode'     => 'nullable|string',
        ]);

        $response = $this->api->patch("/api/staf-admin/assets/{$id}/label", $validated);

        if ($response->successful()) {
            return redirect()->route('staf-admin.assets.index')->with('success', 'Label aset berhasil diperbarui.');
        }

        return back()->withErrors($response->json('message'));
    }

    // Simpan tanggal barang diterima secara fisik
    public function setReceived(Request $request, string $id)
    {
        $validated = $request->validate([
            'receivedDate' => 'required|date',
        ]);

        $response = $this->api->patch("/api/staf-admin/assets/{$id}/receive", $validated);

        if ($response->successful()) {
            return redirect()->route('staf-admin.assets.index')->with('success', 'Tanggal penerimaan berhasil disimpan.');
        }

        return back()->withErrors($response->json('message'));
    }
}
