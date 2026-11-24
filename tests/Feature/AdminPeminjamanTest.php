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

class AdminPeminjamanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Book $book1;

    private Book $book2;

    protected function setUp(): void
    {
        parent::setUp();

        LibrarySetting::create([
            'nama_perpustakaan' => 'Perpustakaan SMK Uji Coba',
            'tarif_denda_harian' => 1000,
        ]);

        $this->admin = User::create([
            'name' => 'Petugas Admin',
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->book1 = Book::create([
            'judul' => 'Pemrograman Web Modern',
            'penulis' => 'Taylor Otwell',
            'penerbit' => 'Laravel Press',
            'stok' => 5,
        ]);

        $this->book2 = Book::create([
            'judul' => 'Algoritma & Struktur Data',
            'penulis' => 'Donald Knuth',
            'penerbit' => 'Informatika',
            'stok' => 3,
        ]);
    }

    public function test_halaman_peminjaman_admin_memerlukan_login_admin(): void
    {
        // Unauthenticated harus diarahkan ke login
        $guestResponse = $this->get('/peminjaman');
        $guestResponse->assertRedirect('/login');

        // Admin berhasil mengakses
        $adminResponse = $this->actingAs($this->admin)->get('/peminjaman');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSee('Kelola Peminjaman', false);
        $adminResponse->assertSee('Pencarian Cepat', false);
        $adminResponse->assertSee('Status Transaksi', false);
    }

    public function test_fitur_pencarian_berdasarkan_nama_siswa_dan_judul_buku(): void
    {
        Peminjaman::create([
            'nama_siswa' => 'Rina Gunawan',
            'kelas' => '11 RPL 1',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'dipinjam',
        ]);

        Peminjaman::create([
            'nama_siswa' => 'Dimas Pratama',
            'kelas' => '10 TKJ 2',
            'book_id' => $this->book2->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'dipinjam',
        ]);

        // Cari berdasarkan nama siswa "Rina"
        $searchSiswa = $this->actingAs($this->admin)->get('/peminjaman?search=Rina');
        $searchSiswa->assertStatus(200);
        $searchSiswa->assertSee('Rina Gunawan');
        $searchSiswa->assertSee('Pemrograman Web Modern');
        $searchSiswa->assertDontSee('Dimas Pratama');

        // Cari berdasarkan judul buku "Algoritma"
        $searchBuku = $this->actingAs($this->admin)->get('/peminjaman?search=Algoritma');
        $searchBuku->assertStatus(200);
        $searchBuku->assertSee('Dimas Pratama');
        $searchBuku->assertSee('Algoritma & Struktur Data');
        $searchBuku->assertDontSee('Rina Gunawan');
    }

    public function test_filter_berdasarkan_status(): void
    {
        Peminjaman::create([
            'nama_siswa' => 'Siswa Dipinjam',
            'kelas' => '12 RPL 1',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'dipinjam',
        ]);

        Peminjaman::create([
            'nama_siswa' => 'Siswa Ditolak',
            'kelas' => '12 RPL 2',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'ditolak',
        ]);

        Peminjaman::create([
            'nama_siswa' => 'Siswa Kembali',
            'kelas' => '12 RPL 3',
            'book_id' => $this->book2->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'dikembalikan',
        ]);

        // Filter status ditolak
        $responseDitolak = $this->actingAs($this->admin)->get('/peminjaman?status=ditolak');
        $responseDitolak->assertStatus(200);
        $responseDitolak->assertSee('Siswa Ditolak');
        $responseDitolak->assertDontSee('Siswa Dipinjam');
        $responseDitolak->assertDontSee('Siswa Kembali');

        // Filter status dikembalikan
        $responseKembali = $this->actingAs($this->admin)->get('/peminjaman?status=dikembalikan');
        $responseKembali->assertStatus(200);
        $responseKembali->assertSee('Siswa Kembali');
        $responseKembali->assertDontSee('Siswa Ditolak');
    }

    public function test_filter_berdasarkan_rentang_waktu_tanggal(): void
    {
        Peminjaman::create([
            'nama_siswa' => 'Siswa Bulan Ini',
            'kelas' => '11 RPL 1',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => '2026-10-05',
            'status' => 'dipinjam',
        ]);

        Peminjaman::create([
            'nama_siswa' => 'Siswa Bulan Lalu',
            'kelas' => '11 RPL 2',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => '2026-09-10',
            'status' => 'dikembalikan',
        ]);

        $response = $this->actingAs($this->admin)->get('/peminjaman?tanggal_awal=2026-10-01&tanggal_akhir=2026-10-31');
        $response->assertStatus(200);
        $response->assertSee('Siswa Bulan Ini');
        $response->assertDontSee('Siswa Bulan Lalu');
    }

    public function test_pengurutan_sorting_data(): void
    {
        Peminjaman::create([
            'nama_siswa' => 'Ahmad Siswa',
            'kelas' => '10 RPL 1',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => '2026-10-01',
            'denda' => 1000,
            'status' => 'dikembalikan',
        ]);

        Peminjaman::create([
            'nama_siswa' => 'Zaky Siswa',
            'kelas' => '10 RPL 2',
            'book_id' => $this->book2->id,
            'tanggal_pinjam' => '2026-10-05',
            'denda' => 15000,
            'status' => 'dikembalikan',
        ]);

        // Sort denda tertinggi
        $responseSortDenda = $this->actingAs($this->admin)->get('/peminjaman?sort=denda_tertinggi');
        $responseSortDenda->assertStatus(200);
        $responseSortDenda->assertSeeInOrder(['Zaky Siswa', 'Ahmad Siswa']);

        // Sort nama A-Z
        $responseSortNama = $this->actingAs($this->admin)->get('/peminjaman?sort=nama_asc');
        $responseSortNama->assertStatus(200);
        $responseSortNama->assertSeeInOrder(['Ahmad Siswa', 'Zaky Siswa']);
    }

    public function test_admin_menerima_buku_dan_menghitung_denda_siswa(): void
    {
        $stokAwal = $this->book1->stok;

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Budi Terlambat',
            'kelas' => '11 TKJ 1',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => Carbon::today()->subDays(10)->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->subDays(4)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $response = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/kembali", [
            'kondisi_buku_kembali' => 'baik',
            'denda_tambahan' => 0,
            'status_denda' => 'belum_dibayar',
        ]);

        $response->assertRedirect();

        $pinjam->refresh();
        $this->assertEquals('dikembalikan', $pinjam->status);
        $this->assertEquals(4000, $pinjam->denda); // 4 hari x Rp 1000
        $this->assertEquals('belum_dibayar', $pinjam->status_denda);
        $this->assertEquals($stokAwal + 1, $this->book1->fresh()->stok);
    }

    public function test_admin_dapat_menandai_denda_sebagai_lunas(): void
    {
        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Citra Denda',
            'kelas' => '11 PPLG 2',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => Carbon::today()->subDays(10)->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->subDays(2)->toDateString(),
            'status' => 'dikembalikan',
            'tanggal_kembali' => Carbon::today()->toDateString(),
            'denda' => 2000,
            'status_denda' => 'belum_dibayar',
        ]);

        $response = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/bayar-denda");
        $response->assertRedirect();

        $pinjam->refresh();
        $this->assertEquals('lunas', $pinjam->status_denda);
        $this->assertTrue($pinjam->is_denda_lunas);
    }

    public function test_admin_dapat_mengubah_nominal_dan_status_denda_manual(): void
    {
        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Fajar Rusak',
            'kelas' => '12 TKJ 3',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'dikembalikan',
            'tanggal_kembali' => Carbon::today()->toDateString(),
            'denda' => 0,
            'status_denda' => 'bebas',
        ]);

        $response = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/update-denda", [
            'denda' => 15000,
            'status_denda' => 'belum_dibayar',
            'kondisi_buku_kembali' => 'rusak sampul',
        ]);

        $response->assertRedirect();

        $pinjam->refresh();
        $this->assertEquals(15000, $pinjam->denda);
        $this->assertEquals('belum_dibayar', $pinjam->status_denda);
        $this->assertEquals('rusak sampul', $pinjam->kondisi_buku_kembali);
    }

    public function test_admin_dashboard_menampilkan_statistik_denda(): void
    {
        Peminjaman::create([
            'nama_siswa' => 'Siswa Denda Dashboard',
            'kelas' => '10 PPLG 1',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'dikembalikan',
            'denda' => 5000,
            'status_denda' => 'belum_dibayar',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Denda Belum Lunas', false);
        $response->assertSee('Rp 5.000', false);
    }

    public function test_admin_dapat_menyetujui_peminjaman_dan_mencatat_admin_pemroses(): void
    {
        $stokAwal = $this->book1->stok;

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Budi Siswa',
            'kelas' => '10 PPLG 1',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/setujui", [
            'catatan' => 'Disetujui untuk peminjaman 7 hari',
        ]);

        $response->assertRedirect();
        $pinjam->refresh();
        $this->assertEquals('dipinjam', $pinjam->status);
        $this->assertEquals($this->admin->name, $pinjam->diproses_oleh);
        $this->assertEquals('Disetujui untuk peminjaman 7 hari', $pinjam->catatan);
        $this->assertEquals($stokAwal - 1, $this->book1->fresh()->stok);
    }

    public function test_admin_dapat_menolak_peminjaman_dengan_alasan_dan_mencatat_admin_pemroses(): void
    {
        $stokAwal = $this->book1->stok;

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Siti Siswa',
            'kelas' => '11 RPL 2',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->delete("/peminjaman/{$pinjam->id}/tolak", [
            'alasan_penolakan' => 'Buku sedang dalam perbaikan fisik',
            'catatan' => 'Silakan pilih judul buku alternatif',
        ]);

        $response->assertRedirect();
        $pinjam->refresh();
        $this->assertEquals('ditolak', $pinjam->status);
        $this->assertEquals('Buku sedang dalam perbaikan fisik', $pinjam->alasan_penolakan);
        $this->assertEquals($this->admin->name, $pinjam->diproses_oleh);
        $this->assertEquals('Silakan pilih judul buku alternatif', $pinjam->catatan);
        // Stok buku tidak berkurang saat ditolak
        $this->assertEquals($stokAwal, $this->book1->fresh()->stok);
    }

    public function test_admin_dapat_menghapus_data_pengajuan_dan_tidak_muncul_di_daftar_maupun_excel(): void
    {
        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Siswa Dihapus',
            'kelas' => '10 DKV 1',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'ditolak',
            'alasan_penolakan' => 'Data ganda',
        ]);

        // Pastikan muncul di daftar sebelum dihapus
        $responseBefore = $this->actingAs($this->admin)->get('/peminjaman');
        $responseBefore->assertSee('Siswa Dihapus');

        // Lakukan penghapusan
        $deleteResponse = $this->actingAs($this->admin)->delete("/peminjaman/{$pinjam->id}");
        $deleteResponse->assertRedirect();

        // Pastikan berstatus soft deleted di DB
        $this->assertSoftDeleted('peminjamen', ['id' => $pinjam->id]);

        // Pastikan tidak muncul lagi di daftar peminjaman
        $this->flushSession();
        $responseAfter = $this->actingAs($this->admin)->get('/peminjaman');
        $responseAfter->assertDontSee('Siswa Dihapus');
        $this->assertCount(0, $responseAfter->viewData('peminjaman'));

        // Pastikan tidak muncul pada export Excel
        $excelResponse = $this->actingAs($this->admin)->get('/peminjaman/export-excel');
        $excelResponse->assertDontSee('Siswa Dihapus');
    }

    public function test_admin_dapat_mengekspor_rekap_excel_dengan_detail_lengkap(): void
    {
        Peminjaman::create([
            'nama_siswa' => 'Rangga Sasongko',
            'kelas' => '12 RPL 1',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => '2026-10-01',
            'tanggal_perkiraan_kembali' => '2026-10-08',
            'status' => 'ditolak',
            'alasan_penolakan' => 'Stok buku habis di rak',
            'diproses_oleh' => 'Petugas Admin',
            'catatan' => 'Perlu restock',
        ]);

        $response = $this->actingAs($this->admin)->get('/peminjaman/export-excel');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');

        $content = $response->getContent();
        $this->assertStringContainsString('REKAP DATA PEMINJAMAN &amp; PENGAJUAN PERPUSTAKAAN', $content);
        $this->assertStringContainsString('Rangga Sasongko', $content);
        $this->assertStringContainsString('12 RPL 1', $content);
        $this->assertStringContainsString('Pemrograman Web Modern', $content);
        $this->assertStringContainsString('Stok buku habis di rak', $content);
        $this->assertStringContainsString('Petugas Admin', $content);
        $this->assertStringContainsString('Perlu restock', $content);
    }

    public function test_rekap_excel_mendukung_filter_status_dan_rentang_tanggal(): void
    {
        Peminjaman::create([
            'nama_siswa' => 'Siswa Disetujui',
            'kelas' => '11 RPL 1',
            'book_id' => $this->book1->id,
            'tanggal_pinjam' => '2026-10-05',
            'status' => 'dipinjam',
            'diproses_oleh' => 'Petugas Admin',
        ]);

        Peminjaman::create([
            'nama_siswa' => 'Siswa Ditolak Filter',
            'kelas' => '11 RPL 2',
            'book_id' => $this->book2->id,
            'tanggal_pinjam' => '2026-10-05',
            'status' => 'ditolak',
            'alasan_penolakan' => 'Buku rusak',
            'diproses_oleh' => 'Petugas Admin',
        ]);

        // Filter status ditolak
        $response = $this->actingAs($this->admin)->get('/peminjaman/export-excel?status=ditolak');
        $response->assertStatus(200);
        $content = $response->getContent();
        $this->assertStringContainsString('Siswa Ditolak Filter', $content);
        $this->assertStringNotContainsString('Siswa Disetujui', $content);
    }

    public function test_admin_dapat_mengakses_halaman_pengaturan_akun(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/pengaturan-akun');
        $response->assertStatus(200);
        $response->assertSee('Pengaturan Akun Petugas / Admin', false);
        $response->assertSee($this->admin->username);
    }

    public function test_admin_dapat_mengubah_username_dan_password_dengan_hashing_serta_login_ulang(): void
    {
        $response = $this->actingAs($this->admin)->put('/admin/pengaturan-akun', [
            'name' => 'Admin Baru Sukses',
            'username' => 'admin_baru',
            'email' => 'adminbaru@test.com',
            'current_password' => 'password',
            'password' => 'passwordbaru123',
            'password_confirmation' => 'passwordbaru123',
        ]);

        $response->assertRedirect('/admin/pengaturan-akun');
        $response->assertSessionHas('success');

        $this->admin->refresh();
        $this->assertEquals('admin_baru', $this->admin->username);
        $this->assertEquals('Admin Baru Sukses', $this->admin->name);
        $this->assertTrue(Hash::check('passwordbaru123', $this->admin->password));

        // Logout dan coba login dengan kredensial baru
        $this->post('/logout');

        $loginResponse = $this->post('/login', [
            'username' => 'admin_baru',
            'password' => 'passwordbaru123',
        ]);

        $loginResponse->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }
}
