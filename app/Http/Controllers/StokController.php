<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stok;
use Carbon\Carbon;

class StokController extends Controller
{
    public function index()
    {
        // Ambil semua data stok dengan relasi user (untuk kolom Petugas)
        $riwayat = Stok::with('user')->orderBy('tanggal_masuk', 'desc')->get();

        // Hitung stok saat ini per bahan baku
        $stokSaatIni = $riwayat
            ->groupBy('bahan_baku')
            ->map(function ($items) {
                return $items->sum('jumlah');
            });

        return view('admin.stock', [
            'stokSaatIni' => $stokSaatIni,
            'riwayat' => $riwayat
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'bahan_baku'    => 'required|string',
            'jumlah'        => 'required|numeric|min:0.01', // numeric agar bisa input desimal (misal 0.5 kg)
            'satuan'        => 'required|string',
            'tanggal_masuk' => 'required|date',
            'expired'       => 'nullable|date',
            'harga'         => 'required|numeric', // Ini menangkap TOTAL harga dari hasil perkalian JS
            'tipe'          => 'required|in:masuk,keluar',
        ]);

        $jumlah = $request->jumlah;
        $totalHarga = $request->harga;

        // 2. Logika Khusus Stok Keluar
        if ($request->tipe === 'keluar') {
            // Hitung sisa stok yang ada di database untuk bahan tersebut
            $stokSekarang = Stok::where('bahan_baku', $request->bahan_baku)->sum('jumlah');

            // Cek apakah sisa cukup untuk dikurangi
            if ($stokSekarang < $jumlah) {
                return redirect()->back()
                    ->with('error', "Stok {$request->bahan_baku} tidak mencukupi! Sisa saat ini: {$stokSekarang}");
            }

            // Jadikan jumlah negatif untuk mengurangi total stok (logic pengurangan)
            $jumlah = -abs($jumlah);
            
            // Untuk stok keluar, harga biasanya dicatat sebagai 0 atau beban biaya
            // Tergantung kebutuhan laporan keuangan Anda.
        }

        // 3. Simpan Data
        Stok::create([
            'bahan_baku'    => $request->bahan_baku,
            'jumlah'        => $jumlah,
            'satuan'        => $request->satuan,
            'tanggal_masuk' => $request->tanggal_masuk,
            'expired'       => $request->expired,
            'harga'         => $totalHarga,
            // Menambahkan ID User/Admin yang menginput (Petugas) sesuai permintaan sebelumnya
            'id_user'       => auth()->id(), 
        ]);

        return redirect()->back()->with('success', 'Data stok berhasil diperbarui!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'bahan_baku'    => 'required|string',
            'jumlah'        => 'required|numeric',
            'tanggal_masuk' => 'required|date',
        ]);

        $stok = Stok::find($id);
        if (!$stok) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan'], 404);
        }

        // Jika Anda mengedit dari Masuk menjadi Keluar secara manual, 
        // pastikan logika negatif/positifnya ditangani di sini.
        
        $stok->update([
            'bahan_baku'    => $request->bahan_baku,
            'jumlah'        => $request->jumlah, // Hati-hati: pastikan tetap negatif jika itu pengeluaran
            'satuan'        => $request->satuan,
            'tanggal_masuk' => $request->tanggal_masuk,
            'expired'       => $request->expired,
            'harga'         => $request->harga,
        ]);

        return response()->json(['success' => true]);
    }

    // --- TAMBAHKAN FUNGSI DESTROY DI BAWAH INI ---
    public function destroy($id)
    {
        $stok = Stok::find($id);
        if ($stok) {
            $stok->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 404);
    }

}