<?php

namespace App\Http\Controllers\Api;

use App\Models\Menu;
use App\Models\Stok;
use App\Models\User;
use App\Models\Transaksi;
use App\Models\StokKeluar;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use App\Models\DetailTransaksi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class KandangKopiController extends Controller
{
    // ==========================================
    // 1. FITUR KASIR (TRANSAKSI & MENU)
    // ==========================================

    public function kasirLihatMenu() {
        return response()->json(['status' => 'success', 'data' => Menu::all()]);
    }

    public function simpanTransaksi(Request $request) {
        return DB::transaction(function () use ($request) {
            try {
                $totalHarga = 0;
                foreach ($request->items as $item) {
                    $totalHarga += ($item['harga_satuan'] * $item['qty']);
                }

                $transaksi = Transaksi::create([
                    'kode_transaksi' => 'TRS-' . date('YmdHis'),
                    'id_kasir'       => $request->id_kasir, 
                    'tgl_transaksi'  => now(),
                    'total_bayar'    => $totalHarga,
                    'jumlah_bayar'   => $request->jumlah_bayar,
                    'kembalian'      => $request->jumlah_bayar - $totalHarga,
                ]);

                foreach ($request->items as $item) {
                    DetailTransaksi::create([
                        'kode_transaksi' => $transaksi->kode_transaksi,
                        'id_menu'        => $item['id_menu'],
                        'harga_satuan'   => $item['harga_satuan'],
                        'quantity'       => $item['qty'],
                        'subtotal'       => $item['qty'] * $item['harga_satuan']
                    ]);
                }
                return response()->json(['status' => 'success', 'message' => 'Nota Berhasil Dicetak']);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        });
    }

    // ==========================================
    // 2. FITUR ADMIN (LENGKAP SEMUANYA)
    // ==========================================

    // 1. Tambah Pengeluaran Manual (Listrik, Gaji, dll)
    public function tambahPengeluaran(Request $request) {
        try {
            $admin = User::where('role', 'admin')->first();
            $pengeluaran = Pengeluaran::create([
                'id_pemilik'           => $admin ? $admin->id_user : 1,
                'tanggal'              => now()->toDateString(),
                'kategori_pengeluaran' => $request->kategori, // Contoh: Operasional
                'catatan'              => $request->catatan,  // Contoh: Bayar Listrik
                'nominal'              => $request->nominal
            ]);
            return response()->json(['status' => 'success', 'data' => $pengeluaran]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 2. Lihat Semua Pengeluaran untuk Tabel
    public function adminLihatPengeluaran() {
        return response()->json(['status' => 'success', 'data' => Pengeluaran::all()]);
    }
    // admin melihat menu
    public function adminLihatMenu() {
        return response()->json(['status' => 'success', 'data' => Menu::all()]);
    }
    // Mengelola Menu
    public function tambahMenu(Request $request) {
        $menu = Menu::create($request->all());
        return response()->json(['status' => 'success', 'data' => $menu]);
    }

    public function updateMenu(Request $request, $id) {
        $menu = Menu::find($id);
        if ($menu) {
            $menu->update($request->all());
            return response()->json(['status' => 'success', 'message' => 'Menu berhasil diupdate', 'data' => $menu]);
        }
        return response()->json(['status' => 'error', 'message' => 'Menu tidak ditemukan'], 404);
    }

    public function hapusMenu($id) {
        $menu = Menu::find($id);
        if ($menu) {
            $menu->delete();
            return response()->json(['status' => 'success', 'message' => 'Menu berhasil dihapus']);
        }
        return response()->json(['status' => 'error', 'message' => 'Menu tidak ditemukan'], 404);
    }
    // Admin: Lihat Semua User untuk Tabel
    public function adminLihatUser() {
        return response()->json(['status' => 'success', 'data' => User::all()]);
    }
    // Mengelola Akun User
    public function tambahUser(Request $request) {
        $user = User::create([
            'nama'     => $request->nama,
            'username' => $request->username,
            'role'     => $request->role, 
            'password' => bcrypt($request->password)
        ]);
        return response()->json(['status' => 'success', 'data' => $user]);
    }

    // Admin: Lihat Semua Stok untuk Tabel
    public function adminLihatStok() {
        return response()->json(['status' => 'success', 'data' => Stok::all()]);
    }

    // Mengelola Stok & Pengeluaran Otomatis (Solusi Error Null)
    public function tambahStok(Request $request) {
        try {
            return DB::transaction(function () use ($request) {
                // Gunakan input() agar data terbaca meskipun format Postman kurang pas
                $stok = Stok::create([
                    'bahan_baku'    => $request->input('bahan_baku'), 
                    'tanggal_masuk' => now()->toDateString(),
                    'expired'       => $request->input('expired'),
                    'jumlah'        => $request->input('jumlah'),
                    'satuan'        => $request->input('satuan'), 
                    'harga'         => $request->input('harga_beli')
                ]);

                // Otomatis catat ke pengeluaran (Cari Admin pertama)
                $admin = User::where('role', 'admin')->first();
                Pengeluaran::create([
                    'id_pemilik'           => $admin ? $admin->id_user : 1, 
                    'tanggal'              => now()->toDateString(),
                    'kategori_pengeluaran' => 'Bahan Baku',
                    'catatan'              => 'Beli Stok: ' . $request->input('bahan_baku'),
                    'nominal'              => $request->input('harga_beli')
                ]);

                return response()->json(['status' => 'success', 'data' => $stok]);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    // 1. Input data stok yang dipakai (Keluar)
    public function tambahStokKeluar(Request $request) {
        try {
            $stokKeluar = StokKeluar::create([
                'bahan_baku_keluar'    => $request->bahan_baku, // Misal: Kopi Bubuk
                'tanggal_masuk_keluar' => now()->toDateString(),
                'expired_keluar'       => $request->expired,
                'jumlah_keluar'        => $request->jumlah, 
                'satuan_keluar'        => $request->satuan,
                'harga_keluar'         => $request->harga
            ]);
            return response()->json(['status' => 'success', 'data' => $stokKeluar]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // 2. Fungsi sakti untuk liat sisa stok (Stok Masuk - Stok Keluar)
    public function cekSaldoStok() {
        // Ambil semua jenis bahan baku unik
        $bahanBaku = Stok::distinct()->pluck('bahan_baku'); 
        $laporanStok = [];

        foreach ($bahanBaku as $nama) {
            $totalMasuk = Stok::where('bahan_baku', $nama)->sum('jumlah'); // Dari tabel stok
            $totalKeluar = StokKeluar::where('bahan_baku_keluar', $nama)->sum('jumlah_keluar'); // Dari tabel stok_keluar
            
            $laporanStok[] = [
                'bahan_baku' => $nama,
                'total_masuk' => (int)$totalMasuk,
                'total_keluar' => (int)$totalKeluar,
                'sisa_stok' => $totalMasuk - $totalKeluar // Ini hasilnya!
            ];
        }

        return response()->json(['status' => 'success', 'data' => $laporanStok]);
    }

    // Laporan Seluruh (Pemasukan & Pengeluaran)
    public function laporanLengkap() {
        $pemasukan = Transaksi::sum('total_bayar');
        $pengeluaran = Pengeluaran::sum('nominal');
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_pemasukan' => $pemasukan,
                'total_pengeluaran' => $pengeluaran,
                'keuntungan' => $pemasukan - $pengeluaran,
                'riwayat_pengeluaran' => Pengeluaran::all()
            ]
        ]);
    }
}