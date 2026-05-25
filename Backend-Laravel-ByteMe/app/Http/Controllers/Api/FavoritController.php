<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Favorit;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FavoritController extends Controller
{
    // Lihat daftar favorit buyer
    public function index(Request $request)
    {
        $favorit = Favorit::where('user_id', $request->user()->id)
            ->with(['produk:produk_id,nama_produk,harga,file_path,status'])
            ->latest()
            ->get();

        return response()->json([
            'message' => 'Daftar favorit berhasil diambil',
            'data'    => $favorit,
        ]);
    }

    // Tambah ke favorit
    public function store(Request $request)
    {
        $user = $request->user();

        // Cek role buyer
        if ($user->role !== 'buyer') {
            return response()->json([
                'message' => 'Hanya buyer yang bisa menambahkan favorit'
            ], 403);
        }

        $request->validate([
            'produk_id' => 'required|uuid',
        ]);

        // Cek produk exist dan approved
        $produk = Produk::where('produk_id', $request->produk_id)
            ->where('status', 'approved')
            ->first();

        if (!$produk) {
            return response()->json([
                'message' => 'Produk tidak ditemukan atau belum tersedia'
            ], 404);
        }

        // Cek apakah sudah difavoritin
        $existing = Favorit::where('user_id', $user->id)
            ->where('produk_id', $request->produk_id)
            ->first();

        if ($existing) {
            return response()->json([
                'message' => 'Produk sudah ada di favorit'
            ], 409);
        }

        $favorit = Favorit::create([
            'favorit_id' => Str::uuid(),
            'user_id'    => $user->id,
            'produk_id'  => $request->produk_id,
        ]);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan ke favorit',
            'data'    => $favorit,
        ], 201);
    }

    // Hapus dari favorit
    public function destroy(Request $request, string $produkId)
    {
        $user = $request->user();

        $favorit = Favorit::where('user_id', $user->id)
            ->where('produk_id', $produkId)
            ->first();

        if (!$favorit) {
            return response()->json([
                'message' => 'Produk tidak ditemukan di favorit'
            ], 404);
        }

        $favorit->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus dari favorit'
        ]);
    }

    // Cek apakah produk sudah difavoritin
    public function check(Request $request, string $produkId)
    {
        $isFavorit = Favorit::where('user_id', $request->user()->id)
            ->where('produk_id', $produkId)
            ->exists();

        return response()->json([
            'is_favorit' => $isFavorit,
        ]);
    }
}