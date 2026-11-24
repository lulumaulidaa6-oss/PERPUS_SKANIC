<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Perpustakaan Siswa</title>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        /* Custom smooth scrollbar */
        .scrollbar-thin::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.2);
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.4);
        }
    </style>
</head>
<body class="bg-slate-50 font-sans h-screen flex antialiased text-slate-800 overflow-hidden">

    <!-- Mobile Drawer Backdrop Overlay -->
    <div id="sidebarBackdrop" onclick="closeSidebar()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 hidden md:hidden transition-opacity duration-200"></div>

    <!-- Sidebar Khusus Siswa (Fixed / Sticky di Desktop & Drawer di Mobile) -->
    <aside id="siswaSidebar" class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:w-64 md:h-screen md:shrink-0 flex flex-col bg-slate-900 text-white border-r border-slate-800 shadow-xl overflow-hidden">
        
        <!-- Logo Brand Khusus Siswa -->
        <div class="shrink-0 p-5 sm:p-6 border-b border-slate-800/80 flex items-center justify-between">
            <a href="/siswa/dashboard" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-lg shadow-blue-500/25 group-hover:scale-105 transition-transform">
                    <x-icon name="BookOpen" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-white flex items-center">
                        Perpus<span class="text-blue-400">Siswa</span>
                    </h2>
                    <p class="text-[11px] text-slate-400 font-medium">Layanan Mandiri Siswa</p>
                </div>
            </a>

            <!-- Tombol Tutup Sidebar untuk Mobile -->
            <button type="button" onclick="closeSidebar()" class="md:hidden p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition focus:outline-none focus:ring-2 focus:ring-blue-500/30" aria-label="Tutup Menu Navigasi">
                <x-icon name="X" class="w-5 h-5" />
            </button>
        </div>

        <!-- Banner Identitas Layanan Siswa (Tanpa Akun / Tanpa Login) -->
        <div class="shrink-0 px-4 py-2.5 border-b border-slate-800 bg-slate-850/60 flex items-center space-x-2.5">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
            <div>
                <p class="text-xs font-semibold text-white">Akses Bebas Siswa</p>
                <p class="text-[10px] text-slate-400">Cukup gunakan nama saat meminjam</p>
            </div>
        </div>
        
        <!-- Menu Navigasi Siswa (Scrollable independen hanya di bagian dalam menu) -->
        <nav aria-label="Navigasi Menu Siswa" class="p-3.5 flex-1 overflow-y-auto space-y-3.5 scrollbar-thin">
            
            <!-- Kelompok 1: Peminjaman & Buku -->
            <div>
                <p class="px-3 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Katalog & Peminjaman
                </p>
                <ul class="space-y-1">
                    <!-- 1. Melihat stok & daftar buku -->
                    <li>
                        <a href="/siswa/dashboard" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition {{ request()->is('siswa/dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <x-icon name="BookOpen" class="w-4 h-4 sm:w-5 sm:h-5 opacity-80 shrink-0" />
                            <span class="truncate">Stok & Katalog Buku</span>
                        </a>
                    </li>

                    <!-- 2. Mengisi formulir peminjaman -->
                    <li>
                        <a href="/siswa/pinjam" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition {{ request()->is('siswa/pinjam') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <x-icon name="Pencil" class="w-4 h-4 sm:w-5 sm:h-5 opacity-80 shrink-0" />
                            <span class="truncate">Formulir Pinjam Buku</span>
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Kelompok 2: Status Pinjaman Siswa -->
            <div>
                <p class="px-3 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    Status Pinjaman Siswa
                </p>
                <ul class="space-y-1">
                    <!-- 3. Melihat peminjaman yang masih menunggu persetujuan -->
                    <li>
                        <a href="/siswa/menunggu" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition {{ request()->is('siswa/menunggu*') ? 'bg-amber-600 text-white shadow-md shadow-amber-600/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center space-x-3 min-w-0">
                                <x-icon name="Clock" class="w-4 h-4 sm:w-5 sm:h-5 text-amber-400 shrink-0" />
                                <span class="truncate">Menunggu Persetujuan</span>
                            </div>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-400/20 text-amber-300 shrink-0">Antrean</span>
                        </a>
                    </li>

                    <!-- 4. Melihat peminjaman yang sudah disetujui / di-ACC -->
                    <li>
                        <a href="/siswa/disetujui" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition {{ request()->is('siswa/disetujui*') ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center space-x-3 min-w-0">
                                <x-icon name="CircleCheck" class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-400 shrink-0" />
                                <span class="truncate">Sudah Di-ACC (Aktif)</span>
                            </div>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-400/20 text-emerald-300 shrink-0">Di-ACC</span>
                        </a>
                    </li>

                    <!-- 5. Melihat denda yang dimiliki -->
                    <li>
                        <a href="/siswa/denda" class="flex items-center justify-between px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition {{ request()->is('siswa/denda*') ? 'bg-rose-600 text-white shadow-md shadow-rose-600/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <div class="flex items-center space-x-3 min-w-0">
                                <x-icon name="Wallet" class="w-4 h-4 sm:w-5 sm:h-5 text-rose-400 shrink-0" />
                                <span class="truncate">Denda Siswa</span>
                            </div>
                            <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-rose-400/20 text-rose-300 shrink-0">Denda</span>
                        </a>
                    </li>

                    <!-- 6. Melihat riwayat buku yang pernah dipinjam -->
                    <li>
                        <a href="/siswa/riwayat-selesai" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition {{ request()->is('siswa/riwayat-selesai*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <x-icon name="History" class="w-4 h-4 sm:w-5 sm:h-5 opacity-80 shrink-0" />
                            <span class="truncate">Riwayat Buku Selesai</span>
                        </a>
                    </li>
                </ul>
            </div>

        </nav>
        
        <!-- Area Bawah Sidebar: Tombol Login Admin & Informasi Perpustakaan (Sticky & Fixed di Paling Bawah) -->
        <div class="shrink-0 mt-auto p-3.5 border-t border-slate-800 bg-slate-900/95 space-y-2.5">
            <!-- Tombol Login Admin (Compact, Ikon Sederhana, Arahkan ke /login) -->
            <a href="{{ route('login') }}" 
               class="group flex items-center justify-center space-x-2 w-full px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white bg-slate-800/90 hover:bg-slate-750 border border-slate-700/70 hover:border-blue-500/50 hover:shadow-xs transition duration-150 focus:outline-none focus:ring-2 focus:ring-blue-500/30"
               title="Masuk ke Panel Petugas / Admin">
                <x-icon name="LogIn" class="w-3.5 h-3.5 text-slate-400 group-hover:text-blue-400 transition shrink-0" />
                <span>Login Admin</span>
            </a>

            <!-- Informasi Perpustakaan Sekolah (Tepat di bawah tombol Login Admin) -->
            <div class="pt-2 border-t border-slate-800/80 text-[11px] text-slate-400 space-y-0.5">
                <p class="font-semibold text-slate-300 flex items-center">
                    <x-icon name="BookOpen" class="w-3.5 h-3.5 mr-1.5 text-blue-400 shrink-0 inline-block" />
                    <span>Perpustakaan Sekolah</span>
                </p>
                <p class="text-[10px] text-slate-400">Buka: Senin - Jumat (07.30 - 15.00)</p>
                <p class="text-[9.5px] text-slate-500">Kembalikan buku tepat waktu bebas denda.</p>
            </div>
        </div>
    </aside>

    <!-- Area Konten Utama Halaman Siswa (Scrollable independen, Sidebar tetap fixed) -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
        
        <!-- Topbar Navbar Siswa -->
        <header class="bg-white/85 backdrop-blur-md sticky top-0 z-30 border-b border-slate-200/80 px-4 sm:px-6 py-3.5 flex justify-between items-center shadow-xs">
            <div class="flex items-center space-x-3 min-w-0">
                <!-- Hamburger Button untuk Mobile & Tablet -->
                <button type="button" 
                        onclick="openSidebar()" 
                        class="md:hidden p-2 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition shrink-0 focus:outline-none focus:ring-2 focus:ring-blue-500/20" 
                        aria-label="Buka Menu Navigasi">
                    <x-icon name="Menu" class="w-5 h-5" />
                </button>

                <h1 class="text-base sm:text-xl font-bold text-slate-800 truncate">@yield('title')</h1>
            </div>
            
            <!-- Quick Links Tab Siswa -->
            <div class="flex items-center space-x-1.5 sm:space-x-2 shrink-0">
                <a href="/siswa/menunggu" class="hidden sm:inline-flex items-center text-xs font-semibold px-2.5 sm:px-3 py-1.5 rounded-lg bg-amber-50 text-amber-700 border border-amber-200 hover:bg-amber-100 transition focus:outline-none focus:ring-2 focus:ring-amber-500/20">
                    <x-icon name="Clock" class="w-3.5 h-3.5 text-amber-600 mr-1.5 shrink-0" />
                    <span>Menunggu ACC</span>
                </a>
                <a href="/siswa/disetujui" class="hidden sm:inline-flex items-center text-xs font-semibold px-2.5 sm:px-3 py-1.5 rounded-lg bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 transition focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    <x-icon name="CircleCheck" class="w-3.5 h-3.5 text-emerald-600 mr-1.5 shrink-0" />
                    <span>Sudah Di-ACC</span>
                </a>
                <a href="/siswa/denda" class="inline-flex items-center text-xs font-semibold px-2.5 sm:px-3 py-1.5 rounded-lg bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-100 transition focus:outline-none focus:ring-2 focus:ring-rose-500/20">
                    <x-icon name="Wallet" class="w-3.5 h-3.5 text-rose-600 mr-1.5 shrink-0" />
                    <span>Cek Denda</span>
                </a>
            </div>
        </header>

        <!-- Pesan Notifikasi Sukses / Gagal -->
        @if(session('success'))
            <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 pt-4">
                <div class="flex items-center p-3.5 sm:p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs sm:text-sm shadow-xs" role="alert">
                    <x-icon name="CircleCheck" class="w-5 h-5 mr-2.5 text-emerald-500 shrink-0" />
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif
        @if(session('error'))
            <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 pt-4">
                <div class="flex items-center p-3.5 sm:p-4 bg-rose-50 border border-rose-200 rounded-xl text-rose-800 text-xs sm:text-sm shadow-xs" role="alert">
                    <x-icon name="AlertCircle" class="w-5 h-5 mr-2.5 text-rose-500 shrink-0" />
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif
        @if(session('info'))
            <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 pt-4">
                <div class="flex items-center p-3.5 sm:p-4 bg-sky-50 border border-sky-200 rounded-xl text-sky-800 text-xs sm:text-sm shadow-xs" role="alert">
                    <x-icon name="Info" class="w-5 h-5 mr-2.5 text-sky-500 shrink-0" />
                    <span>{{ session('info') }}</span>
                </div>
            </div>
        @endif

        <!-- Tempat Konten Utama Siswa -->
        <main class="p-3.5 sm:p-6 lg:p-8 flex-1">
            @yield('content')
        </main>
    </div>

    <!-- Script Kontrol Drawer Responsif -->
    <script>
        function openSidebar() {
            const sidebar = document.getElementById('siswaSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            }
        }
        function closeSidebar() {
            const sidebar = document.getElementById('siswaSidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>
</body>
</html>
