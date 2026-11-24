@extends('layouts.siswa')

@section('title', 'Menunggu Persetujuan Petugas')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

    <!-- Header Banner Compact -->
    <div class="bg-gradient-to-r from-amber-600 via-orange-600 to-slate-900 rounded-2xl sm:rounded-3xl p-5 sm:p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-2.5 py-1 bg-white/20 backdrop-blur-md rounded-full text-[11px] font-semibold text-amber-100 mb-2 border border-white/10">
                    <x-icon name="Clock" class="w-3.5 h-3.5 text-amber-300" />
                    <span>Status: Antrean Verifikasi</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Peminjaman Menunggu Persetujuan</h2>
                <p class="text-amber-100/90 mt-1 text-xs sm:text-sm max-w-2xl leading-relaxed">
                    Daftar pengajuan buku yang masih diproses dan menunggu persetujuan (ACC) dari petugas perpustakaan.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="/siswa/disetujui" class="bg-white/15 hover:bg-white/25 text-white px-3.5 py-2 rounded-xl backdrop-blur-md border border-white/20 font-semibold text-xs sm:text-sm transition flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-white/40">
                    <span>Lihat yang Sudah Di-ACC</span>
                    <x-icon name="CircleCheck" class="w-3.5 h-3.5" />
                </a>
            </div>
        </div>
    </div>

    <!-- Kotak Pencarian Siswa Compact -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5 sm:p-4">
        <form action="/siswa/menunggu" method="GET">
            <div class="flex flex-col sm:flex-row gap-2.5">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="Search" class="w-4 h-4" />
                    </div>
                    <input type="text" 
                           name="nama" 
                           value="{{ $nama_pencarian ?? '' }}" 
                           placeholder="Ketik namamu atau kelas untuk memantau status antrean..." 
                           class="w-full pl-9 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 focus:border-amber-500 rounded-xl text-slate-800 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-amber-500/10">
                    @if(!empty($nama_pencarian))
                        <a href="/siswa/menunggu" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" aria-label="Hapus kata kunci pencarian" title="Hapus kata kunci pencarian">
                            <x-icon name="X" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
                <button type="submit" class="bg-amber-600 hover:bg-amber-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-xs text-xs sm:text-sm shrink-0 flex items-center justify-center space-x-1.5 transition focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <x-icon name="Search" class="w-3.5 h-3.5" />
                    <span>Cari Antrean Saya</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Daftar Pengajuan Menunggu Compact -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-800">
                    Daftar Pengajuan Sedang Diproses
                </h3>
                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Petugas perpustakaan akan segera memverifikasi buku dan kartu pelajarmu.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200 shrink-0">
                {{ $peminjaman->count() }} Pengajuan
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-4">
            @forelse($peminjaman as $item)
            <div class="p-3.5 sm:p-4 rounded-xl border border-amber-200/80 bg-amber-50/20 hover:bg-amber-50/40 transition flex flex-col justify-between space-y-3">
                <div class="flex items-start space-x-3.5">
                    <!-- Foto Cover Buku (Compact) -->
                    <div class="w-14 h-20 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center shadow-2xs">
                        @if($item->book && $item->book->foto)
                            <img src="{{ asset('storage/' . $item->book->foto) }}" alt="{{ $item->book->judul }}" class="w-full h-full object-cover">
                        @else
                            <x-icon name="BookOpen" class="w-7 h-7 text-slate-300" />
                        @endif
                    </div>

                    <!-- Informasi Judul & Peminjam -->
                    <div class="flex-1 min-w-0">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-300 mb-1">
                            <x-icon name="Clock" class="w-3 h-3" />
                            <span>Menunggu Persetujuan</span>
                        </span>
                        <h4 class="font-bold text-slate-800 text-sm sm:text-base truncate" title="{{ $item->book->judul ?? 'Buku' }}">
                            {{ $item->book->judul ?? 'Judul Buku Tidak Diketahui' }}
                        </h4>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Peminjam: <strong class="text-slate-900">{{ $item->nama_siswa }}</strong> 
                            <span class="text-slate-400">({{ $item->kelas }})</span>
                        </p>
                        
                        <div class="mt-1.5 text-xs text-slate-500 space-y-0.5">
                            <p>Diajukan: <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y') }}</span></p>
                            <p>Rencana Kembali: <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($item->tanggal_perkiraan_kembali)->translatedFormat('d M Y') }}</span></p>
                        </div>
                    </div>
                </div>

                <div class="pt-2.5 border-t border-amber-200/60 flex items-center justify-between text-xs text-slate-500">
                    <span class="flex items-center text-amber-700 font-medium text-[11px] sm:text-xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5 animate-ping"></span>
                        Menunggu verifikasi admin
                    </span>
                    <span class="text-[11px] text-slate-400">ID: #{{ $item->id }}</span>
                </div>
            </div>
            @empty
            <div class="col-span-full p-8 sm:p-10 text-center">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center mx-auto mb-2.5">
                    <x-icon name="Clock" class="w-6 h-6 text-amber-600" />
                </div>
                <h4 class="text-sm sm:text-base font-bold text-slate-700">Tidak Ada Peminjaman yang Sedang Menunggu</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Semua pengajuanmu sudah diverifikasi atau belum ada pengajuan baru yang dikirim.
                </p>
                <div class="mt-3.5 flex flex-wrap justify-center gap-2">
                    <a href="/siswa/pinjam" class="inline-flex items-center gap-1 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs transition focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <x-icon name="Plus" class="w-3.5 h-3.5" />
                        <span>Ajukan Pinjam Buku</span>
                    </a>
                    <a href="/siswa/disetujui" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 font-semibold text-xs rounded-xl transition focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <span>Lihat Buku yang Sudah Di-ACC</span>
                        <x-icon name="CircleCheck" class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
