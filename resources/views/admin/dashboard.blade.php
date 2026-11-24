@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Hero Greeting Header -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/4 -top-12 w-48 h-48 bg-indigo-500/20 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-blue-200 mb-3 border border-white/10">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Sistem Perpustakaan Digital Aktif</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ auth()->user()->name ?? 'Admin' }}!</h2>
                <p class="text-slate-300 text-sm sm:text-base mt-2 leading-relaxed max-w-2xl">
                    Berikut ringkasan statistik buku, sirkulasi peminjaman siswa, dan pantauan denda keterlambatan saat ini.
                </p>
            </div>

            <div class="flex flex-wrap gap-3">
                <a href="/peminjaman" class="inline-flex items-center space-x-2 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm px-5 py-2.5 rounded-xl shadow-lg shadow-blue-900/30 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30">
                    <x-icon name="ClipboardList" class="w-4 h-4" />
                    <span>Kelola Peminjaman & Denda</span>
                </a>
                <a href="/buku/create" class="inline-flex items-center space-x-2 bg-white/15 hover:bg-white/25 text-white font-semibold text-sm px-4 py-2.5 rounded-xl backdrop-blur-md border border-white/20 transition duration-200 focus:outline-none focus:ring-2 focus:ring-white/30">
                    <x-icon name="Plus" class="w-4 h-4" />
                    <span>Tambah Buku</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Grid Kartu Statistik Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- Kartu 1: Total Buku -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Koleksi Buku</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <x-icon name="BookOpen" class="w-5 h-5 text-blue-600" />
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-extrabold text-slate-800">{{ $total_buku }}</h3>
                <p class="text-xs text-slate-500 mt-1 flex items-center justify-between">
                    <span>Judul ({{ $total_stok_buku ?? 0 }} stok fisik)</span>
                    <a href="/buku" class="text-blue-600 font-semibold hover:underline inline-flex items-center">
                        <span>Kelola</span>
                        <x-icon name="ArrowRight" class="w-3.5 h-3.5 ml-1" />
                    </a>
                </p>
            </div>
        </div>

        <!-- Kartu 2: Buku Sedang Dipinjam -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Sedang Dipinjam</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <x-icon name="ClipboardList" class="w-5 h-5 text-indigo-600" />
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-extrabold text-indigo-600">{{ $buku_dipinjam }}</h3>
                <p class="text-xs text-slate-500 mt-1 flex items-center justify-between">
                    <span>
                        @if(($terlambat_aktif ?? 0) > 0)
                            <strong class="text-rose-600 inline-flex items-center">
                                <x-icon name="Clock" class="w-3.5 h-3.5 mr-1 text-rose-600" />
                                {{ $terlambat_aktif }} terlambat
                            </strong>
                        @else
                            Aktif di tangan siswa
                        @endif
                    </span>
                    <a href="/peminjaman?status=dipinjam" class="text-indigo-600 font-semibold hover:underline inline-flex items-center">
                        <span>Pantau</span>
                        <x-icon name="ArrowRight" class="w-3.5 h-3.5 ml-1" />
                    </a>
                </p>
            </div>
        </div>

        <!-- Kartu 3: Total Siswa Peminjam -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Siswa Peminjam</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <x-icon name="Users" class="w-5 h-5 text-emerald-600" />
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-3xl font-extrabold text-slate-800">{{ $total_siswa }}</h3>
                <p class="text-xs text-slate-500 mt-1 flex items-center justify-between">
                    <span>Terdaftar di sistem</span>
                    @if(($menunggu_persetujuan ?? 0) > 0)
                        <a href="/peminjaman?status=menunggu" class="text-amber-600 font-bold hover:underline inline-flex items-center">
                            <x-icon name="Clock" class="w-3.5 h-3.5 mr-1 text-amber-600" />
                            <span>{{ $menunggu_persetujuan }} antrean</span>
                        </a>
                    @else
                        <span class="text-emerald-600 font-medium">Bebas antrean</span>
                    @endif
                </p>
            </div>
        </div>

        <!-- Kartu 4: Denda Siswa Belum Lunas -->
        <div class="bg-white rounded-2xl shadow-sm border {{ ($denda_belum_dibayar ?? 0) > 0 ? 'border-rose-300 bg-rose-50/20' : 'border-slate-200/80' }} p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Denda Belum Lunas</span>
                <div class="w-10 h-10 rounded-xl {{ ($denda_belum_dibayar ?? 0) > 0 ? 'bg-rose-100 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center">
                    <x-icon name="Wallet" class="w-5 h-5 {{ ($denda_belum_dibayar ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}" />
                </div>
            </div>
            <div class="mt-3">
                <h3 class="text-2xl sm:text-3xl font-extrabold {{ ($denda_belum_dibayar ?? 0) > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                    Rp {{ number_format($denda_belum_dibayar ?? 0, 0, ',', '.') }}
                </h3>
                <p class="text-xs text-slate-500 mt-1 flex items-center justify-between">
                    <span>
                        @if(($transaksi_denda_aktif ?? 0) > 0)
                            <strong class="text-rose-600">{{ $transaksi_denda_aktif }} siswa menunggak</strong>
                        @else
                            <span class="text-emerald-600 font-medium">Semua denda lunas</span>
                        @endif
                    </span>
                    <a href="/peminjaman?status=denda" class="text-rose-600 font-bold hover:underline inline-flex items-center">
                        <span>Kelola</span>
                        <x-icon name="ArrowRight" class="w-3.5 h-3.5 ml-1" />
                    </a>
                </p>
            </div>
        </div>

    </div>

    <!-- Pintasan Cepat & Informasi Terkini -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Pintasan Aksi Cepat -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-slate-800">Aksi & Akses Cepat Petugas</h3>
                <span class="text-xs text-slate-400">Navigasi Langsung</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <!-- Cari Peminjaman -->
                <a href="/peminjaman" class="p-4 rounded-xl border border-slate-200 hover:border-blue-400 hover:bg-blue-50/40 transition group focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-icon name="Search" class="w-5 h-5 text-blue-600" />
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-800 group-hover:text-blue-600 transition">Cari & Filter Riwayat</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Cari berdasarkan nama siswa / buku</p>
                        </div>
                    </div>
                </a>

                <!-- Cek Tagihan Denda Siswa -->
                <a href="/peminjaman?status=denda" class="p-4 rounded-xl border border-slate-200 hover:border-rose-400 hover:bg-rose-50/40 transition group focus:outline-none focus:ring-2 focus:ring-rose-500/20">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-icon name="Wallet" class="w-5 h-5 text-rose-600" />
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-800 group-hover:text-rose-600 transition">Data Denda Siswa</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Saring siswa yang ada tagihan denda</p>
                        </div>
                    </div>
                </a>

                <!-- Konfirmasi Antrean Pengajuan -->
                <a href="/peminjaman?status=menunggu" class="p-4 rounded-xl border border-slate-200 hover:border-amber-400 hover:bg-amber-50/40 transition group focus:outline-none focus:ring-2 focus:ring-amber-500/20">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-icon name="Clock" class="w-5 h-5 text-amber-600" />
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-800 group-hover:text-amber-600 transition">Verifikasi Pinjaman</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Setujui atau tolak pengajuan siswa</p>
                        </div>
                    </div>
                </a>

                <!-- Buku & Stok -->
                <a href="/buku" class="p-4 rounded-xl border border-slate-200 hover:border-indigo-400 hover:bg-indigo-50/40 transition group focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                            <x-icon name="BookOpen" class="w-5 h-5 text-indigo-600" />
                        </div>
                        <div>
                            <h4 class="font-bold text-sm text-slate-800 group-hover:text-indigo-600 transition">Kelola Katalog Buku</h4>
                            <p class="text-xs text-slate-500 mt-0.5">Tambah buku & kelola stok perpustakaan</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Kolom Rangkuman Info Denda & Peraturan -->
        <div class="bg-gradient-to-br from-slate-50 to-blue-50/30 rounded-2xl border border-slate-200/80 p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center space-x-2 mb-3">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                    <h3 class="font-bold text-sm text-slate-800">Panduan Denda Perpustakaan</h3>
                </div>
                <div class="space-y-2.5 text-xs text-slate-600">
                    <div class="p-3 bg-white rounded-xl border border-slate-200/70">
                        <p class="font-bold text-slate-800 flex items-center space-x-1.5">
                            <x-icon name="Clock" class="w-4 h-4 text-amber-600" />
                            <span>Denda Keterlambatan:</span>
                        </p>
                        <p class="mt-1 text-slate-600">Dihitung otomatis <strong>Rp 1.000 / hari</strong> setelah melewati tanggal perkiraan kembali.</p>
                    </div>
                    <div class="p-3 bg-white rounded-xl border border-slate-200/70">
                        <p class="font-bold text-slate-800 flex items-center space-x-1.5">
                            <x-icon name="BookOpen" class="w-4 h-4 text-blue-600" />
                            <span>Denda Kondisi Buku:</span>
                        </p>
                        <p class="mt-1 text-slate-600">Petugas dapat mencatat denda kerusakan atau ganti rugi saat menerima pengembalian buku.</p>
                    </div>
                    <div class="p-3 bg-white rounded-xl border border-slate-200/70">
                        <p class="font-bold text-slate-800 flex items-center space-x-1.5">
                            <x-icon name="CircleCheck" class="w-4 h-4 text-emerald-600" />
                            <span>Pelunasan Denda:</span>
                        </p>
                        <p class="mt-1 text-slate-600">Klik tombol <em>"Tandai Lunas"</em> di tabel peminjaman begitu siswa membayar di meja perpustakaan.</p>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-200 text-center">
                <a href="/peminjaman" class="text-xs font-bold text-blue-600 hover:text-blue-800 hover:underline inline-flex items-center">
                    <span>Buka Halaman Peminjaman & Denda</span>
                    <x-icon name="ArrowRight" class="w-3.5 h-3.5 ml-1" />
                </a>
            </div>
        </div>

    </div>

</div>
@endsection