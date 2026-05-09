<?php

namespace App\Models;

use App\Models\Kategori;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $primaryKey = 'produk_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'produk_id', 'user_id', 'nama_produk',
        'deskripsi', 'harga', 'status',
        'file_path', 'file_bucket', 'access_url'
    ];

    public function categories()
    {
        return $this->belongsToMany(Kategori::class, 'kategori_produk', 'produk_id', 'kategori_id');
    }
}
