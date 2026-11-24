<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\LibrarySetting;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SiswaMenuSeparationTest extends TestCase
{
    use RefreshDatabase;

    private Book $book;

    protected function setUp(): void
    {
        parent::setUp();

        LibrarySetting::create([
            'nama_perpustakaan' => 'Perpustakaan SMK Uji',
            'tarif_denda_harian' => 1000,
        ]);

        $this->book = Book::create([
            'judul' => 'Filosofi Teras',
            'penulis' => 'Henry Manampiring',
            'penerbit' => 'Kompas',
            'stok' => 4,
            'foto' => 'buku/contoh.jpg',
        ]);
    }

    public function test_halaman_siswa_tidak_menampilkan_menu_admin_meskipun_admin_sedang_login(): void
    {
        $admin = User::create([
            'name' => 'Petugas Admin',
            'username' => 'admin_test_sep',
            'email' => 'admin_sep@perpus.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Mengakses halaman siswa sebagai admin
        $response = $this->actingAs($admin)->get('/siswa/dashboard');
        $response->assertStatus(200);

        // Harus menampilkan menu siswa
        $response->assertSee('Stok & Katalog Buku', false);
        $response->assertSee('Menunggu Persetujuan', false);
        $response->assertSee('Sudah Di-ACC', false);
        $response->assertSee('Denda Siswa', false);
        $response->assertSee('Riwayat Buku Selesai', false);

        // TIDAK BOLEH menampilkan menu admin di halaman siswa!
        $response->assertDontSee('Dashboard Admin');
        $response->assertDontSee('Kelola Buku');
        $response->assertDontSee('Peminjaman & Denda');
        $response->assertDontSee('Menu Petugas');
    }

    public function test_menu_menunggu_persetujuan_hanya_menampilkan_data_status_menunggu(): void
    {
        Peminjaman::create([
            'nama_siswa' => 'Rina Antrean',
            'kelas' => '10 PPLG 1',
            'book_id' => $this->book->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(7)->toDateString(),
            'status' => 'menunggu',
        ]);

        Peminjaman::create([
            'nama_siswa' => 'Budi Sudah ACC',
            'kelas' => '10 PPLG 2',
            'book_id' => $this->book->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $response = $this->get('/siswa/menunggu?nama=Rina');
        $response->assertStatus(200);
        $response->assertSee('Rina Antrean');
        $response->assertSee('Menunggu Persetujuan');
        $response->assertDontSee('Budi Sudah ACC');
    }

    public function test_menu_sudah_di_acc_hanya_menampilkan_data_status_dipinjam(): void
    {
        Peminjaman::create([
            'nama_siswa' => 'Siswa Belum ACC',
            'kelas' => '11 RPL 1',
            'book_id' => $this->book->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(7)->toDateString(),
            'status' => 'menunggu',
        ]);

        Peminjaman::create([
            'nama_siswa' => 'Siswa Sudah ACC',
            'kelas' => '11 RPL 2',
            'book_id' => $this->book->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(5)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $response = $this->get('/siswa/disetujui?nama=Siswa');
        $response->assertStatus(200);
        $response->assertSee('Siswa Sudah ACC');
        $response->assertSee('Sudah Di-ACC');
        $response->assertDontSee('Siswa Belum ACC');
    }

    public function test_menu_denda_siswa_menampilkan_tagihan_denda_aktif(): void
    {
        // Peminjaman terlambat (belum lunas)
        Peminjaman::create([
            'nama_siswa' => 'Siswa Kena Denda',
            'kelas' => '12 TKJ 1',
            'book_id' => $this->book->id,
            'tanggal_pinjam' => Carbon::today()->subDays(10)->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->subDays(3)->toDateString(),
            'status' => 'dipinjam',
        ]);

        // Peminjaman tepat waktu (bebas denda)
        Peminjaman::create([
            'nama_siswa' => 'Siswa Tertib',
            'kelas' => '12 TKJ 2',
            'book_id' => $this->book->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $response = $this->get('/siswa/denda?nama=Siswa');
        $response->assertStatus(200);
        $response->assertSee('Siswa Kena Denda');
        $response->assertSee('Rp 3.000');
        $response->assertDontSee('Siswa Tertib');
    }

    public function test_menu_riwayat_selesai_hanya_menampilkan_buku_yang_sudah_dikembalikan(): void
    {
        Peminjaman::create([
            'nama_siswa' => 'Siswa Selesai',
            'kelas' => '10 AKL 1',
            'book_id' => $this->book->id,
            'tanggal_pinjam' => '2026-09-01',
            'tanggal_kembali' => '2026-09-07',
            'status' => 'dikembalikan',
        ]);

        Peminjaman::create([
            'nama_siswa' => 'Siswa Masih Pinjam',
            'kelas' => '10 AKL 2',
            'book_id' => $this->book->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'dipinjam',
        ]);

        $response = $this->get('/siswa/riwayat-selesai?nama=Siswa');
        $response->assertStatus(200);
        $response->assertSee('Siswa Selesai');
        $response->assertSee('Selesai Dikembalikan');
        $response->assertDontSee('Siswa Masih Pinjam');
    }
}
