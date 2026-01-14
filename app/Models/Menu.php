<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $table = 'menus'; //

    // Daftar kolom yang diizinkan untuk disimpan ke database
    protected $fillable = [
        'nama_menu',
        'kategori',
        'harga',
        'foto',
        'status',
        'stok'
    ]; //
}