<?php

use App\Http\Controllers\AgentSetupController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\MonitoringHistoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QosAnalysisController;
use App\Http\Controllers\QosTestController;
use App\Http\Controllers\RecommendationController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Halaman Utama
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::get('/agent/files/{file}', [AgentSetupController::class, 'file'])
    ->where('file', 'qos-agent\.ps1|install-agent-task\.ps1')
    ->name('agent.files');

/*
|--------------------------------------------------------------------------
| Halaman yang membutuhkan login
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/schools/{school}/agent-setup', [AgentSetupController::class, 'download'])
        ->name('agent.setup');

    Route::get('/monitoring-qos', [MonitoringController::class, 'index'])
        ->name('monitoring.index');

    Route::get('/riwayat-monitoring', [MonitoringHistoryController::class, 'index'])
        ->name('monitoring.history');

    Route::delete('/riwayat-monitoring', [MonitoringHistoryController::class, 'destroy'])
        ->name('monitoring.history.destroy');

    Route::get('/analisis-qos', [QosAnalysisController::class, 'index'])
        ->name('qos.analysis');

    Route::get('/notifikasi', [NotificationController::class, 'index'])
        ->name('notifications.index');

    Route::delete('/notifikasi', [NotificationController::class, 'destroy'])
        ->name('notifications.destroy');

    Route::get('/rekomendasi', [RecommendationController::class, 'index'])
        ->name('recommendations.index');

    Route::post('/dashboard/test-qos/{school}', [
        QosTestController::class,
        'store',
    ])->name('qos.test');

    /*
    |--------------------------------------------------------------------------
    | Pengaturan
    |--------------------------------------------------------------------------
    | Semua pengguna dapat melihat, tetapi hanya admin yang boleh mengubah.
    */

    Route::get('/pengaturan', [SettingsController::class, 'index'])
        ->name('settings.index');

    Route::put('/pengaturan', [SettingsController::class, 'update'])
        ->middleware('admin')
        ->name('settings.update');

    /*
    |--------------------------------------------------------------------------
    | Data Sekolah (khusus admin)
    |--------------------------------------------------------------------------
    */

    Route::middleware('admin')->group(function () {
        Route::post(
            '/schools/{school}/agent-token',
            [SchoolController::class, 'regenerateAgentToken']
        )->name('schools.agent-token');

        Route::resource('schools', SchoolController::class)
            ->only([
                'index',
                'create',
                'store',
                'edit',
                'update',
                'destroy',
            ]);
    });
});
