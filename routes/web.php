<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\UserController; 
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\StokController;


/*
|--------------------------------------------------------------------------
| AUTH & PUBLIC
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate']);

Route::middleware(['auth'])->group(function () {

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/', function () {
        return auth()->user()->role === 'admin'
            ? redirect('/admin/dashboard')
            : redirect('/kasir/dashboard');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN AREA
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->group(function () {
        
        // DASHBOARD (Sekarang menggunakan DashboardController agar grafik muncul)
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        //LAPORAN
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
        Route::get('/laporan/pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');
        Route::get('/admin/laporan/excel', [LaporanController::class, 'exportExcel'])->name('laporan.excel');

        //PENGELUARAN
        Route::get('/pengeluaran', [PengeluaranController::class, 'index'])->name('pengeluaran.index');
        Route::post('/pengeluaran', [PengeluaranController::class, 'store'])->name('pengeluaran.store');
        Route::put('/pengeluaran/{id}', [PengeluaranController::class, 'update'])->name('pengeluaran.update');
        Route::delete('/pengeluaran/{id}', [PengeluaranController::class, 'destroy'])->name('pengeluaran.destroy');

        //STOK
        Route::get('/stock', [StokController::class, 'index'])->name('stok.index');
        Route::post('/stock', [StokController::class, 'store'])->name('stok.store');
        Route::put('/admin/stok/{id}', [StokController::class, 'update']);
        Route::delete('/admin/stok/{id}', [StokController::class, 'destroy']);

        // --- RUTE KELOLA AKUN KASIR ---
        Route::get('/kelola_akun', [UserController::class, 'index'])->name('user.index');
        Route::post('/kelola_akun', [UserController::class, 'store'])->name('user.store');
        Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update');
        Route::delete('/kelola_akun/{id}', [UserController::class, 'destroy'])->name('user.destroy');

        // --- RUTE MENU (KELOLA PRODUK KOPI) ---
        Route::get('/menu', [MenuController::class, 'index'])->name('menu.index');
        Route::post('/menu', [MenuController::class, 'store'])->name('menu.store');
        Route::put('/menu/{id}', [MenuController::class, 'update'])->name('menu.update');
        Route::delete('/menu/{id}', [MenuController::class, 'destroy'])->name('menu.destroy');
        Route::patch('/menu/{id}/status', [MenuController::class, 'updateStatus'])->name('menu.status');
    });

    /*
    |--------------------------------------------------------------------------
    | KASIR AREA
    |--------------------------------------------------------------------------
    */
    // Halaman Utama Kasir (Tampilan Transaksi)
    Route::get('/kasir/dashboard', [KasirController::class, 'index'])->name('kasir.dashboard');
    
    // Fitur Simpan Transaksi (Menghubungkan JS Kasir ke Database)
    Route::post('/kasir/transaksi/simpan', [KasirController::class, 'store'])->name('transaksi.store');
});