<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LaporanExport;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // 1. Ambil Input Filter Baru
        $filterType = $request->get('filter_type', 'range');
        $tipe = $request->get('tipe', 'semua');
        $search = $request->get('search');

        // Default Date Range (untuk fallback)
        $dari = $request->get('dari', Carbon::now()->startOfMonth()->toDateString());
        $sampai = $request->get('sampai', Carbon::now()->toDateString());

        // 2. Siapkan Query Pemasukan & Pengeluaran
        $queryPenjualan = Transaksi::query();
        $queryPengeluaran = Pengeluaran::query();

        // 3. Terapkan Logika Filter Waktu (Penting!)
        if ($filterType == 'bulan') {
            $bulan = $request->get('bulan', date('m'));
            $tahunBulan = $request->get('tahun_bulan', date('Y'));

            $queryPenjualan->whereMonth('tgl_transaksi', $bulan)->whereYear('tgl_transaksi', $tahunBulan);
            $queryPengeluaran->whereMonth('tgl_pengeluaran', $bulan)->whereYear('tgl_pengeluaran', $tahunBulan);
        } elseif ($filterType == 'tahun') {
            $tahun = $request->get('tahun', date('Y'));

            $queryPenjualan->whereYear('tgl_transaksi', $tahun);
            $queryPengeluaran->whereYear('tgl_pengeluaran', $tahun);
        } else {
            // Default: Range Tanggal
            $queryPenjualan->whereBetween('tgl_transaksi', [$dari . ' 00:00:00', $sampai . ' 23:59:59']);
            $queryPengeluaran->whereBetween('tgl_pengeluaran', [$dari, $sampai]);
        }

        // 4. Filter Berdasarkan Pencarian
        if ($search) {
            $queryPenjualan->where('kode_transaksi', 'like', "%$search%");
            $queryPengeluaran->where('catatan', 'like', "%$search%");
        }

        // 5. Hitung Statistik (Gunakan Clone Query agar tidak merusak data untuk tabel)
        $totalPendapatan = (clone $queryPenjualan)->sum('total_bayar');
        $totalPengeluaran = (clone $queryPengeluaran)->sum('nominal');
        $profit = $totalPendapatan - $totalPengeluaran;

        // 6. Ambil Data dan Mapping untuk Tabel
        $penjualanMap = collect();
        if ($tipe == 'semua' || $tipe == 'pemasukan') {
            $penjualanMap = $queryPenjualan->get()->map(function ($item) {
                return (object) [
                    'tanggal' => $item->tgl_transaksi,
                    'jenis' => 'Pemasukan',
                    'info' => 'Penjualan (' . $item->kode_transaksi . ')',
                    'nominal' => $item->total_bayar,
                    'warna' => 'green',
                    'petugas' => $item->kasir->name ?? 'Kasir Terhapus'
                ];
            });
        }

        $pengeluaranMap = collect();
        if ($tipe == 'semua' || $tipe == 'pengeluaran') {
            $pengeluaranMap = $queryPengeluaran->get()->map(function ($item) {
                return (object) [
                    'tanggal' => $item->tgl_pengeluaran,
                    'jenis' => 'Pengeluaran',
                    'info' => $item->kategori . ' (' . $item->catatan . ')',
                    'nominal' => $item->nominal,
                    'warna' => 'red',
                    'petugas' => $item->user->name ?? 'Admin Terhapus'
                ];
            });
        }

        // 7. Proses Pagination Manual
        $mergedData = $penjualanMap->concat($pengeluaranMap)->sortByDesc('tanggal');
        
        $currentPage = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;
        $currentItems = $mergedData->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $transaksiGabungan = new LengthAwarePaginator(
            $currentItems, 
            $mergedData->count(), 
            $perPage, 
            $currentPage, 
            ['path' => Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('admin.laporan', compact(
            'totalPendapatan', 'totalPengeluaran', 'profit', 
            'transaksiGabungan', 'dari', 'sampai', 'tipe'
        ));
    }

    public function exportPdf(Request $request)
    {
        // 1. Ambil Input Filter (Sama seperti index)
        $dari = $request->get('dari', Carbon::now()->startOfMonth()->toDateString());
        $sampai = $request->get('sampai', Carbon::now()->toDateString());
        $tipe = $request->get('tipe', 'semua');
        $search = $request->get('search');

        // 2. Logika Ambil Data Pemasukan
        $penjualanMap = collect();
        if ($tipe == 'semua' || $tipe == 'pemasukan') {
            $queryPenjualan = Transaksi::whereBetween('tgl_transaksi', [$dari . ' 00:00:00', $sampai . ' 23:59:59']);
            if ($search) { $queryPenjualan->where('kode_transaksi', 'like', "%$search%"); }
            $penjualanMap = $queryPenjualan->get()->map(function ($item) {
                return (object) [
                    'tanggal' => $item->tgl_transaksi,
                    'jenis' => 'Pemasukan',
                    'info' => 'Penjualan (' . $item->kode_transaksi . ')',
                    'nominal' => $item->total_bayar,
                    'warna' => 'green'
                ];
            });
        }

        // 3. Logika Ambil Data Pengeluaran
        $pengeluaranMap = collect();
        if ($tipe == 'semua' || $tipe == 'pengeluaran') {
            $queryPengeluaran = Pengeluaran::whereBetween('tgl_pengeluaran', [$dari, $sampai]);
            if ($search) { $queryPengeluaran->where('catatan', 'like', "%$search%"); }
            $pengeluaranMap = $queryPengeluaran->get()->map(function ($item) {
                return (object) [
                    'tanggal' => $item->tgl_pengeluaran,
                    'jenis' => 'Pengeluaran',
                    'info' => $item->kategori . ' (' . $item->catatan . ')',
                    'nominal' => $item->nominal,
                    'warna' => 'red'
                ];
            });
        }

        // 4. Gabungkan & Urutkan (Tanpa Pagination)
        $data = $penjualanMap->merge($pengeluaranMap)->sortByDesc('tanggal');

        // 5. Hitung Statistik Ringkas
        $totalMasuk = $penjualanMap->sum('nominal');
        $totalKeluar = $pengeluaranMap->sum('nominal');
        $saldo = $totalMasuk - $totalKeluar;

        // 6. Generate PDF
        $pdf = Pdf::loadView('admin.laporan_pdf', compact('data', 'dari', 'sampai', 'totalMasuk', 'totalKeluar', 'saldo'));
        
        // Set format kertas A4
        $pdf->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Keuangan_'.$dari.'_sd_'.$sampai.'.pdf');
    }

    public function exportExcel(Request $request)
    {
        // 1. Ambil Input Filter (Sama dengan fungsi index)
        $filterType = $request->get('filter_type', 'range');
        $tipe = $request->get('tipe', 'semua');
        $search = $request->get('search');
        $dari = $request->get('dari', now()->startOfMonth()->toDateString());
        $sampai = $request->get('sampai', now()->toDateString());

        // 2. Query Data (Pastikan logika filter waktu sama dengan dashboard/laporan)
        $queryPenjualan = Transaksi::query();
        $queryPengeluaran = Pengeluaran::query();

        if ($filterType == 'bulan') {
            $bulan = $request->get('bulan', date('m'));
            $tahunBulan = $request->get('tahun_bulan', date('Y'));
            $queryPenjualan->whereMonth('tgl_transaksi', $bulan)->whereYear('tgl_transaksi', $tahunBulan);
            $queryPengeluaran->whereMonth('tgl_pengeluaran', $bulan)->whereYear('tgl_pengeluaran', $tahunBulan);
        } elseif ($filterType == 'tahun') {
            $tahun = $request->get('tahun', date('Y'));
            $queryPenjualan->whereYear('tgl_transaksi', $tahun);
            $queryPengeluaran->whereYear('tgl_pengeluaran', $tahun);
        } else {
            $queryPenjualan->whereBetween('tgl_transaksi', [$dari . ' 00:00:00', $sampai . ' 23:59:59']);
            $queryPengeluaran->whereBetween('tgl_pengeluaran', [$dari, $sampai]);
        }

        // 3. Mapping Data untuk Excel
        $penjualanMap = ($tipe == 'semua' || $tipe == 'pemasukan') ? $queryPenjualan->get()->map(function ($item) {
            return (object) ['tanggal' => $item->tgl_transaksi, 'jenis' => 'Pemasukan', 'info' => 'Penjualan (' . $item->kode_transaksi . ')', 'nominal' => $item->total_bayar];
        }) : collect();

        $pengeluaranMap = ($tipe == 'semua' || $tipe == 'pengeluaran') ? $queryPengeluaran->get()->map(function ($item) {
            return (object) ['tanggal' => $item->tgl_pengeluaran, 'jenis' => 'Pengeluaran', 'info' => $item->kategori . ' (' . $item->catatan . ')', 'nominal' => $item->nominal];
        }) : collect();

        $data = $penjualanMap->concat($pengeluaranMap)->sortByDesc('tanggal');

        // 4. Download File
        return Excel::download(new LaporanExport($data), 'Laporan_Keuangan_KandangKopi.xlsx');
    }
}