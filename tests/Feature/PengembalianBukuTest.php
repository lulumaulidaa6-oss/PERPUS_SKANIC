<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\LibrarySetting;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PengembalianBukuTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        LibrarySetting::create([
            'nama_perpustakaan' => 'Perpustakaan SMK Uji Coba',
            'tarif_denda_harian' => 1000,
        ]);

        $this->admin = User::create([
            'name' => 'Petugas Admin',
            'username' => 'admin_kembali',
            'email' => 'admin_kembali@perpus.sch.id',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);
    }

    public function test_siswa_melihat_daftar_buku_yang_sedang_dipinjam(): void
    {
        $book = Book::create([
            'judul' => 'Pemrograman Web Lanjut',
            'penulis' => 'John Doe',
            'penerbit' => 'Informatika',
            'stok' => 5,
        ]);

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Budi Santoso',
            'kelas' => 'XII RPL 1',
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::today()->subDays(3)->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(4)->toDateString(),
            'status' => 'dipinjam',
            'status_denda' => 'bebas',
        ]);

        $response = $this->get('/siswa/disetujui');
        $response->assertStatus(200);
        $response->assertSee('Pemrograman Web Lanjut');
        $response->assertSee('Budi Santoso');
        $response->assertSee('Ajukan Pengembalian Buku');
    }

    public function test_siswa_wajib_upload_foto_saat_mengajukan_pengembalian(): void
    {
        Storage::fake('public');

        $book = Book::create([
            'judul' => 'Algoritma dan Struktur Data',
            'penulis' => 'Jane Smith',
            'penerbit' => 'Erlangga',
            'stok' => 3,
        ]);

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Siti Aisyah',
            'kelas' => 'XI TKJ 2',
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::today()->subDays(2)->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(5)->toDateString(),
            'status' => 'dipinjam',
            'status_denda' => 'bebas',
        ]);

        // Request tanpa upload foto harus ditolak
        $response = $this->post("/siswa/peminjaman/{$pinjam->id}/kembalikan", []);
        $response->assertSessionHasErrors(['foto_kembali']);

        $this->assertEquals('dipinjam', $pinjam->fresh()->status);
    }

    public function test_siswa_berhasil_mengajukan_pengembalian_dengan_foto(): void
    {
        Storage::fake('public');

        $book = Book::create([
            'judul' => 'Basis Data Relasional',
            'penulis' => 'Ahmad R',
            'penerbit' => 'Andi Offset',
            'stok' => 4,
        ]);

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Rian Hidayat',
            'kelas' => 'X RPL 2',
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::today()->subDays(4)->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(3)->toDateString(),
            'status' => 'dipinjam',
            'status_denda' => 'bebas',
        ]);

        $foto = UploadedFile::fake()->image('kondisi_buku.jpg');

        $response = $this->post("/siswa/peminjaman/{$pinjam->id}/kembalikan", [
            'foto_kembali' => $foto,
            'catatan_kembali' => 'Buku dalam kondisi sangat mulus tanpa coretan.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $pinjamFresh = $pinjam->fresh();
        $this->assertEquals('menunggu_pengembalian', $pinjamFresh->status);
        $this->assertNotNull($pinjamFresh->foto_kembali);
        $this->assertEquals('Buku dalam kondisi sangat mulus tanpa coretan.', $pinjamFresh->catatan_kembali);
        $this->assertEquals(Carbon::today()->toDateString(), $pinjamFresh->tanggal_pengajuan_kembali->toDateString());

        Storage::disk('public')->assertExists($pinjamFresh->foto_kembali);

        // Status muncul di menu siswa disetujui sebagai Menunggu Pengembalian
        $disetujuiResponse = $this->get('/siswa/disetujui');
        $disetujuiResponse->assertSee('Menunggu Pengembalian');
    }

    public function test_admin_melihat_pengajuan_pengembalian_dan_dapat_mengonfirmasi(): void
    {
        Storage::fake('public');

        $book = Book::create([
            'judul' => 'Jaringan Komputer Dasar',
            'penulis' => 'Bambang S',
            'penerbit' => 'Informatika',
            'stok' => 2, // Stok awal 2
        ]);

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Fajar Pratama',
            'kelas' => 'XII TKJ 1',
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::today()->subDays(5)->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(2)->toDateString(),
            'tanggal_pengajuan_kembali' => Carbon::today()->toDateString(),
            'foto_kembali' => 'bukti_kembali/dummy.jpg',
            'catatan_kembali' => 'Sampul lengkap',
            'status' => 'menunggu_pengembalian',
            'status_denda' => 'bebas',
        ]);

        // 1. Admin melihat di daftar peminjaman
        $indexResponse = $this->actingAs($this->admin)->get('/peminjaman');
        $indexResponse->assertStatus(200);
        $indexResponse->assertSee('Menunggu Pengembalian');
        $indexResponse->assertSee('Periksa');
        $indexResponse->assertSee('Konfirmasi');

        // 2. Admin mengonfirmasi pengembalian buku (kondisi baik)
        $kembaliResponse = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/kembali", [
            'kondisi_buku_kembali' => 'baik',
            'denda_tambahan' => 0,
            'catatan_admin' => 'Kondisi buku baik sesuai foto.',
        ]);

        $kembaliResponse->assertRedirect();
        $kembaliResponse->assertSessionHas('success');

        $pinjamFresh = $pinjam->fresh();
        $this->assertEquals('dikembalikan', $pinjamFresh->status);
        $this->assertEquals(Carbon::today()->toDateString(), $pinjamFresh->tanggal_kembali->toDateString());
        $this->assertEquals('baik', $pinjamFresh->kondisi_buku_kembali);

        // 3. Stok buku otomatis bertambah (2 + 1 = 3)
        $this->assertEquals(3, $book->fresh()->stok);

        // 4. Data masuk ke riwayat buku selesai siswa
        $riwayatResponse = $this->get('/siswa/riwayat-selesai');
        $riwayatResponse->assertStatus(200);
        $riwayatResponse->assertSee('Jaringan Komputer Dasar');
        $riwayatResponse->assertSee('Fajar Pratama');
    }

    public function test_pengembalian_terlambat_dan_rusak_menghitung_denda_sesuai_ketentuan(): void
    {
        $book = Book::create([
            'judul' => 'Sistem Operasi Modern',
            'penulis' => 'Tanenbaum',
            'penerbit' => 'Pearson',
            'stok' => 1,
        ]);

        // Pinjaman lewat tempo 3 hari (tarif denda 1000/hari -> telat = Rp 3.000)
        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Doni Kusuma',
            'kelas' => 'XI RPL 1',
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::today()->subDays(10)->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->subDays(3)->toDateString(),
            'tanggal_pengajuan_kembali' => Carbon::today()->toDateString(),
            'foto_kembali' => 'bukti_kembali/doni.jpg',
            'status' => 'menunggu_pengembalian',
            'status_denda' => 'bebas',
        ]);

        // Admin memeriksa, menemukan buku rusak dengan denda tambahan Rp 10.000
        $kembaliResponse = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/kembali", [
            'kondisi_buku_kembali' => 'rusak',
            'denda_tambahan' => 10000,
            'status_denda' => 'belum_dibayar',
        ]);

        $kembaliResponse->assertRedirect();

        $pinjamFresh = $pinjam->fresh();
        $this->assertEquals('dikembalikan', $pinjamFresh->status);
        // Denda keterlambatan (3 hari * 1000 = 3000) + denda kerusakan (10000) = 13000
        $this->assertEquals(13000, $pinjamFresh->denda);
        $this->assertEquals('belum_dibayar', $pinjamFresh->status_denda);

        // Stok buku tetap bertambah karena buku tidak hilang
        $this->assertEquals(2, $book->fresh()->stok);

        // Siswa dapat melihat tagihan denda di portal denda
        $dendaResponse = $this->get('/siswa/denda?nama=Doni+Kusuma');
        $dendaResponse->assertStatus(200);
        $dendaResponse->assertSee('13.000');
    }

    public function test_pengembalian_buku_hilang_tidak_menambah_stok_buku(): void
    {
        $book = Book::create([
            'judul' => 'Kecerdasan Buatan',
            'penulis' => 'Russell',
            'penerbit' => 'Erlangga',
            'stok' => 5,
        ]);

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Rina Melati',
            'kelas' => 'XII MM 1',
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::today()->subDays(2)->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(5)->toDateString(),
            'status' => 'menunggu_pengembalian',
            'status_denda' => 'bebas',
        ]);

        // Admin konfirmasi dengan kondisi buku hilang (ganti rugi 50000)
        $kembaliResponse = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/kembali", [
            'kondisi_buku_kembali' => 'hilang',
            'denda_tambahan' => 50000,
            'status_denda' => 'belum_dibayar',
        ]);

        $kembaliResponse->assertRedirect();

        // Stok buku TIDAK bertambah karena hilang
        $this->assertEquals(5, $book->fresh()->stok);
        $this->assertEquals(50000, $pinjam->fresh()->denda);
    }
}
