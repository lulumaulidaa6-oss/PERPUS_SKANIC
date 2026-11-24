<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\LibrarySetting;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    /**
     * 1. Menampilkan halaman daftar peminjaman dengan fitur:
     *    - Pencarian (Nama Siswa atau Judul Buku)
     *    - Filter Status (Menunggu, Dipinjam, Dikembalikan, Ditolak, Ada Denda)
     *    - Filter Rentang Waktu (Tanggal Awal dan Akhir)
     *    - Pengurutan (Sorting)
     *    - Informasi Denda Siswa & Ringkasan Statistik
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = (string) $request->input('status', 'semua');
        $tanggal_awal = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $sort = (string) $request->input('sort', 'terbaru');

        // Query dasar dengan eager loading relasi user dan book
        $query = Peminjaman::with(['user', 'book']);

        // 1. Fitur Pencarian: Nama Siswa atau Judul Buku
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_siswa', 'LIKE', "%{$search}%")
                    ->orWhere('kelas', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($qu) use ($search) {
                        $qu->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('book', function ($qb) use ($search) {
                        $qb->where('judul', 'LIKE', "%{$search}%")
                            ->orWhere('penulis', 'LIKE', "%{$search}%");
                    });
            });
        }

        // 2. Filter Status
        if ($status !== 'semua' && $status !== '') {
            if ($status === 'denda') {
                // Saring peminjaman yang memiliki denda tersimpan atau sedang terlambat
                $today = Carbon::today()->toDateString();
                $query->where(function ($q) use ($today) {
                    $q->where('denda', '>', 0)
                        ->orWhere(function ($sub) use ($today) {
                            $sub->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
                                ->whereNotNull('tanggal_perkiraan_kembali')
                                ->where('tanggal_perkiraan_kembali', '<', $today);
                        });
                });
            } else {
                $query->where('status', $status);
            }
        }

        // 3. Filter Rentang Waktu (tanggal awal dan akhir)
        if ($tanggal_awal) {
            $query->whereDate('tanggal_pinjam', '>=', $tanggal_awal);
        }
        if ($tanggal_akhir) {
            $query->whereDate('tanggal_pinjam', '<=', $tanggal_akhir);
        }

        // 4. Sorting / Pengurutan
        switch ($sort) {
            case 'terlama':
                $query->orderBy('tanggal_pinjam', 'asc')->orderBy('id', 'asc');
                break;
            case 'denda_tertinggi':
                $query->orderByDesc('denda')->orderByDesc('id');
                break;
            case 'jatuh_tempo':
                $query->orderByRaw('CASE WHEN tanggal_perkiraan_kembali IS NULL THEN 1 ELSE 0 END, tanggal_perkiraan_kembali ASC');
                break;
            case 'nama_asc':
                $query->orderBy('nama_siswa', 'asc');
                break;
            case 'nama_desc':
                $query->orderBy('nama_siswa', 'desc');
                break;
            case 'terbaru':
            default:
                $query->orderByDesc('tanggal_pinjam')->orderByDesc('id');
                break;
        }

        // Ambil data dengan pagination agar tetap cepat ketika data sudah ratusan
        $peminjaman = $query->paginate(15)->withQueryString();

        // Mengambil data ringkasan untuk statistik KPI di atas tabel
        $allRecords = Peminjaman::with('book')->get();
        $setting = LibrarySetting::first();
        $tarif_denda = $setting?->tarif_denda_harian ?? 1000;

        $stats = [
            'total' => $allRecords->count(),
            'dipinjam' => $allRecords->where('status', 'dipinjam')->count(),
            'menunggu' => $allRecords->where('status', 'menunggu')->count(),
            'menunggu_pengembalian' => $allRecords->where('status', 'menunggu_pengembalian')->count(),
            'dikembalikan' => $allRecords->where('status', 'dikembalikan')->count(),
            'ditolak' => $allRecords->where('status', 'ditolak')->count(),
            'terlambat' => $allRecords->filter(fn ($p) => $p->is_terlambat && in_array($p->status, ['dipinjam', 'menunggu_pengembalian']))->count(),
            'total_denda' => (int) $allRecords->sum(fn ($p) => $p->total_denda),
            'denda_belum_dibayar' => (int) $allRecords->filter(fn ($p) => $p->status_pembayaran_denda === 'belum_dibayar')->sum(fn ($p) => $p->total_denda),
            'transaksi_denda_aktif' => $allRecords->filter(fn ($p) => $p->status_pembayaran_denda === 'belum_dibayar' && $p->total_denda > 0)->count(),
        ];

        return view('peminjaman.index', compact(
            'peminjaman',
            'search',
            'status',
            'tanggal_awal',
            'tanggal_akhir',
            'sort',
            'stats',
            'tarif_denda'
        ));
    }

    // 2. Menampilkan formulir catat peminjaman
    public function create()
    {
        $users = User::all();
        $books = Book::where('stok', '>', 0)->get();

        return view('peminjaman.create', compact('users', 'books'));
    }

    // 3. Menyimpan data peminjaman dan mengurangi stok buku
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable',
            'nama_siswa' => 'nullable|string|max:255',
            'kelas' => 'nullable|string|max:50',
            'book_id' => 'required|exists:books,id',
            'tanggal_pinjam' => 'required|date',
            'tanggal_perkiraan_kembali' => 'nullable|date|after_or_equal:tanggal_pinjam',
        ]);

        return DB::transaction(function () use ($request) {
            $book = Book::where('id', $request->book_id)->lockForUpdate()->firstOrFail();

            // Cegah stok menjadi minus
            if ($book->stok <= 0) {
                return redirect()->back()->withInput()->with('error', "Stok buku '{$book->judul}' sudah habis (0), transaksi peminjaman tidak dapat diproses.");
            }

            $namaSiswa = $request->nama_siswa;
            if (! $namaSiswa && $request->user_id) {
                $user = User::find($request->user_id);
                $namaSiswa = $user?->name;
            }

            $tanggalPinjam = $request->tanggal_pinjam;
            $jatuhTempo = $request->tanggal_perkiraan_kembali ?: Carbon::parse($tanggalPinjam)->addDays(7)->toDateString();

            // Simpan data ke tabel peminjaman
            Peminjaman::create([
                'user_id' => $request->user_id ?: null,
                'nama_siswa' => $namaSiswa ?: 'Siswa',
                'kelas' => $request->kelas ?: '-',
                'book_id' => $request->book_id,
                'tanggal_pinjam' => $tanggalPinjam,
                'tanggal_perkiraan_kembali' => $jatuhTempo,
                'status' => 'dipinjam',
                'status_denda' => 'bebas',
            ]);

            // Kurangi stok buku yang dipinjam secara otomatis
            $book->decrement('stok');

            return redirect('/peminjaman')->with('success', 'Transaksi peminjaman berhasil dicatat.');
        });
    }

    // 4. Admin memeriksa kondisi buku, mengonfirmasi pengembalian, mencatat denda, dan menambah stok buku
    public function kembalikan(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $pinjam = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            // Cegah buku dihitung dua kali jika tombol konfirmasi pengembalian diklik berulang kali
            if ($pinjam->status === 'dikembalikan') {
                return redirect()->back()->with('info', 'Pengembalian buku "'.$pinjam->book?->judul.'" oleh '.$pinjam->nama_siswa.' sudah dikonfirmasi sebelumnya.');
            }

            if (! in_array($pinjam->status, ['dipinjam', 'menunggu_pengembalian'])) {
                return redirect()->back()->with('error', 'Status peminjaman bukan sedang dipinjam atau menunggu pengembalian.');
            }

            $tglKembali = Carbon::today();
            $denda = 0;

            // Hitung denda keterlambatan jika melewati tanggal perkiraan kembali
            // Jika ada tanggal_pengajuan_kembali dari siswa, hitung berdasarkan tanggal tersebut (atau today)
            $tglHitungTerlambat = $pinjam->tanggal_pengajuan_kembali
                ? Carbon::parse($pinjam->tanggal_pengajuan_kembali)->startOfDay()
                : $tglKembali->copy()->startOfDay();

            if ($pinjam->tanggal_perkiraan_kembali) {
                $jatuhTempo = Carbon::parse($pinjam->tanggal_perkiraan_kembali)->startOfDay();
                if ($tglHitungTerlambat->greaterThan($jatuhTempo)) {
                    $hariTerlambat = (int) abs($tglHitungTerlambat->diffInDays($jatuhTempo));
                    $setting = LibrarySetting::first();
                    $tarif = $setting?->tarif_denda_harian ?? 1000;
                    $denda += ($hariTerlambat * $tarif);
                }
            }

            // Denda tambahan dari pemeriksaan admin (misal kerusakan atau kehilangan)
            if ($request->filled('denda_tambahan')) {
                $denda += (int) $request->input('denda_tambahan');
            } elseif ($request->filled('nominal_denda')) {
                $denda = (int) $request->input('nominal_denda');
            }

            // Kondisi fisik buku saat diperiksa admin
            $kondisi = $request->input('kondisi_buku_kembali', 'baik');
            $statusDenda = 'bebas';
            if ($denda > 0) {
                $statusDenda = $request->input('status_denda', 'belum_dibayar');
            }

            // Catatan admin jika ada keterangan tambahan
            $catatanAdmin = $request->input('catatan_admin');
            $catatanGabungan = $pinjam->catatan_kembali;
            if ($catatanAdmin) {
                $catatanGabungan = $catatanGabungan ? ($catatanGabungan.' | Catatan Petugas: '.$catatanAdmin) : ('Catatan Petugas: '.$catatanAdmin);
            }

            // 1. Ubah status menjadi 'dikembalikan', catat tanggal kembali, kondisi, denda, dan catatan
            $pinjam->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => $tglKembali->toDateString(),
                'kondisi_buku_kembali' => $kondisi,
                'denda' => $denda,
                'status_denda' => $statusDenda,
                'catatan_kembali' => $catatanGabungan,
            ]);

            // 2. Tambahkan stok buku secara otomatis jika kondisi bukan 'hilang'
            if ($kondisi !== 'hilang') {
                $book = Book::where('id', $pinjam->book_id)->lockForUpdate()->firstOrFail();
                $book->increment('stok');
            }

            $pesan = 'Pengembalian buku "'.$pinjam->book?->judul.'" oleh '.$pinjam->nama_siswa.' berhasil dikonfirmasi! Status kini Dikembalikan dan stok buku bertambah.';
            if ($denda > 0) {
                $statusLabel = $statusDenda === 'lunas' ? 'LUNAS' : 'BELUM DIBAYAR';
                $pesan .= ' Denda dicatat: Rp '.number_format($denda, 0, ',', '.')." ({$statusLabel}).";
            }

            return redirect()->back()->with('success', $pesan);
        });
    }

    // 5. Menandai denda siswa sebagai Lunas
    public function bayarDenda($id)
    {
        return DB::transaction(function () use ($id) {
            $pinjam = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($pinjam->total_denda > 0) {
                $nominal = $pinjam->total_denda;
                $pinjam->update([
                    'denda' => $nominal,
                    'status_denda' => 'lunas',
                ]);

                $nama = $pinjam->nama_siswa ?? 'Siswa';

                return redirect()->back()->with('success', 'Denda sebesar Rp '.number_format($nominal, 0, ',', '.')." untuk {$nama} berhasil ditandai LUNAS! ✅");
            }

            return redirect()->back()->with('info', 'Tidak ada denda yang perlu dilunasi.');
        });
    }

    // 6. Mengubah / menyesuaikan nominal denda siswa secara manual oleh Admin
    public function updateDenda(Request $request, $id)
    {
        $request->validate([
            'denda' => 'required|integer|min:0',
            'status_denda' => 'required|in:bebas,belum_dibayar,lunas',
            'kondisi_buku_kembali' => 'nullable|string|max:50',
        ]);

        return DB::transaction(function () use ($request, $id) {
            $pinjam = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            $denda = (int) $request->denda;
            $statusDenda = $denda === 0 ? 'bebas' : $request->status_denda;

            $updateData = [
                'denda' => $denda,
                'status_denda' => $statusDenda,
            ];

            if ($request->filled('kondisi_buku_kembali')) {
                $updateData['kondisi_buku_kembali'] = $request->kondisi_buku_kembali;
            }

            $pinjam->update($updateData);

            return redirect()->back()->with('success', 'Rincian denda siswa '.$pinjam->nama_siswa.' berhasil diperbarui.');
        });
    }

    // 7. Menyetujui pinjaman (Ubah status jadi dipinjam, kurangi stok, catat admin pemroses)
    public function setujui(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $pinjam = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            // Cegah peminjaman dihitung dua kali jika tombol ACC diklik berulang kali
            if ($pinjam->status === 'dipinjam') {
                return redirect()->back()->with('info', "Peminjaman buku '{$pinjam->book?->judul}' oleh {$pinjam->nama_siswa} sudah disetujui sebelumnya.");
            }

            if ($pinjam->status !== 'menunggu') {
                return redirect()->back()->with('error', "Peminjaman tidak dapat disetujui karena status saat ini adalah: {$pinjam->status}.");
            }

            $buku = Book::where('id', $pinjam->book_id)->lockForUpdate()->firstOrFail();

            // Cegah stok menjadi minus
            if ($buku->stok <= 0) {
                return redirect()->back()->with('error', "Stok buku '{$buku->judul}' sudah habis!");
            }

            $adminName = auth()->user()?->name ?? 'Petugas Perpustakaan';
            $catatan = $request->input('catatan');

            $updateData = [
                'status' => 'dipinjam',
                'diproses_oleh' => $adminName,
            ];
            if ($catatan) {
                $updateData['catatan'] = $catatan;
            }

            $pinjam->update($updateData);
            $buku->decrement('stok'); // Kurangi stok buku secara otomatis

            return redirect()->back()->with('success', "Peminjaman buku '{$buku->judul}' oleh {$pinjam->nama_siswa} disetujui (di-ACC). Diproses oleh: {$adminName}.");
        });
    }

    // 8. Menolak pinjaman (Catat status ditolak, alasan penolakan, dan admin pemroses)
    public function tolak(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {
            $pinjam = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            if ($pinjam->status === 'ditolak') {
                return redirect()->back()->with('info', "Pengajuan peminjaman oleh {$pinjam->nama_siswa} telah ditolak sebelumnya.");
            }

            // Jika peminjaman yang ditolak sebelumnya berstatus disetujui (dipinjam / menunggu_pengembalian),
            // kembalikan stoknya secara otomatis
            if (in_array($pinjam->status, ['dipinjam', 'menunggu_pengembalian'])) {
                $buku = Book::where('id', $pinjam->book_id)->lockForUpdate()->first();
                $buku?->increment('stok');
            }
            // Saat status 'menunggu', stok buku memang belum berkurang sehingga stok tidak perlu diubah

            $adminName = auth()->user()?->name ?? 'Petugas Perpustakaan';
            $alasan = $request->input('alasan_penolakan') ?: $request->input('alasan', 'Pengajuan peminjaman tidak memenuhi syarat perpustakaan.');
            $catatan = $request->input('catatan');

            $updateData = [
                'status' => 'ditolak',
                'alasan_penolakan' => $alasan,
                'diproses_oleh' => $adminName,
            ];
            if ($catatan) {
                $updateData['catatan'] = $catatan;
            }

            $pinjam->update($updateData);

            return redirect()->back()->with('success', "Pengajuan peminjaman oleh {$pinjam->nama_siswa} telah ditolak. Alasan: {$alasan}");
        });
    }

    // 9. Menghapus data pengajuan / peminjaman dari tampilan (Soft Delete agar tidak muncul di daftar & Excel)
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            $pinjam = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

            // Jika peminjaman masih aktif (status 'dipinjam' atau 'menunggu_pengembalian') dan belum dikembalikan, kembalikan stok buku
            if (in_array($pinjam->status, ['dipinjam', 'menunggu_pengembalian'])) {
                $buku = Book::where('id', $pinjam->book_id)->lockForUpdate()->first();
                $buku?->increment('stok');
            }

            $nama = $pinjam->nama_siswa ?? 'Siswa';
            $statusLabel = ucfirst(str_replace('_', ' ', $pinjam->status));
            $pinjam->delete(); // Soft delete via SoftDeletes trait

            return redirect()->back()->with('success', "Data peminjaman ({$statusLabel}) untuk {$nama} berhasil dihapus dari daftar dan rekap Excel.");
        });
    }

    // 10. Rekap Export Excel Peminjaman (Mendukung filter status, tanggal, dan kata kunci)
    public function exportExcel(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = (string) $request->input('status', 'semua');
        $tanggal_awal = $request->input('tanggal_awal');
        $tanggal_akhir = $request->input('tanggal_akhir');
        $format = strtolower((string) $request->input('format', 'xls'));

        // Query data peminjaman aktif (Soft delete otomatis terfilter)
        $query = Peminjaman::with(['user', 'book']);

        // Filter kata kunci pencarian
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_siswa', 'LIKE', "%{$search}%")
                    ->orWhere('kelas', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($qu) use ($search) {
                        $qu->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('book', function ($qb) use ($search) {
                        $qb->where('judul', 'LIKE', "%{$search}%")
                            ->orWhere('penulis', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filter status
        if ($status !== 'semua' && $status !== '') {
            if ($status === 'denda') {
                $today = Carbon::today()->toDateString();
                $query->where(function ($q) use ($today) {
                    $q->where('denda', '>', 0)
                        ->orWhere(function ($sub) use ($today) {
                            $sub->whereIn('status', ['dipinjam', 'menunggu_pengembalian'])
                                ->whereNotNull('tanggal_perkiraan_kembali')
                                ->where('tanggal_perkiraan_kembali', '<', $today);
                        });
                });
            } elseif ($status === 'acc') {
                $query->whereIn('status', ['dipinjam', 'dikembalikan', 'menunggu_pengembalian']);
            } else {
                $query->where('status', $status);
            }
        }

        // Filter rentang tanggal pinjam
        if ($tanggal_awal) {
            $query->whereDate('tanggal_pinjam', '>=', $tanggal_awal);
        }
        if ($tanggal_akhir) {
            $query->whereDate('tanggal_pinjam', '<=', $tanggal_akhir);
        }

        $records = $query->orderByDesc('tanggal_pinjam')->orderByDesc('id')->get();

        $timestamp = date('Ymd_His');
        $statusKeterangan = $status === 'semua' ? 'Semua Status' : ucfirst(str_replace('_', ' ', $status));
        $filterInfo = "Status: {$statusKeterangan}";
        if ($tanggal_awal || $tanggal_akhir) {
            $filterInfo .= ' | Periode: '.($tanggal_awal ?: 'Awal').' s/d '.($tanggal_akhir ?: 'Sekarang');
        }

        // Jika user meminta format CSV
        if ($format === 'csv') {
            $headers = [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"Rekap_Peminjaman_{$timestamp}.csv\"",
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ];

            $output = "\xEF\xBB\xBF"; // UTF-8 BOM agar terbaca sempurna di Microsoft Excel
            $handle = fopen('php://temp', 'r+');

            fputcsv($handle, [
                'No',
                'Nama Siswa',
                'Kelas',
                'Judul Buku',
                'Penulis',
                'Tanggal Pengajuan',
                'Tanggal Peminjaman',
                'Perkiraan Pengembalian',
                'Tanggal Dikembalikan',
                'Status',
                'Alasan Penolakan',
                'Admin yang Memproses',
                'Catatan',
                'Nominal Denda',
                'Status Denda',
            ]);

            foreach ($records as $index => $item) {
                $statusText = match ($item->status) {
                    'dipinjam' => 'Disetujui (ACC)',
                    'menunggu' => 'Menunggu Persetujuan',
                    'menunggu_pengembalian' => 'Menunggu Pengembalian',
                    'dikembalikan' => 'Selesai Dikembalikan',
                    'ditolak' => 'Ditolak',
                    default => ucfirst($item->status),
                };

                $catatanLengkap = trim(($item->catatan ? $item->catatan.' ' : '').($item->catatan_kembali ? '['.$item->catatan_kembali.']' : ''));

                fputcsv($handle, [
                    $index + 1,
                    $item->nama_siswa ?? ($item->user->name ?? '-'),
                    $item->kelas ?? '-',
                    $item->book->judul ?? 'Buku Tidak Diketahui',
                    $item->book->penulis ?? '-',
                    $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-',
                    $item->tanggal_pinjam ? Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') : '-',
                    $item->tanggal_perkiraan_kembali ? Carbon::parse($item->tanggal_perkiraan_kembali)->format('d/m/Y') : '-',
                    $item->tanggal_kembali ? Carbon::parse($item->tanggal_kembali)->format('d/m/Y') : '-',
                    $statusText,
                    $item->alasan_penolakan ?: '-',
                    $item->diproses_oleh ?: '-',
                    $catatanLengkap ?: '-',
                    $item->total_denda > 0 ? 'Rp '.number_format($item->total_denda, 0, ',', '.') : 'Rp 0',
                    strtoupper($item->status_pembayaran_denda),
                ]);
            }

            rewind($handle);
            $csvData = stream_get_contents($handle);
            fclose($handle);

            return response($output.$csvData, 200, $headers);
        }

        // Format default: Microsoft Excel XML Spreadsheet 2003 (.xls)
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<?mso-application progid="Excel.Sheet"?>'."\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"'."\n";
        $xml .= ' xmlns:o="urn:schemas-microsoft-com:office:office"'."\n";
        $xml .= ' xmlns:x="urn:schemas-microsoft-com:office:excel"'."\n";
        $xml .= ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"'."\n";
        $xml .= ' xmlns:html="http://www.w3.org/TR/REC-html40">'."\n";
        $xml .= ' <Styles>'."\n";
        $xml .= '  <Style ss:ID="Default" ss:Name="Normal"><Alignment ss:Vertical="Center"/><Font ss:FontName="Calibri" ss:Size="11"/></Style>'."\n";
        $xml .= '  <Style ss:ID="Title"><Font ss:FontName="Calibri" ss:Size="14" ss:Bold="1" ss:Color="#1E3A8A"/><Alignment ss:Vertical="Center"/></Style>'."\n";
        $xml .= '  <Style ss:ID="Subtitle"><Font ss:FontName="Calibri" ss:Size="10" ss:Italic="1" ss:Color="#475569"/><Alignment ss:Vertical="Center"/></Style>'."\n";
        $xml .= '  <Style ss:ID="Header">'."\n";
        $xml .= '   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>'."\n";
        $xml .= '   <Borders>'."\n";
        $xml .= '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0F172A"/>'."\n";
        $xml .= '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0F172A"/>'."\n";
        $xml .= '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0F172A"/>'."\n";
        $xml .= '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#0F172A"/>'."\n";
        $xml .= '   </Borders>'."\n";
        $xml .= '   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#FFFFFF"/>'."\n";
        $xml .= '   <Interior ss:Color="#1E3A8A" ss:Pattern="Solid"/>'."\n";
        $xml .= '  </Style>'."\n";
        $xml .= '  <Style ss:ID="CellData">'."\n";
        $xml .= '   <Borders>'."\n";
        $xml .= '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '   </Borders>'."\n";
        $xml .= '   <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#0F172A"/>'."\n";
        $xml .= '  </Style>'."\n";
        $xml .= '  <Style ss:ID="CellDataCenter">'."\n";
        $xml .= '   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>'."\n";
        $xml .= '   <Borders>'."\n";
        $xml .= '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '   </Borders>'."\n";
        $xml .= '   <Font ss:FontName="Calibri" ss:Size="10" ss:Color="#0F172A"/>'."\n";
        $xml .= '  </Style>'."\n";
        $xml .= '  <Style ss:ID="CellDataDitolak">'."\n";
        $xml .= '   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>'."\n";
        $xml .= '   <Borders>'."\n";
        $xml .= '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '   </Borders>'."\n";
        $xml .= '   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#991B1B"/>'."\n";
        $xml .= '   <Interior ss:Color="#FEE2E2" ss:Pattern="Solid"/>'."\n";
        $xml .= '  </Style>'."\n";
        $xml .= '  <Style ss:ID="CellDataACC">'."\n";
        $xml .= '   <Alignment ss:Horizontal="Center" ss:Vertical="Center"/>'."\n";
        $xml .= '   <Borders>'."\n";
        $xml .= '    <Border ss:Position="Bottom" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '    <Border ss:Position="Left" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '    <Border ss:Position="Right" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '    <Border ss:Position="Top" ss:LineStyle="Continuous" ss:Weight="1" ss:Color="#CBD5E1"/>'."\n";
        $xml .= '   </Borders>'."\n";
        $xml .= '   <Font ss:FontName="Calibri" ss:Size="10" ss:Bold="1" ss:Color="#166534"/>'."\n";
        $xml .= '   <Interior ss:Color="#DCFCE7" ss:Pattern="Solid"/>'."\n";
        $xml .= '  </Style>'."\n";
        $xml .= ' </Styles>'."\n";
        $xml .= ' <Worksheet ss:Name="Rekap Peminjaman">'."\n";
        $xml .= '  <Table>'."\n";

        // Kolom spesifikasi lebar
        $xml .= '   <Column ss:Width="30"/>'."\n";  // No
        $xml .= '   <Column ss:Width="130"/>'."\n"; // Nama Siswa
        $xml .= '   <Column ss:Width="70"/>'."\n";  // Kelas
        $xml .= '   <Column ss:Width="170"/>'."\n"; // Judul Buku
        $xml .= '   <Column ss:Width="110"/>'."\n"; // Penulis
        $xml .= '   <Column ss:Width="105"/>'."\n"; // Tgl Pengajuan
        $xml .= '   <Column ss:Width="95"/>'."\n";  // Tgl Pinjam
        $xml .= '   <Column ss:Width="95"/>'."\n";  // Perkiraan Kembali
        $xml .= '   <Column ss:Width="95"/>'."\n";  // Tgl Kembali
        $xml .= '   <Column ss:Width="110"/>'."\n"; // Status
        $xml .= '   <Column ss:Width="160"/>'."\n"; // Alasan Penolakan
        $xml .= '   <Column ss:Width="120"/>'."\n"; // Admin Pemroses
        $xml .= '   <Column ss:Width="150"/>'."\n"; // Catatan
        $xml .= '   <Column ss:Width="85"/>'."\n";  // Denda
        $xml .= '   <Column ss:Width="80"/>'."\n";  // Status Denda

        // Judul Laporan
        $xml .= '   <Row ss:Height="22">'."\n";
        $xml .= '    <Cell ss:StyleID="Title"><Data ss:Type="String">REKAP DATA PEMINJAMAN &amp; PENGAJUAN PERPUSTAKAAN</Data></Cell>'."\n";
        $xml .= '   </Row>'."\n";
        $xml .= '   <Row ss:Height="16">'."\n";
        $xml .= '    <Cell ss:StyleID="Subtitle"><Data ss:Type="String">Diunduh pada: '.date('d/m/Y H:i:s').' | '.htmlspecialchars($filterInfo, ENT_XML1, 'UTF-8').' | Total: '.$records->count().' Transaksi</Data></Cell>'."\n";
        $xml .= '   </Row>'."\n";
        $xml .= '   <Row ss:Height="8"/>'."\n"; // Jarak kosong

        // Header Tabel
        $xml .= '   <Row ss:Height="24">'."\n";
        $headersList = [
            'No',
            'Nama Siswa',
            'Kelas',
            'Judul Buku',
            'Penulis',
            'Tanggal Pengajuan',
            'Tanggal Peminjaman',
            'Perkiraan Pengembalian',
            'Tanggal Kembali',
            'Status',
            'Alasan Penolakan',
            'Admin yang Memproses',
            'Catatan',
            'Denda',
            'Status Denda',
        ];
        foreach ($headersList as $col) {
            $xml .= '    <Cell ss:StyleID="Header"><Data ss:Type="String">'.htmlspecialchars($col, ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
        }
        $xml .= '   </Row>'."\n";

        // Isi Baris Data
        foreach ($records as $index => $item) {
            $statusText = match ($item->status) {
                'dipinjam' => 'Disetujui (ACC)',
                'menunggu' => 'Menunggu Persetujuan',
                'menunggu_pengembalian' => 'Menunggu Pengembalian',
                'dikembalikan' => 'Selesai Dikembalikan',
                'ditolak' => 'Ditolak',
                default => ucfirst($item->status),
            };

            $statusStyle = 'CellDataCenter';
            if ($item->status === 'ditolak') {
                $statusStyle = 'CellDataDitolak';
            } elseif (in_array($item->status, ['dipinjam', 'dikembalikan'])) {
                $statusStyle = 'CellDataACC';
            }

            $catatanLengkap = trim(($item->catatan ? $item->catatan.' ' : '').($item->catatan_kembali ? '['.$item->catatan_kembali.']' : ''));

            $xml .= '   <Row ss:Height="20">'."\n";
            $xml .= '    <Cell ss:StyleID="CellDataCenter"><Data ss:Type="Number">'.($index + 1).'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="CellData"><Data ss:Type="String">'.htmlspecialchars($item->nama_siswa ?? ($item->user->name ?? '-'), ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="CellDataCenter"><Data ss:Type="String">'.htmlspecialchars($item->kelas ?? '-', ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="CellData"><Data ss:Type="String">'.htmlspecialchars($item->book->judul ?? 'Buku Tidak Diketahui', ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="CellData"><Data ss:Type="String">'.htmlspecialchars($item->book->penulis ?? '-', ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="CellDataCenter"><Data ss:Type="String">'.htmlspecialchars($item->created_at ? $item->created_at->format('d/m/Y H:i') : '-', ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="CellDataCenter"><Data ss:Type="String">'.htmlspecialchars($item->tanggal_pinjam ? Carbon::parse($item->tanggal_pinjam)->format('d/m/Y') : '-', ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="CellDataCenter"><Data ss:Type="String">'.htmlspecialchars($item->tanggal_perkiraan_kembali ? Carbon::parse($item->tanggal_perkiraan_kembali)->format('d/m/Y') : '-', ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="CellDataCenter"><Data ss:Type="String">'.htmlspecialchars($item->tanggal_kembali ? Carbon::parse($item->tanggal_kembali)->format('d/m/Y') : '-', ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="'.$statusStyle.'"><Data ss:Type="String">'.htmlspecialchars($statusText, ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="CellData"><Data ss:Type="String">'.htmlspecialchars($item->alasan_penolakan ?: '-', ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="CellData"><Data ss:Type="String">'.htmlspecialchars($item->diproses_oleh ?: '-', ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="CellData"><Data ss:Type="String">'.htmlspecialchars($catatanLengkap ?: '-', ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="CellDataCenter"><Data ss:Type="String">'.htmlspecialchars($item->total_denda > 0 ? 'Rp '.number_format($item->total_denda, 0, ',', '.') : 'Rp 0', ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '    <Cell ss:StyleID="CellDataCenter"><Data ss:Type="String">'.htmlspecialchars(strtoupper($item->status_pembayaran_denda), ENT_XML1, 'UTF-8').'</Data></Cell>'."\n";
            $xml .= '   </Row>'."\n";
        }

        $xml .= '  </Table>'."\n";
        $xml .= ' </Worksheet>'."\n";
        $xml .= '</Workbook>';

        return response($xml, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"Rekap_Peminjaman_{$timestamp}.xls\"",
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
