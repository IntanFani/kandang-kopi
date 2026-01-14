<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\KandangKopiController;

// RUTE KASIR
Route::prefix('kasir')->group(function () {
    Route::get('/menu', [KandangKopiController::class, 'kasirLihatMenu']);
    Route::post('/transaksi', [KandangKopiController::class, 'simpanTransaksi']);
});

// RUTE ADMIN (PEMILIK)
Route::prefix('admin')->group(function () {
    // Jalur untuk melihat data tabel
    Route::get('/stok', [KandangKopiController::class, 'adminLihatStok']);
    Route::get('/user', [KandangKopiController::class, 'adminLihatUser']);
    Route::get('/pengeluaran', [KandangKopiController::class, 'adminLihatPengeluaran']);
    // Kelola Menu
    Route::get('/menu', [KandangKopiController::class, 'adminLihatMenu']);
    Route::post('/menu/tambah', [KandangKopiController::class, 'tambahMenu']);
    Route::put('/menu/update/{id}', [KandangKopiController::class, 'updateMenu']); 
    Route::delete('/menu/hapus/{id}', [KandangKopiController::class, 'hapusMenu']); 

    // Kelola Stok Masuk
    Route::post('/stok/tambah', [KandangkopiController::class, 'tambahStok']);
    
    // Kelola Stok Keluar (BARU)
    Route::post('/stok-keluar/tambah', [KandangkopiController::class, 'tambahStokKeluar']);
    
    // Lihat Sisa Stok Akhir (BARU)
    Route::get('/stok/saldo', [KandangkopiController::class, 'cekSaldoStok']);

    // Jalur untuk menambah data
    Route::post('/stok/tambah', [KandangKopiController::class, 'tambahStok']);
    Route::post('/user/tambah', [KandangKopiController::class, 'tambahUser']);
    Route::get('/laporan', [KandangKopiController::class, 'laporanLengkap']);
    Route::post('/pengeluaran/tambah', [KandangKopiController::class, 'tambahPengeluaran']);
});