<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Produk;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    protected SupabaseStorageService $storage;

    public function __construct(SupabaseStorageService $storage)
    {
        $this->storage = $storage;
    }

    // 1. List semua produk approved (Halaman Marketplace Pembeli)
    public function index()
    {
        $produkTable = (new Produk())->getTable();

        // 🌟 FIX URUTAN: select() ditaruh di paling atas agar tidak menghapus fungsi withAvg/withCount
        $produk = Produk::select($produkTable . '.*')
            ->with('categories')
            ->with('peninjauan')
            ->withAvg('reviews', 'rating')  
            ->withCount('reviews')          
            ->selectRaw("(SELECT COALESCE(SUM(qty_terjual), 0) FROM v_riwayat_penjualan_produk_v2 WHERE v_riwayat_penjualan_produk_v2.produk_id = {$produkTable}.produk_id AND LOWER(status_pembayaran) IN ('success', 'paid', 'settlement')) as qty_terjual")
            ->where('status', 'approved')
            ->latest()
            ->get();

        return response()->json($produk);
    }

    // 2. Detail satu produk (Saat diklik oleh pembeli)
    public function show(string $id)
    {
        $produkTable = (new Produk())->getTable();

        // 🌟 FIX URUTAN: select() ditaruh di paling atas
        $produk = Produk::select($produkTable . '.*')
            ->with('categories')
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->selectRaw("(SELECT COALESCE(SUM(qty_terjual), 0) FROM v_riwayat_penjualan_produk_v2 WHERE v_riwayat_penjualan_produk_v2.produk_id = {$produkTable}.produk_id AND LOWER(status_pembayaran) IN ('success', 'paid', 'settlement')) as qty_terjual")
            ->where('produk_id', $id)
            ->where('status', 'approved')
            ->first();

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json($produk);
    }

    // Upload produk baru (khusus seller)
    public function store(Request $request)
    {
        $request->validate([
            'nama_produk' => 'required|string|max:255',
            'deskripsi'   => 'required|string',
            'harga'       => 'required|numeric|min:0',
            'file'        => 'required|file|mimes:jpg,jpeg,png,pdf,zip|max:51200',
            'access_url'  => 'nullable|string',
            'kategori_ids' => 'sometimes|array',
            'kategori_ids.*' => 'string|exists:kategori,id',
            'kategori' => 'sometimes|array',
            'kategori.*' => 'string|exists:kategori,id',
        ]);

        $user = $request->user();

        if ($user->role !== 'seller') {
            return response()->json(['message' => 'Hanya penjual yang bisa upload produk'], 403);
        }

        $file     = $request->file('file');
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->getRealPath();
        $mimeType = $file->getMimeType();

        $uploadedUrl = $this->storage->upload($filePath, $fileName, $mimeType);

        if (!$uploadedUrl) {
            return response()->json(['message' => 'Gagal mengupload file'], 500);
        }

        $produk = Produk::create([
            'produk_id'   => Str::uuid(),
            'user_id'     => $user->id,
            'nama_produk' => $request->nama_produk,
            'deskripsi'   => $request->deskripsi,
            'harga'       => $request->harga,
            'status'      => 'pending',
            'file_path'   => $uploadedUrl,
            'file_bucket' => config('services.supabase.bucket'),
            'access_url'  => $request->access_url ?? '-', 
        ]);

        $kategoriIds = $request->input('kategori_ids', $request->input('kategori', []));
        if (!empty($kategoriIds)) {
            $produk->categories()->sync($kategoriIds);
        }

        $produk->load('categories');

        return response()->json([
            'message' => 'Produk berhasil diupload, menunggu persetujuan admin',
            'produk'  => $produk,
        ], 201);
    }

    // Edit produk (khusus pemilik produk)
    public function update(Request $request, string $id)
    {
        $produk = Produk::where('produk_id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_produk' => 'sometimes|string|max:255',
            'deskripsi'   => 'sometimes|string',
            'harga'       => 'sometimes|numeric|min:0',
            'file'        => 'sometimes|file|mimes:jpg,jpeg,png,pdf,zip|max:51200',
            'access_url'  => 'sometimes|string|max:255',
            'kategori_ids' => 'sometimes|array',
            'kategori_ids.*' => 'string|exists:kategori,id',
            'kategori' => 'sometimes|array',
            'kategori.*' => 'string|exists:kategori,id',
        ]);

        if ($request->hasFile('file')) {
            $oldFileName = basename($produk->file_path);
            $this->storage->delete($oldFileName);

            $file     = $request->file('file');
            $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $uploadedUrl = $this->storage->upload(
                $file->getRealPath(),
                $fileName,
                $file->getMimeType()
            );

            if (!$uploadedUrl) {
                return response()->json(['message' => 'Gagal mengupload file baru'], 500);
            }

            $produk->file_path = $uploadedUrl;
        }

        $produk->fill($request->only(['nama_produk', 'deskripsi', 'harga', 'access_url']));
        $produk->status = 'pending'; 
        $produk->save();

        if ($request->has('kategori_ids') || $request->has('kategori')) {
            $kategoriIds = $request->input('kategori_ids', $request->input('kategori', []));
            $produk->categories()->sync($kategoriIds);
        }

        $produk->load('categories');

        return response()->json([
            'message' => 'Produk berhasil diupdate, menunggu persetujuan ulang admin',
            'produk'  => $produk,
        ]);
    }

    // Hapus produk (khusus pemilik produk)
    public function destroy(Request $request, string $id)
    {
        $produk = Produk::where('produk_id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        $oldFileName = basename($produk->file_path);
        $this->storage->delete($oldFileName);
        $produk->delete();

        return response()->json(['message' => 'Produk berhasil dihapus']);
    }

    // 3. List produk milik seller yang sedang login (Halaman Dashboard Seller)
    public function myProduk(Request $request)
    {
        $produkTable = (new Produk())->getTable();

        // 🌟 FIX URUTAN UTAMANYA DI SINI: select() wajib dipanggil duluan!
        $produk = Produk::select($produkTable . '.*')
            ->with('categories')
            ->withAvg('reviews', 'rating')  
            ->withCount('reviews')          
            ->selectRaw("(SELECT COALESCE(SUM(qty_terjual), 0) FROM v_riwayat_penjualan_produk_v2 WHERE v_riwayat_penjualan_produk_v2.produk_id = {$produkTable}.produk_id AND LOWER(status_pembayaran) IN ('success', 'paid', 'settlement')) as qty_terjual")
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($produk);
    }

    public function sellerOrders(Request $request)
    {
        $userId = $request->user()->id;

        try {
            $orders = DB::table('v_riwayat_penjualan_produk_v2')
                ->where('user_id', $userId)
                ->orderByDesc('tgl_pesanan')
                ->get();

            return response()->json($orders);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }
}