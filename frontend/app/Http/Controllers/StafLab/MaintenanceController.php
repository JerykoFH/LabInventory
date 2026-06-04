<?php

namespace App\Http\Controllers\StafLab;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;

// Controller staf lab — catat dan lihat log pemeliharaan aset
class MaintenanceController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    // Tampilkan semua log pemeliharaan, terbaru di atas
    public function index()
    {
        $response = $this->api->get('/api/staf-lab/maintenance');
        $logs = $response->successful() ? $response->json('data') : [];

        return view('staf_lab.maintenance.index', compact('logs'));
    }

    // Ajax endpoint untuk mengambil aset berdasarkan ruangan
    public function getAssetsByRoom(string $roomId)
    {
        $response = $this->api->get("/api/staf-lab/rooms/{$roomId}/assets");
        return response()->json($response->successful() ? $response->json('data') : []);
    }

    // Tampilkan form catat pemeliharaan
    public function create()
    {
        // Ambil daftar ruangan untuk opsi form
        $roomsResp = $this->api->get('/api/staf-lab/rooms');
        $rooms = $roomsResp->successful() ? $roomsResp->json('data') : [];

        // Hapus pemanggilan API aset di awal, karena sekarang diambil via AJAX saat ruangan dipilih

        $consumablesResp = $this->api->get('/api/staf-lab/consumables');
        $consumables = $consumablesResp->successful() ? $consumablesResp->json('data') : [];

        return view('staf_lab.maintenance.create', compact('rooms', 'consumables'));
    }

    // Simpan log baru, kurangi stok BHP yang digunakan, dan update kondisi aset
    public function store(Request $request)
    {
        $validated = $request->validate([
            'room'                           => 'required|string',
            'assets'                         => 'nullable|array',
            'assets.*.asset'                 => 'required|string',
            'assets.*.conditionBefore'       => 'nullable|in:baik,rusak_ringan,rusak_berat',
            'assets.*.conditionAfter'        => 'nullable|in:baik,rusak_ringan,rusak_berat,tidak_aktif',
            'assets.*.photoBefore'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'assets.*.photoAfter'            => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'maintenanceDate'                => 'required|date',
            'type'                           => 'required|in:rutin,perbaikan,pengecekan',
            'description'                    => 'required|string',
            'notes'                          => 'nullable|string',
            'consumablesUsed'                => 'nullable|array',
            'consumablesUsed.*.item'         => 'required|string',
            'consumablesUsed.*.quantityUsed' => 'required|integer|min:0',
        ]);

        $data = $validated;
        
        // Ekstrak files
        $files = [];
        if (isset($request->assets) && is_array($request->assets)) {
            foreach ($request->assets as $index => $assetData) {
                if ($request->hasFile("assets.{$index}.photoBefore")) {
                    $files["photoBefore_{$index}"] = $request->file("assets.{$index}.photoBefore");
                }
                if ($request->hasFile("assets.{$index}.photoAfter")) {
                    $files["photoAfter_{$index}"] = $request->file("assets.{$index}.photoAfter");
                }
            }
        }
        
        // Remove uploaded files from data array to prevent serialization issues
        // We only send the text data in $data
        foreach ($data['assets'] ?? [] as $i => $assetData) {
            unset($data['assets'][$i]['photoBefore'], $data['assets'][$i]['photoAfter']);
        }

        $response = $this->api->postMultipart('/api/staf-lab/maintenance', $data, $files);

        if ($response->successful()) {
            return redirect()->route('staf-lab.maintenance.index')
                ->with('success', 'Log maintenance berhasil disimpan.');
        }

        return back()->withErrors($response->json('message') ?? 'Terjadi kesalahan pada server.')->withInput();
    }

    // Tampilkan detail satu log pemeliharaan
    public function show(string $id)
    {
        $response = $this->api->get("/api/staf-lab/maintenance/{$id}");
        $log = $response->successful() ? $response->json('data') : null;

        return view('staf_lab.maintenance.show', compact('log'));
    }
}
