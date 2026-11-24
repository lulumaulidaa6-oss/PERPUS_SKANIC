@extends('layouts.siswa')

@section('title', 'Data Tagihan Denda Siswa')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

    <!-- Header Banner Compact -->
    <div class="bg-gradient-to-r from-rose-700 via-rose-800 to-slate-900 rounded-2xl sm:rounded-3xl p-5 sm:p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-2.5 py-1 bg-white/20 backdrop-blur-md rounded-full text-[11px] font-semibold text-rose-100 mb-2 border border-white/10">
                    <x-icon name="Wallet" class="w-3.5 h-3.5 text-rose-300" />
                    <span>Informasi Transparansi Denda</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Tagihan Denda Keterlambatan Siswa</h2>
                <p class="text-rose-100/90 mt-1 text-xs sm:text-sm max-w-2xl leading-relaxed">
                    Cek apakah kamu memiliki denda keterlambatan pengembalian buku atau denda kondisi buku. Denda dihitung transparan sebesar <strong>Rp {{ number_format($tarif_denda, 0, ',', '.') }} / hari</strong>.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="/siswa/disetujui" class="bg-white/15 hover:bg-white/25 text-white px-3.5 py-2 rounded-xl backdrop-blur-md border border-white/20 font-semibold text-xs sm:text-sm transition flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-white/40">
                    <span>Lihat Buku yang Dipinjam</span>
                    <x-icon name="BookOpen" class="w-3.5 h-3.5" />
                </a>
            </div>
        </div>
    </div>

    <!-- Kotak Pencarian Siswa Compact -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5 sm:p-4">
        <form action="/siswa/denda" method="GET">
            <div class="flex flex-col sm:flex-row gap-2.5">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="Search" class="w-4 h-4" />
                    </div>
                    <input type="text" 
                           name="nama" 
                           value="{{ $nama_pencarian ?? '' }}" 
                           placeholder="Ketik namamu atau kelas untuk cek apakah kamu ada denda..." 
                           class="w-full pl-9 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 focus:border-rose-500 rounded-xl text-slate-800 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-rose-500/10">
                    @if(!empty($nama_pencarian))
                        <a href="/siswa/denda" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" aria-label="Hapus kata kunci pencarian" title="Hapus kata kunci pencarian">
                            <x-icon name="X" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
                <button type="submit" class="bg-rose-600 hover:bg-rose-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-xs text-xs sm:text-sm shrink-0 flex items-center justify-center space-x-1.5 transition focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <x-icon name="Search" class="w-3.5 h-3.5" />
                    <span>Cek Denda Saya</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Ringkasan Total Tagihan & Kartu Denda Compact -->
    @if(isset($totalTagihanDenda) && $totalTagihanDenda > 0)
        <div class="p-4 sm:p-5 rounded-2xl bg-rose-50 border border-rose-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3.5 shadow-2xs">
            <div class="flex items-center space-x-3.5">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
                    <x-icon name="Wallet" class="w-5 h-5 text-rose-600" />
                </div>
                <div>
                    <h3 class="text-base font-black text-rose-900">Total Tagihan Denda Terbuka</h3>
                    <p class="text-xs text-rose-700 mt-0.5">Segera lunasi denda ke meja petugas perpustakaan saat mengembalikan buku.</p>
                </div>
            </div>
            <div class="text-left sm:text-right shrink-0">
                <span class="text-[11px] uppercase font-bold text-rose-600 block">Nominal yang Harus Dibayar:</span>
                <span class="text-2xl font-black text-rose-700">Rp {{ number_format($totalTagihanDenda, 0, ',', '.') }}</span>
            </div>
        </div>
    @endif

    <!-- Daftar Tagihan Denda Siswa Compact -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-800">
                    Daftar Buku yang Memiliki Tagihan Denda
                </h3>
                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Denda terjadi jika buku dikembalikan melewati tanggal perkiraan kembali atau terjadi kerusakan.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold shrink-0 {{ $peminjaman->count() > 0 ? 'bg-rose-100 text-rose-800 border border-rose-200' : 'bg-emerald-100 text-emerald-800 border border-emerald-200' }}">
                {{ $peminjaman->count() }} Tagihan
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-4">
            @forelse($peminjaman as $item)
            <div class="p-3.5 sm:p-4 rounded-xl border border-rose-200 bg-rose-50/20 hover:bg-rose-50/40 transition flex flex-col justify-between space-y-3">
                <div class="flex items-start space-x-3.5">
                    <!-- Foto Cover Buku (Compact) -->
                    <div class="w-14 h-20 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center shadow-2xs">
                        @if($item->book && $item->book->foto)
                            <img src="{{ asset('storage/' . $item->book->foto) }}" alt="{{ $item->book->judul }}" class="w-full h-full object-cover">
                        @else
                            <x-icon name="BookOpen" class="w-7 h-7 text-slate-300" />
                        @endif
                    </div>

                    <!-- Informasi Judul & Rincian Denda -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-300">
                                <x-icon name="Clock" class="w-3 h-3" />
                                <span>Belum Lunas</span>
                            </span>
                            @if($item->hari_terlambat > 0)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">
                                    Terlambat {{ $item->hari_terlambat }} Hari
                                </span>
                            @endif
                        </div>

                        <h4 class="font-bold text-slate-800 text-sm sm:text-base truncate" title="{{ $item->book->judul ?? 'Buku' }}">
                            {{ $item->book->judul ?? 'Judul Buku Tidak Diketahui' }}
                        </h4>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Peminjam: <strong class="text-slate-900">{{ $item->nama_siswa }}</strong> 
                            <span class="text-slate-400">({{ $item->kelas }})</span>
                        </p>
                        
                        <!-- Rincian Hitungan Denda Compact -->
                        <div class="mt-2 p-2.5 rounded-xl bg-white border border-rose-200/80 space-y-1 text-xs">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-500">Nominal Denda:</span>
                                <span class="font-extrabold text-rose-600 text-sm">{{ $item->formatted_denda }}</span>
                            </div>
                            @if($item->hari_terlambat > 0)
                                <div class="flex justify-between items-center text-[11px] text-slate-500">
                                    <span>Hitungan:</span>
                                    <span>{{ $item->hari_terlambat }} hari × Rp {{ number_format($tarif_denda, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($item->kondisi_buku_kembali && $item->kondisi_buku_kembali !== 'baik')
                                <div class="flex justify-between items-center text-[11px] text-amber-700">
                                    <span>Kondisi buku:</span>
                                    <span class="font-semibold">{{ ucfirst($item->kondisi_buku_kembali) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-2.5 border-t border-rose-200/60 flex items-center justify-between text-xs text-slate-500">
                    <span class="text-rose-700 text-[11px] font-medium flex items-center">
                        <x-icon name="Info" class="w-3.5 h-3.5 mr-1 text-rose-500 shrink-0" />
                        <span>Bayar di meja perpustakaan</span>
                    </span>
                    <span class="text-[11px] text-slate-400">ID: #{{ $item->id }}</span>
                </div>
            </div>
            @empty
            <div class="col-span-full p-8 sm:p-10 text-center">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center mx-auto mb-2.5">
                    <x-icon name="CircleCheck" class="w-6 h-6 text-emerald-600" />
                </div>
                <h4 class="text-sm sm:text-base font-bold text-slate-700">Bebas Denda! Tidak Ada Tagihan Aktif</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Kamu tidak memiliki tagihan denda keterlambatan atau semua denda sebelumnya sudah lunas. Pertahankan kedisiplinan membaca ya!
                </p>
                <div class="mt-3.5 flex flex-wrap justify-center gap-2">
                    <a href="/siswa/disetujui" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 font-semibold text-xs rounded-xl transition focus:outline-none focus:ring-2 focus:ring-emerald-400">
                        <span>Lihat Buku yang Dipinjam</span>
                        <x-icon name="BookOpen" class="w-3.5 h-3.5" />
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
