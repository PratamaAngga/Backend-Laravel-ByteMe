<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\NotifikasiHelper;

class AdminUserController extends Controller
{
    // List semua user (bukan admin)
    public function index()
    {
        $users = User::where('role', '!=', 'admin')
            ->latest()
            ->get(['id', 'username', 'email', 'role', 'status', 'created_at']);

        return response()->json($users);
    }

    // Detail satu user
    public function show(string $id)
    {
        $user = User::where('id', $id)
            ->where('role', '!=', 'admin')
            ->first(['id', 'username', 'email', 'role', 'status', 'created_at']);

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        return response()->json($user);
    }

    // Beri sanksi ke user
    public function ban(Request $request, string $id)
    {
        $request->validate([
            'type' => 'required|in:warning,suspended,banned',
            'alasan' => 'required|string',
        ]);

        $user = User::where('id', $id)
            ->where('role', '!=', 'admin')
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->status === 'banned') {
            return response()->json(['message' => 'User sudah dalam status banned'], 409);
        }

        $user->status = $request->type;

        // Kalau suspended, set waktu berakhirnya
        if ($request->type === 'suspended') {
            $user->suspended_until = now()->addDays(7);
        } else {
            $user->suspended_until = null;
        }

        $user->save();

        // Hapus semua token aktif biar langsung logout
        // Warning masih bisa login, jadi jangan hapus tokennya
        if (in_array($request->type, ['suspended', 'banned'])) {
            $user->tokens()->delete();
        }

        // Kirim notifikasi
        $pesanNotif = match($request->type) {
            'warning'   => '⚠️ Akunmu mendapat peringatan dari admin. Alasan: ' . $request->alasan . '. Harap perhatikan ketentuan penggunaan.',
            'suspended' => '🚫 Akunmu disuspend selama 7 hari hingga ' . now()->addDays(7)->format('d/m/Y') . '. Alasan: ' . $request->alasan,
            'banned'    => '🚫 Akunmu telah dibanned secara permanen. Alasan: ' . $request->alasan . '. Hubungi admin jika ada keberatan.',
        };

        NotifikasiHelper::kirim(
            userId:  $user->id,
            type:    'sanksi',
            catatan: $pesanNotif,
        );

        return response()->json([
            'message' => 'Akun user berhasil di-' . $request->type,
            'user'    => $user->only(['id', 'username', 'email', 'role', 'status', 'suspended_until']),
        ]);
    }

    // Cabut sanksi user
    public function unban(string $id)
    {
        $user = User::where('id', $id)
            ->where('role', '!=', 'admin')
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->status === 'active') {
            return response()->json(['message' => 'User sudah dalam status active'], 409);
        }

        $user->status = 'active';
        $user->suspended_until = null;
        $user->save();

        NotifikasiHelper::kirim(
            userId:  $user->id,
            type:    'sanksi',
            catatan: '✅ Sanksi pada akunmu telah dicabut oleh admin. Akunmu kembali aktif.',
        );

        return response()->json([
            'message' => 'Sanksi user berhasil dicabut',
            'user'    => $user->only(['id', 'username', 'email', 'role', 'status']),
        ]);
    }
}