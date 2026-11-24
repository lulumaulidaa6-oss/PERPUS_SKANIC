<?php

use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\SiswaController;
use App\Models\Book;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Support\Facades\Route;

// Arahkan pengunjung halaman utama langsung ke beranda katalog siswa (tanpa login)
Route::get('/', function () {
    return redirect('/siswa/dashboard');
});

// Jalur khusus sistem autentikasi Petugas/Admin (Login & Logout)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- HALAMAN PUBLIK SISWA (MANDIRI TANPA LOGIN) ---
Route::get('/siswa/dashboard', [SiswaController::class, 'index'])->name('siswa.dashboard');
Route::get('/siswa/pinjam', [SiswaController::class, 'create'])->name('siswa.pinjam');
Route::post('/siswa/pinjam', [SiswaController::class, 'store']);

// Menu Terpisah Khusus Siswa Sesuai Permintaan:
// 1. Melihat peminjaman yang masih menunggu persetujuan
Route::get('/siswa/menunggu', [SiswaController::class, 'menunggu'])->name('siswa.menunggu');

// 2. Melihat peminjaman yang sudah disetujui / di-ACC & Ajukan Pengembalian Buku
Route::get('/siswa/disetujui', [SiswaController::class, 'disetujui'])->name('siswa.disetujui');
Route::post('/siswa/peminjaman/{id}/kembalikan', [SiswaController::class, 'ajukanPengembalian'])->name('siswa.peminjaman.kembalikan');

// 3. Melihat denda yang dimiliki siswa
Route::get('/siswa/denda', [SiswaController::class, 'denda'])->name('siswa.denda');

// 4. Melihat riwayat buku yang pernah dipinjam (selesai)
Route::get('/siswa/riwayat-selesai', [SiswaController::class, 'riwayatSelesai'])->name('siswa.riwayat-selesai');

// 5. Fitur Cek Riwayat Peminjaman & Data Denda Mandiri
Route::get('/siswa/riwayat', [SiswaController::class, 'riwayat'])->name('siswa.riwayat');

// Route khusus Admin / Petugas Perpustakaan
Route::middleware(['auth', 'role:admin'])->group(function () {

    Route::get('/admin/dashboard', function () {
        // Menghitung jumlah data dari database
        $total_buku = Book::count();
        $total_stok_buku = (int) Book::sum('stok');
        // Menghitung total siswa dari riwayat peminjam atau data pengguna
        $total_siswa = Peminjaman::whereNotNull('nama_siswa')->distinct('nama_siswa')->count('nama_siswa') ?: User::where('role', 'siswa')->count();
        $buku_dipinjam = Peminjaman::where('status', 'dipinjam')->count();

        // Data statistik denda siswa & antrean
        $allPeminjaman = Peminjaman::all();
        $total_denda = (int) $allPeminjaman->sum(fn ($p) => $p->total_denda);
        $denda_belum_dibayar = (int) $allPeminjaman->filter(fn ($p) => $p->status_pembayaran_denda === 'belum_dibayar')->sum(fn ($p) => $p->total_denda);
        $transaksi_denda_aktif = $allPeminjaman->filter(fn ($p) => $p->status_pembayaran_denda === 'belum_dibayar' && $p->total_denda > 0)->count();
        $menunggu_persetujuan = $allPeminjaman->where('status', 'menunggu')->count();
        $terlambat_aktif = $allPeminjaman->filter(fn ($p) => $p->is_terlambat && $p->status === 'dipinjam')->count();

        // Mengirim data hitungan ke halaman view admin
        return view('admin.dashboard', compact(
            'total_buku',
            'total_stok_buku',
            'total_siswa',
            'buku_dipinjam',
            'total_denda',
            'denda_belum_dibayar',
            'transaksi_denda_aktif',
            'menunggu_persetujuan',
            'terlambat_aktif'
        ));
    })->name('admin.dashboard');
    // ------------------------------

    Route::get('/buku', [BookController::class, 'index']);
    Route::get('/buku/create', [BookController::class, 'create']);
    Route::post('/buku', [BookController::class, 'store']);
    Route::get('/buku/{id}/edit', [BookController::class, 'edit']);
    Route::put('/buku/{id}', [BookController::class, 'update']);

    // Tambahkan rute hapus buku di sini:
    Route::delete('/buku/{id}', [BookController::class, 'destroy']);

    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/export-excel', [PeminjamanController::class, 'exportExcel'])->name('peminjaman.export-excel');
    Route::get('/peminjaman/create', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/peminjaman', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    // Tambahkan rute pengembalian & denda siswa:
    Route::put('/peminjaman/{id}/kembali', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembali');
    Route::put('/peminjaman/{id}/bayar-denda', [PeminjamanController::class, 'bayarDenda'])->name('peminjaman.bayar-denda');
    Route::put('/peminjaman/{id}/update-denda', [PeminjamanController::class, 'updateDenda'])->name('peminjaman.update-denda');
    // Rute Persetujuan Peminjaman & Penolakan & Hapus Data
    Route::put('/peminjaman/{id}/setujui', [PeminjamanController::class, 'setujui'])->name('peminjaman.setujui');
    Route::match(['delete', 'post'], '/peminjaman/{id}/tolak', [PeminjamanController::class, 'tolak'])->name('peminjaman.tolak');
    Route::delete('/peminjaman/{id}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');

    // Rute Pengaturan Akun Admin (Ubah Username & Password)
    Route::get('/admin/pengaturan-akun', [AdminProfileController::class, 'edit'])->name('admin.pengaturan-akun');
    Route::put('/admin/pengaturan-akun', [AdminProfileController::class, 'update'])->name('admin.pengaturan-akun.update');
});
