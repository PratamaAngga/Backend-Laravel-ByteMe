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
}
