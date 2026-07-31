<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Livewire\BwoaVisualizer;
use App\Livewire\DetectionSimulator;
use App\Livewire\LiveMonitor;
use App\Livewire\DeviceManager;
use App\Livewire\AdminDashboard;
use App\Http\Controllers\Api\ExternalApiController;
use Illuminate\Support\Facades\Route;

// Public Pages (Accessible to Guests and Logged In users)
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/api-docs', function () {
    return view('dashboard.api-docs');
})->name('api-docs');

Route::get('/model-insights', function () {
    return redirect()->route('research');
})->name('model-insights');

Route::get('/research', function () {
    return view('dashboard.research');
})->name('research');

// Auth Routes (Guest Only)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/signup', [AuthController::class, 'showSignup'])->name('signup');
    Route::post('/signup', [AuthController::class, 'signup']);
});

use App\Livewire\UserSettings;

// Authenticated Dashboards & Interactive Features
Route::middleware('auth')->group(function () {
    Route::get('/bwoa', BwoaVisualizer::class)->name('bwoa');
    Route::get('/simulator', DetectionSimulator::class)->name('simulator');
    Route::get('/live-monitor', LiveMonitor::class)->name('live-monitor');
    Route::get('/devices', DeviceManager::class)->name('devices');
    Route::get('/settings', UserSettings::class)->name('settings');
    Route::get('/admin/dashboard', AdminDashboard::class)->name('admin-dashboard');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// External REST API (Uses custom token header verification)
Route::prefix('api/external')->group(function () {
    Route::match(['get', 'post'], '/analyze', [ExternalApiController::class, 'analyze'])->name('api.analyze');
    Route::get('/status', [ExternalApiController::class, 'status'])->name('api.status');
});
