<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksi';
    public $timestamps = false;

    protected $fillable = [
        'kode_transaksi',
        'nama_customer', 
        'id_kasir',
        'tgl_transaksi',
        'total_bayar',
        'jumlah_bayar',
        'kembalian',
    ];

    // TAMBAHKAN INI: Relasi ke User (Kasir)
    public function kasir() 
    {
        return $this->belongsTo(User::class, 'id_kasir');
    }

    public function details() {
        return $this->hasMany(DetailTransaksi::class, 'kode_transaksi', 'kode_transaksi');
    }
}