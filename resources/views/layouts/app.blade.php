<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Admin Perpustakaan</title>
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
        /* Sembunyikan icon mata bawaan browser (Edge/IE) agar tidak muncul ganda */
        input::-ms-reveal,
        input::-ms-clear,
        ::-ms-reveal,
        ::-ms-clear {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            pointer-events: none !important;
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
    <div id="adminSidebarBackdrop" onclick="closeAdminSidebar()" class="fixed inset-0 bg-slate-950/60 backdrop-blur-xs z-40 hidden md:hidden transition-opacity duration-200"></div>

    <!-- Sidebar Khusus Admin / Petugas (Fixed / Sticky di Desktop & Drawer di Mobile) -->
    <aside id="adminSidebar" class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] -translate-x-full transition-transform duration-300 ease-in-out md:translate-x-0 md:static md:w-64 md:h-screen md:shrink-0 flex flex-col bg-slate-900 text-white border-r border-slate-800 shadow-xl overflow-hidden">
        
        <!-- Logo Brand Admin -->
        <div class="shrink-0 p-5 sm:p-6 border-b border-slate-800/80 flex items-center justify-between">
            <a href="/admin/dashboard" class="flex items-center space-x-3 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-blue-600 to-indigo-500 flex items-center justify-center shadow-lg shadow-blue-500/25 group-hover:scale-105 transition-transform">
                    <x-icon name="BookOpen" class="w-6 h-6 text-white" />
                </div>
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-white flex items-center">
                        Perpus<span class="text-blue-400">Admin</span>
                    </h2>
                    <p class="text-[11px] text-slate-400 font-medium">Panel Pengelola Perpustakaan</p>
                </div>
            </a>

            <!-- Tombol Tutup Sidebar untuk Mobile -->
            <button type="button" onclick="closeAdminSidebar()" class="md:hidden p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition focus:outline-none focus:ring-2 focus:ring-blue-500/30" aria-label="Tutup Menu Navigasi">
                <x-icon name="X" class="w-5 h-5" />
            </button>
        </div>

        <!-- Status Identitas Petugas / Admin -->
        <div class="shrink-0 px-5 py-3.5 border-b border-slate-800 bg-slate-850/50">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-full bg-indigo-600/30 text-indigo-400 border border-indigo-500/30 flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</p>
                    <div class="flex items-center mt-0.5">
                        <span class="inline-flex items-center px-1.5 py-0.2 text-[10px] font-semibold text-indigo-300 bg-indigo-500/20 rounded">Petugas / Admin</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Menu Navigasi Admin (Scrollable independen hanya di bagian dalam menu) -->
        <nav aria-label="Navigasi Menu Petugas" class="p-3.5 flex-1 overflow-y-auto space-y-3 scrollbar-thin">
            <p class="px-3 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                Menu Petugas
            </p>
            <ul class="space-y-1">
                <li>
                    <a href="/admin/dashboard" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition {{ request()->is('admin/dashboard') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <x-icon name="LayoutDashboard" class="w-4 h-4 sm:w-5 sm:h-5 opacity-80 shrink-0" />
                        <span class="truncate">Dashboard Admin</span>
                    </a>
                </li>
                <li>
                    <a href="/buku" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition {{ request()->is('buku*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <x-icon name="BookOpen" class="w-4 h-4 sm:w-5 sm:h-5 opacity-80 shrink-0" />
                        <span class="truncate">Kelola Buku</span>
                    </a>
                </li>
                <li>
                    <a href="/peminjaman" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition {{ request()->is('peminjaman*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <x-icon name="ClipboardList" class="w-4 h-4 sm:w-5 sm:h-5 opacity-80 shrink-0" />
                        <span class="truncate">Peminjaman & Denda</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.pengaturan-akun') }}" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs sm:text-sm font-medium transition {{ request()->is('admin/pengaturan-akun*') ? 'bg-blue-600 text-white shadow-md shadow-blue-600/25' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <x-icon name="Settings" class="w-4 h-4 sm:w-5 sm:h-5 opacity-80 shrink-0" />
                        <span class="truncate">Pengaturan Akun</span>
                    </a>
                </li>
                <li class="pt-2.5 border-t border-slate-800">
                    <a href="/siswa/dashboard" target="_blank" class="flex items-center space-x-3 px-3 py-2 rounded-xl text-xs font-medium text-slate-400 hover:bg-slate-800 hover:text-slate-200 transition">
                        <x-icon name="ExternalLink" class="w-4 h-4 opacity-70 shrink-0" />
                        <span class="truncate">Buka Portal Siswa</span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Footer Sidebar: Logout Petugas (Sticky & Fixed di Paling Bawah) -->
        <div class="shrink-0 mt-auto p-3.5 border-t border-slate-800 bg-slate-900/95">
            @if(auth()->check())
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center space-x-2 px-3 py-2 rounded-xl text-xs font-medium text-rose-400 hover:bg-rose-500/10 hover:text-rose-300 border border-rose-500/20 hover:border-rose-500/40 transition focus:outline-none focus:ring-2 focus:ring-rose-500/30">
                        <x-icon name="LogOut" class="w-3.5 h-3.5 shrink-0" />
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            @endif
        </div>
    </aside>

    <!-- Area Konten Utama Admin (Scrollable independen, Sidebar tetap fixed) -->
    <div class="flex-1 flex flex-col min-w-0 h-screen overflow-y-auto">
        <!-- Topbar Navbar Admin -->
        <header class="bg-white/85 backdrop-blur-md sticky top-0 z-30 border-b border-slate-200/80 px-4 sm:px-6 py-3.5 flex justify-between items-center shadow-xs">
            <div class="flex items-center space-x-3 min-w-0">
                <!-- Hamburger Button untuk Mobile & Tablet -->
                <button type="button" 
                        onclick="openAdminSidebar()" 
                        class="md:hidden p-2 -ml-1 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl transition shrink-0 focus:outline-none focus:ring-2 focus:ring-blue-500/20" 
                        aria-label="Buka Menu Navigasi">
                    <x-icon name="Menu" class="w-5 h-5" />
                </button>

                <h1 class="text-base sm:text-xl font-bold text-slate-800 truncate">@yield('title')</h1>
            </div>
            
            <div class="flex items-center space-x-2 sm:space-x-3 shrink-0">
                @if(auth()->check())
                    <span class="text-xs text-slate-500 font-medium hidden sm:inline">Petugas: <strong class="text-slate-700">{{ auth()->user()->name }}</strong></span>
                @endif
                <a href="/buku/create" class="inline-flex items-center space-x-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold shadow-2xs transition focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <x-icon name="Plus" class="w-3.5 h-3.5" />
                    <span>Tambah Buku</span>
                </a>
            </div>
        </header>

        <!-- Pesan Notifikasi Sukses / Gagal Global -->
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

        <!-- Tempat Konten Admin -->
        <main class="p-3.5 sm:p-6 lg:p-8 flex-1">
            @yield('content')
        </main>
    </div>

    <!-- Script Kontrol Drawer Responsif Admin -->
    <script>
        function openAdminSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.remove('-translate-x-full');
                backdrop.classList.remove('hidden');
            }
        }
        function closeAdminSidebar() {
            const sidebar = document.getElementById('adminSidebar');
            const backdrop = document.getElementById('adminSidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.add('hidden');
            }
        }
    </script>
</body>
</html>