<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VHistoryPembelian extends Model
{
    protected $table = 'v_history_pembelian';
    public $timestamps = false;
    public $incrementing = false;

    // View = read only, jadi kosongkan fillable
    protected $fillable = [];
}