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

        try {
            $response = $this->api->get('/api/dashboard/stats');
            
            if ($response->successful()) {
                $data = $response->json('data') ?? [];
                
                $stats['totalAssets'] = $data['totalAssets'] ?? 0;
                $stats['totalConsumables'] = $data['totalConsumables'] ?? 0;
                $stats['lowStockConsumables'] = $data['lowStockConsumables'] ?? 0;
                $stats['totalRooms'] = $data['totalRooms'] ?? 0;
                $stats['totalDrafts'] = $data['totalDrafts'] ?? 0;
                $stats['submittedDrafts'] = $data['submittedDrafts'] ?? 0;
                $stats['totalUsers'] = $data['totalUsers'] ?? 0;
                $stats['maintenanceNeeded'] = $data['maintenanceNeeded'] ?? 0;
            }
        } catch (\Exception $e) {
            // Dashboard tetap tampil walaupun API gagal
        }

        return view('dashboard.index', compact('user', 'stats'));
    }
}
