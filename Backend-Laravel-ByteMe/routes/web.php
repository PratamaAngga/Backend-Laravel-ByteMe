<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AdminWebController;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('admin.login');
});

// Admin web routes
Route::prefix('admin')->group(function () {
    Route::get('/login', [AdminWebController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [AdminWebController::class, 'login']);
    Route::post('/logout', [AdminWebController::class, 'logout'])->name('admin.logout');

    // Protected admin routes
    Route::middleware('auth:web')->group(function () {
        Route::get('/dashboard', [AdminWebController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/produk-pending', [AdminWebController::class, 'produkPending'])->name('admin.produk.pending');
        Route::patch('/produk/{id}/approve', [AdminWebController::class, 'approveProduk'])->name('admin.produk.approve');
        Route::patch('/produk/{id}/reject', [AdminWebController::class, 'rejectProduk'])->name('admin.produk.reject');
        Route::get('/users', [AdminWebController::class, 'users'])->name('admin.users');
        Route::patch('/users/{id}/ban', [AdminWebController::class, 'banUser'])->name('admin.users.ban');
        Route::patch('/users/{id}/unban', [AdminWebController::class, 'unbanUser'])->name('admin.users.unban');
        Route::get('/withdraws', [AdminWebController::class, 'withdraws'])->name('admin.withdraws');
        Route::patch('/withdraws/{id}/approve', [AdminWebController::class, 'approveWithdraw'])->name('admin.withdraws.approve');
        Route::patch('/withdraws/{id}/reject', [AdminWebController::class, 'rejectWithdraw'])->name('admin.withdraws.reject');
        Route::get('/profile', [AdminWebController::class, 'profile'])->name('admin.profile');
        Route::patch('/profile', [AdminWebController::class, 'updateProfile'])->name('admin.profile.update');
    });
});
