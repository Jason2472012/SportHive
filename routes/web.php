<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\SuperAdmin\ActivityLogController as SuperAdminActivityLogController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\ReportController as SuperAdminReportController;
use App\Http\Controllers\SuperAdmin\SportController as SuperAdminSportController;
use App\Http\Controllers\SuperAdmin\UserController as SuperAdminUserController;
use App\Http\Controllers\SuperAdmin\VenueController as SuperAdminVenueController;
use App\Http\Controllers\VenueOwner\BookingController as VenueOwnerBookingController;
use App\Http\Controllers\VenueOwner\CourtController as VenueOwnerCourtController;
use App\Http\Controllers\VenueOwner\DashboardController as VenueOwnerDashboardController;
use App\Http\Controllers\VenueOwner\TimeSlotController as VenueOwnerTimeSlotController;
use App\Http\Controllers\VenueOwner\VenueProfileController as VenueOwnerVenueProfileController;
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

        if (auth()->user()->role === 'VENUE_OWNER') {
            return redirect()->route('venueowner.dashboard');
        }

        return view('dashboard');
    })->name('dashboard');

    Route::prefix('superadmin')
        ->name('superadmin.')
        ->middleware('superadmin')
        ->group(function () {
            Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

            // 1. CRUD User & Suspend User
            Route::get('/users', [SuperAdminUserController::class, 'index'])->name('users.index');
            Route::post('/users', [SuperAdminUserController::class, 'store'])->name('users.store');
            Route::put('/users/{user}', [SuperAdminUserController::class, 'update'])->name('users.update');
            Route::patch('/users/{user}/toggle-suspend', [SuperAdminUserController::class, 'toggleSuspend'])->name('users.toggle-suspend');
            Route::delete('/users/{user}', [SuperAdminUserController::class, 'destroy'])->name('users.destroy');

            // 2. Kelola Venue & Validasi Mitra
            Route::get('/venues', [SuperAdminVenueController::class, 'index'])->name('venues.index');
            Route::post('/venues', [SuperAdminVenueController::class, 'store'])->name('venues.store');
            Route::put('/venues/{venue}', [SuperAdminVenueController::class, 'update'])->name('venues.update');
            Route::patch('/venues/{venue}/approve', [SuperAdminVenueController::class, 'approve'])->name('venues.approve');
            Route::patch('/venues/{venue}/reject', [SuperAdminVenueController::class, 'reject'])->name('venues.reject');
            Route::delete('/venues/{venue}', [SuperAdminVenueController::class, 'destroy'])->name('venues.destroy');

            // 3. CRUD Master Data Olahraga & Daftar Skill Level
            Route::get('/sports', [SuperAdminSportController::class, 'index'])->name('sports.index');
            Route::post('/sports', [SuperAdminSportController::class, 'store'])->name('sports.store');
            Route::put('/sports/{sport}', [SuperAdminSportController::class, 'update'])->name('sports.update');
            Route::patch('/sports/{sport}/toggle-status', [SuperAdminSportController::class, 'toggleStatus'])->name('sports.toggle-status');
            Route::delete('/sports/{sport}', [SuperAdminSportController::class, 'destroy'])->name('sports.destroy');

            Route::post('/sports/{sport}/skill-levels', [SuperAdminSportController::class, 'storeSkillLevel'])->name('sports.skill-levels.store');
            Route::put('/skill-levels/{skillLevel}', [SuperAdminSportController::class, 'updateSkillLevel'])->name('skill-levels.update');
            Route::delete('/skill-levels/{skillLevel}', [SuperAdminSportController::class, 'destroySkillLevel'])->name('skill-levels.destroy');

            // 4. Handle Laporan & Feedback
            Route::get('/reports', [SuperAdminReportController::class, 'index'])->name('reports.index');
            Route::patch('/reports/{report}/status', [SuperAdminReportController::class, 'updateStatus'])->name('reports.update-status');
            Route::delete('/reports/{report}', [SuperAdminReportController::class, 'destroy'])->name('reports.destroy');

            // 5. Pantau Statistik & Log Aktivitas
            Route::get('/logs', [SuperAdminActivityLogController::class, 'index'])->name('logs.index');
        });

    Route::prefix('venueowner')
        ->name('venueowner.')
        ->middleware('venueowner')
        ->group(function () {
            // Dashboard Operasional Pengelola Arena
            Route::get('/dashboard', [VenueOwnerDashboardController::class, 'index'])->name('dashboard');

            // 1. Mengelola Profil Venue (Nama arena, alamat, foto fasilitas, kota, deskripsi)
            Route::get('/venues', [VenueOwnerVenueProfileController::class, 'index'])->name('venues.index');
            Route::get('/venues/create', [VenueOwnerVenueProfileController::class, 'create'])->name('venues.create');
            Route::post('/venues', [VenueOwnerVenueProfileController::class, 'store'])->name('venues.store');
            Route::get('/venues/{venue}', [VenueOwnerVenueProfileController::class, 'show'])->name('venues.show');
            Route::get('/venues/{venue}/edit', [VenueOwnerVenueProfileController::class, 'edit'])->name('venues.edit');
            Route::put('/venues/{venue}', [VenueOwnerVenueProfileController::class, 'update'])->name('venues.update');

            // 2. Mengelola Lapangan & Jenis Olahraga
            Route::get('/courts', [VenueOwnerCourtController::class, 'index'])->name('courts.index');
            Route::get('/courts/create', [VenueOwnerCourtController::class, 'create'])->name('courts.create');
            Route::post('/courts', [VenueOwnerCourtController::class, 'store'])->name('courts.store');
            Route::get('/courts/{court}/edit', [VenueOwnerCourtController::class, 'edit'])->name('courts.edit');
            Route::put('/courts/{court}', [VenueOwnerCourtController::class, 'update'])->name('courts.update');
            Route::patch('/courts/{court}/toggle-status', [VenueOwnerCourtController::class, 'toggleStatus'])->name('courts.toggle-status');
            Route::delete('/courts/{court}', [VenueOwnerCourtController::class, 'destroy'])->name('courts.destroy');

            // 3. Mengatur Slot Ketersediaan Waktu (Time Slot) & Harga Sewa Real-Time
            Route::get('/slots', [VenueOwnerTimeSlotController::class, 'index'])->name('slots.index');
            Route::get('/slots/create', [VenueOwnerTimeSlotController::class, 'create'])->name('slots.create');
            Route::post('/slots', [VenueOwnerTimeSlotController::class, 'store'])->name('slots.store');
            Route::post('/slots/bulk', [VenueOwnerTimeSlotController::class, 'storeBulk'])->name('slots.bulk');
            Route::put('/slots/{slot}', [VenueOwnerTimeSlotController::class, 'update'])->name('slots.update');
            Route::patch('/slots/{slot}/toggle-status', [VenueOwnerTimeSlotController::class, 'toggleStatus'])->name('slots.toggle-status');
            Route::delete('/slots/{slot}', [VenueOwnerTimeSlotController::class, 'destroy'])->name('slots.destroy');

            // 4. Memantau Daftar Reservasi Masuk & Operasional Lapangan (Check-in E-Ticket)
            Route::get('/bookings', [VenueOwnerBookingController::class, 'index'])->name('bookings.index');
            Route::get('/bookings/{booking}', [VenueOwnerBookingController::class, 'show'])->name('bookings.show');
            Route::patch('/bookings/{booking}/check-in', [VenueOwnerBookingController::class, 'checkIn'])->name('bookings.check-in');
            Route::patch('/bookings/{booking}/confirm-payment', [VenueOwnerBookingController::class, 'confirmPayment'])->name('bookings.confirm-payment');
            Route::patch('/bookings/{booking}/cancel', [VenueOwnerBookingController::class, 'cancel'])->name('bookings.cancel');
        });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
