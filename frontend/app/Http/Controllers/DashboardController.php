<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    public function index()
    {
        $user = Session::get('api_user');
        $stats = [
            'totalAssets' => 0,
            'totalConsumables' => 0,
            'lowStockConsumables' => 0,
            'totalRooms' => 0,
            'totalDrafts' => 0,
            'submittedDrafts' => 0,
            'totalUsers' => 0,
            'maintenanceNeeded' => 0,
        ];

        // Ambil data statistik sesuai role user
        // Pakai try-catch biar dashboard tetap kebuka walaupun API lagi error
        try {
            $role = $user['role'] ?? '';

            if ($role === 'admin') {
                $usersResp = $this->api->get('/api/admin/users');
                if ($usersResp->successful()) {
                    $stats['totalUsers'] = count($usersResp->json('data') ?? []);
                }
                $roomsResp = $this->api->get('/api/admin/rooms');
                if ($roomsResp->successful()) {
                    $stats['totalRooms'] = count($roomsResp->json('data') ?? []);
                }
            }

            if (in_array($role, ['kepala_lab', 'kaprodi'])) {
                $prefix = $role === 'kepala_lab' ? '/api/kepala-lab' : '/api/kaprodi';
                $draftsResp = $this->api->get("$prefix/procurements");
                if ($draftsResp->successful()) {
                    $drafts = $draftsResp->json('data') ?? [];
                    $stats['totalDrafts'] = count($drafts);
                    $stats['submittedDrafts'] = count(array_filter($drafts, fn($d) => ($d['status'] ?? '') === 'submitted'));
                }
            }

        } catch (\Exception $e) {
            // Dashboard tetap tampil walaupun API gagal
        }

        return view('dashboard.index', compact('user', 'stats'));
    }
}
