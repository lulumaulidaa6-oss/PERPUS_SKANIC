<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas - Sistem Perpustakaan</title>

    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind CSS CDN -->

    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* Sembunyikan icon mata  */
        input::-ms-reveal,
        input::-ms-clear,
        ::-ms-reveal,
        ::-ms-clear {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
            pointer-events: none !important;
        }
    </style>
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center p-4 text-slate-800 antialiased">

    <div class="bg-white rounded-2xl shadow-xl border border-slate-200/80 p-6 sm:p-8 w-full max-w-md relative overflow-hidden">
        <!-- Accent top border -->
        <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-500"></div>

        <div class="text-center mb-6">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center mx-auto mb-3 shadow-xs">
                <x-icon name="BookOpen" class="w-7 h-7 text-blue-600" />
            </div>
            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight">Login Petugas Perpustakaan</h1>
            <p class="text-xs text-slate-500 mt-1">
                Khusus admin & petugas sirkulasi PerpusAdmin.
            </p>
        </div>

        <!-- Pesan info untuk siswa -->
        <div class="bg-blue-50 text-blue-800 border border-blue-200 p-3.5 rounded-xl text-xs mb-5 flex items-start gap-2.5 leading-relaxed">
            <x-icon name="Info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" />
            <div>
                <strong class="font-bold">Siswa tidak perlu login!</strong> Kamu bisa langsung meminjam buku dan mengecek riwayat & denda tanpa akun.
            </div>
        </div>
        
        <!-- Menampilkan pesan error jika username/password salah -->
        @if($errors->any())
            <div class="bg-rose-50 text-rose-700 border border-rose-200 p-3 rounded-xl text-xs mb-4 flex items-center gap-2">
                <x-icon name="AlertCircle" class="w-4 h-4 text-rose-600 shrink-0" />
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="/login" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Username Petugas
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="Users" class="w-4 h-4" />
                    </div>
                    <input type="text" 
                           id="username"
                           name="username" 
                           placeholder="Masukkan username admin..." 
                           class="w-full pl-10 pr-3.5 py-2.5 bg-slate-50 border border-slate-300 focus:bg-white focus:border-blue-500 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition"
                           required 
                           autofocus>
                </div>
            </div>

            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Password
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="Lock" class="w-4 h-4" />
                    </div>
                    <input type="password" 
                           id="password"
                           name="password" 
                           placeholder="Masukkan password..." 
                           class="w-full pl-10 pr-10 py-2.5 bg-slate-50 border border-slate-300 focus:bg-white focus:border-blue-500 rounded-xl text-sm font-medium text-slate-800 focus:outline-none focus:ring-4 focus:ring-blue-500/10 transition"
                           required>
                    <button type="button"
                            id="togglePasswordBtn"
                            onclick="togglePasswordVisibility()"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none focus:text-blue-600 transition"
                            title="Lihat password selama 3 detik"
                            aria-label="Lihat password selama 3 detik">
                        <span id="eyeIcon">
                            <x-icon name="Eye" class="w-4 h-4" />
                        </span>
                        <span id="eyeOffIcon" class="hidden" style="display: none;">
                            <x-icon name="EyeOff" class="w-4 h-4 text-blue-600" />
                        </span>
                    </button>
                </div>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-bold text-sm rounded-xl shadow-md shadow-blue-600/20 transition flex items-center justify-center gap-2 focus:outline-none focus:ring-4 focus:ring-blue-500/20">
                <x-icon name="LogIn" class="w-4 h-4" />
                <span>Masuk Sebagai Petugas</span>
            </button>
        </form>

        <div class="text-center mt-6 pt-5 border-t border-slate-100">
            <a href="/siswa/dashboard" class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-800 font-semibold text-xs transition focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">
                <x-icon name="ArrowLeft" class="w-3.5 h-3.5" />
                <span>Kembali ke Halaman Siswa / Katalog</span>
            </a>
        </div>
    </div>

    <script>
        let passwordTimeout = null;

        function togglePasswordVisibility() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            const btn = document.getElementById('togglePasswordBtn');

            if (!passwordInput) return;

            if (passwordInput.type === 'password') {
                // Tampilkan password dalam format teks
                passwordInput.type = 'text';
                if (eyeIcon && eyeOffIcon) {
                    eyeIcon.style.display = 'none';
                    eyeOffIcon.style.display = 'inline-block';
                    eyeIcon.classList.add('hidden');
                    eyeOffIcon.classList.remove('hidden');
                }
                if (btn) {
                    btn.setAttribute('title', 'Sembunyikan password');
                    btn.setAttribute('aria-label', 'Sembunyikan password');
                }

                // Bersihkan timer lama jika masih ada
                if (passwordTimeout) {
                    clearTimeout(passwordTimeout);
                }

                // Sembunyikan kembali secara otomatis setelah 3 detik
                passwordTimeout = setTimeout(function () {
                    hidePassword();
                }, 3000);
            } else {
                // Jika sedang terlihat dan diklik lagi, sembunyikan segera
                hidePassword();
            }
        }

        function hidePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            const btn = document.getElementById('togglePasswordBtn');

            if (passwordTimeout) {
                clearTimeout(passwordTimeout);
                passwordTimeout = null;
            }

            if (passwordInput) {
                passwordInput.type = 'password';
            }
            if (eyeIcon && eyeOffIcon) {
                eyeIcon.style.display = 'inline-block';
                eyeOffIcon.style.display = 'none';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
            if (btn) {
                btn.setAttribute('title', 'Lihat password selama 3 detik');
                btn.setAttribute('aria-label', 'Lihat password selama 3 detik');
            }
        }
    </script>
</body>
</html>