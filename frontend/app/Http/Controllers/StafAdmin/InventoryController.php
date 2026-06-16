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

    // Ubah status pengadaan dari locked menjadi in_progress (sedang diproses)
    public function setProgress(Request $request, string $id)
    {
        $response = $this->api->patch("/api/staf-admin/procurements/{$id}/progress");

        if ($response->successful()) {
            return redirect()->route('staf-admin.procurements.index')
                             ->with('success', 'Status pengadaan berhasil diubah menjadi Sedang Diproses.');
        }

        return back()->with('error', $response->json('message') ?? 'Gagal mengubah status pengadaan.');
    }

    // Memproses penerimaan barang secara parsial per item
    public function receiveItem(Request $request, string $id, string $itemId)
    {
        $validated = $request->validate([
            'receivedQuantity' => 'required|numeric|min:0',
        ]);

        $response = $this->api->patch("/api/staf-admin/procurements/{$id}/items/{$itemId}/receive", $validated);

        if ($response->successful()) {
            $data = $response->json('data');
            $message = 'Jumlah penerimaan item berhasil diperbarui.';
            if (isset($data['draftStatus']) && $data['draftStatus'] === 'completed') {
                $message .= ' Semua item telah diterima, status pengadaan Selesai.';
            }
            return back()->with('success', $message);
        }

        return back()->with('error', $response->json('message') ?? 'Gagal memperbarui jumlah penerimaan.');
    }

    public function assets(Request $request)
    {
        $allResp = $this->api->get('/api/staf-admin/assets');
        $allData = $allResp->successful() ? $allResp->json('data') : [];
        $categories = collect($allData)->pluck('category')->filter()->unique()->values();

        $response = $this->api->get('/api/staf-admin/assets', $request->query());
        $assets = $response->successful() ? $response->json('data') : [];

        $roomsResponse = $this->api->get('/api/global/rooms');
        $rooms = $roomsResponse->successful() ? $roomsResponse->json('data') : [];

        return view('staf_admin.assets.index', compact('assets', 'rooms', 'categories'));
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

    public function updateCondition(Request $request, string $id)
    {
        $validated = $request->validate([
            'condition' => 'required|in:baik,rusak_ringan,rusak_berat,tidak_aktif',
        ]);

        $response = $this->api->patch("/api/staf-admin/assets/{$id}/condition", $validated);

        if ($response->successful()) {
            return redirect()->route('staf-admin.assets.index')->with('success', 'Kondisi aset berhasil diperbarui.');
        }

        return back()->withErrors($response->json('message'));
    }
}
