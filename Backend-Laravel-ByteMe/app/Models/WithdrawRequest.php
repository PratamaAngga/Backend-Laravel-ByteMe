<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WithdrawRequest extends Model
{
    protected $table      = 'withdraw_requests';
    protected $primaryKey = 'id';
    public $incrementing  = false;
    protected $keyType    = 'string';

    // status: pending, approved, rejected
    protected $fillable = [
        'id',
        'user_id',
        'amount',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
        'status',
        'admin_note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}