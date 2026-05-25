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
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\FavoritController;
use App\Http\Controllers\Api\KategoriController;   // ← tambah import
use App\Http\Controllers\Api\EmailLogController;   // ← tambah import
use App\Models\Kategori;
use App\Models\EmailLog;

// ─── Public routes ────────────────────────────────────────────────────────────
Route::prefix('auth')->middleware('cors')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

// Webhook Midtrans (public, tidak perlu token)
Route::post('/webhook/midtrans', [PesananController::class, 'webhook']);

// ─── Protected routes ─────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'cors'])->group(function () {

    // Auth
    Route::post('/auth/logout',  [AuthController::class, 'logout']);
    Route::get('/auth/me',       [AuthController::class, 'me']);
    Route::post('/auth/update',  [AuthController::class, 'update'])->name('auth.update');

    // Produk marketplace
    Route::get('/produk',       [ProdukController::class, 'index']);
    Route::get('/produk/{id}',  [ProdukController::class, 'show']);

    // Produk seller
    Route::post('/produk',          [ProdukController::class, 'store']);
    Route::post('/produk/{id}',     [ProdukController::class, 'update']);
    Route::delete('/produk/{id}',   [ProdukController::class, 'destroy']);
    Route::get('/my-produk',        [ProdukController::class, 'myProduk']);

    // Keranjang
    Route::get('/keranjang',               [KeranjangController::class, 'index']);
    Route::post('/keranjang',              [KeranjangController::class, 'store']);
    Route::delete('/keranjang/{detailId}', [KeranjangController::class, 'destroy']);
    Route::post('/checkout',               [PesananController::class, 'checkout']);
    Route::get('/pesanan',                 [PesananController::class, 'index']);
    Route::get('/pesanan/{id}',            [PesananController::class, 'show']);

    // Withdraw
    Route::get('/withdraws',      [WithdrawController::class, 'index']);
    Route::post('/withdraws',     [WithdrawController::class, 'store']);
    Route::get('/withdraws/{id}', [WithdrawController::class, 'show']);

    // Kategori — DIPINDAH ke array syntax agar route:cache bisa berjalan
    Route::get('/seller/orders', [ProdukController::class, 'sellerOrders']);

    Route::get('/kategori', function () {
        return response()->json(\App\Models\Kategori::all());
    });

    Route::get('/favorit',              [FavoritController::class, 'index']);
    Route::post('/favorit',             [FavoritController::class, 'store']);
    Route::delete('/favorit/{produkId}',[FavoritController::class, 'destroy']);
    Route::get('/favorit/check/{produkId}', [FavoritController::class, 'check']);
});

// ─── Admin routes ─────────────────────────────────────────────────────────────
Route::middleware(['auth:sanctum', 'is_admin', 'cors'])->prefix('admin')->group(function () {

    Route::get('/produk',                [AdminProdukController::class, 'allList']);
    Route::get('/produk/pending',        [AdminProdukController::class, 'pendingList']);
    Route::patch('/produk/{id}/approve', [AdminProdukController::class, 'approve']);
    Route::patch('/produk/{id}/reject',  [AdminProdukController::class, 'reject']);
    Route::delete('/produk/{id}',        [AdminProdukController::class, 'destroy']);

    Route::get('/users',              [AdminUserController::class, 'index']);
    Route::get('/users/{id}',         [AdminUserController::class, 'show']);
    Route::patch('/users/{id}/ban',   [AdminUserController::class, 'ban']);
    Route::patch('/users/{id}/unban', [AdminUserController::class, 'unban']);

    Route::get('/withdraws',                [AdminWithdrawController::class, 'index']);
    Route::get('/withdraws/pending',        [AdminWithdrawController::class, 'pendingList']);
    Route::patch('/withdraws/{id}/approve', [AdminWithdrawController::class, 'approve']);
    Route::patch('/withdraws/{id}/reject',  [AdminWithdrawController::class, 'reject']);

    // ------- REVIEW ROUTES (tambahkan di dalam blok auth:sanctum) --------
 
    // Buyer: submit review baru
    Route::post('/review', [ReviewController::class, 'store']);
    
    // Buyer: update review sendiri (per produk)
    Route::patch('/review/{produk_id}', [ReviewController::class, 'update']);
    
    // Buyer: lihat status & review sendiri per produk
    Route::get('/review/status/{produk_id}', [ReviewController::class, 'status']);
    
    // Buyer: semua review yang pernah ditulis
    Route::get('/my-reviews', [ReviewController::class, 'myReviews']);
    
    // ------- REVIEW BY PRODUK (public, tapi tetap butuh token untuk context) --------
    // Ambil semua review + summary rating untuk 1 produk
    Route::get('/produk/{produk_id}/reviews', [ReviewController::class, 'indexByProduk']);

    // Email log — DIPINDAH ke array syntax agar route:cache bisa berjalan
    Route::get('/email-log', function () {
        return response()->json(EmailLog::latest('sent_at')->get());
    });
});
