@extends('layouts.app')

@section('title', 'Catat Peminjaman')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-8 max-w-2xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-xl font-bold text-slate-800">Catat Peminjaman Baru</h2>
        <a href="/peminjaman" class="inline-flex items-center text-sm font-semibold text-blue-600 hover:text-blue-800 transition focus:outline-none focus:ring-2 focus:ring-blue-400 rounded-lg px-2 py-1">
            <x-icon name="ArrowLeft" class="w-4 h-4 mr-1.5" />
            <span>Batal & Kembali</span>
        </a>
    </div>

    <!-- Form akan mengirim data ke fungsi store di controller -->
    <form action="/peminjaman" method="POST">
        @csrf
        
        <div class="mb-4">
            <label class="block text-slate-700 text-sm font-semibold mb-2">Nama Peminjam (Siswa)</label>
            <select name="user_id" class="w-full border border-slate-300 rounded-xl p-2.5 bg-slate-50 focus:bg-white text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" required>
                <option value="">-- Pilih Siswa --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-slate-700 text-sm font-semibold mb-2">Buku yang Dipinjam</label>
            <select name="book_id" class="w-full border border-slate-300 rounded-xl p-2.5 bg-slate-50 focus:bg-white text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" required>
                <option value="">-- Pilih Buku --</option>
                @foreach($books as $book)
                    <option value="{{ $book->id }}">{{ $book->judul }} (Sisa Stok: {{ $book->stok }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label class="block text-slate-700 text-sm font-semibold mb-2">Tanggal Pinjam</label>
            <!-- Secara otomatis terisi tanggal hari ini -->
            <input type="date" name="tanggal_pinjam" value="{{ date('Y-m-d') }}" class="w-full border border-slate-300 rounded-xl p-2.5 bg-slate-50 focus:bg-white text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500" required>
        </div>

        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm hover:shadow transition duration-150 inline-flex items-center justify-center space-x-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <x-icon name="Save" class="w-4 h-4" />
            <span>Simpan Transaksi</span>
        </button>
    </form>
</div>
@endsection