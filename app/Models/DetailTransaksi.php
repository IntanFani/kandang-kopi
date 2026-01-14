<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DetailTransaksi extends Model
{
    use HasFactory;

    protected $table = 'detail_transaksi';

    public $timestamps = false;

    protected $fillable = [
        'kode_transaksi', 
        'id_menu', 
        'quantity', 
        'harga_satuan', 
        'subtotal'
    ];

    // Relasi ke Menu (Sangat penting untuk menampilkan nama menu di nota)
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }

    // Relasi balik ke Transaksi
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'kode_transaksi', 'kode_transaksi');
    }
}