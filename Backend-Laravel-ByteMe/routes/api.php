<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\AdminProdukController;
use App\Http\Controllers\Api\KeranjangController;

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// Protected routes (butuh token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);
    // Produk public (tidak perlu login)
    Route::get('/produk', [ProdukController::class, 'index']);
    Route::get('/produk/{id}', [ProdukController::class, 'show']);
    // Produk private (perlu login)
    Route::post('/produk', [ProdukController::class, 'store']);
    Route::post('/produk/{id}', [ProdukController::class, 'update']);
    Route::delete('/produk/{id}', [ProdukController::class, 'destroy']);
    Route::get('/my-produk', [ProdukController::class, 'myProduk']);
    Route::get('/keranjang', [KeranjangController::class, 'index']);
    Route::post('/keranjang', [KeranjangController::class, 'store']);
    Route::delete('/keranjang/{detailId}', [KeranjangController::class, 'destroy']);
});

// Admin routes
Route::middleware(['auth:sanctum', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/produk', [AdminProdukController::class, 'allList']);
    Route::get('/produk/pending', [AdminProdukController::class, 'pendingList']);
    Route::patch('/produk/{id}/approve', [AdminProdukController::class, 'approve']);
    Route::patch('/produk/{id}/reject', [AdminProdukController::class, 'reject']);
    Route::delete('/produk/{id}', [AdminProdukController::class, 'destroy']);
});