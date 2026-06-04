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

    // Tampilkan daftar lengkap semua barang di inventaris laboratorium
    public function assets()
    {
        $response = $this->api->get('/api/staf-admin/assets');
        $assets = $response->successful() ? $response->json('data') : [];

        return view('staf_admin.assets.index', compact('assets'));
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

    // Catat tanggal barang diterima secara fisik
    // Penting: Setelah dicatat, tanggal tidak bisa diubah lagi!
    public function setReceived(Request $request, string $id)
    {
        $validated = $request->validate([
            'receivedDate' => 'required|date',
        ]);

        $response = $this->api->patch("/api/staf-admin/assets/{$id}/receive", $validated);

        if ($response->successful()) {
            return redirect()->route('staf-admin.assets.index')
                ->with('success', 'Tanggal penerimaan berhasil disimpan.');
        }

        $errorMsg = $response->json('message', 'Gagal menyimpan tanggal penerimaan.');
        return back()->withErrors($errorMsg);
    }
}
