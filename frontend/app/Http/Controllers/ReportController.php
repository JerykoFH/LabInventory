<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssetsExport;

class ReportController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    public function exportAssetsPdf(Request $request)
    {
        $user = Session::get('api_user');
        $rolePrefix = str_replace('_', '-', $user['role']);
        
        $response = $this->api->get("/api/{$rolePrefix}/assets");
        $assets = $response->successful() ? $response->json('data') : [];

        $pdf = Pdf::loadView('reports.assets_pdf', compact('assets'))
                  ->setPaper('a4', 'landscape');
        
        return $pdf->stream('laporan-aset.pdf');
    }

    public function exportAssetsExcel(Request $request)
    {
        $user = Session::get('api_user');
        $rolePrefix = str_replace('_', '-', $user['role']);
        
        $response = $this->api->get("/api/{$rolePrefix}/assets");
        $assets = $response->successful() ? $response->json('data') : [];

        return Excel::download(new AssetsExport($assets), 'laporan-aset.xlsx');
    }
}
