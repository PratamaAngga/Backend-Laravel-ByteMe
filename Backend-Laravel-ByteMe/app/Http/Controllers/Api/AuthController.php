<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:profiles',
            'email'    => 'required|email|unique:profiles',
            'password' => 'required|min:8|confirmed',
            'role'     => 'in:buyer,seller',
        ]);

        $user = User::create([
            'id'       => Str::uuid(),
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role ?? 'buyer',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'token'   => $token,
            'user'    => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required',
            'role'     => 'required|in:buyer,seller',  // ← TAMBAHKAN
        ]);

        $user = User::where('username', $request->username)
            ->where('role', $request->role)      // ← TAMBAHKAN filter role
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username, password, atau role salah',
            ], 401);
        }

        $statusMessages = [
            'warning'   => 'Akun kamu sedang dalam status peringatan. Harap perhatikan ketentuan penggunaan.',
            'suspended' => 'Akun kamu sedang disuspend sementara. Hubungi admin untuk informasi lebih lanjut.',
            'banned'    => 'Akun kamu telah dibanned secara permanen. Hubungi admin jika ada keberatan.',
        ];

        if (array_key_exists($user->status, $statusMessages)) {
            $httpCode = $user->status === 'warning' ? 200 : 403;

            $token = null;
            if ($user->status === 'warning') {
                $token = $user->createToken('auth_token')->plainTextToken;
            }

            return response()->json([
                'message' => $statusMessages[$user->status],
                'status'  => $user->status,
                'token'   => $token,
            ], $httpCode);
        }

        // Cek apakah akun di-ban
        if ($user->is_banned) {
            return response()->json([
                'message' => 'Akun Anda telah diblokir. Hubungi admin untuk informasi lebih lanjut.',
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'token'   => $token,
            'user'    => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil',
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // Edit profile (tidak boleh ubah role)
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'username' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique('profiles')->ignore($user->id, 'id'),
            ],
            'email' => [
                'sometimes',
                'email',
                Rule::unique('profiles')->ignore($user->id, 'id'),
            ],
            'password'              => 'sometimes|min:8|confirmed',
            'password_confirmation' => 'required_with:password',
        ]);

        if ($request->filled('username')) {
            $user->username = $request->username;
        }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'user'    => $user,
        ]);
    }
}