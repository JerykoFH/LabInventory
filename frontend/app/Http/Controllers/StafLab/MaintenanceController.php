<?php

namespace App\Http\Controllers\StafLab;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;

// Catat dan kelola pemeliharaan aset laboratorium
class MaintenanceController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    // Tampilkan semua catatan pemeliharaan, urut dari yang paling baru
    public function index()
    {
        $response = $this->api->get('/api/staf-lab/maintenance');
        $logs = $response->successful() ? $response->json('data') : [];

        return view('staf_lab.maintenance.index', compact('logs'));
    }

    // Siapkan form pemeliharaan dan muat data aset, BHP, dan ruangan untuk dropdown
    public function create()
    {
        // Ambil daftar aset, BHP, dan ruangan dari API untuk opsi di form
        $assetsResp = $this->api->get('/api/staf-admin/assets');
        $assets = $assetsResp->successful() ? $assetsResp->json('data') : [];

        $consumablesResp = $this->api->get('/api/staf-lab/consumables');
        $consumables = $consumablesResp->successful() ? $consumablesResp->json('data') : [];

        $roomsResp = $this->api->get('/api/staf-lab/rooms');
        $rooms = $roomsResp->successful() ? $roomsResp->json('data') : [];

        return view('staf_lab.maintenance.create', compact('assets', 'consumables', 'rooms'));
    }

    // Catat pemeliharaan baru, update stok BHP yang dipakai, dan catat kondisi aset setelahnya
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset'                          => 'required|string',
            'room'                           => 'nullable|string',
            'maintenanceDate'                => 'required|date',
            'type'                           => 'required|in:rutin,perbaikan,pengecekan',
            'description'                    => 'required|string',
            'conditionBefore'                => 'nullable|in:baik,rusak_ringan,rusak_berat',
            'conditionAfter'                 => 'nullable|in:baik,rusak_ringan,rusak_berat,tidak_aktif',
            'notes'                          => 'nullable|string',
            'consumablesUsed'                => 'nullable|array',
            'consumablesUsed.*.item'         => 'required|string',
            'consumablesUsed.*.quantityUsed' => 'required|numeric|min:0',
        ]);

        $response = $this->api->post('/api/staf-lab/maintenance', $validated);

        if ($response->successful()) {
            return redirect()->route('staf-lab.maintenance.index')
                ->with('success', 'Log maintenance berhasil disimpan.');
        }

        return back()->withErrors($response->json('message'))->withInput();
    }

    // Tampilkan detail lengkap satu catatan pemeliharaan
    public function show(string $id)
    {
        $response = $this->api->get("/api/staf-lab/maintenance/{$id}");
        $log = $response->successful() ? $response->json('data') : null;

        return view('staf_lab.maintenance.show', compact('log'));
    }
}
