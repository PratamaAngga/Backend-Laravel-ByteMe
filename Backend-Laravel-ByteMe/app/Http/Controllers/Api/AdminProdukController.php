<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;

class AdminProdukController extends Controller
{
    // List semua produk pending (untuk direview admin)
    public function pendingList()
    {
        $produk = Produk::where('status', 'pending')
            ->latest()
            ->get();

        return response()->json($produk);
    }

    // List semua produk (semua status)
    public function allList()
    {
        $produk = Produk::latest()->get();
        return response()->json($produk);
    }

    // Approve produk
    public function approve(string $id)
    {
        $produk = Produk::where('produk_id', $id)->first();

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $produk->status = 'approved';
        $produk->save();

        return response()->json([
            'message' => 'Produk berhasil diapprove',
            'produk'  => $produk,
        ]);
    }

    // Reject produk
    public function reject(Request $request, string $id)
    {
        $request->validate([
            'alasan' => 'required|string',
        ]);

        $produk = Produk::where('produk_id', $id)->first();

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $produk->status = 'nonaktif';
        $produk->save();

        // Nanti bisa ditambah notifikasi ke seller di sini

        return response()->json([
            'message' => 'Produk berhasil direject',
            'alasan'  => $request->alasan,
            'produk'  => $produk,
        ]);
    }

    // Hapus produk (soft delete)
    public function destroy(string $id)
    {
        $produk = Produk::where('produk_id', $id)->first();

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $produk->status = 'dihapus';
        $produk->save();

        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}