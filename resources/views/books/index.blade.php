@extends('layouts.app')

@section('title', 'Kelola Katalog Buku')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/4 -top-12 w-48 h-48 bg-indigo-500/20 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-blue-200 mb-3 border border-white/10">
                    <x-icon name="BookOpen" class="w-3.5 h-3.5 text-blue-400" />
                    <span>Koleksi Perpustakaan</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Kelola Katalog Buku</h2>
                <p class="text-slate-300 text-sm sm:text-base mt-2 leading-relaxed">
                    Daftar seluruh koleksi buku perpustakaan lengkap dengan foto cover dan ketersediaan stok fisik.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="/buku/create" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm px-5 py-2.5 rounded-xl shadow-lg shadow-blue-900/30 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30">
                    <x-icon name="Plus" class="w-4 h-4" />
                    <span>Tambah Buku Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Tabel Daftar Buku -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center">
                    <span>Semua Koleksi Buku</span>
                    <span class="ml-2.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                        Total: {{ $books->count() }} Judul
                    </span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Semua buku wajib memiliki foto sampul (cover) sebelum dipublikasikan ke siswa.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-600 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200">
                        <th class="p-3.5 pl-5">Cover Buku</th>
                        <th class="p-3.5">Judul Buku</th>
                        <th class="p-3.5">Penulis & Penerbit</th>
                        <th class="p-3.5 text-center">Status Stok</th>
                        <th class="p-3.5 pr-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($books as $book)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        
                        <!-- 1. Cover Thumbnail -->
                        <td class="p-3.5 pl-5">
                            <div class="w-14 h-20 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center relative shadow-xs group">
                                @if($book->foto)
                                    <img src="{{ asset('storage/' . $book->foto) }}" alt="{{ $book->judul }}" class="w-full h-full object-cover">
                                    <button type="button" 
                                            onclick="previewCoverModal('{{ asset('storage/' . $book->foto) }}', '{{ addslashes($book->judul) }}')" 
                                            class="absolute inset-0 bg-slate-900/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs focus:opacity-100 focus:outline-none" 
                                            aria-label="Perbesar sampul buku {{ $book->judul }}"
                                            title="Perbesar Cover">
                                        <x-icon name="Eye" class="w-5 h-5 text-white" />
                                    </button>
                                @else
                                    <div class="flex flex-col items-center justify-center text-slate-300">
                                        <x-icon name="BookOpen" class="w-6 h-6 text-slate-300" />
                                        <span class="text-[9px] text-slate-400">No Cover</span>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <!-- 2. Judul Buku -->
                        <td class="p-3.5">
                            <div class="max-w-md">
                                <h4 class="font-bold text-slate-800 text-base leading-snug">{{ $book->judul }}</h4>
                                <span class="text-xs text-slate-400 mt-0.5 block">ID Buku: #{{ $book->id }}</span>
                            </div>
                        </td>

                        <!-- 3. Penulis & Penerbit -->
                        <td class="p-3.5">
                            <div class="text-xs space-y-1">
                                <p class="text-slate-700">Penulis: <strong class="text-slate-800 font-semibold">{{ $book->penulis }}</strong></p>
                                <p class="text-slate-500">Penerbit: <span class="text-slate-700">{{ $book->penerbit }}</span></p>
                            </div>
                        </td>

                        <!-- 4. Stok Buku -->
                        <td class="p-3.5 text-center">
                            @if($book->stok > 0)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <x-icon name="CircleCheck" class="w-3.5 h-3.5 text-emerald-600 mr-1" />
                                    <span>{{ $book->stok }} Tersedia</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    <x-icon name="X" class="w-3.5 h-3.5 text-rose-600 mr-1" />
                                    <span>Stok Habis</span>
                                </span>
                            @endif
                        </td>

                        <!-- 5. Aksi -->
                        <td class="p-3.5 pr-5 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <a href="/buku/{{ $book->id }}/edit" 
                                   class="px-3 py-1.5 rounded-lg border border-slate-300 text-slate-700 hover:text-blue-600 hover:border-blue-300 text-xs font-semibold transition inline-flex items-center space-x-1 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                                    <x-icon name="Pencil" class="w-3.5 h-3.5" />
                                    <span>Edit</span>
                                </a>

                                <form action="/buku/{{ $book->id }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku \'{{ addslashes($book->judul) }}\'? Data peminjaman buku ini juga dapat terpengaruh.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-semibold transition inline-flex items-center space-x-1 focus:outline-none focus:ring-2 focus:ring-rose-500/20">
                                        <x-icon name="Trash2" class="w-3.5 h-3.5" />
                                        <span>Hapus</span>
                                    </button>
                                </form>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <x-icon name="BookOpen" class="w-8 h-8 text-slate-400" />
                            </div>
                            <h4 class="text-base font-bold text-slate-700">Belum Ada Buku di Perpustakaan</h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                Mulai tambahkan koleksi buku baru dengan menyertakan foto sampul (cover).
                            </p>
                            <a href="/buku/create" class="inline-flex items-center space-x-2 mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-bold transition focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                                <x-icon name="Plus" class="w-3.5 h-3.5" />
                                <span>Tambah Buku Sekarang</span>
                            </a>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Modal Pratinjau Cover Buku -->
<div id="coverModal" class="fixed inset-0 z-50 hidden bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h4 id="coverModalTitle" class="font-bold text-slate-800 text-sm truncate">Sampul Buku</h4>
            <button onclick="closeCoverModal()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition focus:outline-none focus:ring-2 focus:ring-slate-400/30" aria-label="Tutup pratinjau sampul">
                <x-icon name="X" class="w-4 h-4" />
            </button>
        </div>
        <div class="p-4 bg-slate-100 flex items-center justify-center max-h-[70vh] overflow-auto">
            <img id="coverModalImg" src="" alt="Cover Buku" class="max-w-full max-h-[60vh] rounded-xl shadow-sm object-contain">
        </div>
        <div class="p-3 bg-white border-t border-slate-100 text-right">
            <button onclick="closeCoverModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-500/30">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function previewCoverModal(url, title) {
        document.getElementById('coverModalImg').src = url;
        document.getElementById('coverModalTitle').innerText = title;
        document.getElementById('coverModal').classList.remove('hidden');
    }

    function closeCoverModal() {
        document.getElementById('coverModal').classList.add('hidden');
    }

    document.getElementById('coverModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCoverModal();
        }
    });
</script>
@endsection