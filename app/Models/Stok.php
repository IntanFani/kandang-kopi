<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stok extends Model
{
    protected $table = 'stok';
    protected $primaryKey = 'id_stok';
    public $timestamps = false;

    protected $fillable = [
        'bahan_baku',
        'tanggal_masuk',
        'expired',
        'jumlah',
        'satuan',
        'harga',
    ];

    public function user()
    {
        // Hubungkan id_user di tabel stoks dengan id di tabel users
        return $this->belongsTo(User::class, 'id_user');
    }
}
