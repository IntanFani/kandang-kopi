<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class MenuController extends Controller
{
    /**
     * Menampilkan halaman menu
     */
    public function index(Request $request)
    {
        // 1. Inisialisasi query
        $query = Menu::query();

        // 2. Logika Pencarian: Jika input search diisi
        if ($request->filled('search')) {
            $query->where('nama_menu', 'like', '%' . $request->search . '%');
        }

        // 3. Logika Filter: Jika tombol kategori diklik
        if ($request->filled('category')) {
            $query->where('kategori', $request->category);
        }

        // 4. Ambil data final (Urutkan dari yang terbaru)
        $menus = $query->latest()->get();

        return view('admin.menu', compact('menus'));
    }

    /**
     * Menyimpan data menu baru
     */
    public function store(Request $request)
    {
        // 1. Validasi data yang masuk dari form
        $validatedData = $request->validate([
            'nama_menu' => 'required|string|max:255',
            'kategori'  => 'required',
            'harga'     => 'required|numeric|min:0',
            'foto'      => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 2. Logika Simpan Foto ke folder public/images
        if ($request->hasFile('foto')) {
            if (!File::isDirectory(public_path('images'))) {
                File::makeDirectory(public_path('images'), 0777, true, true);
            }

            $file = $request->file('foto');
            $nama_foto = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('images'), $nama_foto);
            $validatedData['foto'] = $nama_foto;
        }

        // 3. Set nilai default untuk status dan stok
        $validatedData['status'] = $request->has('status') ? 1 : 0;
        $validatedData['stok'] = 0; // Default stok 0 karena di form tidak ada inputan stok

        // 4. Eksekusi simpan ke Database
        try {
            Menu::create($validatedData);
            return redirect()->back()->with('success', 'Menu Berhasil Ditambahkan!');
        } catch (\Exception $e) {
            // Jika ada error database, akan muncul pesan errornya
            return redirect()->back()->with('error', 'Gagal simpan data: ' . $e->getMessage());
        }
    }

    // Fungsi untuk menghapus menu
    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);
        
        // Hapus foto dari folder public/images jika ada
        if ($menu->foto && file_exists(public_path('images/' . $menu->foto))) {
            unlink(public_path('images/' . $menu->foto));
        }

        $menu->delete();
        return back()->with('success', 'Menu berhasil dihapus!');
    }

    // Fungsi untuk update status (Tersedia/Habis)
    public function updateStatus($id)
    {
        $menu = Menu::findOrFail($id);
        $menu->status = !$menu->status; // Toggle status (0 jadi 1, 1 jadi 0)
        $menu->save();

        return back()->with('success', 'Status menu berhasil diubah!');
    }


    //fungsi untuk mengedit menu
    public function update(Request $request, $id)
{
    $request->validate([
        'nama_menu' => 'required',
        'harga' => 'required|numeric',
        'foto' => 'image|mimes:jpeg,png,jpg|max:2048',
    ]);

    $menu = Menu::findOrFail($id);
    $menu->nama_menu = $request->nama_menu;
    $menu->harga = $request->harga;
    $menu->kategori = $request->kategori;

    if ($request->hasFile('foto')) {
        // Hapus foto lama jika ada foto baru
        if ($menu->foto && file_exists(public_path('images/' . $menu->foto))) {
            unlink(public_path('images/' . $menu->foto));
        }
        $namaFoto = time() . '.' . $request->foto->extension();
        $request->foto->move(public_path('images'), $namaFoto);
        $menu->foto = $namaFoto;
    }

    $menu->save();
    return back()->with('success', 'Menu berhasil diperbarui!');
}
}