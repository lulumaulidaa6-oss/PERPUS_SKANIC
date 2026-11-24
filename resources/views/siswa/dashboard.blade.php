@extends('layouts.siswa')

@section('title', 'Katalog & Stok Buku Tersedia')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">
    
    <!-- Hero Header Siswa Compact -->
    <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 rounded-2xl sm:rounded-3xl p-5 sm:p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-2.5 py-1 bg-white/15 backdrop-blur-md rounded-full text-[11px] font-semibold text-blue-100 mb-2 border border-white/10">
                    <x-icon name="BookOpen" class="w-3.5 h-3.5 text-blue-300" />
                    <span>Katalog Perpustakaan Siswa</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Cari Buku & Cek Stok Tersedia</h2>
                <p class="text-blue-100/90 mt-1 text-xs sm:text-sm max-w-2xl leading-relaxed">
                    Pilih buku yang ingin kamu baca, periksa ketersediaan stok fisik di perpustakaan, dan ajukan peminjaman secara langsung.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="/siswa/pinjam" class="bg-white text-blue-700 hover:bg-blue-50 px-4 py-2 rounded-xl shadow-xs font-bold text-xs sm:text-sm transition flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-white/40">
                    <x-icon name="Pencil" class="w-3.5 h-3.5" />
                    <span>Formulir Pinjam</span>
                </a>
                <a href="/siswa/riwayat" class="bg-amber-500 hover:bg-amber-600 text-white px-3.5 py-2 rounded-xl shadow-xs font-semibold text-xs sm:text-sm transition flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-amber-300">
                    <x-icon name="ClipboardList" class="w-3.5 h-3.5" />
                    <span>Cek Riwayat & Denda</span>
                </a>
                <a href="/siswa/menunggu" class="bg-white/15 hover:bg-white/25 text-white px-3 py-2 rounded-xl backdrop-blur-md border border-white/20 font-semibold text-xs sm:text-sm transition flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-white/40">
                    <x-icon name="Clock" class="w-3.5 h-3.5" />
                    <span>Menunggu ACC</span>
                </a>
                <a href="/siswa/disetujui" class="bg-white/15 hover:bg-white/25 text-white px-3 py-2 rounded-xl backdrop-blur-md border border-white/20 font-semibold text-xs sm:text-sm transition flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-white/40">
                    <x-icon name="CircleCheck" class="w-3.5 h-3.5" />
                    <span>Sudah Di-ACC</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Search Bar Katalog Buku Compact -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5 sm:p-4">
        <form action="/siswa/dashboard" method="GET">
            <div class="flex flex-col sm:flex-row gap-2.5">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="Search" class="w-4 h-4" />
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Cari judul buku, nama penulis, atau penerbit..." 
                           class="w-full pl-9 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 focus:border-blue-500 rounded-xl text-slate-800 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                    @if(!empty($search))
                        <a href="/siswa/dashboard" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" aria-label="Hapus kata kunci pencarian" title="Hapus kata kunci pencarian">
                            <x-icon name="X" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-xs text-xs sm:text-sm shrink-0 flex items-center justify-center space-x-1.5 transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <x-icon name="Search" class="w-3.5 h-3.5" />
                    <span>Cari Buku</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Grid Katalog Buku Compact -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-800 flex items-center">
                    <span>Daftar Buku Perpustakaan</span>
                    <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                        {{ $books->count() }} Koleksi
                    </span>
                </h3>
                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Pilih buku yang berstatus "Tersedia" untuk mengajukan pinjaman.</p>
            </div>
        </div>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
            @forelse($books as $book)
            <div class="bg-white border border-slate-200 rounded-xl overflow-hidden hover:shadow-md transition-all duration-200 flex flex-col group">
                
                <!-- Foto Sampul (Cover Buku Compact) -->
                <div class="h-44 sm:h-48 bg-slate-100 relative overflow-hidden flex items-center justify-center border-b border-slate-100">
                    @if($book->foto)
                        <img src="{{ asset('storage/' . $book->foto) }}" alt="{{ $book->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="flex flex-col items-center justify-center text-slate-400 p-4 text-center">
                            <x-icon name="BookOpen" class="w-10 h-10 mb-1 opacity-50" />
                            <span class="text-[11px]">Sampul Buku</span>
                        </div>
                    @endif
                    
                    <!-- Badge Stok Buku -->
                    <div class="absolute top-2.5 right-2.5">
                        @if($book->stok > 0)
                            <span class="bg-emerald-600/90 backdrop-blur-xs text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full shadow-2xs flex items-center space-x-1.5">
                                <x-icon name="CircleCheck" class="w-3 h-3" />
                                <span>Stok: {{ $book->stok }}</span>
                            </span>
                        @else
                            <span class="bg-rose-600/90 backdrop-blur-xs text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full shadow-2xs flex items-center space-x-1">
                                <x-icon name="X" class="w-3 h-3" />
                                <span>Stok Habis</span>
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Informasi Rincian Buku -->
                <div class="p-3.5 sm:p-4 flex flex-col flex-grow justify-between space-y-3">
                    <div>
                        <h4 class="font-bold text-slate-800 text-sm sm:text-base line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors">
                            {{ $book->judul }}
                        </h4>
                        <p class="text-xs text-slate-500 mt-1">Penulis: <span class="font-semibold text-slate-700">{{ $book->penulis }}</span></p>
                        <p class="text-[11px] text-slate-400 mt-0.5">Penerbit: {{ $book->penerbit }}</p>
                    </div>

                    <!-- Tombol Aksi Pinjam Compact -->
                    <div class="pt-2.5 border-t border-slate-100 flex items-center justify-between">
                        @if($book->stok > 0)
                            <a href="/siswa/pinjam?book_id={{ $book->id }}" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs py-2 px-3 rounded-xl transition duration-150 text-center flex items-center justify-center space-x-1.5 shadow-2xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <x-icon name="Plus" class="w-3.5 h-3.5" />
                                <span>Ajukan Pinjam Buku</span>
                            </a>
                        @else
                            <button disabled class="w-full bg-slate-100 text-slate-400 font-semibold text-xs py-2 px-3 rounded-xl cursor-not-allowed text-center">
                                Stok Tidak Tersedia
                            </button>
                        @endif
                    </div>
                </div>

            </div>
            @empty
            <div class="col-span-full p-8 sm:p-10 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2.5">
                    <x-icon name="Search" class="w-6 h-6 text-slate-400" />
                </div>
                <h4 class="text-sm sm:text-base font-bold text-slate-700">Tidak Ada Buku yang Cocok</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Koleksi buku dengan kata kunci pencarian "{{ $search }}" tidak ditemukan.
                </p>
                <a href="/siswa/dashboard" class="inline-flex items-center gap-1 mt-3 text-xs font-semibold text-blue-600 hover:underline">
                    <x-icon name="ArrowLeft" class="w-3.5 h-3.5" />
                    <span>Tampilkan Semua Buku</span>
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection