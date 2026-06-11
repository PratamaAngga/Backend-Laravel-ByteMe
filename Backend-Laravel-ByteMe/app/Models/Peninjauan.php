<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // 1. Import trait HasUuids

class Peninjauan extends Model
{
    use HasUuids; // 2. Gunakan trait di dalam class

    protected $table = 'peninjauan';
    protected $primaryKey = 'peninjauan_id';
    public $incrementing = false;
    protected $keyType = 'string';
    
    // Pastikan di tabel Supabase kamu memang TIDAK ADA kolom created_at dan updated_at.
    // Jika ada, ubah ini jadi true atau hapus saja baris ini.
    public $timestamps = false; 

    protected $fillable = [
        'peninjauan_id', 
        'user_id',
        'produk_id', 
        'catatan', 
        'status',
    ];
}