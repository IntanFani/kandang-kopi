<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Models\DetailTransaksi;

class KasirController extends Controller
{
    public function index()
    {
        // Mengambil menu yang statusnya aktif (1)
        $menus = Menu::where('status', 1)->get();
        $categories = Menu::distinct()->pluck('kategori');
        
        // Sesuaikan dengan nama file: resources/views/kasir/dashboard.blade.php
        return view('kasir.dashboard', compact('menus', 'categories'));
    }

    public function store(Request $request)
    {
        try {
            // 1. Simpan ke tabel Transaksi
            $transaksi = Transaksi::create([
                'kode_transaksi' => 'TRS-' . time(),
                'nama_customer'  => $request->nama_customer,
                'id_kasir'       => auth()->id(),
                'tgl_transaksi'  => now(),
                'total_bayar'    => $request->total_harga,
                'jumlah_bayar'   => $request->tunai,
                'kembalian'      => $request->kembalian,
            ]);

            // 2. Simpan ke tabel Detail Transaksi
            foreach ($request->items as $item) {
                // Cari menu berdasarkan nama untuk mendapatkan id_menu
                $menu = Menu::where('nama_menu', $item['nama'])->first();
                
                DetailTransaksi::create([
                    'kode_transaksi' => $transaksi->kode_transaksi,
                    'id_menu'        => $menu->id,
                    'quantity'       => $item['qty'],
                    'harga_satuan'   => $item['harga'],
                    'subtotal'       => $item['subtotal'],
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Transaksi Berhasil']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}