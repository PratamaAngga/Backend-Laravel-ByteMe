<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:profiles',
            'email'    => 'required|email|unique:profiles',
            'phone'    => 'sometimes|nullable|string|max:30',
            'password' => 'required|min:8|confirmed',
            'role'     => 'in:buyer,seller',
        ]);

        $user = User::create([
            'id'       => Str::uuid(),
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => $request->role ?? 'buyer',
            'balance'  => 0.0,
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
            'role'     => 'required|in:buyer,seller,admin',
        ]);

        $user = User::where('username', $request->username)
            ->where('role', $request->role)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username, password, atau role salah',
            ], 401);
        }

        $user->balance = (double)($user->balance ?? 0.0);

        // 1. 🌟 CEK APAKAH MASA SUSPEND SUDAH HABIS (DILAKUKAN DI AWAL SEBELUM INTERSEPSI STATUS)
        if ($user->status === 'suspended') {
            if ($user->suspended_until && now()->gt($user->suspended_until)) {
                // Masa suspend habis, aktifkan lagi otomatis di database
                $user->status = 'active';
                $user->suspended_until = null;
                $user->status_reason = null; // Bersihkan alasan
                $user->save();
            }
        }

        // 2. AMBIL ALASAN DAN FORMAT TANGGAL SELESAI SUSPEND SECARA DINAMIS
        $reason = $user->status_reason ?? 'Harap hubungi pihak administrasi.';
        
        // Format tanggal suspend_until ke waktu lokal (WIB / Asia/Jakarta) agar rapi dibaca manusia
        $suspendedUntilFormatted = $user->suspended_until 
            ? Carbon::parse($user->suspended_until)->timezone('Asia/Jakarta')->format('d/m/Y H:i') 
            : null;

        // Siapkan pesan sanksi kustom
        $statusMessages = [
            'warning'   => "Akun kamu sedang dalam status peringatan. Alasan: {$reason}",
            'suspended' => "Akun kamu sedang disuspend sementara" . ($suspendedUntilFormatted ? " hingga {$suspendedUntilFormatted} WIB" : "") . ". Alasan: {$reason}",
            'banned'    => "Akun kamu telah dibanned secara permanen. Alasan: {$reason}",
        ];

        // 3. MEMERIKSA STATUS USER UNTUK MENGEMBALIKAN RESPONSE SANKSI (JIKA MASIH TERDAMPAK SANKSI)
        if (array_key_exists($user->status, $statusMessages)) {
            $httpCode = $user->status === 'warning' ? 200 : 403;

            $token = null;
            if ($user->status === 'warning') {
                $token = $user->createToken('auth_token')->plainTextToken;
            }

            return response()->json([
                'message'         => $statusMessages[$user->status], // Pesan ini sudah dinamis berisi Alasan + Tanggal Selesai!
                'status'          => $user->status,
                'suspended_until' => $user->suspended_until, // Raw datetime jika Flutter butuh parsing kalkulasi hari
                'token'           => $token,
                'user'            => $user,
            ], $httpCode);
        }

        // Jika lolos semua pemeriksaan sanksi, izinkan login normal
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
        $user = $request->user();
        $user->balance = (double)($user->balance ?? 0.0);
        return response()->json($user);
    }

    /**
     * Update profil user.
     * Menerima POST dengan field _method=PATCH agar bisa kirim multipart/form-data
     * (Flutter http package tidak support PATCH + file upload).
     */

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
            'phone'                 => 'sometimes|nullable|string|max:30',
            'password'              => 'sometimes|min:6|confirmed',
            'password_confirmation' => 'required_with:password',
            'profile_image'         => 'sometimes|nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($request->filled('username')) {
            $user->username = $request->username;
        }

        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        if ($request->filled('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('profile_image')) {
            $file      = $request->file('profile_image');
            $mimeType  = $file->getMimeType();
            $extension = $file->getClientOriginalExtension();

            // Pakai UUID user sebagai nama file agar unik dan bisa di-upsert

            $fileName = $user->id . '.' . $extension;

            Log::info('AuthController: upload foto profil', [
                'user_id'   => $user->id,
                'fileName'  => $fileName,
                'mimeType'  => $mimeType,
            ]);

            $supabase = (new SupabaseStorageService())->useBucket('bucket_profile');
            $imageUrl = $supabase->upload($file->getRealPath(), $fileName, $mimeType);

            if ($imageUrl !== false) {
                $user->profile_image = $imageUrl;
                Log::info('AuthController: foto profil tersimpan', ['url' => $imageUrl]);
            } else {
                Log::error('AuthController: gagal upload foto profil', ['user_id' => $user->id]);
                return response()->json([
                    'message' => 'Gagal upload foto profil ke storage. Pastikan bucket sudah dibuat dan nama bucket di .env sudah benar.',
                ], 500);
            }
        }

        $user->save();
        $user->balance = (double)($user->balance ?? 0.0);

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'user'    => $user,
        ]);
    }
}