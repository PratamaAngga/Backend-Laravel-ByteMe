<?php

namespace App\Models;

use App\Models\Produk;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nama',
    ];

    public function produk()
    {
        return $this->belongsToMany(Produk::class, 'kategori_produk', 'kategori_id', 'produk_id');
    }
}
