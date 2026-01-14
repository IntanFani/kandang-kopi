<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StokKeluar extends Model {
    protected $table = 'stok_keluar'; // Sesuai gambar image_b04b70.jpg
    protected $primaryKey = 'id_stok_keluar'; // Sesuai image_b04b70.jpg
    public $timestamps = false;
    protected $fillable = [
        'bahan_baku_keluar', 
        'tanggal_masuk_keluar', 
        'expired_keluar', 
        'jumlah_keluar', 
        'satuan_keluar', 
        'harga_keluar'
    ];
}