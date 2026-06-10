<?php

namespace App\Http\Controllers;

use App\Services\ApiClient;
use Illuminate\Http\Request;

class GlobalViewController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    public function assets()
    {
        $response = $this->api->get('/api/global/assets');
        $assets = $response->successful() ? $response->json('data') : [];

        return view('global.assets.index', compact('assets'));
    }

    public function consumables()
    {
        $response = $this->api->get('/api/global/consumables');
        $items = $response->successful() ? $response->json('data') : [];

        return view('global.consumables.index', compact('items'));
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

        return view('global.rooms.show', compact('room', 'assets'));
    }
}
