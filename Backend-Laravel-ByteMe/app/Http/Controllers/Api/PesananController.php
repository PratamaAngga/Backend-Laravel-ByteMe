<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailKeranjang;
use App\Models\DetailPesanan;
use App\Models\Keranjang;
use App\Models\Pembayaran;
use App\Models\Pesanan;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\ProdukAccessMail;
use App\Models\EmailLog;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class PesananController extends Controller
{
    protected MidtransService $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
    }

    // Checkout satu atau banyak item dari keranjang
    public function checkout(Request $request)
    {
        $request->validate([
            'detail_keranjang_id'    => 'required_without:detail_keranjang_ids|uuid',
            'detail_keranjang_ids'   => 'required_without:detail_keranjang_id|array|min:1',
            'detail_keranjang_ids.*' => 'uuid',
        ]);

        $user = $request->user();

        $keranjang = Keranjang::where('user_id', $user->id)->first();

        if (!$keranjang) {
            return response()->json(['message' => 'Keranjang tidak ditemukan'], 404);
        }

        $detailIds = $request->input('detail_keranjang_ids', []);
        if ($request->filled('detail_keranjang_id')) {
            $detailIds[] = $request->detail_keranjang_id;
        }

        $detailIds = array_values(array_unique($detailIds));

        $items = DetailKeranjang::whereIn('detail_keranjang_id', $detailIds)
            ->where('keranjang_id', $keranjang->keranjang_id)
            ->with('produk')
            ->get();

        if ($items->count() !== count($detailIds)) {
            return response()->json(['message' => 'Satu atau lebih item tidak ditemukan di keranjang'], 404);
        }

        DB::beginTransaction();

        try {
            $pesananId = Str::uuid();
            $totalHarga = 0;
            $itemDetails = [];

            foreach ($items as $item) {
                $jumlah = $item->jumlah ?? 1;
                $subtotal = $item->harga_satuan * $jumlah;
                $totalHarga += $subtotal;

                DetailPesanan::create([
                    'detail_pesanan_id' => Str::uuid(),
                    'pesanan_id'        => $pesananId,
                    'produk_id'         => $item->produk_id,
                    'jumlah'            => $jumlah,
                    'harga_satuan'      => $item->harga_satuan,
                ]);

                $itemDetails[] = [
                    'id'       => $item->produk_id,
                    'price'    => (int) $item->harga_satuan,
                    'quantity' => (int) $jumlah,
                    'name'     => $item->produk->nama_produk,
                ];
            }

            $pesanan = Pesanan::create([
                'pesanan_id'  => $pesananId,
                'user_id'     => $user->id,
                'tgl_pesanan' => now(),
                'total_harga' => $totalHarga,
                'status'      => 'pending',
            ]);

            $midtransParams = [
                'transaction_details' => [
                    'order_id'     => $pesananId,
                    'gross_amount' => (int) $totalHarga,
                ],
                'customer_details' => [
                    'first_name' => $user->username,
                    'email'      => $user->email,
                    'phone'      => $user->phone,
                ],
                'item_details' => $itemDetails,
            ];

            $midtransResponse = $this->midtrans->createTransaction($midtransParams);

            Pembayaran::create([
                'pembayaran_id' => Str::uuid(),
                'pesanan_id'    => $pesananId,
                'metode'        => 'midtrans',
                'status'        => 'pending',
            ]);

            DetailKeranjang::whereIn('detail_keranjang_id', $detailIds)
                ->where('keranjang_id', $keranjang->keranjang_id)
                ->delete();

            $totalItem = DetailKeranjang::where('keranjang_id', $keranjang->keranjang_id)->count();
            $keranjang->total_item = $totalItem;
            $keranjang->save();

            DB::commit();

            return response()->json([
                'message'      => 'Checkout berhasil',
                'pesanan_id'   => $pesananId,
                'snap_token'   => $midtransResponse->token,
                'redirect_url' => $midtransResponse->redirect_url,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Checkout gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    // List riwayat pesanan buyer
    public function index(Request $request)
    {
        $pesanan = Pesanan::where('user_id', $request->user()->id)
            ->with(['detailPesanan.produk:produk_id,nama_produk,harga,file_path'])
            ->latest('tgl_pesanan')
            ->get();

        return response()->json($pesanan);
    }

    // Detail satu pesanan
    public function show(Request $request, string $id)
    {
        $pesanan = Pesanan::where('pesanan_id', $id)
            ->where('user_id', $request->user()->id)
            ->with(['detailPesanan.produk', 'pembayaran'])
            ->first();

        if (!$pesanan) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json($pesanan);
    }

    // Webhook dari Midtrans (notifikasi pembayaran)
    public function webhook(Request $request)
    {
        $serverKey  = config('services.midtrans.server_key');
        $orderId    = $request->order_id;
        $statusCode = $request->status_code;
        $grossAmount = $request->gross_amount;

        // Verifikasi signature
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($signature !== $request->signature_key) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $pesanan = Pesanan::where('pesanan_id', $orderId)->first();

        if (!$pesanan) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        $transactionStatus = $request->transaction_status;
        $fraudStatus       = $request->fraud_status;

        if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
            $pesanan->status = 'paid';
        } elseif ($transactionStatus === 'settlement') {
            $pesanan->status = 'paid';
        } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
            $pesanan->status = 'cancelled';
        } elseif ($transactionStatus === 'pending') {
            $pesanan->status = 'pending';
        }

        $pesanan->save();

        // Update status pembayaran
        $pembayaran = Pembayaran::where('pesanan_id', $orderId)->first();
        if ($pembayaran) {
            $pembayaran->status   = $pesanan->status === 'paid' ? 'success' : $transactionStatus;
            $pembayaran->metode   = $request->payment_type ?? 'midtrans';
            $pembayaran->tgl_bayar = now();
            $pembayaran->save();
        }

        // Kirim email kalau pembayaran berhasil
        if ($pesanan->status === 'paid') {
            $this->kirimEmailAksesProduk($pesanan);
        }

        return response()->json(['message' => 'Webhook berhasil diproses']);
    }

    private function kirimEmailAksesProduk(Pesanan $pesanan)
    {
        $user          = $pesanan->user;
        $detailPesanan = DetailPesanan::where('pesanan_id', $pesanan->pesanan_id)
            ->with('produk')
            ->get();

        foreach ($detailPesanan as $detail) {
            $produk = $detail->produk;

            try {
                Mail::to($user->email)->send(new ProdukAccessMail(
                    username:   $user->username,
                    namaProduk: $produk->nama_produk,
                    linkAkses:  $produk->access_url,
                    pesananId:  $pesanan->pesanan_id,
                ));

                // Catat berhasil
                EmailLog::create([
                    'email_log_id' => \Illuminate\Support\Str::uuid(),
                    'pesanan_id'   => $pesanan->pesanan_id,
                    'user_id'      => $user->id,
                    'recipient_email' => $user->email,
                    'status'       => 'sent',
                    'sent_at'      => now(),
                ]);

            } catch (\Exception $e) {
                // Catat gagal
                EmailLog::create([
                    'email_log_id'  => \Illuminate\Support\Str::uuid(),
                    'pesanan_id'    => $pesanan->pesanan_id,
                    'user_id'       => $user->id,
                    'recipient_email'  => $user->email,
                    'status'        => 'failed',
                    'error_message' => $e->getMessage(),
                    'sent_at'       => now(),
                ]);

                Log::error('Failed to send product access email: ' . $e->getMessage());
            }
        }
    }
}