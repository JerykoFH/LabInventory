<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\ApiClient;
use Illuminate\Support\Facades\Session;

class ScannerController extends Controller
{
    public function __construct(protected ApiClient $api) {}

    public function index()
    {
        $user = Session::get('api_user');
        $rolePrefix = str_replace('_', '-', $user['role']);
        $apiToken = Session::get('api_token');
        
        return view('scanner.index', compact('rolePrefix', 'apiToken'));
    }
}
