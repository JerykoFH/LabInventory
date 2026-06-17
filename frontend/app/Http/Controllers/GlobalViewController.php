<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;

class GlobalViewController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    public function assets(Request $request)
    {
        $allResp = $this->api->get('/api/global/assets');
        $allData = $allResp->successful() ? $allResp->json('data') : [];
        $categories = collect($allData)->pluck('category')->filter()->unique()->values();

        $response = $this->api->get('/api/global/assets', $request->query());
        $assets = $response->successful() ? $response->json('data') : [];

        $roomsResponse = $this->api->get('/api/global/rooms');
        $rooms = $roomsResponse->successful() ? $roomsResponse->json('data') : [];

        return view('global.assets.index', compact('assets', 'rooms', 'categories'));
    }

    public function showAsset(string $id)
    {
        $response = $this->api->get("/api/global/assets/{$id}");
        if (!$response->successful()) {
            return redirect()->route('global.assets.index')->with('error', 'Aset tidak ditemukan.');
        }
        $asset = $response->json('data');

        return view('global.assets.show', compact('asset'));
    }

    public function consumables(Request $request)
    {
        $allResp = $this->api->get('/api/global/consumables');
        $allData = $allResp->successful() ? $allResp->json('data') : [];
        $categories = collect($allData)->pluck('category')->filter()->unique()->values();
        $locations = collect($allData)->pluck('location')->filter()->unique()->values();

        $response = $this->api->get('/api/global/consumables', $request->query());
        $items = $response->successful() ? $response->json('data') : [];

        return view('global.consumables.index', compact('items', 'categories', 'locations'));
    }

    public function rooms()
    {
        $response = $this->api->get('/api/global/rooms');
        $rooms = $response->successful() ? $response->json('data') : [];

        return view('global.rooms.index', compact('rooms'));
    }

    public function roomAssets(string $id)
    {
        $roomsResponse = $this->api->get('/api/global/rooms');
        $rooms = $roomsResponse->successful() ? $roomsResponse->json('data') : [];
        $room = collect($rooms)->firstWhere('_id', $id);

        $assetsResponse = $this->api->get("/api/global/rooms/{$id}/assets");
        $assets = $assetsResponse->successful() ? $assetsResponse->json('data') : [];

        $consumablesResponse = $this->api->get('/api/global/consumables');
        $consumables = [];
        if ($consumablesResponse->successful() && $room) {
            $allConsumables = $consumablesResponse->json('data');
            $consumables = collect($allConsumables)->filter(function($item) use ($room) {
                $loc = strtolower($item['location'] ?? '');
                $rName = strtolower($room['name'] ?? '');
                $keywords = ['jaringan', 'pemrograman', 'multimedia', 'basis data'];
                foreach ($keywords as $keyword) {
                    if (str_contains($loc, $keyword) && str_contains($rName, $keyword)) {
                        return true;
                    }
                }
                return false;
            })->values()->all();
        }

        return view('global.rooms.show', compact('room', 'assets', 'consumables'));
    }
}
