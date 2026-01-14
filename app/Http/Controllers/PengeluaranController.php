<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengeluaran::query();

        // 1. Filter Berdasarkan Pencarian
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('kategori', 'like', '%' . $request->search . '%')
                ->orWhere('catatan', 'like', '%' . $request->search . '%');
            });
        }

        // 2. Logika Filter Tanggal Dinamis
        $filterType = $request->get('filter_type');

        if ($filterType == 'hari') {
            $query->whereDate('tgl_pengeluaran', $request->get('tgl_hari', date('Y-m-d')));
        } elseif ($filterType == 'bulan') {
            $bulan = explode('-', $request->get('tgl_bulan', date('Y-m'))); // Format: YYYY-MM
            $query->whereYear('tgl_pengeluaran', $bulan[0])
                ->whereMonth('tgl_pengeluaran', $bulan[1]);
        } elseif ($filterType == 'tahun') {
            $query->whereYear('tgl_pengeluaran', $request->get('tgl_tahun', date('Y')));
        } elseif ($filterType == 'range') {
            if ($request->dari && $request->sampai) {
                $query->whereBetween('tgl_pengeluaran', [$request->dari, $request->sampai]);
            }
        } else {
            // Default: Menampilkan semua data jika tidak ada filter
        }

        // Ambil data dengan paginasi 10
        $data = $query->latest('tgl_pengeluaran')->paginate(10);

        // Hitung statistik (Tanpa terpengaruh paginasi)
        $hariIni = Pengeluaran::whereDate('tgl_pengeluaran', date('Y-m-d'))->sum('nominal');
        $bulanIni = Pengeluaran::whereMonth('tgl_pengeluaran', date('m'))->whereYear('tgl_pengeluaran', date('Y'))->sum('nominal');
        $totalPengeluaran = Pengeluaran::sum('nominal');

        return view('admin.pengeluaran', compact('data', 'hariIni', 'bulanIni', 'totalPengeluaran'));
    }

   public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'tgl_pengeluaran' => 'required|date',
            'kategori'        => 'required',
            'nominal'         => 'required|numeric',
            'catatan'         => 'nullable|string'
        ]);

        // 2. Simpan Data
        Pengeluaran::create([
            'tgl_pengeluaran' => $request->tgl_pengeluaran,
            'kategori'        => $request->kategori,
            'nominal'         => $request->nominal,
            'catatan'         => $request->catatan,
        ]);

        // 3. Respon JSON (Penting untuk AJAX)
        return response()->json([
            'success' => true,
            'message' => 'Data berhasil disimpan'
        ]);
    }

    // app/Http/Controllers/PengeluaranController.php

    public function update(Request $request, $id)
    {
        // Cari data berdasarkan ID
        $pengeluaran = Pengeluaran::find($id);

        if (!$pengeluaran) {
            return response()->json([
                'success' => false, 
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        // Update data
        $pengeluaran->tgl_pengeluaran = $request->tgl_pengeluaran;
        $pengeluaran->kategori = $request->kategori;
        $pengeluaran->catatan = $request->catatan;
        $pengeluaran->nominal = $request->nominal;
        
        $simpan = $pengeluaran->save();

        return response()->json(['success' => $simpan]);
    }

    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::find($id);
        
        if ($pengeluaran) {
            $hapus = $pengeluaran->delete();
            return response()->json(['success' => $hapus]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal menghapus']);
    }
}