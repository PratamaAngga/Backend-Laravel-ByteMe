<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\DetailKeranjang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KeranjangController extends Controller
{
    // Lihat isi keranjang
    public function index(Request $request)
    {
        $keranjang = Keranjang::where('user_id', $request->user()->id)->first();

        if (!$keranjang) {
            return response()->json([
                'message'    => 'Keranjang kosong',
                'keranjang'  => null,
                'items'      => [],
            ]);
        }

        $items = DetailKeranjang::where('keranjang_id', $keranjang->keranjang_id)
            ->with(['produk:produk_id,nama_produk,harga,file_path'])
            ->get();

        return response()->json([
            'keranjang' => $keranjang,
            'items'     => $items,
        ]);
    }

    // Tambah produk ke keranjang
    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|uuid',
        ]);

        $user = $request->user();

        // Cek produk exist dan statusnya approved
        $produk = Produk::where('produk_id', $request->produk_id)
            ->where('status', 'approved')
            ->first();

        if (!$produk) {
            return response()->json([
                'message' => 'Produk tidak ditemukan atau belum tersedia'
            ], 404);
        }

        // Cek apakah produk milik seller sendiri
        if ($produk->user_id === $user->id) {
            return response()->json([
                'message' => 'Tidak bisa membeli produk sendiri'
            ], 403);
        }

        // Ambil atau buat keranjang user
        $keranjang = Keranjang::firstOrCreate(
            ['user_id' => $user->id],
            [
                'keranjang_id' => Str::uuid(),
                'user_id'      => $user->id,
                'total_item'   => 0,
            ]
        );

        // Cek apakah produk sudah ada di keranjang
        $existingItem = DetailKeranjang::where('keranjang_id', $keranjang->keranjang_id)
            ->where('produk_id', $request->produk_id)
            ->first();

        if ($existingItem) {
            return response()->json([
                'message' => 'Produk sudah ada di keranjang'
            ], 409);
        }

        // Tambah item ke keranjang
        DetailKeranjang::create([
            'detail_keranjang_id' => Str::uuid(),
            'keranjang_id'        => $keranjang->keranjang_id,
            'produk_id'           => $request->produk_id,
            'jumlah'              => 1,
            'harga_satuan'        => $produk->harga,
        ]);

        // Update total item keranjang
        $totalItem = DetailKeranjang::where('keranjang_id', $keranjang->keranjang_id)->count();
        $keranjang->total_item = $totalItem;
        $keranjang->save();

        return response()->json([
            'message'   => 'Produk berhasil ditambahkan ke keranjang',
            'keranjang' => $keranjang,
        ], 201);
    }

    // Hapus item dari keranjang
    public function destroy(Request $request, string $detailId)
    {
        $user = $request->user();

        $keranjang = Keranjang::where('user_id', $user->id)->first();

        if (!$keranjang) {
            return response()->json(['message' => 'Keranjang tidak ditemukan'], 404);
        }

        $item = DetailKeranjang::where('detail_keranjang_id', $detailId)
            ->where('keranjang_id', $keranjang->keranjang_id)
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Item tidak ditemukan di keranjang'], 404);
        }

        $item->delete();

        // Hitung ulang total_item (biar selalu akurat)
        $totalItem = DetailKeranjang::where('keranjang_id', $keranjang->keranjang_id)->count();
        $keranjang->total_item = $totalItem;
        $keranjang->save();

        return response()->json(['message' => 'Item berhasil dihapus dari keranjang']);
    }
}