<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Public check-in Kiosk Interface
Route::get('/', [AttendanceController::class, 'index'])->name('checkin');
Route::post('/checkin', [AttendanceController::class, 'store'])->name('checkin.store');

Route::post('/register', [MemberController::class, 'publicStore'])->name('members.store');
// Admin Authentication Gateway
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Management Portal (Protected Infrastructure)
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    // Core Reporting Hubs
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/rankings', [AdminController::class, 'rankings'])->name('rankings');

    // Member Operations Management
    Route::get('/members', [MemberController::class, 'index'])->name('members.index');
    Route::post('/members', [MemberController::class, 'store'])->name('members.store');
    Route::put('/members/{member}', [MemberController::class, 'update'])->name('members.update');
    Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');

    // Legacy deactivate route (if still needed)
    Route::patch('/members/{member}/deactivate', [MemberController::class, 'deactivate'])->name('members.deactivate');

    // Aggregation & Data Export Engine
    Route::get('/export', [AdminController::class, 'exportPage'])->name('export');
    Route::get('/export/csv', [AdminController::class, 'exportCsv'])->name('export.csv');
});
