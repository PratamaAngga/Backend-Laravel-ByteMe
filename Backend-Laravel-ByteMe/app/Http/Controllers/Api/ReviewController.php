<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailPesanan;
use App\Models\Pesanan;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    /**
     * Cek apakah buyer sudah membeli produk ini (status paid).
     * Hanya buyer yang pernah beli boleh review.
     */
    private function sudahBeli(string $userId, string $produkId): bool
    {
        return DetailPesanan::whereHas('pesanan', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('status', 'paid');
        })->where('produk_id', $produkId)->exists();
    }

    /**
     * Buyer submit review untuk produk yang sudah dibeli.
     * POST /api/review
     */
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|uuid|exists:produk,produk_id',
            'rating'    => 'required|integer|min:1|max:5',
            'komentar'  => 'nullable|string|max:1000',
        ]);

        $user = $request->user();

        // Pastikan role buyer
        if ($user->role !== 'buyer') {
            return response()->json([
                'message' => 'Hanya buyer yang bisa memberikan review',
            ], 403);
        }

        // Pastikan sudah pernah membeli produk ini
        if (!$this->sudahBeli($user->id, $request->produk_id)) {
            return response()->json([
                'message' => 'Kamu belum pernah membeli produk ini',
            ], 403);
        }

        // Cek sudah pernah review (unique constraint di DB juga ada)
        $existing = Review::where('user_id', $user->id)
            ->where('produk_id', $request->produk_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Kamu sudah pernah memberikan review untuk produk ini',
                'review'  => $this->formatReview($existing),
            ], 409);
        }

        $review = Review::create([
            'review_id'  => (string) Str::uuid(),
            'user_id'    => $user->id,
            'produk_id'  => $request->produk_id,
            'rating'     => $request->rating,
            'komentar'   => $request->komentar,
            'tgl_review' => now(),
        ]);

        return response()->json([
            'message' => 'Review berhasil dikirim',
            'review'  => $this->formatReview($review->load('user')),
        ], 201);
    }

    /**
     * Update review yang sudah ada (buyer edit review sendiri).
     * PATCH /api/review/{produk_id}
     */
    public function update(Request $request, string $produkId)
    {
        $request->validate([
            'rating'   => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);

        $user   = $request->user();
        $review = Review::where('user_id', $user->id)
            ->where('produk_id', $produkId)
            ->first();

        if (!$review) {
            return response()->json(['message' => 'Review tidak ditemukan'], 404);
        }

        $review->update([
            'rating'   => $request->rating,
            'komentar' => $request->komentar,
        ]);

        return response()->json([
            'message' => 'Review berhasil diperbarui',
            'review'  => $this->formatReview($review->load('user')),
        ]);
    }

    /**
     * Ambil semua review untuk satu produk (publik, untuk halaman produk & seller).
     * GET /api/produk/{produk_id}/reviews
     */
    public function indexByProduk(string $produkId)
    {
        $reviews = Review::with('user:id,username,profile_image')
            ->where('produk_id', $produkId)
            ->orderByDesc('tgl_review')
            ->get();

        $summary = $this->buildSummary($produkId);

        return response()->json([
            'produk_id' => $produkId,
            'summary'   => $summary,
            'reviews'   => $reviews->map(fn ($r) => $this->formatReview($r)),
        ]);
    }

    /**
     * Riwayat review milik buyer yang login.
     * GET /api/my-reviews
     */
    public function myReviews(Request $request)
    {
        $reviews = Review::with('produk:produk_id,nama_produk,file_path')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('tgl_review')
            ->get();

        return response()->json(
            $reviews->map(fn ($r) => $this->formatReview($r))
        );
    }

    /**
     * Cek apakah buyer sudah review produk tertentu & apakah boleh review.
     * GET /api/review/status/{produk_id}
     */
    public function status(Request $request, string $produkId)
    {
        $user   = $request->user();
        $review = Review::where('user_id', $user->id)
            ->where('produk_id', $produkId)
            ->first();

        $sudahBeli = $this->sudahBeli($user->id, $produkId);

        return response()->json([
            'sudah_beli'   => $sudahBeli,
            'sudah_review' => $review !== null,
            'review'       => $review ? $this->formatReview($review) : null,
        ]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function formatReview(Review $r): array
    {
        return [
            'review_id'  => $r->review_id,
            'user_id'    => $r->user_id,
            'produk_id'  => $r->produk_id,
            'rating'     => $r->rating,
            'komentar'   => $r->komentar,
            'tgl_review' => $r->tgl_review,
            'username'   => $r->user->username ?? null,
            'avatar'     => $r->user->profile_image ?? null,
            'nama_produk'=> $r->produk->nama_produk ?? null,
            'file_path'  => $r->produk->file_path ?? null,
        ];
    }

    private function buildSummary(string $produkId): array
    {
        $rows = Review::where('produk_id', $produkId)
            ->selectRaw('rating, COUNT(*) as jumlah')
            ->groupBy('rating')
            ->pluck('jumlah', 'rating')
            ->toArray();

        $total = array_sum($rows);
        $avg   = $total > 0
            ? round(array_sum(array_map(
                fn ($r, $j) => $r * $j, array_keys($rows), $rows
            )) / $total, 1)
            : 0.0;

        // Distribusi bintang 1–5
        $distribusi = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribusi[$i] = $rows[$i] ?? 0;
        }

        return [
            'rata_rata'  => $avg,
            'total'      => $total,
            'distribusi' => $distribusi,   // {1:0, 2:0, 3:1, 4:3, 5:10}
        ];
    }
}