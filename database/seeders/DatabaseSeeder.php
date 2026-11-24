<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\LibrarySetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Membuat Data Pengaturan Perpustakaan
        LibrarySetting::create([
            'nama_perpustakaan' => 'Perpustakaan SMK Bisa',
            'tarif_denda_harian' => 1000,
        ]);

        // 2. Membuat Akun Admin
        User::create([
            'name' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@perpus.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // 3. Membuat Akun Siswa
        User::create([
            'name' => 'Budi Santoso',
            'username' => 'siswa',
            'email' => 'budi@sekolah.com',
            'password' => Hash::make('password'),
            'role' => 'siswa',
            'nis' => '100101',
            'kelas' => 'XII RPL 1',
        ]);

        // 4. Membuat 5 Kategori Awal
        $categories = [
            ['nama_kategori' => 'Fiksi', 'deskripsi' => 'Buku cerita, novel, dan khayalan'],
            ['nama_kategori' => 'Buku Pelajaran', 'deskripsi' => 'Materi pelajaran sekolah'],
            ['nama_kategori' => 'Teknologi', 'deskripsi' => 'Buku tentang komputer dan IT'],
            ['nama_kategori' => 'Sejarah', 'deskripsi' => 'Catatan peristiwa masa lalu'],
            ['nama_kategori' => 'Referensi', 'deskripsi' => 'Kamus, ensiklopedia, dan atlas'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
