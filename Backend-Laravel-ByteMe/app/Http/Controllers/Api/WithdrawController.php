<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WithdrawController extends Controller
{
    // List withdraw request milik seller yang login
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'seller') {
            return response()->json(['message' => 'Hanya seller yang bisa melihat withdraw request'], 403);
        }

        $withdraws = WithdrawRequest::where('user_id', $user->id)
            ->latest()
            ->get();

        return response()->json($withdraws);
    }

    // Seller buat withdraw request baru
    public function store(Request $request)
    {
        $user = $request->user();

        if ($user->role !== 'seller') {
            return response()->json(['message' => 'Hanya seller yang bisa mengajukan withdraw'], 403);
        }

        $request->validate([
            'amount'              => 'required|numeric|min:10000',
            'bank_name'           => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_name'   => 'required|string|max:255',
        ]);

        // Cek apakah ada pending request yang belum selesai
        $pendingExists = WithdrawRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($pendingExists) {
            return response()->json([
                'message' => 'Anda masih memiliki withdraw request yang sedang diproses. Tunggu hingga selesai sebelum mengajukan yang baru.',
            ], 409);
        }

        $withdraw = WithdrawRequest::create([
            'id'                  => Str::uuid(),
            'user_id'             => $user->id,
            'amount'              => $request->amount,
            'bank_name'           => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name'   => $request->bank_account_name,
            'status'              => 'pending',
            'admin_note'          => null,
        ]);

        return response()->json([
            'message'  => 'Withdraw request berhasil diajukan, menunggu persetujuan admin',
            'withdraw' => $withdraw,
        ], 201);
    }

    // Detail satu withdraw request milik seller
    public function show(Request $request, string $id)
    {
        $withdraw = WithdrawRequest::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$withdraw) {
            return response()->json(['message' => 'Withdraw request tidak ditemukan'], 404);
        }

        return response()->json($withdraw);
    }
}