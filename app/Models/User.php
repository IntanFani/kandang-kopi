<?php

namespace App\Models;

// Import Authenticatable agar fitur login Laravel berfungsi
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable 
{
    use Notifiable;

    // 1. Ubah ke 'users' karena di migration kamu menggunakan Schema::create('users')
    protected $table = 'users'; 

    // 2. Ubah ke 'id' karena di migration kamu menggunakan $table->id()
    protected $primaryKey = 'id'; 

    // 3. Ubah ke true karena di migration ada $table->timestamps()
    public $timestamps = true;

    // 4. Sesuaikan fillable dengan kolom yang ada di migration
    // Pastikan menggunakan 'name' (bukan 'nama') sesuai file migration tadi
    protected $fillable = [
        'name',
        'username',
        'password',
        'role',
    ];

    // 5. Sembunyikan password saat data user dipanggil
    protected $hidden = [
        'password',
        'remember_token',
    ];
}