<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WithdrawController extends Controller
{
    // List riwayat withdraw seller
    public function index(Request $request)
    {
        $withdraws = WithdrawRequest::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($withdraws);
    }

    // Detail satu withdraw
    public function show(Request $request, string $id)
    {
        $withdraw = WithdrawRequest::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$withdraw) {
            return response()->json(['message' => 'Request withdraw tidak ditemukan'], 404);
        }

        return response()->json($withdraw);
    }

    // Request withdraw baru
    public function store(Request $request)
    {
        $user = $request->user();

        // Cek role seller
        if ($user->role !== 'seller') {
            return response()->json([
                'message' => 'Hanya seller yang bisa request withdraw'
            ], 403);
        }

        $request->validate([
            'amount'              => 'required|numeric|min:50000',
            'bank_name'           => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_name'   => 'required|string|max:100',
        ]);

        // Cek saldo mencukupi
        if ($user->saldo < $request->amount) {
            return response()->json([
                'message' => 'Saldo tidak mencukupi',
                'saldo'   => $user->saldo,
            ], 400);
        }

        // Cek apakah ada withdraw pending
        $pendingWithdraw = WithdrawRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        if ($pendingWithdraw) {
            return response()->json([
                'message' => 'Kamu masih memiliki request withdraw yang pending'
            ], 400);
        }

        // Kurangi saldo sementara (hold)
        $user->saldo -= $request->amount;
        $user->save();

        $withdraw = WithdrawRequest::create([
            'id'                  => Str::uuid(),
            'user_id'             => $user->id,
            'amount'              => $request->amount,
            'bank_name'           => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name'   => $request->bank_account_name,
            'status'              => 'pending',
        ]);

        return response()->json([
            'message'  => 'Request withdraw berhasil dikirim',
            'withdraw' => $withdraw,
            'saldo'    => $user->saldo,
        ], 201);
    }
}