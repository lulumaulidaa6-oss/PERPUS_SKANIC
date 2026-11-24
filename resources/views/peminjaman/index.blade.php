@extends('layouts.app')

@section('title', 'Data Peminjaman & Denda Siswa')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header & Action -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/4 -top-12 w-48 h-48 bg-indigo-500/20 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-blue-200 mb-3 border border-white/10">
                    <x-icon name="ClipboardList" class="w-3.5 h-3.5 text-blue-400" />
                    <span>Panel Petugas Perpustakaan</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Kelola Peminjaman & Denda Siswa</h2>
                <p class="text-slate-300 text-sm sm:text-base mt-2 leading-relaxed">
                    Kelola persetujuan pinjaman, pantau masa pengembalian buku, dan catat pelunasan denda keterlambatan siswa secara transparan.
                </p>
            </div>

            <div class="flex flex-wrap gap-2.5">
                <a href="{{ route('peminjaman.export-excel', request()->all()) }}" class="inline-flex items-center space-x-2 bg-emerald-600 hover:bg-emerald-500 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-900/30 transition duration-200 focus:outline-none focus:ring-2 focus:ring-emerald-400" title="Unduh Rekap Excel Sesuai Filter">
                    <x-icon name="FileSpreadsheet" class="w-4 h-4" />
                    <span>Export Excel</span>
                </a>
                <a href="/peminjaman/create" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm px-4 py-2.5 rounded-xl shadow-lg shadow-blue-900/30 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <x-icon name="Plus" class="w-4 h-4" />
                    <span>Catat Pinjam Manual</span>
                </a>
                <a href="/buku" class="inline-flex items-center space-x-2 bg-white/15 hover:bg-white/25 text-white font-semibold text-sm px-3.5 py-2.5 rounded-xl backdrop-blur-md border border-white/20 transition duration-200 focus:outline-none focus:ring-2 focus:ring-white/40">
                    <x-icon name="BookOpen" class="w-4 h-4" />
                    <span>Katalog Buku</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Ringkasan Statistik Kartu (KPI) Termasuk Denda Siswa -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Peminjaman -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <x-icon name="ClipboardList" class="w-6 h-6 text-blue-600" />
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Transaksi</p>
                <h4 class="text-2xl font-extrabold text-slate-800">{{ $stats['total'] }}</h4>
                <p class="text-[11px] text-slate-500 mt-0.5">{{ $stats['dikembalikan'] }} sudah kembali</p>
            </div>
        </div>

        <!-- Sedang Dipinjam -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                <x-icon name="BookOpen" class="w-6 h-6 text-indigo-600" />
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Sedang Dipinjam</p>
                <h4 class="text-2xl font-extrabold text-indigo-600">{{ $stats['dipinjam'] }}</h4>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    @if($stats['terlambat'] > 0)
                        <span class="inline-flex items-center gap-1 text-rose-600 font-bold"><x-icon name="Clock" class="w-3.5 h-3.5 shrink-0" /> {{ $stats['terlambat'] }} terlambat tempo</span>
                    @else
                        <span class="text-emerald-600 font-medium">Semua tepat waktu</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Menunggu Persetujuan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <x-icon name="Clock" class="w-6 h-6 text-amber-600" />
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Menunggu</p>
                <h4 class="text-2xl font-extrabold text-amber-600">{{ $stats['menunggu'] }}</h4>
                <p class="text-[11px] text-slate-500 mt-0.5">Perlu diverifikasi admin</p>
            </div>
        </div>

        <!-- Total Denda Siswa -->
        <div class="bg-white p-5 rounded-2xl border {{ $stats['denda_belum_dibayar'] > 0 ? 'border-rose-300 bg-rose-50/20' : 'border-slate-200/80' }} shadow-xs flex items-center space-x-4">
            <div class="w-12 h-12 rounded-xl {{ $stats['denda_belum_dibayar'] > 0 ? 'bg-rose-100 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center shrink-0">
                <x-icon name="Wallet" class="w-6 h-6 {{ $stats['denda_belum_dibayar'] > 0 ? 'text-rose-600' : 'text-emerald-600' }}" />
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Denda Belum Lunas</p>
                <h4 class="text-xl sm:text-2xl font-extrabold {{ $stats['denda_belum_dibayar'] > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    Rp {{ number_format($stats['denda_belum_dibayar'], 0, ',', '.') }}
                </h4>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    @if($stats['transaksi_denda_aktif'] > 0)
                        <span class="text-rose-600 font-semibold">{{ $stats['transaksi_denda_aktif'] }} siswa menunggak</span>
                    @else
                        <span class="text-emerald-600 font-medium">Semua denda lunas</span>
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- PANEL PENCARIAN, FILTER & SORTING -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 sm:p-6">
        <form action="/peminjaman" method="GET" id="filterForm">
            <div class="space-y-4">
                
                <!-- Baris 1: Kolom Pencarian Utama -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5 flex items-center justify-between">
                        <span>Pencarian Cepat</span>
                        <span class="text-[11px] font-normal text-slate-400">Cari berdasarkan Nama Siswa, Kelas, atau Judul Buku</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <x-icon name="Search" class="w-5 h-5" />
                        </div>
                        <input type="text" 
                               name="search" 
                               value="{{ $search }}" 
                               placeholder="Ketik nama siswa (contoh: lulu Maul) atau judul buku (contoh: Laskar Pelangi)..." 
                               class="w-full pl-10 pr-10 py-3 bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 focus:border-blue-500 rounded-xl text-slate-800 text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                        @if($search)
                            <a href="/peminjaman?{{ http_build_query(request()->except(['search', 'page'])) }}" 
                               class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" 
                               aria-label="Hapus kata kunci pencarian"
                               title="Hapus kata kunci pencarian">
                                <x-icon name="X" class="w-4 h-4" />
                            </a>
                        @endif
                    </div>
                </div>

                <!-- Baris 2: Filter Status, Rentang Waktu & Sorting Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
                    
                    <!-- Filter Status Dropdown -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Status Transaksi
                        </label>
                        <select name="status" class="w-full py-2.5 px-3 bg-slate-50 hover:bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10">
                            <option value="semua" {{ $status === 'semua' ? 'selected' : '' }}>Semua Status ({{ $stats['total'] }})</option>
                            <option value="menunggu" {{ $status === 'menunggu' ? 'selected' : '' }}>Menunggu Persetujuan ({{ $stats['menunggu'] }})</option>
                            <option value="dipinjam" {{ $status === 'dipinjam' ? 'selected' : '' }}>Sedang Dipinjam ({{ $stats['dipinjam'] }})</option>
                            <option value="menunggu_pengembalian" {{ $status === 'menunggu_pengembalian' ? 'selected' : '' }}>Menunggu Pengembalian ({{ $stats['menunggu_pengembalian'] }})</option>
                            <option value="denda" {{ $status === 'denda' ? 'selected' : '' }}>Ada Denda / Terlambat ({{ $stats['transaksi_denda_aktif'] + $stats['terlambat'] }})</option>
                            <option value="dikembalikan" {{ $status === 'dikembalikan' ? 'selected' : '' }}>Sudah Dikembalikan ({{ $stats['dikembalikan'] }})</option>
                            <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak ({{ $stats['ditolak'] }})</option>
                        </select>
                    </div>

                    <!-- Tanggal Awal -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Tanggal Pinjam Awal
                        </label>
                        <input type="date" 
                               name="tanggal_awal" 
                               value="{{ $tanggal_awal }}" 
                               class="w-full py-2 px-3 bg-slate-50 hover:bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10">
                    </div>

                    <!-- Tanggal Akhir -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Tanggal Pinjam Akhir
                        </label>
                        <input type="date" 
                               name="tanggal_akhir" 
                               value="{{ $tanggal_akhir }}" 
                               class="w-full py-2 px-3 bg-slate-50 hover:bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10">
                    </div>

                    <!-- Sorting Dropdown -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">
                            Urutan (Sorting)
                        </label>
                        <select name="sort" class="w-full py-2.5 px-3 bg-slate-50 hover:bg-white border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-700 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/10">
                            <option value="terbaru" {{ $sort === 'terbaru' ? 'selected' : '' }}>Terkini (Terbaru)</option>
                            <option value="terlama" {{ $sort === 'terlama' ? 'selected' : '' }}>Terlama</option>
                            <option value="denda_tertinggi" {{ $sort === 'denda_tertinggi' ? 'selected' : '' }}>Denda Tertinggi</option>
                            <option value="jatuh_tempo" {{ $sort === 'jatuh_tempo' ? 'selected' : '' }}>Jatuh Tempo Terdekat</option>
                            <option value="nama_asc" {{ $sort === 'nama_asc' ? 'selected' : '' }}>Nama Siswa (A - Z)</option>
                            <option value="nama_desc" {{ $sort === 'nama_desc' ? 'selected' : '' }}>Nama Siswa (Z - A)</option>
                        </select>
                    </div>

                </div>

                <!-- Baris 3: Tombol Aksi Submit & Reset -->
                <div class="pt-2 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100">
                    <div class="flex items-center space-x-2 text-xs text-slate-500">
                        <span>Tarif Denda Standar: <strong class="text-slate-700">Rp {{ number_format($tarif_denda, 0, ',', '.') }} / hari keterlambatan</strong></span>
                    </div>

                    <div class="flex flex-wrap items-center gap-2">
                        @if($search || $status !== 'semua' || $tanggal_awal || $tanggal_akhir || $sort !== 'terbaru')
                            <a href="/peminjaman" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 hover:text-slate-800 hover:bg-slate-100 font-semibold text-xs transition duration-150 inline-flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-slate-400">
                                <x-icon name="RefreshCw" class="w-3.5 h-3.5" />
                                <span>Reset Filter</span>
                            </a>
                        @endif

                        <a href="{{ route('peminjaman.export-excel', request()->all()) }}" 
                           class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl shadow-xs hover:shadow transition duration-150 text-xs sm:text-sm inline-flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                           title="Unduh Rekap Excel Sesuai Filter Saat Ini">
                            <x-icon name="FileSpreadsheet" class="w-4 h-4" />
                            <span>Export Excel</span>
                        </a>

                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 py-2 rounded-xl shadow-sm hover:shadow transition duration-150 text-xs sm:text-sm inline-flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <x-icon name="Filter" class="w-4 h-4" />
                            <span>Terapkan Filter</span>
                        </button>
                    </div>
                </div>

            </div>
        </form>

        <!-- Badge Filter Aktif -->
        @if($search || $status !== 'semua' || $tanggal_awal || $tanggal_akhir)
            <div class="mt-4 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-2 text-xs">
                <span class="font-semibold text-slate-500">Filter Aktif:</span>
                @if($search)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                        Cari: "{{ $search }}"
                        <a href="/peminjaman?{{ http_build_query(request()->except(['search', 'page'])) }}" class="ml-1.5 text-blue-400 hover:text-blue-800">×</a>
                    </span>
                @endif
                @if($status !== 'semua')
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-200">
                        Status: {{ ucfirst($status) }}
                        <a href="/peminjaman?{{ http_build_query(request()->except(['status', 'page'])) }}" class="ml-1.5 text-indigo-400 hover:text-indigo-800">×</a>
                    </span>
                @endif
                @if($tanggal_awal || $tanggal_akhir)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 border border-amber-200">
                        Periode: {{ $tanggal_awal ?: '...' }} s/d {{ $tanggal_akhir ?: '...' }}
                        <a href="/peminjaman?{{ http_build_query(request()->except(['tanggal_awal', 'tanggal_akhir', 'page'])) }}" class="ml-1.5 text-amber-400 hover:text-amber-800">×</a>
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- TABEL DATA PEMINJAMAN & DENDA SISWA -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-slate-800 flex items-center">
                    <span>Daftar Transaksi Peminjaman</span>
                    <span class="ml-2.5 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-600">
                        Total: {{ $peminjaman->total() }} Data
                    </span>
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Kelola konfirmasi pinjam, status pengembalian, dan tagihan denda siswa.</p>
            </div>

            <div class="text-xs text-slate-500">
                Menampilkan <strong class="text-slate-800">{{ $peminjaman->firstItem() ?? 0 }} - {{ $peminjaman->lastItem() ?? 0 }}</strong> dari {{ $peminjaman->total() }} entri
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-600 text-[11px] uppercase tracking-wider font-bold border-b border-slate-200">
                        <th class="p-3.5 pl-5">Siswa (Peminjam)</th>
                        <th class="p-3.5">Buku & Jadwal Pinjam</th>
                        <th class="p-3.5 text-center">Bukti Foto</th>
                        <th class="p-3.5">Status Pinjam</th>
                        <th class="p-3.5">Denda Siswa</th>
                        <th class="p-3.5 pr-5 text-right">Aksi & Manajemen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($peminjaman as $pinjam)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        
                        <!-- 1. Kolom Siswa (Peminjam) -->
                        <td class="p-3.5 pl-5">
                            <div class="flex items-center space-x-3">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-xs">
                                    {{ strtoupper(substr($pinjam->nama_siswa ?? 'S', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h5 class="font-bold text-slate-800 truncate">
                                        {{ $pinjam->nama_siswa ?? ($pinjam->user->name ?? 'Siswa') }}
                                    </h5>
                                    <div class="flex items-center gap-1.5 mt-0.5">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-600 border border-slate-200/70">
                                            Kls: {{ $pinjam->kelas ?? '-' }}
                                        </span>
                                        @if($pinjam->user_id)
                                            <span class="text-[10px] text-blue-600 font-medium">Akun Siswa</span>
                                        @else
                                            <span class="text-[10px] text-slate-400">Mandiri</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- 2. Kolom Buku & Jadwal -->
                        <td class="p-3.5">
                            <div class="max-w-xs">
                                <p class="font-semibold text-slate-800 truncate" title="{{ $pinjam->book->judul ?? 'Buku' }}">
                                    {{ $pinjam->book->judul ?? 'Buku Tidak Ditemukan' }}
                                </p>
                                <div class="text-xs text-slate-500 mt-1 space-y-0.5">
                                    <div class="flex items-center space-x-1">
                                        <span class="text-slate-400">Stok:</span>
                                        @if(($pinjam->book?->stok ?? 0) > 0)
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                {{ $pinjam->book->stok }} Tersedia
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-1.5 py-0.2 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                Habis (0)
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <span class="text-slate-400">Pinjam:</span>
                                        <span class="font-medium text-slate-700">{{ $pinjam->tanggal_pinjam ? \Carbon\Carbon::parse($pinjam->tanggal_pinjam)->translatedFormat('d M Y') : '-' }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <span class="text-slate-400">Tempo:</span>
                                        <span class="font-medium {{ $pinjam->is_terlambat && $pinjam->status === 'dipinjam' ? 'text-rose-600 font-bold' : 'text-slate-700' }}">
                                            {{ $pinjam->tanggal_perkiraan_kembali ? \Carbon\Carbon::parse($pinjam->tanggal_perkiraan_kembali)->translatedFormat('d M Y') : '-' }}
                                        </span>
                                    </div>
                                    @if($pinjam->tanggal_kembali)
                                        <div class="flex items-center space-x-1">
                                            <span class="text-slate-400">Kembali:</span>
                                            <span class="font-medium text-emerald-700">{{ \Carbon\Carbon::parse($pinjam->tanggal_kembali)->translatedFormat('d M Y') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <!-- 3. Kolom Bukti Foto -->
                        <td class="p-3.5 text-center">
                            <div class="flex flex-col items-center justify-center gap-1.5">
                                @if($pinjam->foto_kembali)
                                    <button type="button" 
                                            onclick="previewFoto('{{ asset('storage/' . $pinjam->foto_kembali) }}', 'Foto Kondisi Buku Saat Dikembalikan - {{ addslashes($pinjam->nama_siswa) }}')" 
                                            class="inline-flex items-center space-x-1 text-[11px] font-bold px-2.5 py-1 rounded-lg bg-amber-50 text-amber-800 border border-amber-300 hover:bg-amber-100 transition shadow-2xs focus:outline-none focus:ring-2 focus:ring-amber-400"
                                            title="Foto Bukti Kondisi Buku yang Diunggah Siswa">
                                        <x-icon name="Eye" class="w-3.5 h-3.5 text-amber-600" />
                                        <span>Kondisi Kembali</span>
                                    </button>
                                @endif

                                @if($pinjam->bukti_foto)
                                    <button type="button" 
                                            onclick="previewFoto('{{ asset('storage/' . $pinjam->bukti_foto) }}', 'Foto Pinjam - {{ addslashes($pinjam->nama_siswa) }}')" 
                                            class="inline-flex items-center space-x-1 text-[11px] font-semibold px-2 py-0.5 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hover:bg-blue-100 transition focus:outline-none focus:ring-2 focus:ring-blue-400"
                                            title="Foto Bukti Peminjaman Awal">
                                        <x-icon name="Eye" class="w-3.5 h-3.5 text-blue-500" />
                                        <span>Foto Pinjam</span>
                                    </button>
                                @endif

                                @if(!$pinjam->foto_kembali && !$pinjam->bukti_foto)
                                    <span class="text-slate-300 text-xs">-</span>
                                @endif
                            </div>
                        </td>

                        <!-- 4. Kolom Status Pinjam -->
                        <td class="p-3.5">
                            @if($pinjam->status == 'menunggu')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    <x-icon name="Clock" class="w-3.5 h-3.5 mr-1" />
                                    <span>Menunggu Persetujuan</span>
                                </span>
                            @elseif($pinjam->status == 'dipinjam')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                    <x-icon name="BookOpen" class="w-3.5 h-3.5 mr-1" />
                                    <span>Dipinjam</span>
                                </span>
                                @if($pinjam->is_terlambat)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white animate-pulse">
                                            <x-icon name="Clock" class="w-3 h-3 mr-1" />
                                            <span>Lewat {{ $pinjam->hari_terlambat }} Hari</span>
                                        </span>
                                    </div>
                                @elseif($pinjam->sisa_hari !== null)
                                    <div class="mt-1 text-[11px] text-slate-500 font-medium">
                                        Sisa {{ $pinjam->sisa_hari }} hari
                                    </div>
                                @endif
                                @if($pinjam->diproses_oleh)
                                    <p class="text-[10px] text-slate-400 mt-0.5">
                                        ACC: {{ $pinjam->diproses_oleh }}
                                    </p>
                                @endif
                            @elseif($pinjam->status == 'menunggu_pengembalian')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-amber-500 text-white shadow-xs animate-pulse">
                                    <x-icon name="Clock" class="w-3.5 h-3.5 mr-1" />
                                    <span>Menunggu Pengembalian</span>
                                </span>
                                @if($pinjam->is_terlambat)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white">
                                            <x-icon name="Clock" class="w-3 h-3 mr-1" />
                                            <span>Lewat {{ $pinjam->hari_terlambat }} Hari</span>
                                        </span>
                                    </div>
                                @endif
                                @if($pinjam->tanggal_pengajuan_kembali)
                                    <div class="mt-1 text-[11px] text-slate-500 font-medium">
                                        Diajukan: {{ \Carbon\Carbon::parse($pinjam->tanggal_pengajuan_kembali)->translatedFormat('d M Y') }}
                                    </div>
                                @endif
                            @elseif($pinjam->status == 'ditolak')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    <x-icon name="X" class="w-3.5 h-3.5 mr-1" />
                                    <span>Ditolak</span>
                                </span>
                                @if($pinjam->alasan_penolakan)
                                    <div class="mt-1 max-w-xs">
                                        <p class="text-[11px] text-rose-700 bg-rose-50 px-2 py-0.5 rounded border border-rose-200/60 leading-tight" title="{{ $pinjam->alasan_penolakan }}">
                                            Alasan: {{ $pinjam->alasan_penolakan }}
                                        </p>
                                    </div>
                                @endif
                                @if($pinjam->diproses_oleh)
                                    <p class="text-[10px] text-slate-400 mt-0.5">
                                        Oleh: {{ $pinjam->diproses_oleh }}
                                    </p>
                                @endif
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <x-icon name="CircleCheck" class="w-3.5 h-3.5 mr-1" />
                                    <span>Dikembalikan</span>
                                </span>
                                @if($pinjam->kondisi_buku_kembali && $pinjam->kondisi_buku_kembali !== 'baik')
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-semibold bg-amber-100 text-amber-800 border border-amber-200">
                                            Kondisi: {{ ucfirst($pinjam->kondisi_buku_kembali) }}
                                        </span>
                                    </div>
                                @endif
                            @endif
                        </td>

                        <!-- 5. Kolom Denda Siswa -->
                        <td class="p-3.5">
                            <div>
                                <!-- Nominal Denda -->
                                <div class="font-extrabold text-sm {{ $pinjam->total_denda > 0 ? 'text-rose-600' : 'text-slate-600' }}">
                                    {{ $pinjam->formatted_denda }}
                                </div>

                                <!-- Status Pembayaran Denda -->
                                <div class="mt-1 flex flex-wrap items-center gap-1">
                                    @if($pinjam->total_denda > 0)
                                        @if($pinjam->is_denda_lunas)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                <x-icon name="CircleCheck" class="w-3 h-3 mr-1" />
                                                <span>LUNAS</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                                <x-icon name="Clock" class="w-3 h-3 mr-1" />
                                                <span>BELUM DIBAYAR</span>
                                            </span>
                                        @endif

                                        @if($pinjam->hari_terlambat > 0)
                                            <span class="text-[10px] text-slate-500">
                                                ({{ $pinjam->hari_terlambat }} hr)
                                            </span>
                                        @endif
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500">
                                            Bebas Denda
                                        </span>
                                    @endif
                                </div>

                                <!-- Tombol Aksi Cepat Denda: Tandai Lunas -->
                                @if($pinjam->total_denda > 0 && !$pinjam->is_denda_lunas)
                                    <div class="mt-1.5 flex items-center space-x-1.5">
                                        <form action="/peminjaman/{{ $pinjam->id }}/bayar-denda" method="POST" onsubmit="return confirm('Tandai denda {{ $pinjam->formatted_denda }} untuk {{ addslashes($pinjam->nama_siswa) }} sebagai LUNAS?');">
                                            @csrf @method('PUT')
                                            <button type="submit" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-800 hover:underline inline-flex items-center focus:outline-none focus:ring-1 focus:ring-emerald-500 rounded">
                                                <x-icon name="CircleCheck" class="w-3.5 h-3.5 mr-1" />
                                                <span>Tandai Lunas</span>
                                            </button>
                                        </form>
                                        <span class="text-slate-300">•</span>
                                        <button type="button" 
                                                onclick="openEditDendaModal({{ $pinjam->id }}, '{{ addslashes($pinjam->nama_siswa) }}', {{ (int)$pinjam->total_denda }}, '{{ $pinjam->status_denda }}', '{{ $pinjam->kondisi_buku_kembali ?? 'baik' }}')"
                                                class="text-[11px] text-blue-600 hover:text-blue-800 hover:underline focus:outline-none focus:ring-1 focus:ring-blue-500 rounded">
                                            Atur Denda
                                        </button>
                                    </div>
                                @elseif($pinjam->total_denda > 0 && $pinjam->is_denda_lunas)
                                    <div class="mt-1">
                                        <button type="button" 
                                                onclick="openEditDendaModal({{ $pinjam->id }}, '{{ addslashes($pinjam->nama_siswa) }}', {{ (int)$pinjam->total_denda }}, '{{ $pinjam->status_denda }}', '{{ $pinjam->kondisi_buku_kembali ?? 'baik' }}')"
                                                class="text-[11px] text-slate-500 hover:text-slate-700 hover:underline focus:outline-none focus:ring-1 focus:ring-slate-400 rounded">
                                            Ubah Denda
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </td>

                        <!-- 6. Kolom Aksi & Manajemen -->
                        <td class="p-3.5 pr-5 text-right">
                            <div class="flex items-center justify-end space-x-1.5">
                                
                                <!-- Jika status Menunggu: Setujui & Tolak Bermodal -->
                                @if($pinjam->status == 'menunggu')
                                    @if(($pinjam->book?->stok ?? 0) > 0)
                                        <form action="/peminjaman/{{ $pinjam->id }}/setujui" method="POST" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
                                            @csrf @method('PUT')
                                            <button type="submit" 
                                                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-xs hover:shadow transition inline-flex items-center space-x-1 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                                    title="Setujui Pengajuan Pinjaman (ACC)">
                                                <x-icon name="CircleCheck" class="w-3.5 h-3.5" />
                                                <span>Setujui</span>
                                            </button>
                                        </form>
                                    @else
                                        <button type="button" 
                                                disabled 
                                                class="bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed px-2.5 py-1.5 rounded-lg text-xs font-semibold inline-flex items-center space-x-1"
                                                title="Stok buku habis (0), tidak dapat di-ACC">
                                            <x-icon name="X" class="w-3.5 h-3.5 text-slate-400" />
                                            <span>Stok Habis</span>
                                        </button>
                                    @endif

                                    <button type="button" 
                                            onclick="openTolakModal({{ $pinjam->id }}, '{{ addslashes($pinjam->nama_siswa ?? 'Siswa') }}', '{{ addslashes($pinjam->book->judul ?? 'Buku') }}')"
                                            class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition inline-flex items-center space-x-1 focus:outline-none focus:ring-2 focus:ring-rose-400"
                                            title="Tolak Pengajuan & Isi Alasan">
                                        <x-icon name="X" class="w-3.5 h-3.5 text-rose-500" />
                                        <span>Tolak</span>
                                    </button>

                                <!-- Jika status Menunggu Pengembalian: Periksa & Konfirmasi, serta tombol Hapus -->
                                @elseif($pinjam->status == 'menunggu_pengembalian')
                                    <button type="button" 
                                            onclick="openPengembalianModal({{ $pinjam->id }}, '{{ addslashes($pinjam->nama_siswa) }}', '{{ addslashes($pinjam->book->judul ?? 'Buku') }}', {{ $pinjam->hari_terlambat }}, {{ $pinjam->total_denda }}, {{ $tarif_denda }}, '{{ $pinjam->foto_kembali ? asset('storage/' . $pinjam->foto_kembali) : '' }}', '{{ addslashes($pinjam->catatan_kembali ?? '') }}', '{{ $pinjam->tanggal_pengajuan_kembali ? \Carbon\Carbon::parse($pinjam->tanggal_pengajuan_kembali)->translatedFormat('d M Y') : '' }}')"
                                            class="bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-xs hover:shadow transition inline-flex items-center space-x-1 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                        <x-icon name="CircleCheck" class="w-3.5 h-3.5" />
                                        <span>Periksa</span>
                                    </button>

                                    <button type="button" 
                                            onclick="openHapusModal({{ $pinjam->id }}, '{{ addslashes($pinjam->nama_siswa ?? 'Siswa') }}', '{{ addslashes($pinjam->book->judul ?? 'Buku') }}', 'Menunggu Pengembalian')"
                                            class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition inline-flex items-center space-x-1 focus:outline-none focus:ring-2 focus:ring-rose-400"
                                            title="Hapus data dari daftar & rekap Excel">
                                        <x-icon name="Trash2" class="w-3.5 h-3.5 text-rose-500" />
                                        <span>Hapus</span>
                                    </button>

                                <!-- Jika status Dipinjam (ACC): Tombol Terima Buku & Tombol Hapus -->
                                @elseif($pinjam->status == 'dipinjam')
                                    <button type="button" 
                                            onclick="openPengembalianModal({{ $pinjam->id }}, '{{ addslashes($pinjam->nama_siswa) }}', '{{ addslashes($pinjam->book->judul ?? 'Buku') }}', {{ $pinjam->hari_terlambat }}, {{ $pinjam->total_denda }}, {{ $tarif_denda }}, '', '', '')"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold shadow-xs hover:shadow transition inline-flex items-center space-x-1 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                        <x-icon name="CircleCheck" class="w-3.5 h-3.5" />
                                        <span>Terima</span>
                                    </button>

                                    <button type="button" 
                                            onclick="openHapusModal({{ $pinjam->id }}, '{{ addslashes($pinjam->nama_siswa ?? 'Siswa') }}', '{{ addslashes($pinjam->book->judul ?? 'Buku') }}', 'Dipinjam (ACC)')"
                                            class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition inline-flex items-center space-x-1 focus:outline-none focus:ring-2 focus:ring-rose-400"
                                            title="Hapus data peminjaman dari daftar & rekap Excel">
                                        <x-icon name="Trash2" class="w-3.5 h-3.5 text-rose-500" />
                                        <span>Hapus</span>
                                    </button>

                                <!-- Jika status Ditolak: Tombol Hapus -->
                                @elseif($pinjam->status == 'ditolak')
                                    <button type="button" 
                                            onclick="openHapusModal({{ $pinjam->id }}, '{{ addslashes($pinjam->nama_siswa ?? 'Siswa') }}', '{{ addslashes($pinjam->book->judul ?? 'Buku') }}', 'Ditolak')"
                                            class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-3 py-1.5 rounded-lg text-xs font-semibold transition inline-flex items-center space-x-1 focus:outline-none focus:ring-2 focus:ring-rose-400"
                                            title="Hapus pengajuan ditolak dari daftar & rekap Excel">
                                        <x-icon name="Trash2" class="w-3.5 h-3.5 text-rose-500" />
                                        <span>Hapus</span>
                                    </button>

                                <!-- Jika Sudah Selesai (Dikembalikan): Kelola Denda & Tombol Hapus -->
                                @else
                                    <button type="button" 
                                            onclick="openEditDendaModal({{ $pinjam->id }}, '{{ addslashes($pinjam->nama_siswa) }}', {{ (int)$pinjam->total_denda }}, '{{ $pinjam->status_denda }}', '{{ $pinjam->kondisi_buku_kembali ?? 'baik' }}')"
                                            class="text-slate-500 hover:text-slate-800 p-1.5 rounded-lg hover:bg-slate-100 transition focus:outline-none focus:ring-2 focus:ring-slate-400" 
                                            aria-label="Kelola Data Denda Siswa"
                                            title="Kelola Data Denda Siswa">
                                        <x-icon name="Settings" class="w-4 h-4" />
                                    </button>

                                    <button type="button" 
                                            onclick="openHapusModal({{ $pinjam->id }}, '{{ addslashes($pinjam->nama_siswa ?? 'Siswa') }}', '{{ addslashes($pinjam->book->judul ?? 'Buku') }}', 'Dikembalikan')"
                                            class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 px-2.5 py-1.5 rounded-lg text-xs font-semibold transition inline-flex items-center space-x-1 focus:outline-none focus:ring-2 focus:ring-rose-400"
                                            title="Hapus data arsip dari daftar & rekap Excel">
                                        <x-icon name="Trash2" class="w-3.5 h-3.5 text-rose-500" />
                                        <span>Hapus</span>
                                    </button>
                                @endif

                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center">
                            <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <x-icon name="Search" class="w-8 h-8 text-slate-400" />
                            </div>
                            <h4 class="text-base font-bold text-slate-700">Tidak Ada Data Peminjaman yang Cocok</h4>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                Coba ubah kata kunci pencarian atau sesuaikan filter status dan rentang tanggal di atas.
                            </p>
                            @if($search || $status !== 'semua' || $tanggal_awal || $tanggal_akhir)
                                <a href="/peminjaman" class="inline-flex items-center gap-1 mt-4 text-xs font-semibold text-blue-600 hover:underline">
                                    <x-icon name="ArrowLeft" class="w-3.5 h-3.5" />
                                    <span>Reset Semua Filter</span>
                                </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($peminjaman->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $peminjaman->links() }}
            </div>
        @endif
    </div>

</div>

<!-- 1. MODAL PENGEMBALIAN BUKU & KALKULASI DENDA (Compact, Rapi & Proporsional) -->
<div id="modalPengembalian" class="fixed inset-0 z-50 hidden bg-slate-900/75 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl max-w-md w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200 max-h-[92vh] flex flex-col">
        <!-- Header Modal Compact -->
        <div class="p-3.5 sm:p-4 bg-gradient-to-r from-indigo-700 to-blue-600 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shadow-xs shrink-0">
                    <x-icon name="Download" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <h4 class="font-bold text-sm sm:text-base">Terima Pengembalian Buku</h4>
                    <p class="text-[11px] text-blue-100">Konfirmasi pengembalian dan kalkulasi denda</p>
                </div>
            </div>
            <button onclick="closePengembalianModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition focus:outline-none focus:ring-2 focus:ring-white/40" aria-label="Tutup modal pengembalian">
                <x-icon name="X" class="w-4 h-4" />
            </button>
        </div>

        <form id="formPengembalian" method="POST" action="" onsubmit="this.querySelector('button[type=submit]').disabled = true;" class="overflow-y-auto flex-1 flex flex-col justify-between scrollbar-thin">
            @csrf
            @method('PUT')

            <div class="p-4 sm:p-4.5 space-y-3">
                
                <!-- Info Peminjam & Buku Compact -->
                <div class="p-2.5 sm:p-3 bg-slate-50 rounded-xl border border-slate-200/80 space-y-0.5 text-xs">
                    <p class="text-slate-500">Peminjam: <strong id="kembaliNamaSiswa" class="text-slate-800">-</strong></p>
                    <p class="text-slate-500">Buku: <strong id="kembaliJudulBuku" class="text-slate-800">-</strong></p>
                </div>

                <!-- Foto & Catatan Pengembalian yang Diunggah Siswa Compact -->
                <div id="kembaliBoxBuktiSiswa" class="hidden p-2.5 sm:p-3 bg-amber-50 rounded-xl border border-amber-200/90 space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-amber-900 flex items-center">
                            <x-icon name="Eye" class="w-3.5 h-3.5 text-amber-700 mr-1.5 shrink-0" />
                            <span>Foto Kondisi Buku dari Siswa:</span>
                        </span>
                        <span id="kembaliTglPengajuan" class="text-[10px] font-medium text-amber-700"></span>
                    </div>
                    <div class="flex items-start space-x-3">
                        <img id="kembaliImgBukti" src="" alt="Bukti Siswa" class="w-14 h-14 sm:w-16 sm:h-16 object-cover rounded-lg border border-amber-300 shadow-2xs cursor-pointer hover:opacity-90 shrink-0" onclick="previewFoto(this.src, 'Bukti Kondisi Buku Saat Dikembalikan')">
                        <div class="flex-1 min-w-0 text-xs">
                            <p class="text-slate-500 font-medium text-[11px]">Catatan Kondisi dari Siswa:</p>
                            <p id="kembaliCatatanSiswa" class="font-medium text-slate-800 bg-white/90 p-1.5 rounded-lg border border-amber-200/60 mt-0.5 text-[11px] italic">-</p>
                            <button type="button" onclick="previewFoto(document.getElementById('kembaliImgBukti').src, 'Bukti Kondisi Buku Saat Dikembalikan')" class="mt-1 text-[11px] text-blue-600 font-bold hover:underline inline-flex items-center gap-1 focus:outline-none">
                                <span>Perbesar Foto</span>
                                <x-icon name="ExternalLink" class="w-3 h-3" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Keterlambatan Otomatis Compact -->
                <div id="kembaliAlertTerlambat" class="p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs hidden">
                    <p class="font-bold flex items-center">
                        <x-icon name="Clock" class="w-3.5 h-3.5 mr-1 text-rose-600 shrink-0" />
                        Terlambat: <span id="kembaliHariTerlambat" class="ml-1 font-extrabold">0 Hari</span>
                    </p>
                    <p class="mt-0.5 text-rose-700 text-[11px]">Denda sistem: <strong id="kembaliDendaTelat">Rp 0</strong></p>
                </div>

                <!-- Kondisi Buku Saat Dikembalikan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Pemeriksaan Kondisi Buku
                    </label>
                    <select name="kondisi_buku_kembali" id="kembaliKondisi" onchange="hitungTotalDendaPengembalian()" class="w-full py-2 px-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-700 focus:border-blue-500 focus:outline-none">
                        <option value="baik">Buku Baik & Utuh (Stok Bertambah +1)</option>
                        <option value="rusak">Buku Rusak (+ Denda Kerusakan, Stok +1)</option>
                        <option value="hilang">Buku Hilang (+ Ganti Rugi, Stok Tetap)</option>
                    </select>
                </div>

                <!-- Nominal Denda Tambahan (Kerusakan / Kehilangan) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Denda Tambahan (Kerusakan / Hilang) - Opsional
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" 
                               name="denda_tambahan" 
                               id="kembaliDendaTambahan" 
                               value="0" 
                               min="0" 
                               oninput="hitungTotalDendaPengembalian()"
                               class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium focus:border-blue-500 focus:outline-none">
                    </div>
                </div>

                <!-- Catatan Petugas -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Catatan Petugas (Opsional)
                    </label>
                    <input type="text" 
                           name="catatan_admin" 
                           placeholder="Contoh: Kondisi buku sesuai foto unggahan siswa..." 
                           class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:border-blue-500 focus:outline-none">
                </div>

                <!-- Total Denda yang Harus Dibayar Compact -->
                <div class="p-2.5 sm:p-3 bg-slate-100 rounded-xl flex items-center justify-between">
                    <div>
                        <span class="text-[11px] uppercase font-bold text-slate-500">Total Denda Ditagihkan:</span>
                        <p class="text-[10px] text-slate-400">Keterlambatan + Tambahan</p>
                    </div>
                    <span id="kembaliTotalDendaDisplay" class="text-lg sm:text-xl font-extrabold text-indigo-700">Rp 0</span>
                </div>

                <!-- Status Pembayaran Denda Compact -->
                <div id="kembaliGroupStatusDenda" class="hidden">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Status Pembayaran Denda
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center p-2 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 text-xs">
                            <input type="radio" name="status_denda" value="belum_dibayar" checked class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-1.5 font-semibold text-rose-700 text-[11px]">Belum Dibayar</span>
                        </label>
                        <label class="flex items-center p-2 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 text-xs">
                            <input type="radio" name="status_denda" value="lunas" class="text-blue-600 focus:ring-blue-500">
                            <span class="ml-1.5 font-semibold text-emerald-700 text-[11px]">Langsung Lunas</span>
                        </label>
                    </div>
                </div>

            </div>

            <!-- Footer Modal Compact -->
            <div class="p-3 sm:p-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-2 shrink-0">
                <button type="button" onclick="closePengembalianModal()" class="px-3.5 py-1.5 border border-slate-300 hover:bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl transition focus:outline-none focus:ring-2 focus:ring-slate-400">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <x-icon name="CircleCheck" class="w-3.5 h-3.5" />
                    <span>Konfirmasi Pengembalian</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. MODAL ATUR / EDIT DENDA SISWA (Compact) -->
<div id="modalEditDenda" class="fixed inset-0 z-50 hidden bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl max-w-sm w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-3.5 sm:p-4 bg-gradient-to-r from-blue-700 to-indigo-700 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2">
                <div class="w-7 h-7 rounded-lg bg-white/10 flex items-center justify-center">
                    <x-icon name="Wallet" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <h4 class="font-bold text-sm sm:text-base">Kelola Denda Siswa</h4>
                    <p class="text-[11px] text-blue-100">Ubah nominal atau status pelunasan</p>
                </div>
            </div>
            <button onclick="closeEditDendaModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-white/40" aria-label="Tutup modal kelola denda">
                <x-icon name="X" class="w-4 h-4" />
            </button>
        </div>

        <form id="formEditDenda" method="POST" action="">
            @csrf
            @method('PUT')

            <div class="p-4 space-y-3">
                <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200/80 text-xs">
                    <p class="text-slate-500">Siswa: <strong id="editDendaNamaSiswa" class="text-slate-800">-</strong></p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Nominal Denda (Rupiah)
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-xs font-bold text-slate-400">Rp</span>
                        <input type="number" 
                               name="denda" 
                               id="editDendaNominal" 
                               min="0" 
                               required
                               class="w-full pl-9 pr-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-semibold text-slate-800 focus:border-blue-500 focus:outline-none">
                    </div>
                    <p class="text-[10px] text-slate-400 mt-0.5">Masukkan 0 jika ingin membebaskan denda.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Status Pembayaran Denda
                    </label>
                    <select name="status_denda" id="editDendaStatus" class="w-full py-1.5 px-2.5 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium text-slate-700 focus:border-blue-500 focus:outline-none">
                        <option value="belum_dibayar">Belum Dibayar (Menunggak)</option>
                        <option value="lunas">Sudah Lunas</option>
                        <option value="bebas">Bebas Denda (Rp 0)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Catatan Kondisi Buku
                    </label>
                    <input type="text" 
                           name="kondisi_buku_kembali" 
                           id="editDendaKondisi" 
                           placeholder="Contoh: Terlambat 3 hari, Halaman robek..."
                           class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:border-blue-500 focus:outline-none">
                </div>
            </div>

            <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeEditDendaModal()" class="px-3.5 py-1.5 border border-slate-300 hover:bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-400">
                    Batal
                </button>
                <button type="submit" class="px-4 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow-xs inline-flex items-center space-x-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <x-icon name="Save" class="w-3.5 h-3.5" />
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. MODAL PREVIEW BUKTI FOTO -->
<div id="fotoModal" class="fixed inset-0 z-50 hidden bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h4 id="fotoModalTitle" class="font-bold text-slate-800 text-sm truncate">Bukti Foto Peminjaman</h4>
            <button onclick="closeFotoModal()" class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-slate-400" aria-label="Tutup modal preview foto">
                <x-icon name="X" class="w-4 h-4" />
            </button>
        </div>
        <div class="p-4 bg-slate-100 flex items-center justify-center max-h-[70vh] overflow-auto">
            <img id="fotoModalImg" src="" alt="Bukti Foto" class="max-w-full max-h-[60vh] rounded-xl shadow-sm object-contain">
        </div>
        <div class="p-3 bg-white border-t border-slate-100 text-right">
            <button onclick="closeFotoModal()" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-500">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- 4. MODAL TOLAK PENGAJUAN PINJAMAN (Dengan Alasan Penolakan) -->
<div id="modalTolakPengajuan" class="fixed inset-0 z-50 hidden bg-slate-900/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl max-w-md w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <!-- Header -->
        <div class="p-4 bg-gradient-to-r from-rose-700 to-red-600 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                    <x-icon name="X" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <h4 class="font-bold text-sm sm:text-base">Tolak Pengajuan Peminjaman</h4>
                    <p class="text-[11px] text-rose-100">Berikan alasan penolakan untuk catatan siswa & rekap</p>
                </div>
            </div>
            <button type="button" onclick="closeTolakModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition focus:outline-none focus:ring-2 focus:ring-white/40" aria-label="Tutup modal tolak pengajuan">
                <x-icon name="X" class="w-4 h-4" />
            </button>
        </div>

        <form id="formTolakPengajuan" method="POST" action="" onsubmit="this.querySelector('button[type=submit]').disabled = true;">
            @csrf
            @method('DELETE')

            <div class="p-4 sm:p-5 space-y-3.5">
                <!-- Info Peminjam & Buku -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-xs space-y-1">
                    <p class="text-slate-500">Nama Siswa: <strong id="tolakNamaSiswa" class="text-slate-800">-</strong></p>
                    <p class="text-slate-500">Judul Buku: <strong id="tolakJudulBuku" class="text-slate-800">-</strong></p>
                </div>

                <!-- Input Alasan Penolakan -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Alasan Penolakan <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="alasan_penolakan" 
                              id="tolakAlasanInput" 
                              rows="3" 
                              required 
                              placeholder="Tuliskan alasan penolakan pengajuan..."
                              class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs sm:text-sm font-medium focus:border-rose-500 focus:outline-none focus:ring-2 focus:ring-rose-500/10"></textarea>
                    
                    <!-- Pilihan Cepat Alasan -->
                    <div class="mt-2">
                        <p class="text-[10px] font-semibold text-slate-400 mb-1">Pilihan Cepat Alasan:</p>
                        <div class="flex flex-wrap gap-1.5">
                            <button type="button" onclick="setQuickAlasan('Buku sedang dalam perawatan/rusak')" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-0.5 rounded-md transition">Buku Rusak / Perawatan</button>
                            <button type="button" onclick="setQuickAlasan('Stok buku fisik saat ini tidak mencukupi')" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-0.5 rounded-md transition">Stok Fisik Habis</button>
                            <button type="button" onclick="setQuickAlasan('Siswa masih memiliki buku pinjaman aktif yang belum dikembalikan')" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-0.5 rounded-md transition">Ada Pinjaman Aktif</button>
                            <button type="button" onclick="setQuickAlasan('Batas maksimal peminjaman telah tercapai')" class="text-[10px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-2 py-0.5 rounded-md transition">Batas Maksimal</button>
                        </div>
                    </div>
                </div>

                <!-- Catatan Tambahan (Opsional) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Catatan Tambahan (Opsional)
                    </label>
                    <input type="text" 
                           name="catatan" 
                           placeholder="Contoh: Silakan ajukan kembali setelah mengembalikan buku sebelumnya" 
                           class="w-full px-3 py-1.5 bg-slate-50 border border-slate-300 rounded-xl text-xs font-medium focus:border-rose-500 focus:outline-none">
                </div>
            </div>

            <!-- Footer -->
            <div class="p-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeTolakModal()" class="px-3.5 py-1.5 border border-slate-300 hover:bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl transition focus:outline-none focus:ring-2 focus:ring-slate-400">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-rose-500">
                    <x-icon name="X" class="w-3.5 h-3.5" />
                    <span>Tolak Pengajuan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 5. MODAL KONFIRMASI HAPUS DATA PENGAJUAN (ACC / Ditolak) -->
<div id="modalKonfirmasiHapus" class="fixed inset-0 z-50 hidden bg-slate-900/75 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl max-w-md w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <!-- Header -->
        <div class="p-4 bg-gradient-to-r from-red-600 to-rose-700 text-white flex items-center justify-between">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shrink-0">
                    <x-icon name="Trash2" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <h4 class="font-bold text-sm sm:text-base">Konfirmasi Hapus Data</h4>
                    <p class="text-[11px] text-red-100">Hapus pengajuan dari daftar & rekap Excel</p>
                </div>
            </div>
            <button type="button" onclick="closeHapusModal()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition focus:outline-none focus:ring-2 focus:ring-white/40" aria-label="Tutup modal konfirmasi hapus">
                <x-icon name="X" class="w-4 h-4" />
            </button>
        </div>

        <form id="formHapusPengajuan" method="POST" action="">
            @csrf
            @method('DELETE')

            <div class="p-4 sm:p-5 space-y-3.5">
                <div class="p-3 bg-rose-50 rounded-xl border border-rose-200 text-rose-800 text-xs flex items-start space-x-2.5">
                    <x-icon name="AlertCircle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" />
                    <div>
                        <strong class="font-bold block text-rose-900">Peringatan:</strong>
                        <span>Data yang dihapus dari daftar <strong>tidak akan muncul lagi di tabel peminjaman maupun export Excel</strong>.</span>
                    </div>
                </div>

                <!-- Info Item Yang Akan Dihapus -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/80 text-xs space-y-1.5">
                    <p class="text-slate-500">Nama Siswa: <strong id="hapusNamaSiswa" class="text-slate-800">-</strong></p>
                    <p class="text-slate-500">Judul Buku: <strong id="hapusJudulBuku" class="text-slate-800">-</strong></p>
                    <p class="text-slate-500">Status Saat Ini: <span id="hapusStatusBadge" class="font-bold text-slate-700 bg-slate-200 px-2 py-0.5 rounded text-[10px]">-</span></p>
                </div>

                <p class="text-xs text-slate-600">
                    Apakah Anda yakin ingin menghapus data pengajuan ini?
                </p>
            </div>

            <!-- Footer -->
            <div class="p-3.5 bg-slate-50 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeHapusModal()" class="px-3.5 py-1.5 border border-slate-300 hover:bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl transition focus:outline-none focus:ring-2 focus:ring-slate-400">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-xs transition inline-flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-red-500">
                    <x-icon name="Trash2" class="w-3.5 h-3.5" />
                    <span>Ya, Hapus Data</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    let dendaTelatDasar = 0;

    // Modal Terima & Periksa Pengembalian Buku
    function openPengembalianModal(id, namaSiswa, judulBuku, hariTerlambat, totalDenda, tarifDenda, fotoKembali, catatanKembali, tglPengajuan) {
        document.getElementById('formPengembalian').action = '/peminjaman/' + id + '/kembali';
        document.getElementById('kembaliNamaSiswa').innerText = namaSiswa;
        document.getElementById('kembaliJudulBuku').innerText = judulBuku;

        // Foto kondisi buku & catatan dari siswa
        const boxBukti = document.getElementById('kembaliBoxBuktiSiswa');
        if (fotoKembali) {
            document.getElementById('kembaliImgBukti').src = fotoKembali;
            document.getElementById('kembaliCatatanSiswa').innerText = catatanKembali ? ('"' + catatanKembali + '"') : 'Tidak ada catatan tambahan dari siswa.';
            document.getElementById('kembaliTglPengajuan').innerText = tglPengajuan ? ('Diajukan: ' + tglPengajuan) : '';
            boxBukti.classList.remove('hidden');
        } else {
            boxBukti.classList.add('hidden');
        }

        dendaTelatDasar = (hariTerlambat > 0) ? (hariTerlambat * tarifDenda) : 0;

        if (hariTerlambat > 0) {
            document.getElementById('kembaliAlertTerlambat').classList.remove('hidden');
            document.getElementById('kembaliHariTerlambat').innerText = hariTerlambat + ' Hari';
            document.getElementById('kembaliDendaTelat').innerText = 'Rp ' + dendaTelatDasar.toLocaleString('id-ID');
        } else {
            document.getElementById('kembaliAlertTerlambat').classList.add('hidden');
        }

        document.getElementById('kembaliDendaTambahan').value = 0;
        document.getElementById('kembaliKondisi').value = 'baik';

        hitungTotalDendaPengembalian();
        document.getElementById('modalPengembalian').classList.remove('hidden');
    }

    function hitungTotalDendaPengembalian() {
        const tambahan = parseInt(document.getElementById('kembaliDendaTambahan').value) || 0;
        const total = dendaTelatDasar + tambahan;

        document.getElementById('kembaliTotalDendaDisplay').innerText = 'Rp ' + total.toLocaleString('id-ID');

        const statusGroup = document.getElementById('kembaliGroupStatusDenda');
        if (total > 0) {
            statusGroup.classList.remove('hidden');
        } else {
            statusGroup.classList.add('hidden');
        }
    }

    function closePengembalianModal() {
        document.getElementById('modalPengembalian').classList.add('hidden');
    }

    // Modal Edit Denda
    function openEditDendaModal(id, namaSiswa, denda, statusDenda, kondisi) {
        document.getElementById('formEditDenda').action = '/peminjaman/' + id + '/update-denda';
        document.getElementById('editDendaNamaSiswa').innerText = namaSiswa;
        document.getElementById('editDendaNominal').value = denda || 0;
        document.getElementById('editDendaStatus').value = statusDenda || (denda > 0 ? 'belum_dibayar' : 'bebas');
        document.getElementById('editDendaKondisi').value = kondisi || '';
        document.getElementById('modalEditDenda').classList.remove('hidden');
    }

    function closeEditDendaModal() {
        document.getElementById('modalEditDenda').classList.add('hidden');
    }

    // Modal Preview Foto
    function previewFoto(url, title) {
        document.getElementById('fotoModalImg').src = url;
        document.getElementById('fotoModalTitle').innerText = 'Bukti: ' + title;
        document.getElementById('fotoModal').classList.remove('hidden');
    }

    function closeFotoModal() {
        document.getElementById('fotoModal').classList.add('hidden');
    }

    // Modal Tolak Pengajuan
    function openTolakModal(id, namaSiswa, judulBuku) {
        document.getElementById('formTolakPengajuan').action = '/peminjaman/' + id + '/tolak';
        document.getElementById('tolakNamaSiswa').innerText = namaSiswa;
        document.getElementById('tolakJudulBuku').innerText = judulBuku;
        document.getElementById('tolakAlasanInput').value = '';
        document.getElementById('modalTolakPengajuan').classList.remove('hidden');
    }

    function setQuickAlasan(text) {
        document.getElementById('tolakAlasanInput').value = text;
    }

    function closeTolakModal() {
        document.getElementById('modalTolakPengajuan').classList.add('hidden');
    }

    // Modal Konfirmasi Hapus
    function openHapusModal(id, namaSiswa, judulBuku, statusText) {
        document.getElementById('formHapusPengajuan').action = '/peminjaman/' + id;
        document.getElementById('hapusNamaSiswa').innerText = namaSiswa;
        document.getElementById('hapusJudulBuku').innerText = judulBuku;
        document.getElementById('hapusStatusBadge').innerText = statusText;
        document.getElementById('modalKonfirmasiHapus').classList.remove('hidden');
    }

    function closeHapusModal() {
        document.getElementById('modalKonfirmasiHapus').classList.add('hidden');
    }

    // Close modal on click backdrop
    ['modalPengembalian', 'modalEditDenda', 'fotoModal', 'modalTolakPengajuan', 'modalKonfirmasiHapus'].forEach(function(modalId) {
        const el = document.getElementById(modalId);
        if (el) {
            el.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                }
            });
        }
    });
</script>
@endsection