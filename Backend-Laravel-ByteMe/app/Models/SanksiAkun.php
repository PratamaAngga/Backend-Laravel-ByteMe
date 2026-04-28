<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SanksiAkun extends Model
{
    protected $table = 'sanksi_akun';
    protected $primaryKey = 'sanksi_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'sanksi_id', 'peninjauan_id',
        'user_id', 'jenis',
    ];
}
