<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Statistik Hari Ini
        $pendapatanHariIni = Transaksi::whereDate('tgl_transaksi', Carbon::today())->sum('total_bayar');
        $pengeluaranHariIni = Pengeluaran::whereDate('tgl_pengeluaran', Carbon::today())->sum('nominal');

        // 2. Filter Bulan & Tahun
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));

        // 3. Ambil Data Penjualan & Transformasi ke Collection
        $penjualan = Transaksi::whereMonth('tgl_transaksi', $bulan)
            ->whereYear('tgl_transaksi', $tahun)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'tanggal'  => $item->tgl_transaksi,
                    'nominal'  => $item->total_bayar,
                    'info'     => $item->kode_transaksi,
                    'jenis'    => 'Penjualan',
                    'kategori' => 'Menu'
                ];
            });

        // 4. Ambil Data Pengeluaran & Transformasi ke Collection
        $pengeluaran = Pengeluaran::whereMonth('tgl_pengeluaran', $bulan)
            ->whereYear('tgl_pengeluaran', $tahun)
            ->get()
            ->map(function ($item) {
                return (object) [
                    'tanggal'  => $item->tgl_pengeluaran,
                    'nominal'  => $item->nominal,
                    'info'     => $item->catatan,
                    'jenis'    => 'Pengeluaran',
                    'kategori' => $item->kategori
                ];
            });

        // 5. Gabungkan Collection, Urutkan Berdasarkan Tanggal Terbaru, dan Ambil 20
        $transaksiGabungan = $penjualan->merge($pengeluaran)
            ->sortByDesc('tanggal')
            ->take(20);

        return view('admin.penjualan', compact(
            'pendapatanHariIni', 
            'pengeluaranHariIni', 
            'transaksiGabungan'
        ));
    }
}