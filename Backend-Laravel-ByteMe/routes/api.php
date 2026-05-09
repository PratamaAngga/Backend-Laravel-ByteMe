<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\AdminProdukController;
use App\Http\Controllers\Api\AdminUserController;
use App\Http\Controllers\Api\AdminWithdrawController;
use App\Http\Controllers\Api\KeranjangController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\WithdrawController;

// ─── Public routes ────────────────────────────────────────────────────────────
Route::prefix('auth')->middleware('cors')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// Webhook Midtrans (public, tidak perlu token)
Route::post('/webhook/midtrans', [PesananController::class, 'webhook']);
// ─── Protected routes (perlu token + cek banned) ──────────────────────────────
Route::middleware(['auth:sanctum', 'cors'])->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);
    Route::patch('/auth/me',    [AuthController::class, 'update']); // Edit profile

    // Produk marketplace (semua user login)
    Route::get('/produk',       [ProdukController::class, 'index']);
    Route::get('/produk/{id}',  [ProdukController::class, 'show']);

    // Produk seller
    Route::post('/produk',          [ProdukController::class, 'store']);
    Route::post('/produk/{id}',     [ProdukController::class, 'update']); // POST karena ada file upload
    Route::delete('/produk/{id}',   [ProdukController::class, 'destroy']);
    Route::get('/my-produk',        [ProdukController::class, 'myProduk']);

    // Keranjang
    Route::get('/keranjang',                    [KeranjangController::class, 'index']);
    Route::post('/keranjang',                   [KeranjangController::class, 'store']);
    Route::delete('/keranjang/{detailId}',      [KeranjangController::class, 'destroy']);
    Route::post('/checkout', [PesananController::class, 'checkout']);
    Route::get('/pesanan', [PesananController::class, 'index']);
    Route::get('/pesanan/{id}', [PesananController::class, 'show']);

    // Withdraw (khusus seller)
    Route::get('/withdraws',        [WithdrawController::class, 'index']);
    Route::post('/withdraws',       [WithdrawController::class, 'store']);
    Route::get('/withdraws/{id}',   [WithdrawController::class, 'show']);
});

// ─── Admin routes ──────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'is_admin', 'cors'])->prefix('admin')->group(function () {

    // Manajemen produk
    Route::get('/produk',                       [AdminProdukController::class, 'allList']);
    Route::get('/produk/pending',               [AdminProdukController::class, 'pendingList']);
    Route::patch('/produk/{id}/approve',        [AdminProdukController::class, 'approve']);
    Route::patch('/produk/{id}/reject',         [AdminProdukController::class, 'reject']);
    Route::delete('/produk/{id}',               [AdminProdukController::class, 'destroy']);

    // Manajemen user
    Route::get('/users',                        [AdminUserController::class, 'index']);
    Route::get('/users/{id}',                   [AdminUserController::class, 'show']);
    Route::patch('/users/{id}/ban',             [AdminUserController::class, 'ban']);
    Route::patch('/users/{id}/unban',           [AdminUserController::class, 'unban']);

    // Manajemen withdraw
    Route::get('/withdraws',                    [AdminWithdrawController::class, 'index']);
    Route::get('/withdraws/pending',            [AdminWithdrawController::class, 'pendingList']);
    Route::patch('/withdraws/{id}/approve',     [AdminWithdrawController::class, 'approve']);
    Route::patch('/withdraws/{id}/reject',      [AdminWithdrawController::class, 'reject']);
    Route::get('/email-log', function() {
        return response()->json(
            \App\Models\EmailLog::latest('sent_at')->get()
        );
    });
});
