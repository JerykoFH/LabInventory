<?php

namespace App\Http\Controllers\StafAdmin;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;

// Handle semua operasi inventory untuk staf admin: label aset dan pencatatan penerimaan barang
class InventoryController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    // Tampilkan daftar semua pengadaan yang sudah final (sudah dikunci oleh kaprodi)
    public function procurements()
    {
        $response = $this->api->get('/api/staf-admin/procurements');
        $drafts = $response->successful() ? $response->json('data') : [];

        return view('staf_admin.procurements.index', compact('drafts'));
    }

    // Tampilkan detail lengkap satu pengadaan beserta item-item yang sudah disetujui
    public function procurementDetail(string $id)
    {
        $response = $this->api->get("/api/staf-admin/procurements/{$id}");
        $draft = $response->successful() ? $response->json('data') : null;

        return view('staf_admin.procurements.show', compact('draft'));
    }

    // Tampilkan semua aset inventaris laboratorium dengan filter penerimaan
    public function assets()
    {
        $status = request('status'); // 'received' untuk sudah diterima, 'pending' untuk belum
        $query = $status === 'received' ? '?received=true' : ($status === 'pending' ? '?received=false' : '');
        
        $response = $this->api->get("/api/staf-admin/assets{$query}");
        $assets = $response->successful() ? $response->json('data') : [];
        $received = $status;

        return view('staf_admin.assets.index', compact('assets', 'received'));
    }

    // Tampilkan detail lengkap satu aset dengan informasi label, QR, dan tanggal penerimaan
    public function show(string $id)
    {
        $response = $this->api->get("/api/staf-admin/assets/{$id}");
        $asset = $response->successful() ? $response->json('data') : null;

        return view('staf_admin.assets.show', compact('asset'));
    }

    // Simpan atau perbarui kode aset, foto label, atau QR code sesuai input staf
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
    public function updateReceivedDate(Request $request, string $id)
    {
        $validated = $request->validate([
            'receivedDate' => 'required|date',
        ]);

        $response = $this->api->patch("/api/staf-admin/assets/{$id}/receive", $validated);

        if ($response->successful()) {
            return redirect()->route('staf-admin.assets.index')->with('success', 'Tanggal penerimaan aset berhasil diperbarui.');
        }

        return back()->withErrors($response->json('message'));
    }
}
