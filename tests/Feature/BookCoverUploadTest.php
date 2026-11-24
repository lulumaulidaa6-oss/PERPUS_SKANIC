<?php

namespace Tests\Feature;

use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookCoverUploadTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Petugas Admin',
            'username' => 'admin_perpus',
            'email' => 'admin@perpus.sch.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
    }

    public function test_upload_buku_wajib_menyertakan_foto_cover(): void
    {
        $response = $this->actingAs($this->admin)->post('/buku', [
            'judul' => 'Buku Tanpa Cover',
            'penulis' => 'Penulis X',
            'penerbit' => 'Penerbit Y',
            'stok' => 5,
            // 'foto' sengaja tidak disertakan
        ]);

        $response->assertSessionHasErrors('foto');
        $this->assertDatabaseMissing('books', [
            'judul' => 'Buku Tanpa Cover',
        ]);
    }

    public function test_upload_buku_berhasil_ketika_foto_cover_disertakan(): void
    {
        Storage::fake('public');

        $coverFile = UploadedFile::fake()->image('laskar_pelangi.jpg', 600, 800);

        $response = $this->actingAs($this->admin)->post('/buku', [
            'judul' => 'Laskar Pelangi Edisi Spesial',
            'penulis' => 'Andrea Hirata',
            'penerbit' => 'Bentang Pustaka',
            'stok' => 10,
            'foto' => $coverFile,
        ]);

        $response->assertRedirect('/buku');
        $response->assertSessionHas('success');

        $book = Book::where('judul', 'Laskar Pelangi Edisi Spesial')->first();
        $this->assertNotNull($book);
        $this->assertNotNull($book->foto);

        Storage::disk('public')->assertExists($book->foto);
    }

    public function test_upload_buku_gagal_jika_file_bukan_gambar(): void
    {
        Storage::fake('public');

        $textFile = UploadedFile::fake()->create('dokumen.pdf', 100);

        $response = $this->actingAs($this->admin)->post('/buku', [
            'judul' => 'Buku PDF Palsu',
            'penulis' => 'Penulis',
            'penerbit' => 'Penerbit',
            'stok' => 5,
            'foto' => $textFile,
        ]);

        $response->assertSessionHasErrors('foto');
    }

    public function test_admin_dapat_mengganti_foto_cover_buku_saat_edit(): void
    {
        Storage::fake('public');

        $oldCover = UploadedFile::fake()->image('old_cover.jpg');
        $oldPath = $oldCover->store('buku', 'public');

        $book = Book::create([
            'judul' => 'Buku Asli',
            'penulis' => 'Penulis Asli',
            'penerbit' => 'Penerbit Asli',
            'stok' => 5,
            'foto' => $oldPath,
        ]);

        $newCover = UploadedFile::fake()->image('new_cover.jpg');

        $response = $this->actingAs($this->admin)->put("/buku/{$book->id}", [
            'judul' => 'Buku Diperbarui',
            'penulis' => 'Penulis Baru',
            'penerbit' => 'Penerbit Baru',
            'stok' => 7,
            'foto' => $newCover,
        ]);

        $response->assertRedirect('/buku');

        $book->refresh();
        $this->assertEquals('Buku Diperbarui', $book->judul);
        $this->assertNotEquals($oldPath, $book->foto);

        // Foto baru harus ada dan foto lama dihapus
        Storage::disk('public')->assertExists($book->foto);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_hapus_buku_juga_menghapus_file_cover_dari_storage(): void
    {
        Storage::fake('public');

        $cover = UploadedFile::fake()->image('cover_hapus.jpg');
        $coverPath = $cover->store('buku', 'public');

        $book = Book::create([
            'judul' => 'Buku Akan Dihapus',
            'penulis' => 'Penulis',
            'penerbit' => 'Penerbit',
            'stok' => 2,
            'foto' => $coverPath,
        ]);

        $response = $this->actingAs($this->admin)->delete("/buku/{$book->id}");
        $response->assertRedirect('/buku');

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
        Storage::disk('public')->assertMissing($coverPath);
    }
}
