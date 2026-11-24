@extends('layouts.siswa')

@section('title', 'Riwayat Peminjaman & Data Denda')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="bg-gradient-to-r from-blue-700 via-indigo-700 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/4 -top-12 w-48 h-48 bg-blue-400/20 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-white/15 backdrop-blur-md rounded-full text-xs font-semibold text-blue-100 mb-3 border border-white/10">
                    <x-icon name="CircleCheck" class="w-3.5 h-3.5" />
                    <span>Layanan Mandiri Siswa</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Cek Riwayat & Data Denda</h2>
                <p class="text-blue-100/90 text-sm sm:text-base mt-2 leading-relaxed">
                    Pantau status buku yang kamu pinjam, tanggal batas pengembalian, dan rincian denda keterlambatan secara transparan dan akurat.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="/siswa/pinjam" class="inline-flex items-center space-x-2 bg-blue-500 hover:bg-blue-400 text-white font-semibold text-sm px-5 py-2.5 rounded-xl shadow-lg shadow-blue-900/30 transition duration-200 focus:outline-none focus:ring-2 focus:ring-white">
                    <x-icon name="Plus" class="w-4 h-4" />
                    <span>Pinjam Buku Baru</span>
                </a>
                <a href="/siswa/dashboard" class="inline-flex items-center space-x-2 bg-white/15 hover:bg-white/25 text-white font-semibold text-sm px-4 py-2.5 rounded-xl backdrop-blur-md border border-white/20 transition duration-200 focus:outline-none focus:ring-2 focus:ring-white">
                    <x-icon name="BookOpen" class="w-4 h-4" />
                    <span>Katalog Buku</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Kotak Pencarian Siswa (Compact & Rapi) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2.5 mb-3.5">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center">
                    <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mr-2 text-sm">
                        <x-icon name="Search" class="w-4 h-4" />
                    </span>
                    Cek Riwayat & Data Siswa
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Ketik nama lengkap siswa atau kelas yang digunakan saat pengajuan pinjaman.</p>
            </div>

            <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-[11px] font-medium shrink-0">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                <span>Terbuka Tanpa Perlu Login</span>
            </div>
        </div>

        <form action="/siswa/riwayat" method="GET" class="relative">
            <div class="flex flex-col sm:flex-row gap-2.5">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="Search" class="w-4 h-4" />
                    </div>
                    <input type="text" 
                           name="nama" 
                           value="{{ $nama_pencarian }}" 
                           placeholder="Contoh: Lulu Maulida atau 11 PPLG 3..." 
                           class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 focus:border-blue-500 rounded-xl text-slate-800 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10"
                           required>
                    @if($nama_pencarian)
                        <a href="/siswa/riwayat" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded" aria-label="Hapus kata kunci pencarian" title="Hapus Pencarian">
                            <x-icon name="X" class="w-4 h-4" />
                        </a>
                    @endif
                </div>

                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-xs hover:shadow transition duration-150 flex items-center justify-center space-x-1.5 text-xs sm:text-sm shrink-0 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <x-icon name="Search" class="w-4 h-4" />
                    <span>Cari Riwayat</span>
                </button>
            </div>
        </form>

        <!-- Informasi Ketentuan Denda & Tips Compact -->
        <div class="mt-3 p-3 rounded-xl bg-amber-50/70 border border-amber-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-xs text-amber-900">
            <div class="flex items-start space-x-2">
                <x-icon name="Info" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" />
                <div>
                    <span class="font-bold">Ketentuan Denda:</span>
                    <span class="text-amber-800 ml-1">
                        Tarif keterlambatan <strong class="underline decoration-amber-400">Rp {{ number_format($tarif_denda, 0, ',', '.') }} / hari</strong> setelah melewati jatuh tempo.
                    </span>
                </div>
            </div>
            <span class="inline-flex items-center px-2 py-0.5 rounded bg-amber-200/60 text-amber-900 font-semibold text-[10px] shrink-0">
                Penting
            </span>
        </div>
    </div>

    <!-- HASIL PENCARIAN -->
    @if($peminjaman !== null)
        @if($peminjaman->count() > 0 || $status_filter !== 'semua')
            <!-- Ringkasan Statistik Kartu (KPI) Compact -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <!-- Total Peminjaman -->
                <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/80 shadow-2xs flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <x-icon name="BookOpen" class="w-5 h-5 text-blue-600" />
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Buku</p>
                        <h4 class="text-xl sm:text-2xl font-extrabold text-slate-800">{{ $stats['total'] }}</h4>
                        <p class="text-[10px] text-slate-500">Semua pengajuan</p>
                    </div>
                </div>

                <!-- Sedang Dipinjam -->
                <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/80 shadow-2xs flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <x-icon name="ClipboardList" class="w-5 h-5 text-indigo-600" />
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Dipinjam</p>
                        <h4 class="text-xl sm:text-2xl font-extrabold text-indigo-600">{{ $stats['dipinjam'] }}</h4>
                        <p class="text-[10px] text-slate-500">
                            @if($stats['terlambat'] > 0)
                                <span class="text-rose-600 font-semibold inline-flex items-center gap-1">
                                    <x-icon name="Clock" class="w-3 h-3 text-rose-500" />
                                    <span>{{ $stats['terlambat'] }} terlambat</span>
                                </span>
                            @else
                                <span class="text-emerald-600 font-medium">Buku aktif</span>
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Menunggu Konfirmasi -->
                <div class="bg-white p-3.5 sm:p-4 rounded-xl border border-slate-200/80 shadow-2xs flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                        <x-icon name="Clock" class="w-5 h-5 text-amber-600" />
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Menunggu</p>
                        <h4 class="text-xl sm:text-2xl font-extrabold text-amber-600">{{ $stats['menunggu'] }}</h4>
                        <p class="text-[10px] text-slate-500">Antrean verifikasi</p>
                    </div>
                </div>

                <!-- Total Tagihan Denda -->
                <div class="bg-white p-3.5 sm:p-4 rounded-xl border {{ $stats['total_denda'] > 0 ? 'border-rose-300 bg-rose-50/30' : 'border-slate-200/80' }} shadow-2xs flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl {{ $stats['total_denda'] > 0 ? 'bg-rose-100 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center shrink-0">
                        <x-icon name="Wallet" class="w-5 h-5 {{ $stats['total_denda'] > 0 ? 'text-rose-600' : 'text-emerald-600' }}" />
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">Total Denda</p>
                        <h4 class="text-lg sm:text-xl font-extrabold {{ $stats['total_denda'] > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            Rp {{ number_format($stats['total_denda'], 0, ',', '.') }}
                        </h4>
                        <p class="text-[10px] {{ $stats['total_denda'] > 0 ? 'text-rose-600 font-semibold' : 'text-emerald-600 font-medium' }}">
                            {{ $stats['total_denda'] > 0 ? 'Wajib dilunasi' : 'Bebas denda' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Filter Status Tab & Judul Daftar -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
                <div class="p-4 sm:p-4.5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-800 flex items-center">
                            Daftar Peminjaman: 
                            <span class="ml-2 px-2 py-0.5 rounded-lg bg-blue-100 text-blue-800 text-xs sm:text-sm font-semibold truncate max-w-[200px]">"{{ $nama_pencarian }}"</span>
                        </h3>
                        <p class="text-[11px] text-slate-500 mt-0.5">Ditemukan {{ $stats['total'] }} riwayat peminjaman.</p>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex flex-wrap items-center gap-1 bg-slate-100 p-1 rounded-xl text-xs">
                        @php
                            $baseUrl = '/siswa/riwayat?nama=' . urlencode($nama_pencarian);
                        @endphp
                        <a href="{{ $baseUrl }}&status=semua" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $status_filter === 'semua' ? 'bg-white text-slate-800 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Semua ({{ $stats['total'] }})
                        </a>
                        <a href="{{ $baseUrl }}&status=menunggu" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $status_filter === 'menunggu' ? 'bg-white text-amber-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Menunggu ({{ $stats['menunggu'] }})
                        </a>
                        <a href="{{ $baseUrl }}&status=dipinjam" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $status_filter === 'dipinjam' ? 'bg-white text-blue-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Dipinjam ({{ $stats['dipinjam'] }})
                        </a>
                        <a href="{{ $baseUrl }}&status=menunggu_pengembalian" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $status_filter === 'menunggu_pengembalian' ? 'bg-white text-amber-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Kembali ({{ $stats['menunggu_pengembalian'] }})
                        </a>
                        <a href="{{ $baseUrl }}&status=denda" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $status_filter === 'denda' ? 'bg-white text-rose-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Denda ({{ $stats['terlambat'] > 0 ? $stats['terlambat'] : '0' }})
                        </a>
                        <a href="{{ $baseUrl }}&status=dikembalikan" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $status_filter === 'dikembalikan' ? 'bg-white text-emerald-700 shadow-2xs' : 'text-slate-600 hover:text-slate-900' }}">
                            Selesai ({{ $stats['dikembalikan'] }})
                        </a>
                    </div>
                </div>

                <!-- List Kartu Peminjaman (Compact, Rapi & Proporsional) -->
                <div class="divide-y divide-slate-100">
                    @forelse($peminjaman as $item)
                        <div class="p-3.5 sm:p-4 hover:bg-slate-50/70 transition-colors">
                            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-3.5 sm:gap-4">
                                
                                <!-- Sisi Kiri: Foto Buku & Rincian Judul Compact -->
                                <div class="flex items-start space-x-3.5 min-w-0 flex-1">
                                    <!-- Foto Buku Compact -->
                                    <div class="w-14 h-20 sm:w-16 sm:h-22 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center relative shadow-2xs">
                                        @if($item->book && $item->book->foto)
                                            <img src="{{ asset('storage/' . $item->book->foto) }}" alt="{{ $item->book->judul }}" class="w-full h-full object-cover">
                                        @else
                                            <x-icon name="BookOpen" class="w-7 h-7 text-slate-300" />
                                        @endif
                                    </div>

                                    <!-- Deskripsi Buku & Peminjam -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                            <!-- Badge Status -->
                                            @if($item->status === 'menunggu')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                                    <x-icon name="Clock" class="w-3 h-3 text-amber-700" />
                                                    <span>Menunggu Persetujuan</span>
                                                </span>
                                            @elseif($item->status === 'dipinjam')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                                    <x-icon name="BookOpen" class="w-3 h-3 text-blue-700" />
                                                    <span>Sedang Dipinjam</span>
                                                </span>
                                            @elseif($item->status === 'menunggu_pengembalian')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500 text-white shadow-2xs">
                                                    <x-icon name="Clock" class="w-3 h-3" />
                                                    <span>Menunggu Pengembalian</span>
                                                </span>
                                            @elseif($item->status === 'dikembalikan')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                    <x-icon name="CircleCheck" class="w-3 h-3 text-emerald-700" />
                                                    <span>Sudah Dikembalikan</span>
                                                </span>
                                            @elseif($item->status === 'ditolak')
                                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                                    <x-icon name="X" class="w-3 h-3 text-rose-700" />
                                                    <span>Ditolak Admin</span>
                                                </span>
                                            @endif

                                            <!-- Indikator Waktu / Terlambat -->
                                            @if($item->status === 'dipinjam')
                                                @if($item->is_terlambat)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">
                                                        <x-icon name="Clock" class="w-3 h-3" />
                                                        <span>Terlambat {{ $item->hari_terlambat }} Hari</span>
                                                    </span>
                                                @elseif($item->sisa_hari !== null)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-700">
                                                        <x-icon name="CalendarDays" class="w-3 h-3 text-slate-500" />
                                                        <span>Sisa {{ $item->sisa_hari }} hari lagi</span>
                                                    </span>
                                                @endif
                                            @endif
                                        </div>

                                        <h4 class="text-sm sm:text-base font-bold text-slate-800 truncate">
                                            {{ $item->book->judul ?? 'Judul Buku Tidak Diketahui' }}
                                        </h4>
                                        <p class="text-xs text-slate-500 mt-0.5">
                                            Penulis: <span class="font-medium text-slate-700">{{ $item->book->penulis ?? '-' }}</span> 
                                            @if($item->book && $item->book->penerbit)
                                                • Penerbit: <span class="text-slate-600">{{ $item->book->penerbit }}</span>
                                            @endif
                                        </p>

                                        <!-- Informasi Tanggal Compact -->
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mt-2 pt-2 border-t border-slate-100 text-xs">
                                            <div>
                                                <span class="text-slate-400 block text-[9.5px] uppercase font-bold">Tanggal Pinjam</span>
                                                <span class="font-semibold text-slate-700 text-[11px] sm:text-xs">
                                                    {{ $item->tanggal_pinjam ? \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y') : '-' }}
                                                </span>
                                            </div>
                                            <div>
                                                <span class="text-slate-400 block text-[9.5px] uppercase font-bold">Jatuh Tempo</span>
                                                <span class="font-semibold text-[11px] sm:text-xs {{ $item->is_terlambat && $item->status === 'dipinjam' ? 'text-rose-600' : 'text-slate-700' }}">
                                                    {{ $item->tanggal_perkiraan_kembali ? \Carbon\Carbon::parse($item->tanggal_perkiraan_kembali)->translatedFormat('d M Y') : '-' }}
                                                </span>
                                            </div>
                                            <div class="col-span-2 sm:col-span-1">
                                                <span class="text-slate-400 block text-[9.5px] uppercase font-bold">Tanggal Kembali</span>
                                                <span class="font-semibold text-slate-700 text-[11px] sm:text-xs">
                                                    {{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->translatedFormat('d M Y') : '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Sisi Kanan: Kartu Informasi Denda & Bukti Foto Compact -->
                                <div class="w-full lg:w-64 bg-slate-50/90 p-3 sm:p-3.5 rounded-xl border border-slate-200/80 flex flex-col justify-between shrink-0">
                                    <div>
                                        <div class="flex items-center justify-between mb-1.5">
                                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-500">Status Denda</span>
                                            @if($item->total_denda > 0)
                                                <span class="px-1.5 py-0.2 text-[10px] font-bold rounded bg-rose-100 text-rose-700 border border-rose-200">Ada Denda</span>
                                            @else
                                                <span class="px-1.5 py-0.2 text-[10px] font-bold rounded bg-emerald-100 text-emerald-700 border border-emerald-200">Bebas Denda</span>
                                            @endif
                                        </div>

                                        <!-- Nominal Denda -->
                                        <div class="text-lg sm:text-xl font-extrabold {{ $item->total_denda > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                            {{ $item->formatted_denda }}
                                        </div>

                                        <!-- Rincian Denda Compact -->
                                        @if($item->total_denda > 0)
                                            <div class="mt-1.5 text-xs text-rose-700 bg-rose-50 p-2 rounded-lg border border-rose-200/80 space-y-0.5 text-[11px]">
                                                @if($item->hari_terlambat > 0)
                                                    <p>• Terlambat: <strong>{{ $item->hari_terlambat }} hari</strong></p>
                                                    <p>• Tarif: Rp {{ number_format($tarif_denda, 0, ',', '.') }} / hari</p>
                                                @endif
                                                @if($item->kondisi_buku_kembali)
                                                    <p>• Kondisi: <strong>{{ ucfirst($item->kondisi_buku_kembali) }}</strong></p>
                                                @endif
                                                <p class="text-[10px] text-rose-600 italic mt-0.5 font-medium">Bayar di meja perpustakaan.</p>
                                            </div>
                                        @else
                                            <p class="text-[11px] text-slate-500 mt-1">
                                                @if($item->status === 'dikembalikan')
                                                    Dikembalikan tepat waktu bebas denda.
                                                @elseif($item->status === 'dipinjam')
                                                    Belum ada keterlambatan.
                                                @else
                                                    Tidak ada denda pada status ini.
                                                @endif
                                            </p>
                                        @endif
                                    </div>

                                    <!-- Link Bukti Foto & Peminjam Compact -->
                                    <div class="mt-2.5 pt-2 border-t border-slate-200/80 flex items-center justify-between text-xs">
                                        <span class="text-slate-500 text-[11px]">Oleh: <strong class="text-slate-700">{{ $item->nama_siswa }}</strong> ({{ $item->kelas }})</span>
                                        @if($item->bukti_foto)
                                            <button type="button" 
                                                    onclick="previewFoto('{{ asset('storage/' . $item->bukti_foto) }}', '{{ $item->book->judul ?? 'Peminjaman Buku' }}')" 
                                                    class="text-blue-600 hover:text-blue-800 font-semibold underline text-[11px] inline-flex items-center gap-1 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">
                                                <x-icon name="Eye" class="w-3.5 h-3.5 text-blue-600" />
                                                <span>Foto</span>
                                            </button>
                                        @endif
                                    </div>
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="p-8 sm:p-12 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <x-icon name="ClipboardList" class="w-7 h-7 text-slate-400" />
                            </div>
                            <h4 class="text-base font-bold text-slate-700">Tidak Ada Data Peminjaman untuk Kategori Ini</h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                Tidak ada data yang cocok dengan filter status "<strong>{{ ucfirst($status_filter) }}</strong>".
                            </p>
                            <a href="/siswa/riwayat?nama={{ urlencode($nama_pencarian) }}&status=semua" class="inline-flex items-center gap-1.5 mt-4 text-xs font-semibold text-blue-600 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">
                                <x-icon name="ArrowLeft" class="w-3.5 h-3.5" />
                                <span>Lihat Semua Status</span>
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            <!-- Data Tidak Ditemukan -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-8 sm:p-12 text-center shadow-xs">
                <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-500 border border-rose-100 flex items-center justify-center mx-auto mb-4">
                    <x-icon name="Search" class="w-8 h-8 text-rose-500" />
                </div>
                <h3 class="text-lg font-bold text-slate-800">Data Peminjaman Tidak Ditemukan</h3>
                <p class="text-sm text-slate-600 mt-1.5 max-w-md mx-auto">
                    Tidak ditemukan data peminjaman buku atas nama <strong class="text-slate-900">"{{ $nama_pencarian }}"</strong>.
                </p>
                
                <div class="mt-6 max-w-md mx-auto p-4 bg-slate-50 rounded-xl text-left border border-slate-200/70 text-xs text-slate-600 space-y-2">
                    <p class="font-bold text-slate-800 flex items-center gap-1.5">
                        <x-icon name="Info" class="w-4 h-4 text-blue-600" />
                        <span>Tips Pencarian:</span>
                    </p>
                    <p>• Pastikan penulisan nama lengkap sesuai dengan yang diketik saat meminjam buku.</p>
                    <p>• Kamu juga bisa mencoba mencari berdasarkan nama kelas (misal: <em>11 pplg 3</em>).</p>
                    <p>• Jika belum pernah meminjam buku, kamu bisa mengajukan peminjaman baru sekarang.</p>
                </div>

                <div class="mt-6 flex flex-wrap justify-center gap-3">
                    <a href="/siswa/riwayat" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition inline-flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-slate-400">
                        <x-icon name="RefreshCw" class="w-3.5 h-3.5 text-slate-500" />
                        <span>Reset Pencarian</span>
                    </a>
                    <a href="/siswa/pinjam" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-semibold text-xs hover:bg-blue-700 transition shadow-sm inline-flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <x-icon name="Plus" class="w-3.5 h-3.5" />
                        <span>Ajukan Pinjam Buku</span>
                    </a>
                </div>
            </div>
        @endif
    @else
        <!-- State Awal Saat Pengunjung Belum Melakukan Pencarian -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-8 sm:p-12 text-center shadow-xs">
            <div class="w-20 h-20 rounded-3xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center mx-auto mb-4 shadow-inner">
                <x-icon name="BookOpen" class="w-10 h-10 text-blue-600" />
            </div>
            <h3 class="text-xl font-bold text-slate-800">Mulai Cek Riwayat & Denda Kamu</h3>
            <p class="text-sm text-slate-600 mt-2 max-w-lg mx-auto">
                Ketik nama lengkapmu pada form pencarian di atas untuk melihat status buku yang sedang dipinjam, tanggal pengembalian, dan tagihan denda.
            </p>

            <!-- 3 Langkah Mudah -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-3xl mx-auto mt-8 text-left">
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 text-white font-bold text-sm flex items-center justify-center shrink-0">1</div>
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm">Ketik Nama</h5>
                        <p class="text-xs text-slate-500 mt-0.5">Masukkan nama lengkap sesuai formulir peminjaman.</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 text-white font-bold text-sm flex items-center justify-center shrink-0">2</div>
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm">Pantau Status</h5>
                        <p class="text-xs text-slate-500 mt-0.5">Lihat apakah bukumu masih menunggu, dipinjam, atau selesai.</p>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/70 flex items-start space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-600 text-white font-bold text-sm flex items-center justify-center shrink-0">3</div>
                    <div>
                        <h5 class="font-bold text-slate-800 text-sm">Cek Denda</h5>
                        <p class="text-xs text-slate-500 mt-0.5">Ketahui apakah ada denda keterlambatan per hari secara transparan.</p>
                    </div>
                </div>
            </div>

            <!-- Contoh Pencarian Cepat -->
            <div class="mt-8 pt-6 border-t border-slate-100 flex flex-wrap items-center justify-center gap-2 text-xs text-slate-500">
                <span>Coba klik contoh pencarian:</span>
                <a href="/siswa/riwayat?nama=lulu" class="px-3 py-1 bg-slate-100 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                    lulu Maul
                </a>
                <a href="/siswa/riwayat?nama=11+pplg+3" class="px-3 py-1 bg-slate-100 hover:bg-blue-50 hover:text-blue-600 rounded-lg font-medium transition focus:outline-none focus:ring-2 focus:ring-blue-500">
                    11 pplg 3
                </a>
            </div>
        </div>
    @endif

</div>

<!-- Modal Pop-up Bukti Foto -->
<div id="fotoModal" class="fixed inset-0 z-50 hidden bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h4 id="fotoModalTitle" class="font-bold text-slate-800 text-sm truncate">Bukti Foto Peminjaman</h4>
            <button onclick="closeModal()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-blue-500" aria-label="Tutup pratinjau foto">
                <x-icon name="X" class="w-4 h-4" />
            </button>
        </div>
        <div class="p-4 bg-slate-100 flex items-center justify-center max-h-[70vh] overflow-auto">
            <img id="fotoModalImg" src="" alt="Bukti Foto" class="max-w-full max-h-[60vh] rounded-lg shadow-sm object-contain">
        </div>
        <div class="p-3 bg-white border-t border-slate-100 text-right">
            <button onclick="closeModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl inline-flex items-center gap-1.5 focus:outline-none focus:ring-2 focus:ring-slate-500">
                <x-icon name="X" class="w-3.5 h-3.5" />
                <span>Tutup</span>
            </button>
        </div>
    </div>
</div>

<script>
    function previewFoto(url, title) {
        document.getElementById('fotoModalImg').src = url;
        document.getElementById('fotoModalTitle').innerText = 'Bukti: ' + title;
        document.getElementById('fotoModal').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('fotoModal').classList.add('hidden');
    }

    // Tutup modal jika klik di luar box
    document.getElementById('fotoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeModal();
        }
    });
</script>
@endsection