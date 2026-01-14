<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Untuk fungsi database transaction
use App\Models\Menu;             // Memanggil model Menu
use App\Models\Transaksi;        // Memanggil model Transaksi
use App\Models\DetailTransaksi;  // Memanggil model Detail

class TransaksiController extends Controller
{
    // FUNGSI 1: Mengambil semua daftar menu untuk ditampilkan di web kasir
    public function index()
    {
        return response()->json(Menu::all());
    }

    // FUNGSI 2: Menyimpan transaksi dan menghitung angka secara OTOMATIS
    public function simpan(Request $request)
    {
        try {
            // Menggunakan DB::transaction agar jika satu langkah gagal, semua dibatalkan (aman)
            return DB::transaction(function () use ($request) {
                
                // --- LANGKAH A: HITUNG TOTAL BELANJA OTOMATIS ---
                $total_bayar_otomatis = 0;
                foreach ($request->items as $item) {
                    // Menjumlahkan: (harga satuan * jumlah beli)
                    $total_bayar_otomatis += ($item['harga_satuan'] * $item['qty']);
                }

                // --- LANGKAH B: SIMPAN DATA NOTA (HEADER) ---
                // Kode transaksi dibuat otomatis berdasarkan tanggal & jam (Contoh: TRS-20251218...)
                $transaksi = Transaksi::create([
                    'kode_transaksi' => 'TRS-' . date('YmdHis'), 
                    'id_kasir'       => $request->id_kasir,     // Diambil dari input (ID User 1)
                    'tgl_transaksi'  => now(),                  // Otomatis ambil waktu sekarang
                    'total_bayar'    => $total_bayar_otomatis,  // Hasil hitungan otomatis di atas
                    'jumlah_bayar'   => $request->jumlah_bayar, // Uang yang dibayar pelanggan
                    'kembalian'      => $request->jumlah_bayar - $total_bayar_otomatis, // Hitung kembalian otomatis
                ]);

                // --- LANGKAH C: SIMPAN RINCIAN BARANG (DETAIL) ---
                foreach ($request->items as $item) {
                    DetailTransaksi::create([
                        'kode_transaksi' => $transaksi->kode_transaksi, // Menghubungkan ke nota di atas
                        'id_menu'        => $item['id_menu'],
                        'quantity'       => $item['qty'],               // Sesuai nama kolom di fotomu
                        'harga_satuan'   => $item['harga_satuan'],
                        'subtotal'       => $item['qty'] * $item['harga_satuan'] // Hitung subtotal otomatis
                    ]);
                }

                // Memberikan respon balik ke Postman/Web Kasir jika berhasil
                return response()->json([
                    'status'  => 'success',
                    'message' => 'Transaksi Kandang Kopi Berhasil Disimpan!',
                    'ringkasan' => [
                        'total' => $total_bayar_otomatis,
                        'kembalian' => $transaksi->kembalian
                    ]
                ]);
            });
        } catch (\Exception $e) {
            // Jika ada yang salah (misal: ID kasir tidak ada), tampilkan pesan errornya
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // FUNGSI 3: Menampilkan Riwayat Laporan Penjualan (Tugas 18 Des)
    public function riwayat()
    {
        // Menampilkan data transaksi terbaru beserta detail itemnya
        $laporan = Transaksi::with('details')->orderBy('tgl_transaksi', 'desc')->get();
        return response()->json($laporan);
    }
}