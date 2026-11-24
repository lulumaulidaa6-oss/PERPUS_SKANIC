<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\LibrarySetting;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    /**
     * 1. Melihat stok dan daftar buku yang tersedia di perpustakaan
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $query = Book::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'LIKE', "%{$search}%")
                    ->orWhere('penulis', 'LIKE', "%{$search}%")
                    ->orWhere('penerbit', 'LIKE', "%{$search}%");
            });
        }

        $books = $query->latest('id')->get();

        return view('siswa.dashboard', compact('books', 'search'));
    }

    /**
     * 2. Menampilkan formulir peminjaman buku
     */
    public function create(Request $request)
    {
        // Hanya buku yang stoknya masih tersedia
        $books = Book::where('stok', '>', 0)->get();
        $selectedBookId = $request->input('book_id');

        return view('siswa.pinjam', compact('books', 'selectedBookId'));
    }

    /**
     * Menyimpan data pengajuan peminjaman dari formulir siswa
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_siswa' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'book_id' => 'required|exists:books,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_perkiraan_kembali' => 'required|date|after_or_equal:tanggal_pinjam|before_or_equal:+1 year',
            'bukti_foto' => 'required|image|max:2048',
        ], [
            'nama_siswa.required' => 'Nama lengkap siswa wajib diisi.',
            'kelas.required' => 'Kelas siswa wajib diisi.',
            'book_id.required' => 'Pilih buku yang ingin dipinjam.',
            'tanggal_pinjam.required' => 'Tanggal pinjam wajib diisi.',
            'tanggal_perkiraan_kembali.required' => 'Tanggal perkiraan kembali wajib diisi.',
            'bukti_foto.required' => 'Bukti foto peminjam / kartu pelajar wajib diunggah.',
        ]);

        return DB::transaction(function () use ($request) {
            $book = Book::where('id', $request->book_id)->lockForUpdate()->firstOrFail();

            if ($book->stok <= 0) {
                return redirect()->back()->withInput()->with('error', "Maaf, stok buku '{$book->judul}' saat ini sedang habis.");
            }

            $fotoPath = $request->file('bukti_foto')->store('bukti_pinjam', 'public');
            StorageFileController::syncToPublic($fotoPath);

            // Saat peminjaman masih menunggu persetujuan (menunggu): stok buku tidak berkurang
            Peminjaman::create([
                'user_id' => null, // Sistem mandiri publik tanpa login
                'nama_siswa' => $request->nama_siswa,
                'kelas' => $request->kelas,
                'book_id' => $request->book_id,
                'tanggal_pinjam' => $request->tanggal_pinjam,
                'tanggal_perkiraan_kembali' => $request->tanggal_perkiraan_kembali,
                'bukti_foto' => $fotoPath,
                'status' => 'menunggu',
                'status_denda' => 'bebas',
            ]);

            return redirect('/siswa/riwayat?nama='.urlencode($request->nama_siswa))
                ->with('success', 'Pengajuan peminjaman berhasil dikirim! Silakan pantau status persetujuan di halaman ini.');
        });
    }

    /**
     * 3. Melihat peminjaman yang masih MENUNGGU PERSETUJUAN petugas
     */
    public function menunggu(Request $request)
    {
        $nama_pencarian = trim((string) $request->input('nama', ''));

        $query = Peminjaman::with('book')->where('status', 'menunggu');

        if ($nama_pencarian !== '') {
            $query->where(function ($q) use ($nama_pencarian) {
                $q->where('nama_siswa', 'LIKE', "%{$nama_pencarian}%")
                    ->orWhere('kelas', 'LIKE', "%{$nama_pencarian}%");
            });
        }

        $peminjaman = $query->latest('id')->get();

        return view('siswa.menunggu', compact('peminjaman', 'nama_pencarian'));
    }

    /**
     * 4. Melihat peminjaman yang SUDAH DISETUJUI / DI-ACC (sedang dipinjam & menunggu pengembalian)
     */
    public function disetujui(Request $request)
    {
        $nama_pencarian = trim((string) $request->input('nama', ''));

        $query = Peminjaman::with('book')->whereIn('status', ['dipinjam', 'menunggu_pengembalian']);

        if ($nama_pencarian !== '') {
            $query->where(function ($q) use ($nama_pencarian) {
                $q->where('nama_siswa', 'LIKE', "%{$nama_pencarian}%")
                    ->orWhere('kelas', 'LIKE', "%{$nama_pencarian}%");
            });
        }

        $peminjaman = $query->latest('id')->get();

        return view('siswa.disetujui', compact('peminjaman', 'nama_pencarian'));
    }

    /**
     * Siswa mengajukan pengembalian buku dengan mengunggah foto kondisi buku
     */
    public function ajukanPengembalian(Request $request, $id)
    {
        $request->validate([
            'foto_kembali' => 'required|image|max:3072',
            'catatan_kembali' => 'nullable|string|max:500',
        ], [
            'foto_kembali.required' => 'Foto bukti kondisi buku saat akan dikembalikan wajib diunggah.',
            'foto_kembali.image' => 'File bukti harus berupa gambar (JPG, JPEG, PNG, atau WEBP).',
            'foto_kembali.max' => 'Ukuran foto maksimal adalah 3 MB.',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $pinjam = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($pinjam->status === 'menunggu_pengembalian') {
                return redirect()->back()->with('info', 'Pengajuan pengembalian buku "'.$pinjam->book?->judul.'" sudah dikirim sebelumnya dan sedang menunggu konfirmasi petugas.');
            }

            if ($pinjam->status !== 'dipinjam') {
                return redirect()->back()->with('error', 'Buku ini tidak dalam status sedang dipinjam.');
            }

            $fotoPath = $request->file('foto_kembali')->store('bukti_kembali', 'public');
            StorageFileController::syncToPublic($fotoPath);

            // Jika pengembalian belum dikonfirmasi admin: stok buku belum bertambah
            $pinjam->update([
                'status' => 'menunggu_pengembalian',
                'tanggal_pengajuan_kembali' => Carbon::today(),
                'foto_kembali' => $fotoPath,
                'catatan_kembali' => $request->catatan_kembali,
            ]);

            return redirect()->back()->with('success', 'Pengajuan pengembalian buku "'.$pinjam->book?->judul.'" berhasil dikirim! Status kini: Menunggu Pengembalian. Silakan serahkan buku ke meja perpustakaan untuk dikonfirmasi oleh petugas.');
        });
    }

    /**
     * 5. Melihat DENDA yang dimiliki siswa
     */
    public function denda(Request $request)
    {
        $nama_pencarian = trim((string) $request->input('nama', ''));

        $setting = LibrarySetting::first();
        $tarif_denda = $setting?->tarif_denda_harian ?? 1000;

        $query = Peminjaman::with('book');

        if ($nama_pencarian !== '') {
            $query->where(function ($q) use ($nama_pencarian) {
                $q->where('nama_siswa', 'LIKE', "%{$nama_pencarian}%")
                    ->orWhere('kelas', 'LIKE', "%{$nama_pencarian}%");
            });
        }

        // Ambil data yang memiliki denda belum lunas atau sedang terlambat
        $allData = $query->latest('id')->get();
        $peminjaman = $allData->filter(function ($item) {
            return $item->total_denda > 0 && ! $item->is_denda_lunas;
        });

        $totalTagihanDenda = (int) $peminjaman->sum(fn ($item) => $item->total_denda);

        return view('siswa.denda', compact('peminjaman', 'nama_pencarian', 'tarif_denda', 'totalTagihanDenda'));
    }

    /**
     * 6. Melihat RIWAYAT buku yang pernah dipinjam (yang sudah selesai dikembalikan)
     */
    public function riwayatSelesai(Request $request)
    {
        $nama_pencarian = trim((string) $request->input('nama', ''));

        $query = Peminjaman::with('book')->where('status', 'dikembalikan');

        if ($nama_pencarian !== '') {
            $query->where(function ($q) use ($nama_pencarian) {
                $q->where('nama_siswa', 'LIKE', "%{$nama_pencarian}%")
                    ->orWhere('kelas', 'LIKE', "%{$nama_pencarian}%");
            });
        }

        $peminjaman = $query->latest('id')->get();

        return view('siswa.riwayat_selesai', compact('peminjaman', 'nama_pencarian'));
    }

    /**
     * Halaman Cek Riwayat Lengkap & Multi-status
     */
    public function riwayat(Request $request)
    {
        $nama_pencarian = trim((string) $request->input('nama', ''));
        $status_filter = (string) $request->input('status', 'semua');

        $peminjaman = null;
        $stats = [
            'total' => 0,
            'menunggu' => 0,
            'dipinjam' => 0,
            'menunggu_pengembalian' => 0,
            'dikembalikan' => 0,
            'ditolak' => 0,
            'terlambat' => 0,
            'total_denda' => 0,
        ];

        $setting = LibrarySetting::first();
        $tarif_denda = $setting?->tarif_denda_harian ?? 1000;

        if ($nama_pencarian !== '') {
            $baseQuery = Peminjaman::with('book')
                ->where(function ($query) use ($nama_pencarian) {
                    $query->where('nama_siswa', 'LIKE', "%{$nama_pencarian}%")
                        ->orWhere('kelas', 'LIKE', "%{$nama_pencarian}%");
                });

            // Ambil semua data untuk menghitung ringkasan statistik
            $allPeminjaman = (clone $baseQuery)->get();

            $stats['total'] = $allPeminjaman->count();
            $stats['menunggu'] = $allPeminjaman->where('status', 'menunggu')->count();
            $stats['dipinjam'] = $allPeminjaman->where('status', 'dipinjam')->count();
            $stats['menunggu_pengembalian'] = $allPeminjaman->where('status', 'menunggu_pengembalian')->count();
            $stats['dikembalikan'] = $allPeminjaman->where('status', 'dikembalikan')->count();
            $stats['ditolak'] = $allPeminjaman->where('status', 'ditolak')->count();
            $stats['terlambat'] = $allPeminjaman->filter(fn ($item) => $item->is_terlambat)->count();
            $stats['total_denda'] = (int) $allPeminjaman->sum(fn ($item) => $item->total_denda);

            // Filter berdasarkan status jika dipilih
            $displayQuery = clone $baseQuery;

            if ($status_filter === 'menunggu') {
                $displayQuery->where('status', 'menunggu');
            } elseif ($status_filter === 'dipinjam') {
                $displayQuery->where('status', 'dipinjam');
            } elseif ($status_filter === 'menunggu_pengembalian') {
                $displayQuery->where('status', 'menunggu_pengembalian');
            } elseif ($status_filter === 'dikembalikan') {
                $displayQuery->where('status', 'dikembalikan');
            } elseif ($status_filter === 'ditolak') {
                $displayQuery->where('status', 'ditolak');
            }

            $results = $displayQuery->latest('id')->get();

            if ($status_filter === 'denda') {
                $results = $results->filter(fn ($item) => $item->total_denda > 0 && ! $item->is_denda_lunas);
            }

            $peminjaman = $results;
        }

        return view('siswa.riwayat', compact('peminjaman', 'nama_pencarian', 'status_filter', 'stats', 'tarif_denda'));
    }
}
