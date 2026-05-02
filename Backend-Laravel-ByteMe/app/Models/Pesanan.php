<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'pesanan_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'pesanan_id', 'user_id', 'tgl_pesanan',
        'total_harga', 'status',
    ];

    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'pesanan_id', 'pesanan_id');
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class, 'pesanan_id', 'pesanan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
