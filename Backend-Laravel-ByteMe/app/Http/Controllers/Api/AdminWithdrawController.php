<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WithdrawRequest;
use Illuminate\Http\Request;

class AdminWithdrawController extends Controller
{
    // List semua withdraw request (semua status)
    public function index()
    {
        $withdraws = WithdrawRequest::with('user:id,username,email')
            ->latest()
            ->get();

        return response()->json($withdraws);
    }

    // List withdraw pending saja
    public function pendingList()
    {
        $withdraws = WithdrawRequest::with('user:id,username,email')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return response()->json($withdraws);
    }

    // Approve withdraw request
    public function approve(Request $request, string $id)
    {
        $request->validate([
            'admin_note' => 'nullable|string|max:500',
        ]);

        $withdraw = WithdrawRequest::find($id);

        if (!$withdraw) {
            return response()->json(['message' => 'Withdraw request tidak ditemukan'], 404);
        }

        if ($withdraw->status !== 'pending') {
            return response()->json([
                'message' => 'Hanya withdraw request dengan status pending yang bisa diapprove',
            ], 409);
        }

        $withdraw->status     = 'approved';
        $withdraw->admin_note = $request->admin_note;
        $withdraw->save();

        return response()->json([
            'message'  => 'Withdraw request berhasil diapprove',
            'withdraw' => $withdraw->load('user:id,username,email'),
        ]);
    }

    // Reject withdraw request
    public function reject(Request $request, string $id)
    {
        $request->validate([
            'admin_note' => 'required|string|max:500',
        ]);

        $withdraw = WithdrawRequest::find($id);

        if (!$withdraw) {
            return response()->json(['message' => 'Withdraw request tidak ditemukan'], 404);
        }

        if ($withdraw->status !== 'pending') {
            return response()->json([
                'message' => 'Hanya withdraw request dengan status pending yang bisa direject',
            ], 409);
        }

        $withdraw->status     = 'rejected';
        $withdraw->admin_note = $request->admin_note;
        $withdraw->save();

        return response()->json([
            'message'  => 'Withdraw request berhasil direject',
            'withdraw' => $withdraw->load('user:id,username,email'),
        ]);
    }
}