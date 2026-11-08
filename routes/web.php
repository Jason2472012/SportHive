<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\SuperAdmin\ActivityLogController as SuperAdminActivityLogController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\ReportController as SuperAdminReportController;
use App\Http\Controllers\SuperAdmin\SportController as SuperAdminSportController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\VenueController as SuperAdminVenueController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('register.store');

    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'SUPER_ADMIN') {
            return redirect()->route('superadmin.dashboard');
        }

        return view('dashboard');
    })->name('dashboard');

    Route::prefix('superadmin')
        ->name('superadmin.')
        ->middleware('superadmin')
        ->group(function () {
            Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

            Route::get('/users', [SuperAdminUserController::class, 'index'])->name('users.index');
            Route::post('/users', [SuperAdminUserController::class, 'store'])->name('users.store');
            Route::put('/users/{user}', [SuperAdminUserController::class, 'update'])->name('users.update');
            Route::patch('/users/{user}/toggle-suspend', [SuperAdminUserController::class, 'toggleSuspend'])->name('users.toggle-suspend');
            Route::delete('/users/{user}', [SuperAdminUserController::class, 'destroy'])->name('users.destroy');

            Route::get('/venues', [SuperAdminVenueController::class, 'index'])->name('venues.index');
            Route::post('/venues', [SuperAdminVenueController::class, 'store'])->name('venues.store');
            Route::put('/venues/{venue}', [SuperAdminVenueController::class, 'update'])->name('venues.update');
            Route::patch('/venues/{venue}/approve', [SuperAdminVenueController::class, 'approve'])->name('venues.approve');
            Route::patch('/venues/{venue}/reject', [SuperAdminVenueController::class, 'reject'])->name('venues.reject');
            Route::delete('/venues/{venue}', [SuperAdminVenueController::class, 'destroy'])->name('venues.destroy');

            Route::get('/sports', [SuperAdminSportController::class, 'index'])->name('sports.index');
            Route::post('/sports', [SuperAdminSportController::class, 'store'])->name('sports.store');
            Route::put('/sports/{sport}', [SuperAdminSportController::class, 'update'])->name('sports.update');
            Route::patch('/sports/{sport}/toggle-status', [SuperAdminSportController::class, 'toggleStatus'])->name('sports.toggle-status');
            Route::delete('/sports/{sport}', [SuperAdminSportController::class, 'destroy'])->name('sports.destroy');

            Route::post('/sports/{sport}/skill-levels', [SuperAdminSportController::class, 'storeSkillLevel'])->name('sports.skill-levels.store');
            Route::put('/skill-levels/{skillLevel}', [SuperAdminSportController::class, 'updateSkillLevel'])->name('skill-levels.update');
            Route::delete('/skill-levels/{skillLevel}', [SuperAdminSportController::class, 'destroySkillLevel'])->name('skill-levels.destroy');

            Route::get('/reports', [SuperAdminReportController::class, 'index'])->name('reports.index');
            Route::patch('/reports/{report}/status', [SuperAdminReportController::class, 'updateStatus'])->name('reports.update-status');
            Route::delete('/reports/{report}', [SuperAdminReportController::class, 'destroy'])->name('reports.destroy');

            Route::get('/logs', [SuperAdminActivityLogController::class, 'index'])->name('logs.index');
        });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
