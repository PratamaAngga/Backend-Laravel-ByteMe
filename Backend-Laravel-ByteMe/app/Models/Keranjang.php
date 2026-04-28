<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    protected $table = 'keranjang';
    protected $primaryKey = 'keranjang_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'keranjang_id', 'user_id', 'total_item',
    ];
}
