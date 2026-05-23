<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $table      = 'review';
    protected $primaryKey = 'review_id';
    public $incrementing  = false;
    protected $keyType    = 'string';
    public $timestamps    = false;

    protected $fillable = [
        'review_id',
        'user_id',
        'produk_id',
        'rating',
        'komentar',
        'tgl_review',
    ];

    protected $casts = [
        'rating'     => 'integer',
        'tgl_review' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }
}