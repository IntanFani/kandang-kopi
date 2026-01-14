<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\User; 

class LoginController extends Controller
{
    public function index(): View 
    {
        return view('login'); 
    }

    public function authenticate(Request $request): RedirectResponse 
    {
        // 1. Validasi input
        $credentials = $request->validate([
            'username' => ['required'], 
            'password' => ['required'],
        ]);

        // 2. Proses Login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // 3. Pengalihan berdasarkan role
            if ($user->role === 'admin') {
                return redirect()->intended('/admin/dashboard'); 
            } 
            
            if ($user->role === 'kasir') {
                return redirect()->intended('/kasir/dashboard');
            }

            return redirect()->intended('/');
        }

        // 4. Jika gagal login
        return back()->with('loginError', 'Username atau password salah!')->withInput($request->only('username'));
    }

    public function logout(Request $request): RedirectResponse 
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}