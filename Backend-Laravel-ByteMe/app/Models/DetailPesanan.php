<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';
    protected $primaryKey = 'detail_pesanan_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'detail_pesanan_id',
        'pesanan_id',
        'produk_id',
        'jumlah',
        'harga_satuan',
    ];

    // ✅ Explicitly exclude subtotal dari mass assignment
    protected $guarded = ['subtotal'];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }
}