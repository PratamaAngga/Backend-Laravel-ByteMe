<?php

namespace App\Helpers;

use App\Models\Notifikasi;
use Illuminate\Support\Str;

class NotifikasiHelper
{
    public static function kirim(
        string $userId,
        string $type,
        string $catatan,
        ?string $sanksiId = null,
        ?string $reviewId = null,
    ): void {
        Notifikasi::create([
            'notif_id'   => (string) Str::uuid(),
            'user_id'    => $userId,
            'type'       => $type,
            'catatan'    => $catatan,
            'sanksi_id'  => $sanksiId,
            'review_id'  => $reviewId,
            'status'     => 'belum_dibaca',
        ]);
    }
}