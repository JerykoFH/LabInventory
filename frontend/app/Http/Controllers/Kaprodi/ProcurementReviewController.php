<?php

namespace App\Http\Controllers\Kaprodi;

use App\Http\Controllers\Controller;
use App\Services\ApiClient;
use Illuminate\Http\Request;

/**
 * Kaprodi\ProcurementReviewController
 * Review dan finalisasi draf pengadaan dari Kepala Lab
 */
class ProcurementReviewController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    /** GET /kaprodi/procurements */
    public function index()
    {
        $response = $this->api->get('/api/kaprodi/procurements');
        $drafts = $response->successful() ? $response->json('data') : [];

        return view('kaprodi.procurements.index', compact('drafts'));
    }

    /** GET /kaprodi/procurements/{id} */
    public function show(string $id)
    {
        $response = $this->api->get("/api/kaprodi/procurements/{$id}");
        $draft = $response->successful() ? $response->json('data') : null;

        return view('kaprodi.procurements.show', compact('draft'));
    }

    /** PATCH /kaprodi/procurements/{id}/items/{itemId}/review */
    public function reviewItem(Request $request, string $id, string $itemId)
    {
        $validated = $request->validate([
            'approvalStatus'  => 'required|in:approved,rejected',
            'rejectionReason' => 'nullable|required_if:approvalStatus,rejected|string',
        ]);

        $response = $this->api->patch(
            "/api/kaprodi/procurements/{$id}/items/{$itemId}/review",
            $validated
        );

        if ($response->successful()) {
            return redirect()->route('kaprodi.procurements.show', $id)
                ->with('success', 'Status item berhasil diperbarui.');
        }

        return back()->withErrors($response->json('message'));
    }

    /** POST /kaprodi/procurements/{id}/finalize */
    public function finalize(string $id)
    {
        $response = $this->api->post("/api/kaprodi/procurements/{$id}/finalize");

        if ($response->successful()) {
            return redirect()->route('kaprodi.procurements.index')
                ->with('success', 'Draf pengadaan berhasil difinalisasi dan dikunci.');
        }

        return back()->withErrors($response->json('message'));
    }
}
