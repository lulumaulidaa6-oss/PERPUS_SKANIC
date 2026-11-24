<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Menampilkan daftar semua buku di perpustakaan
     */
    public function index()
    {
        $books = Book::latest('id')->get();

        return view('books.index', compact('books'));
    }

    /**
     * Menampilkan form tambah buku baru
     */
    public function create()
    {
        return view('books.create');
    }

    /**
     * Menyimpan data buku baru dengan cover foto WAJIB diunggah
     */
    public function store(Request $request)
    {
        // 1. Validasi inputan (Foto cover buku WAJIB)
        $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'foto' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'judul.required' => 'Judul buku wajib diisi.',
            'penulis.required' => 'Nama penulis buku wajib diisi.',
            'penerbit.required' => 'Nama penerbit wajib diisi.',
            'stok.required' => 'Jumlah stok buku wajib diisi.',
            'stok.integer' => 'Stok buku harus berupa bilangan bulat.',
            'stok.min' => 'Stok buku tidak boleh kurang dari 0.',
            'foto.required' => 'Foto sampul (cover buku) wajib diunggah!',
            'foto.image' => 'File cover buku harus berupa gambar.',
            'foto.mimes' => 'Format gambar yang diperbolehkan: JPEG, PNG, JPG, atau WEBP.',
            'foto.max' => 'Ukuran file foto cover maksimal adalah 2MB.',
        ]);

        // 2. Simpan file foto cover buku ke storage public
        $fotoPath = $request->file('foto')->store('buku', 'public');

        // 3. Simpan data ke tabel books
        Book::create([
            'judul' => $request->judul,
            'penulis' => $request->penulis,
            'penerbit' => $request->penerbit,
            'stok' => $request->stok,
            'foto' => $fotoPath,
            'kategori_id' => $request->kategori_id ?? 1,
        ]);

        // 4. Arahkan kembali ke halaman daftar buku dengan notifikasi sukses
        return redirect('/buku')->with('success', 'Buku baru beserta cover buku berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit buku
     */
    public function edit($id)
    {
        $book = Book::findOrFail($id);

        return view('books.edit', compact('book'));
    }

    /**
     * Menyimpan perubahan data buku
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'penulis' => 'required|string|max:255',
            'penerbit' => 'required|string|max:255',
            'stok' => 'required|integer|min:0',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ], [
            'foto.image' => 'File cover buku harus berupa gambar.',
            'foto.mimes' => 'Format gambar yang diperbolehkan: JPEG, PNG, JPG, atau WEBP.',
            'foto.max' => 'Ukuran file foto cover maksimal adalah 2MB.',
        ]);

        $book = Book::findOrFail($id);

        $data = [
            'judul' => $request->judul,
            'penulis' => $request->penulis,
            'penerbit' => $request->penerbit,
            'stok' => $request->stok,
        ];

        // Jika admin mengunggah foto baru, hapus foto lama dan simpan yang baru
        if ($request->hasFile('foto')) {
            if ($book->foto && Storage::disk('public')->exists($book->foto)) {
                Storage::disk('public')->delete($book->foto);
            }
            $data['foto'] = $request->file('foto')->store('buku', 'public');
        }

        $book->update($data);

        return redirect('/buku')->with('success', 'Data buku berhasil diperbarui!');
    }

    /**
     * Menghapus data buku dan file cover dari database dan storage
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id);

        // Hapus file cover buku dari penyimpanan jika ada
        if ($book->foto && Storage::disk('public')->exists($book->foto)) {
            Storage::disk('public')->delete($book->foto);
        }

        $book->delete();

        return redirect('/buku')->with('success', 'Buku berhasil dihapus dari perpustakaan!');
    }
}
