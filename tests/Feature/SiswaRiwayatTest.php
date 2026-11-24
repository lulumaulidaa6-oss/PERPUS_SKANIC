<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\Peminjaman;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiswaRiwayatTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_riwayat_dapat_diakses_secara_publik(): void
    {
        $response = $this->get('/siswa/riwayat');

        $response->assertStatus(200);
        $response->assertSee('Cek Riwayat & Data Denda', false);
        $response->assertSee('Mulai Cek Riwayat & Denda Kamu', false);
    }

    public function test_pencarian_riwayat_berdasarkan_nama_menampilkan_data_dan_statistik(): void
    {
        $book = Book::first() ?? Book::create([
            'judul' => 'Buku Uji Coba',
            'penulis' => 'Penulis Uji',
            'penerbit' => 'Penerbit Uji',
            'stok' => 5,
        ]);

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Ahmad Santoso',
            'kelas' => 'XII RPL 1',
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $response = $this->get('/siswa/riwayat?nama='.urlencode('Ahmad Santoso'));

        $response->assertStatus(200);
        $response->assertSee('Ahmad Santoso');
        $response->assertSee($book->judul);
        $response->assertSee('Sedang Dipinjam');
        $response->assertSee('Bebas Denda');

        // Bersihkan data tes
        $pinjam->delete();
    }

    public function test_perhitungan_denda_keterlambatan_berjalan(): void
    {
        $book = Book::first() ?? Book::create([
            'judul' => 'Buku Denda Uji',
            'penulis' => 'Penulis',
            'penerbit' => 'Penerbit',
            'stok' => 3,
        ]);

        // Pinjam dengan jatuh tempo 3 hari yang lalu
        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Bambang Uji',
            'kelas' => 'XI TKJ 2',
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::today()->subDays(10)->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->subDays(3)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $this->assertEquals(3, $pinjam->hari_terlambat);
        $this->assertTrue($pinjam->is_terlambat);
        $this->assertEquals(3000, $pinjam->total_denda);

        $response = $this->get('/siswa/riwayat?nama='.urlencode('Bambang Uji'));
        $response->assertStatus(200);
        $response->assertSee('Rp 3.000');
        $response->assertSee('Terlambat 3 Hari');

        // Bersihkan data tes
        $pinjam->delete();
    }

    public function test_halaman_katalog_menampilkan_tombol_cek_riwayat_dan_denda(): void
    {
        $response = $this->get('/siswa/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Cek Riwayat & Denda', false);
        $response->assertSee('/siswa/riwayat');
    }

    public function test_siswa_bisa_meminjam_buku_tanpa_akun_dan_melihat_riwayatnya(): void
    {
        Storage::fake('public');

        $book = Book::create([
            'judul' => 'Laskar Pelangi',
            'penulis' => 'Andrea Hirata',
            'penerbit' => 'Bentang Pustaka',
            'stok' => 5,
        ]);

        $file = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->post('/siswa/pinjam', [
            'nama_siswa' => 'Siti Aisyah',
            'kelas' => '10 PPLG 1',
            'book_id' => $book->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(7)->toDateString(),
            'bukti_foto' => $file,
        ]);

        $response->assertRedirect('/siswa/riwayat?nama='.urlencode('Siti Aisyah'));

        $this->assertDatabaseHas('peminjamen', [
            'nama_siswa' => 'Siti Aisyah',
            'kelas' => '10 PPLG 1',
            'user_id' => null,
            'status' => 'menunggu',
        ]);

        $riwayatResponse = $this->get('/siswa/riwayat?nama='.urlencode('Siti Aisyah'));
        $riwayatResponse->assertStatus(200);
        $riwayatResponse->assertSee('Siti Aisyah');
        $riwayatResponse->assertSee('Laskar Pelangi');
        $riwayatResponse->assertSee('Menunggu Persetujuan');
    }
}
