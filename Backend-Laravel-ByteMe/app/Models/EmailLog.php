<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $table = 'email_log';
    protected $primaryKey = 'email_log_id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'email_log_id',
        'pesanan_id',
        'user_id',
        'recipient_email',
        'status',
        'error_message',
        'sent_at',
    ];
}