<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailKeranjang extends Model
{
    protected $table = 'detail_keranjang';
    protected $primaryKey = 'detail_keranjang_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'detail_keranjang_id', 'keranjang_id',
        'produk_id', 'jumlah', 'harga_satuan', 'subtotal',
    ];
}
