<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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

    // Ban akun user
    public function ban(string $id)
    {
        $user = User::where('id', $id)
            ->where('role', '!=', 'admin')
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->status === 'banned') {
            return response()->json(['message' => 'User sudah dalam status banned'], 409);
        }

        $user->status = 'banned';
        $user->save();

        // Hapus semua token aktif user agar langsung logout
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Akun user berhasil dibanned',
            'user'    => $user->only(['id', 'username', 'email', 'role', 'status']),
        ]);
    }

    // Unban akun user
    public function unban(string $id)
    {
        $user = User::where('id', $id)
            ->where('role', '!=', 'admin')
            ->first();

        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        if ($user->status !== 'banned') {
            return response()->json(['message' => 'User tidak dalam status banned'], 409);
        }

        $user->status = 'active';
        $user->save();

        return response()->json([
            'message' => 'Akun user berhasil di-unban',
            'user'    => $user->only(['id', 'username', 'email', 'role', 'status']),
        ]);
    }
}