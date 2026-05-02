<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProdukController extends Controller
{
    protected SupabaseStorageService $storage;

    public function __construct(SupabaseStorageService $storage)
    {
        $this->storage = $storage;
    }

    // List semua produk yang sudah approved (untuk marketplace)
    public function index()
    {
        $produk = Produk::where('status', 'approved')
            ->latest()
            ->get();

        return response()->json($produk);
    }

    // Detail satu produk
    public function show(string $id)
    {
        $produk = Produk::where('produk_id', $id)
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
        ]);

        $user = $request->user();

        if ($user->role !== 'seller') {
            return response()->json(['message' => 'Hanya penjual yang bisa upload produk'], 403);
        }

        // Upload file ke Supabase Storage
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
        ]);

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
        ]);

        // Kalau ada file baru, upload dan hapus yang lama
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

        $produk->fill($request->only(['nama_produk', 'deskripsi', 'harga']));
        $produk->status = 'pending'; // reset ke pending kalau diedit
        $produk->save();

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

    // List produk milik seller yang sedang login
    public function myProduk(Request $request)
    {
        $produk = Produk::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($produk);
    }
}