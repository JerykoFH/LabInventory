<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\KepalaLab\ProcurementController;
use App\Http\Controllers\Kaprodi\ProcurementReviewController;
use App\Http\Controllers\StafAdmin\InventoryController;
use App\Http\Controllers\StafLab\ConsumableController;
use App\Http\Controllers\StafLab\MaintenanceController;
use App\Http\Controllers\GlobalViewController;

// Public Routes
Route::get('/',       fn () => redirect()->route('login'));
Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard (semua role)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['api.auth'])
    ->name('dashboard');

// History/Log (semua role)
use App\Http\Controllers\HistoryController;
Route::get('/history', [HistoryController::class, 'index'])
    ->middleware(['api.auth'])
    ->name('history.index');
Route::get('/history/pdf', [HistoryController::class, 'exportPdf'])
    ->middleware(['api.auth'])
    ->name('history.pdf');

// Administrator 
Route::prefix('admin')->name('admin.')->middleware(['api.auth', 'role:admin'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('rooms', RoomController::class);
});

// Kepala Laboratorium 
Route::prefix('kepala-lab')->name('kepala-lab.')->middleware(['api.auth', 'role:kepala_lab'])->group(function () {
    Route::get('procurements',                                 [ProcurementController::class, 'index'])->name('procurements.index');
    Route::get('procurements/create',                          [ProcurementController::class, 'create'])->name('procurements.create');
    Route::post('procurements',                                [ProcurementController::class, 'store'])->name('procurements.store');
    Route::get('procurements/{id}',                            [ProcurementController::class, 'show'])->name('procurements.show');
    Route::get('procurements/{id}/edit',                       [ProcurementController::class, 'edit'])->name('procurements.edit');
    Route::put('procurements/{id}',                            [ProcurementController::class, 'update'])->name('procurements.update');
    Route::delete('procurements/{id}',                         [ProcurementController::class, 'destroy'])->name('procurements.destroy');
    Route::post('procurements/{id}/submit',                    [ProcurementController::class, 'submit'])->name('procurements.submit');
    Route::post('procurements/{id}/items',                     [ProcurementController::class, 'addItem'])->name('procurements.items.add');
    Route::put('procurements/{id}/items/{itemId}',             [ProcurementController::class, 'updateItem'])->name('procurements.items.update');
    Route::delete('procurements/{id}/items/{itemId}',          [ProcurementController::class, 'removeItem'])->name('procurements.items.remove');

    // Reports
    Route::get('reports/assets/pdf',                           [\App\Http\Controllers\ReportController::class, 'exportAssetsPdf'])->name('reports.assets.pdf');
    Route::get('reports/assets/excel',                         [\App\Http\Controllers\ReportController::class, 'exportAssetsExcel'])->name('reports.assets.excel');
});

// Ketua Program Studi (Kaprodi)
Route::prefix('kaprodi')->name('kaprodi.')->middleware(['api.auth', 'role:kaprodi'])->group(function () {
    Route::get('procurements',                                    [ProcurementReviewController::class, 'index'])->name('procurements.index');
    Route::get('procurements/{id}',                               [ProcurementReviewController::class, 'show'])->name('procurements.show');
    Route::patch('procurements/{id}/items/{itemId}/review',       [ProcurementReviewController::class, 'reviewItem'])->name('procurements.items.review');
    Route::post('procurements/{id}/finalize',                     [ProcurementReviewController::class, 'finalize'])->name('procurements.finalize');

    // Reports
    Route::get('reports/assets/pdf',                              [\App\Http\Controllers\ReportController::class, 'exportAssetsPdf'])->name('reports.assets.pdf');
    Route::get('reports/assets/excel',                            [\App\Http\Controllers\ReportController::class, 'exportAssetsExcel'])->name('reports.assets.excel');
});

// Staf Administrasi
Route::prefix('staf-admin')->name('staf-admin.')->middleware(['api.auth', 'role:staf_admin'])->group(function () {
    Route::get('procurements',         [InventoryController::class, 'procurements'])->name('procurements.index');
    Route::get('procurements/{id}',    [InventoryController::class, 'procurementDetail'])->name('procurements.show');
    Route::patch('procurements/{id}/progress', [InventoryController::class, 'setProgress'])->name('procurements.progress');
    Route::patch('procurements/{id}/items/{itemId}/receive', [InventoryController::class, 'receiveItem'])->name('procurements.items.receive');
    Route::get('assets',               [InventoryController::class, 'assets'])->name('assets.index');
    Route::get('assets/{id}',          [InventoryController::class, 'show'])->name('assets.show');
    Route::patch('assets/{id}/label',  [InventoryController::class, 'updateLabel'])->name('assets.label');
    Route::patch('assets/{id}/receive', [InventoryController::class, 'updateReceivedDate'])->name('assets.receive');
    Route::patch('assets/{id}/condition', [InventoryController::class, 'updateCondition'])->name('assets.condition');

    // Scanner
    Route::get('scanner',              [\App\Http\Controllers\ScannerController::class, 'index'])->name('scanner.index');
});

// Staf Laboratorium 
Route::prefix('staf-lab')->name('staf-lab.')->middleware(['api.auth', 'role:staf_lab'])->group(function () {
    // BHP
    Route::get('consumables',                    [ConsumableController::class, 'index'])->name('consumables.index');
    Route::get('consumables/create',             [ConsumableController::class, 'create'])->name('consumables.create');
    Route::post('consumables',                   [ConsumableController::class, 'store'])->name('consumables.store');
    Route::patch('consumables/{id}/stock',       [ConsumableController::class, 'adjustStock'])->name('consumables.stock');

    // Maintenance
    Route::get('rooms/{id}/assets',              [MaintenanceController::class, 'getAssetsByRoom'])->name('rooms.assets');
    Route::get('maintenance',                    [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('maintenance/create',             [MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('maintenance',                   [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::get('maintenance/{id}',               [MaintenanceController::class, 'show'])->name('maintenance.show');

    // Scanner
    Route::get('scanner',                        [\App\Http\Controllers\ScannerController::class, 'index'])->name('scanner.index');
});

// Global Views (semua role kecuali admin — read-only)
Route::prefix('global')->name('global.')->middleware(['api.auth', 'role:kepala_lab,kaprodi,staf_admin,staf_lab'])->group(function () {
    Route::get('assets',          [GlobalViewController::class, 'assets'])->name('assets.index');
    Route::get('consumables',     [GlobalViewController::class, 'consumables'])->name('consumables.index');
    Route::get('rooms',           [GlobalViewController::class, 'rooms'])->name('rooms.index');
    Route::get('rooms/{id}',      [GlobalViewController::class, 'roomAssets'])->name('rooms.show');
});
