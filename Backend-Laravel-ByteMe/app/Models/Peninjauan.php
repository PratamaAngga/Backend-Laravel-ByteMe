<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peninjauan extends Model
{
    protected $table = 'peninjauan';
    protected $primaryKey = 'peninjauan_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'peninjauan_id', 'user_id',
        'produk_id', 'catatan', 'status',
    ];
}
