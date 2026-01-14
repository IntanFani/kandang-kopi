<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        // Mengambil statistik ringkas
        $totalMenu = Menu::count();
        $totalKasir = User::where('role', 'kasir')->count();
        
        // Kirim data ke view
        return view('admin.dashboard', compact('totalMenu', 'totalKasir'));
    }
}