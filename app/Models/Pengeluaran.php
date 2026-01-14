<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'pengeluaran';

    // Sesuaikan fillable dengan nama kolom di phpMyAdmin kamu
    protected $fillable = [
        'tgl_pengeluaran', // Berubah dari 'tanggal'
        'kategori',
        'catatan',
        'nominal',
    ];

    protected $casts = [
        'tgl_pengeluaran' => 'date',
    ];

    public function user() {
    return $this->belongsTo(User::class, 'id_admin');
}
}