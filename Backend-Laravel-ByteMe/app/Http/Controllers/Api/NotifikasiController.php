<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotifikasiController extends Controller
{
    // List semua notif user
    public function index(Request $request)
    {
        $notif = Notifikasi::where('user_id', $request->user()->id)
            ->latest('created_at')
            ->get();

        $unreadCount = $notif->where('status', 'belum_dibaca')->count();

        return response()->json([
            'unread_count' => $unreadCount,
            'data'         => $notif,
        ]);
    }

    // Tandai satu notif sebagai dibaca
    public function markAsRead(Request $request, string $notifId)
    {
        $notif = Notifikasi::where('notif_id', $notifId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$notif) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notif->status = 'dibaca';
        $notif->save();

        return response()->json(['message' => 'Notifikasi ditandai sebagai dibaca']);
    }

    // Tandai semua notif sebagai dibaca
    public function markAllAsRead(Request $request)
    {
        Notifikasi::where('user_id', $request->user()->id)
            ->where('status', 'belum_dibaca')
            ->update(['status' => 'dibaca']);

        return response()->json(['message' => 'Semua notifikasi ditandai sebagai dibaca']);
    }

    // Hapus satu notif
    public function destroy(Request $request, string $notifId)
    {
        $notif = Notifikasi::where('notif_id', $notifId)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$notif) {
            return response()->json(['message' => 'Notifikasi tidak ditemukan'], 404);
        }

        $notif->delete();

        return response()->json(['message' => 'Notifikasi berhasil dihapus']);
    }
}