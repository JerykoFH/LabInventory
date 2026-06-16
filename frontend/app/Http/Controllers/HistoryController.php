<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ApiClient;

use Barryvdh\DomPDF\Facade\Pdf;

class HistoryController extends Controller
{
    public function __construct(protected ApiClient $api) {}
    /**
     * Tampilkan halaman riwayat aktivitas
     */
    public function index(Request $request)
    {
        $role = session('user_role');
        
        $apiEndpoint = ($role === 'admin') ? '/api/admin/history' : '/api/global/history';
        
        $response = $this->api->get($apiEndpoint, $request->query());
        $logs = $response->successful() ? $response->json('data') : [];
        
        return view('history.index', compact('logs', 'role'));
    }

    /**
     * Export data riwayat ke PDF
     */
    public function exportPdf(Request $request)
    {
        $role = session('user_role');
        $apiEndpoint = ($role === 'admin') ? '/api/admin/history' : '/api/global/history';
        
        $response = $this->api->get($apiEndpoint, $request->query());
        $logs = $response->successful() ? $response->json('data') : [];
        
        $pdf = Pdf::loadView('history.pdf', compact('logs', 'role'));
        return $pdf->download('Riwayat_Aktivitas_' . date('Ymd_His') . '.pdf');
    }
}
