@extends('layouts.app')

@section('title', 'Pengaturan Akun Admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-12 -bottom-12 w-64 h-64 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute right-1/4 -top-12 w-48 h-48 bg-indigo-500/20 rounded-full blur-xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-white/10 backdrop-blur-md rounded-full text-xs font-semibold text-blue-200 mb-3 border border-white/10">
                    <x-icon name="Settings" class="w-3.5 h-3.5 text-blue-400" />
                    <span>Keamanan & Profil Petugas</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Pengaturan Akun Petugas / Admin</h2>
                <p class="text-slate-300 text-sm sm:text-base mt-2 leading-relaxed">
                    Ubah username login dan perbarui password akun Anda secara mandiri. Password disimpan dengan enkripsi hashing standar Laravel.
                </p>
            </div>
            <div>
                <a href="/admin/dashboard" class="inline-flex items-center space-x-2 bg-white/15 hover:bg-white/25 text-white font-semibold text-xs sm:text-sm px-4 py-2.5 rounded-xl backdrop-blur-md border border-white/20 transition duration-200 focus:outline-none focus:ring-2 focus:ring-white/30">
                    <x-icon name="ArrowLeft" class="w-3.5 h-3.5" />
                    <span>Kembali ke Dashboard</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-start space-x-3 shadow-xs" role="alert">
            <x-icon name="CircleCheck" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" />
            <div>
                <p class="font-bold">Sukses!</p>
                <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-start space-x-3 shadow-xs" role="alert">
            <x-icon name="AlertCircle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5" />
            <div>
                <p class="font-bold">Terjadi kesalahan pada isian formulir:</p>
                <ul class="list-disc list-inside text-xs text-rose-700 mt-1 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Kartu Formulir Pengaturan Akun -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold text-base">
                    <x-icon name="Users" class="w-5 h-5 text-blue-600" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-800">Ubah Identitas Akun & Sandi</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Gunakan kombinasi password yang kuat untuk menjaga keamanan data perpustakaan.</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                Role: {{ ucfirst($user->role) }}
            </span>
        </div>

        <form action="{{ route('admin.pengaturan-akun.update') }}" method="POST" class="p-5 sm:p-7 space-y-5" autocomplete="off">
            @csrf
            @method('PUT')

            <!-- Informasi Profil Akun -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Nama Lengkap -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Nama Lengkap Petugas <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           value="{{ old('name', $user->name) }}" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border @error('name') border-rose-500 @else border-slate-300 @enderror focus:border-blue-500 rounded-xl text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
                           required>
                    @error('name')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Username (Digunakan saat login) -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center justify-between">
                        <span>Username Login <span class="text-rose-500">*</span></span>
                        <span class="text-[10px] text-slate-400 font-normal">Digunakan saat masuk sistem</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <span class="text-xs font-bold">@</span>
                        </div>
                        <input type="text" 
                               name="username" 
                               value="{{ old('username', $user->username) }}" 
                               class="w-full pl-8 pr-3.5 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border @error('username') border-rose-500 @else border-slate-300 @enderror focus:border-blue-500 rounded-xl text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
                               required>
                    </div>
                    @error('username')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Email (Opsional) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Alamat Email (Opsional)
                </label>
                <input type="email" 
                       name="email" 
                       value="{{ old('email', $user->email) }}" 
                       placeholder="admin@perpus.sch.id"
                       class="w-full px-3.5 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border @error('email') border-rose-500 @else border-slate-300 @enderror focus:border-blue-500 rounded-xl text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                @error('email')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Divider Penggantian Password -->
            <div class="pt-4 border-t border-slate-100">
                <div class="flex items-center space-x-2 text-slate-800 mb-1">
                    <x-icon name="Lock" class="w-4 h-4 text-amber-500" />
                    <h4 class="font-bold text-sm">Ganti Password Admin</h4>
                </div>
                <p class="text-xs text-slate-400">Kosongkan bagian password di bawah jika Anda hanya ingin memperbarui nama atau username.</p>
            </div>

            <!-- Password Saat Ini (Lama) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5 flex items-center justify-between">
                    <span>Password Saat Ini (Lama)</span>
                    <span class="text-[10px] text-slate-400 font-normal">Password saat ini: <strong class="text-blue-600 font-semibold">admin123</strong></span>
                </label>
                <div class="relative">
                    <input type="password" 
                           name="current_password" 
                           id="currentPasswordInput"
                           autocomplete="new-password"
                           placeholder="Masukkan password saat ini (admin123)..."
                           class="w-full pl-3.5 pr-10 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border @error('current_password') border-rose-500 @else border-slate-300 @enderror focus:border-blue-500 rounded-xl text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                    <button type="button" 
                            onclick="togglePasswordVisibility('currentPasswordInput')" 
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                            title="Lihat password selama 3 detik"
                            aria-label="Tampilkan atau sembunyikan password saat ini">
                        <x-icon name="Eye" class="w-4 h-4" />
                    </button>
                </div>
                @error('current_password')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Password Baru -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Password Baru
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password" 
                               id="newPasswordInput"
                               autocomplete="new-password"
                               placeholder="Minimal 6 karakter"
                               class="w-full pl-3.5 pr-10 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border @error('password') border-rose-500 @else border-slate-300 @enderror focus:border-blue-500 rounded-xl text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                        <button type="button" 
                                onclick="togglePasswordVisibility('newPasswordInput')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                                title="Lihat password selama 3 detik"
                                aria-label="Tampilkan atau sembunyikan password baru">
                            <x-icon name="Eye" class="w-4 h-4" />
                        </button>
                    </div>
                    @error('password')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Konfirmasi Password Baru -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Ulangi Password Baru
                    </label>
                    <div class="relative">
                        <input type="password" 
                               name="password_confirmation" 
                               id="confirmPasswordInput"
                               autocomplete="new-password"
                               placeholder="Ulangi password baru persis"
                               class="w-full pl-3.5 pr-10 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 focus:border-blue-500 rounded-xl text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10">
                        <button type="button" 
                                onclick="togglePasswordVisibility('confirmPasswordInput')" 
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none"
                                title="Lihat password selama 3 detik"
                                aria-label="Tampilkan atau sembunyikan konfirmasi password">
                            <x-icon name="Eye" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tombol Simpan -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <span class="text-xs text-slate-400">Password disimpan aman menggunakan hashing otomatis.</span>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md shadow-blue-600/20 hover:shadow-lg transition text-xs sm:text-sm flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-blue-500/30">
                    <x-icon name="Save" class="w-4 h-4" />
                    <span>Simpan Perubahan Akun</span>
                </button>
            </div>

        </form>
    </div>

</div>

<script>
    const passwordTimeouts = {};

    function togglePasswordVisibility(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;

        if (input.type === 'password') {
            input.type = 'text';

            if (passwordTimeouts[inputId]) {
                clearTimeout(passwordTimeouts[inputId]);
            }

            passwordTimeouts[inputId] = setTimeout(function() {
                if (input) {
                    input.type = 'password';
                }
            }, 3000);
        } else {
            input.type = 'password';
            if (passwordTimeouts[inputId]) {
                clearTimeout(passwordTimeouts[inputId]);
                delete passwordTimeouts[inputId];
            }
        }
    }

    // Pastikan browser tidak mengisikan password lama secara otomatis ke dalam input
    document.addEventListener('DOMContentLoaded', function() {
        const currentPass = document.getElementById('currentPasswordInput');
        if (currentPass) {
            // Bersihkan jika browser mengisi kata sandi lama secara otomatis
            setTimeout(function() {
                if (currentPass.value === 'password') {
                    currentPass.value = '';
                }
            }, 100);
        }
    });
</script>
@endsection
