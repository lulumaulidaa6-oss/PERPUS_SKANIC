<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminProfileController extends Controller
{
    /**
     * Menampilkan formulir Pengaturan Akun Admin
     */
    public function edit()
    {
        $user = Auth::user();

        return view('admin.pengaturan_akun', compact('user'));
    }

    /**
     * Memproses pembaruan username dan password admin
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'current_password' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required' => 'Nama lengkap admin wajib diisi.',
            'username.required' => 'Username admin wajib diisi.',
            'username.unique' => 'Username tersebut sudah digunakan oleh akun lain.',
            'username.alpha_dash' => 'Username hanya boleh terdiri dari huruf, angka, tanda hubung (-), dan garis bawah (_).',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Jika mengisi password baru, verifikasi password saat ini jika disertakan
        if ($request->filled('password')) {
            if ($request->filled('current_password') && ! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Password lama tidak sesuai.'])->withInput();
            }

            // Hashing password secara aman dengan algoritma default Laravel (bcrypt/Argon2)
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->name;
        $user->username = $request->username;
        if ($request->filled('email')) {
            $user->email = $request->email;
        }

        $user->save();

        return redirect()->route('admin.pengaturan-akun')
            ->with('success', 'Akun admin berhasil diperbarui! Anda dapat langsung login menggunakan username dan password yang baru.');
    }
}
