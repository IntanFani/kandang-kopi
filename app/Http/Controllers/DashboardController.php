<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Menu;
use App\Models\Transaksi;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil input mode filter (default ke 'range' / rentang tanggal)
        $mode = $request->get('mode', 'range');
        
        // Inisialisasi query
        $queryPendapatan = Transaksi::query();
        $queryPengeluaran = Pengeluaran::query();

        // 2. Tentukan Logika Filter Berdasarkan Mode
        if ($mode == 'bulan') {
            // Filter Per Bulan (Contoh: 2024-03)
            $bulanPilihan = $request->get('bulan_pilihan', date('Y-m'));
            $start = Carbon::parse($bulanPilihan)->startOfMonth();
            $end = Carbon::parse($bulanPilihan)->endOfMonth();
            
            $queryPendapatan->whereBetween('tgl_transaksi', [$start, $end]);
            $queryPengeluaran->whereBetween('tgl_pengeluaran', [$start, $end]);
            $formatTanggal = "DATE(tgl_transaksi)"; 
            $formatTanggalKeluar = "DATE(tgl_pengeluaran)";
        } 
        elseif ($mode == 'tahun') {
            // Filter Per Tahun (Contoh: 2024)
            $tahunPilihan = $request->get('tahun_pilihan', date('Y'));
            
            $queryPendapatan->whereYear('tgl_transaksi', $tahunPilihan);
            $queryPengeluaran->whereYear('tgl_pengeluaran', $tahunPilihan);
            
            // Kalau tahunan, grafik dikelompokkan per bulan (Jan, Feb, dst)
            $formatTanggal = "DATE_FORMAT(tgl_transaksi, '%Y-%m')";
            $formatTanggalKeluar = "DATE_FORMAT(tgl_pengeluaran, '%Y-%m')";
        } 
        else {
            // Default: Rentang Tanggal (Custom Range)
            $dari = $request->get('dari', now()->subDays(6)->toDateString());
            $sampai = $request->get('sampai', now()->toDateString());
            
            $queryPendapatan->whereBetween('tgl_transaksi', [$dari . ' 00:00:00', $sampai . ' 23:59:59']);
            $queryPengeluaran->whereBetween('tgl_pengeluaran', [$dari, $sampai]);
            $formatTanggal = "DATE(tgl_transaksi)";
            $formatTanggalKeluar = "DATE(tgl_pengeluaran)";
        }

        // 3. Ambil Data Grafik Pendapatan
        $grafikPendapatan = $queryPendapatan
            ->selectRaw("$formatTanggal as tanggal")
            ->selectRaw("SUM(total_bayar) as total")
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // 4. Ambil Data Grafik Pengeluaran
        $grafikPengeluaran = $queryPengeluaran
            ->selectRaw("$formatTanggalKeluar as tanggal")
            ->selectRaw("SUM(nominal) as total")
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // 5. Statistik Card (Tetap sama)
        $totalKasir = User::where('role', 'kasir')->count();
        $totalMenu = Menu::where('status', 1)->count(); 
        $pendapatanAngka = Transaksi::whereMonth('tgl_transaksi', date('m'))->sum('total_bayar');
        $totalPenjualan = "Rp " . number_format($pendapatanAngka, 0, ',', '.');

        return view('admin.dashboard', compact(
            'totalPenjualan', 'totalMenu', 'totalKasir', 
            'grafikPendapatan', 'grafikPengeluaran', 'mode'
        ));
    }
}