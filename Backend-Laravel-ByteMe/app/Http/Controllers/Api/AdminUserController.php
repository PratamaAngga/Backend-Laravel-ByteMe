<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class AdminUserController extends Controller
{
    // List semua user (bukan admin)
    public function index()
    {
        $columns = ['id', 'username', 'email', 'role', 'created_at'];
        if (Schema::hasColumn('profiles', 'is_banned')) {
            $columns[] = 'is_banned';
        }

        $users = User::where('role', '!=', 'admin')
            ->latest()
            ->get($columns);

        return response()->json($users);
    }

    // Detail satu user
    public function show(string $id)
    {
        $columns = ['id', 'username', 'email', 'role', 'created_at'];
        if (Schema::hasColumn('profiles', 'is_banned')) {
            $columns[] = 'is_banned';
        }

        $user = User::where('id', $id)
            ->where('role', '!=', 'admin')
            ->first($columns);

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

        if ($user->is_banned) {
            return response()->json(['message' => 'User sudah dalam status banned'], 409);
        }

        $user->is_banned = true;
        $user->save();

        // Hapus semua token aktif user agar langsung logout
        $user->tokens()->delete();

        return response()->json([
            'message' => 'Akun user berhasil dibanned',
            'user'    => $user->only(['id', 'username', 'email', 'role', 'is_banned']),
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

        if (!$user->is_banned) {
            return response()->json(['message' => 'User tidak dalam status banned'], 409);
        }

        $user->is_banned = false;
        $user->save();

        return response()->json([
            'message' => 'Akun user berhasil di-unban',
            'user'    => $user->only(['id', 'username', 'email', 'role', 'is_banned']),
        ]);
    }
}