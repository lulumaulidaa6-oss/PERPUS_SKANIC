<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\LibrarySetting;
use App\Models\Peminjaman;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StokBukuOtomatisTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Book $buku;

    protected function setUp(): void
    {
        parent::setUp();

        LibrarySetting::create([
            'nama_perpustakaan' => 'Perpustakaan SMK PerpusSiswa',
            'tarif_denda_harian' => 1000,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Perpustakaan',
            'username' => 'admin_perpus',
            'email' => 'admin@perpussiswa.sch.id',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        $this->buku = Book::create([
            'judul' => 'Pemrograman Web dengan Laravel 11',
            'penulis' => 'Lulu Maulida',
            'penerbit' => 'Informatika Press',
            'stok' => 3,
        ]);
    }

    /**
     * 1. Saat peminjaman masih menunggu persetujuan: stok buku tidak berkurang
     */
    public function test_stok_buku_tidak_berkurang_saat_pengajuan_masih_menunggu_persetujuan(): void
    {
        Storage::fake('public');

        $stokAwal = $this->buku->stok; // 3

        $file = UploadedFile::fake()->image('kartu_pelajar.jpg');

        $response = $this->post('/siswa/pinjam', [
            'nama_siswa' => 'Budi Santoso',
            'kelas' => '10 PPLG 1',
            'book_id' => $this->buku->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(7)->toDateString(),
            'bukti_foto' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('peminjamen', [
            'nama_siswa' => 'Budi Santoso',
            'book_id' => $this->buku->id,
            'status' => 'menunggu',
        ]);

        // Stok buku tetap 3 (tidak berkurang saat status menunggu)
        $this->assertEquals($stokAwal, $this->buku->fresh()->stok);
    }

    /**
     * 2. Saat peminjaman disetujui (ACC): stok buku otomatis berkurang sesuai jumlah buku
     */
    public function test_stok_buku_otomatis_berkurang_saat_peminjaman_disetujui_acc(): void
    {
        $stokAwal = $this->buku->stok; // 3

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Ahmad Fauzi',
            'kelas' => '11 RPL 2',
            'book_id' => $this->buku->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(7)->toDateString(),
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/setujui", [
            'catatan' => 'Disetujui untuk peminjaman mandiri',
        ]);

        $response->assertRedirect();
        $pinjam->refresh();
        $this->assertEquals('dipinjam', $pinjam->status);

        // Stok otomatis berkurang 1 (3 -> 2)
        $this->assertEquals($stokAwal - 1, $this->buku->fresh()->stok);
    }

    /**
     * 3. Saat peminjaman ditolak: stok buku tidak berkurang
     */
    public function test_stok_buku_tidak_berkurang_saat_peminjaman_ditolak(): void
    {
        $stokAwal = $this->buku->stok; // 3

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Rina Wijaya',
            'kelas' => '10 DKV 1',
            'book_id' => $this->buku->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->delete("/peminjaman/{$pinjam->id}/tolak", [
            'alasan_penolakan' => 'Foto bukti tidak jelas',
        ]);

        $response->assertRedirect();
        $pinjam->refresh();
        $this->assertEquals('ditolak', $pinjam->status);

        // Stok buku tetap utuh (3)
        $this->assertEquals($stokAwal, $this->buku->fresh()->stok);
    }

    /**
     * 4. Jika pengembalian belum dikonfirmasi admin: stok buku belum bertambah
     */
    public function test_stok_buku_belum_bertambah_saat_siswa_mengajukan_pengembalian_sebelum_dikonfirmasi(): void
    {
        Storage::fake('public');

        // Buku dipinjam sehingga stok berkurang menjadi 2
        $this->buku->update(['stok' => 2]);

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Dewi Sartika',
            'kelas' => '12 TKJ 1',
            'book_id' => $this->buku->id,
            'tanggal_pinjam' => Carbon::today()->subDays(5)->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(2)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $foto = UploadedFile::fake()->image('buku_kembali.jpg');

        $response = $this->post("/siswa/peminjaman/{$pinjam->id}/kembalikan", [
            'foto_kembali' => $foto,
            'catatan_kembali' => 'Kondisi buku baik',
        ]);

        $response->assertRedirect();
        $pinjam->refresh();
        $this->assertEquals('menunggu_pengembalian', $pinjam->status);

        // Stok buku masih 2 (belum bertambah karena belum dikonfirmasi admin)
        $this->assertEquals(2, $this->buku->fresh()->stok);
    }

    /**
     * 5. Saat buku dikembalikan dan dikonfirmasi admin: stok buku otomatis bertambah kembali
     */
    public function test_stok_buku_otomatis_bertambah_saat_pengembalian_dikonfirmasi_admin(): void
    {
        // Kondisi saat dipinjam: stok 2
        $this->buku->update(['stok' => 2]);

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Dewi Sartika',
            'kelas' => '12 TKJ 1',
            'book_id' => $this->buku->id,
            'tanggal_pinjam' => Carbon::today()->subDays(5)->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(2)->toDateString(),
            'status' => 'menunggu_pengembalian',
        ]);

        $response = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/kembali", [
            'kondisi_buku_kembali' => 'baik',
            'denda_tambahan' => 0,
        ]);

        $response->assertRedirect();
        $pinjam->refresh();
        $this->assertEquals('dikembalikan', $pinjam->status);

        // Stok buku bertambah kembali menjadi 3 (2 + 1 = 3)
        $this->assertEquals(3, $this->buku->fresh()->stok);
    }

    /**
     * 6. Jika kondisi buku hilang saat dikembalikan: stok buku tidak bertambah
     */
    public function test_stok_buku_tidak_bertambah_saat_kondisi_buku_hilang(): void
    {
        $this->buku->update(['stok' => 2]);

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Bambang Hilang',
            'kelas' => '12 AKL 2',
            'book_id' => $this->buku->id,
            'tanggal_pinjam' => Carbon::today()->subDays(5)->toDateString(),
            'status' => 'dipinjam',
        ]);

        $response = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/kembali", [
            'kondisi_buku_kembali' => 'hilang',
            'denda_tambahan' => 50000,
        ]);

        $response->assertRedirect();
        $pinjam->refresh();
        $this->assertEquals('dikembalikan', $pinjam->status);

        // Stok buku tetap 2 karena fisiknya hilang
        $this->assertEquals(2, $this->buku->fresh()->stok);
    }

    /**
     * 7. Cegah stok menjadi minus saat stok buku habis (0) dan admin mencoba menyetujui (ACC)
     */
    public function test_cegah_stok_minus_saat_stok_buku_0_dan_admin_mencoba_acc(): void
    {
        // Set stok menjadi 0 (habis)
        $this->buku->update(['stok' => 0]);

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Anto Pengantri',
            'kelas' => '10 PPLG 2',
            'book_id' => $this->buku->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/setujui");

        $response->assertRedirect();
        $response->assertSessionHas('error');

        $pinjam->refresh();
        // Status tetap menunggu, tidak disetujui
        $this->assertEquals('menunggu', $pinjam->status);

        // Stok tetap 0 dan tidak menjadi minus (-1)
        $this->assertEquals(0, $this->buku->fresh()->stok);
    }

    /**
     * 8. Cegah stok minus saat siswa mencoba meminjam buku yang stoknya 0
     */
    public function test_cegah_siswa_meminjam_buku_yang_stoknya_habis(): void
    {
        Storage::fake('public');
        $this->buku->update(['stok' => 0]);

        $file = UploadedFile::fake()->image('kartu.jpg');

        $response = $this->post('/siswa/pinjam', [
            'nama_siswa' => 'Citra Lestari',
            'kelas' => '10 RPL 1',
            'book_id' => $this->buku->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(7)->toDateString(),
            'bukti_foto' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');

        // Tidak ada data peminjaman yang dibuat
        $this->assertDatabaseMissing('peminjamen', [
            'nama_siswa' => 'Citra Lestari',
        ]);

        $this->assertEquals(0, $this->buku->fresh()->stok);
    }

    /**
     * 9. Pastikan buku tidak dihitung dua kali jika tombol ACC diklik berulang kali
     */
    public function test_cegah_perhitungan_ganda_jika_tombol_acc_diklik_berulang_kali(): void
    {
        $stokAwal = $this->buku->stok; // 3

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Fajar Pratama',
            'kelas' => '11 TKJ 1',
            'book_id' => $this->buku->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'menunggu',
        ]);

        // Klik 1: Pertama kali disetujui (ACC)
        $response1 = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/setujui");
        $response1->assertRedirect();
        $response1->assertSessionHas('success');
        $this->assertEquals($stokAwal - 1, $this->buku->fresh()->stok); // 2

        // Klik 2: Klik kedua kali (tombol diklik berulang kali)
        $response2 = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/setujui");
        $response2->assertRedirect();
        $response2->assertSessionHas('info');

        // Klik 3: Klik ketiga kali
        $response3 = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/setujui");
        $response3->assertRedirect();
        $response3->assertSessionHas('info');

        // Stok buku tetap 2 (HANYA berkurang 1 kali, tidak dihitung berulang)
        $this->assertEquals(2, $this->buku->fresh()->stok);
    }

    /**
     * 10. Pastikan buku tidak dihitung dua kali jika tombol konfirmasi pengembalian diklik berulang kali
     */
    public function test_cegah_perhitungan_ganda_jika_tombol_konfirmasi_pengembalian_diklik_berulang_kali(): void
    {
        $this->buku->update(['stok' => 2]);

        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Indra Gunawan',
            'kelas' => '11 RPL 1',
            'book_id' => $this->buku->id,
            'tanggal_pinjam' => Carbon::today()->subDays(5)->toDateString(),
            'tanggal_perkiraan_kembali' => Carbon::today()->addDays(2)->toDateString(),
            'status' => 'dipinjam',
        ]);

        // Klik 1: Pertama kali konfirmasi pengembalian
        $response1 = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/kembali", [
            'kondisi_buku_kembali' => 'baik',
        ]);
        $response1->assertRedirect();
        $response1->assertSessionHas('success');
        $this->assertEquals(3, $this->buku->fresh()->stok); // Bertambah jadi 3

        // Klik 2: Klik konfirmasi lagi pada data yang sama
        $response2 = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/kembali", [
            'kondisi_buku_kembali' => 'baik',
        ]);
        $response2->assertRedirect();
        $response2->assertSessionHas('info');

        // Klik 3: Klik konfirmasi ketiga kali
        $response3 = $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/kembali", [
            'kondisi_buku_kembali' => 'baik',
        ]);
        $response3->assertRedirect();
        $response3->assertSessionHas('info');

        // Stok buku tetap 3 (HANYA bertambah 1 kali, tidak dihitung berkali-kali)
        $this->assertEquals(3, $this->buku->fresh()->stok);
    }

    /**
     * 11. Perubahan stok otomatis diperbarui pada Data Buku Admin, Stok & Katalog Siswa, serta data peminjaman
     */
    public function test_perubahan_stok_otomatis_terefleksi_di_data_buku_admin_dan_katalog_siswa_dan_peminjaman(): void
    {
        // Awal: stok 3
        $pinjam = Peminjaman::create([
            'nama_siswa' => 'Siswa Realtime',
            'kelas' => '10 PPLG 3',
            'book_id' => $this->buku->id,
            'tanggal_pinjam' => Carbon::today()->toDateString(),
            'status' => 'menunggu',
        ]);

        // Verifikasi sebelum di-ACC:
        // 1. Data Buku Admin melihat stok 3
        $resAdminBuku = $this->actingAs($this->admin)->get('/buku');
        $resAdminBuku->assertSee('3 Tersedia');

        // 2. Katalog Siswa melihat stok 3
        $resSiswaKatalog = $this->get('/siswa/dashboard');
        $resSiswaKatalog->assertSee('Stok: 3');

        // 3. Data Peminjaman Admin melihat stok 3
        $resAdminPeminjaman = $this->actingAs($this->admin)->get('/peminjaman');
        $resAdminPeminjaman->assertSee('3 Tersedia');

        // Admin melakukan ACC
        $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/setujui");

        // Verifikasi setelah di-ACC:
        // 1. Data Buku Admin otomatis menunjukkan stok 2
        $resAdminBukuAfter = $this->actingAs($this->admin)->get('/buku');
        $resAdminBukuAfter->assertSee('2 Tersedia');

        // 2. Katalog Siswa otomatis menunjukkan stok 2
        $resSiswaKatalogAfter = $this->get('/siswa/dashboard');
        $resSiswaKatalogAfter->assertSee('Stok: 2');

        // 3. Data Peminjaman Admin otomatis menunjukkan stok 2
        $resAdminPeminjamanAfter = $this->actingAs($this->admin)->get('/peminjaman');
        $resAdminPeminjamanAfter->assertSee('2 Tersedia');

        // Admin konfirmasi pengembalian
        $this->actingAs($this->admin)->put("/peminjaman/{$pinjam->id}/kembali", [
            'kondisi_buku_kembali' => 'baik',
        ]);

        // Verifikasi setelah dikembalikan:
        // Stok kembali menjadi 3 di semua tampilan
        $this->actingAs($this->admin)->get('/buku')->assertSee('3 Tersedia');
        $this->get('/siswa/dashboard')->assertSee('Stok: 3');
        $this->actingAs($this->admin)->get('/peminjaman')->assertSee('3 Tersedia');
    }
}
