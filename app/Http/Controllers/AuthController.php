<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 1. Menampilkan halaman form login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // 2. Memproses data login yang diinput user
    public function login(Request $request)
    {
        // Validasi inputan tidak boleh kosong
        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        // Mengecek kecocokan username dan password di database
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Hanya admin/petugas yang dapat masuk ke panel admin
            if (Auth::user()->role === 'admin') {
                return redirect()->intended('/admin/dashboard');
            }

            // Jika bukan admin, logout otomatis
            Auth::logout();

            return back()->withErrors([
                'username' => 'Hanya petugas / admin perpustakaan yang dapat login. Siswa dapat langsung meminjam buku dan cek riwayat tanpa akun.',
            ]);
        }

        // Jika login gagal, kembalikan ke halaman login dengan pesan error
        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    // 3. Memproses proses keluar (logout)
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/siswa/dashboard')->with('success', 'Berhasil keluar dari akun petugas.');
    }
}
