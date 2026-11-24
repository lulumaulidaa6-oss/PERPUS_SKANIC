@extends('layouts.siswa')

@section('title', 'Formulir Peminjaman Buku')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        
        <!-- Header Form Compact -->
        <div class="p-4 sm:p-5 bg-gradient-to-r from-blue-700 to-indigo-800 text-white flex items-center justify-between">
            <div>
                <h2 class="text-lg sm:text-xl font-extrabold tracking-tight">Formulir Peminjaman Buku</h2>
                <p class="text-[11px] sm:text-xs text-blue-100 mt-0.5">Layanan mandiri siswa tanpa perlu login. Cukup lengkapi data diri.</p>
            </div>
            <a href="/siswa/dashboard" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-md transition focus:outline-none focus:ring-2 focus:ring-white/40">
                <x-icon name="ArrowLeft" class="w-3.5 h-3.5" />
                <span>Kembali ke Katalog</span>
            </a>
        </div>

        <form action="/siswa/pinjam" method="POST" enctype="multipart/form-data" class="p-4 sm:p-6 space-y-4 sm:space-y-5">
            @csrf

            <!-- Info Petunjuk Compact -->
            <div class="p-3 sm:p-3.5 rounded-xl bg-blue-50/70 border border-blue-200/80 flex items-start space-x-2.5 text-xs text-blue-900">
                <x-icon name="Info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5" />
                <div class="leading-relaxed">
                    <span class="font-bold">Langkah Peminjaman:</span>
                    <span class="text-blue-800 ml-1">Setelah mengirim formulir, pengajuanmu akan masuk ke menu <strong>"Menunggu Persetujuan"</strong>. Begitu disetujui petugas, status akan berpindah ke <strong>"Sudah Di-ACC"</strong>.</span>
                </div>
            </div>

            <!-- Nama Siswa & Kelas -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Nama Lengkap Siswa <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="nama_siswa" 
                           value="{{ old('nama_siswa') }}" 
                           placeholder="Contoh: Lulu Maulida, Budi..." 
                           class="w-full px-3 py-2 bg-slate-50 hover:bg-white focus:bg-white border @error('nama_siswa') border-rose-500 @else border-slate-300 @enderror focus:border-blue-500 rounded-xl text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
                           required>
                    @error('nama_siswa')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Kelas <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="kelas" 
                           value="{{ old('kelas') }}" 
                           placeholder="Contoh: 11 PPLG 3, XII RPL 1..." 
                           class="w-full px-3 py-2 bg-slate-50 hover:bg-white focus:bg-white border @error('kelas') border-rose-500 @else border-slate-300 @enderror focus:border-blue-500 rounded-xl text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
                           required>
                    @error('kelas')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Pilih Buku -->
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Pilih Buku yang Ingin Dipinjam <span class="text-rose-500">*</span>
                </label>
                <select name="book_id" class="w-full px-3 py-2 bg-slate-50 hover:bg-white focus:bg-white border @error('book_id') border-rose-500 @else border-slate-300 @enderror focus:border-blue-500 rounded-xl text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10" required>
                    <option value="">-- Pilih Buku Tersedia --</option>
                    @foreach($books as $book)
                        <option value="{{ $book->id }}" {{ (old('book_id', $selectedBookId ?? '') == $book->id) ? 'selected' : '' }}>
                            {{ $book->judul }} (Penulis: {{ $book->penulis }} • Sisa Stok: {{ $book->stok }})
                        </option>
                    @endforeach
                </select>
                @error('book_id')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tanggal Pinjam & Perkiraan Kembali -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Tanggal Pinjam <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal_pinjam" 
                           value="{{ old('tanggal_pinjam', date('Y-m-d')) }}" 
                           class="w-full px-3 py-2 bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 focus:border-blue-500 rounded-xl text-xs sm:text-sm font-medium transition focus:outline-none" 
                           required>
                    @error('tanggal_pinjam')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                        Rencana Tanggal Pengembalian <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" 
                           name="tanggal_perkiraan_kembali" 
                           value="{{ old('tanggal_perkiraan_kembali', date('Y-m-d', strtotime('+7 days'))) }}" 
                           class="w-full px-3 py-2 bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 focus:border-blue-500 rounded-xl text-xs sm:text-sm font-medium transition focus:outline-none" 
                           required>
                    <p class="text-[10px] text-slate-400 mt-0.5">Standar masa pinjam adalah 7 hari.</p>
                    @error('tanggal_perkiraan_kembali')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Upload Bukti Foto / Kartu Pelajar -->
            <div class="pt-2 border-t border-slate-100">
                <label class="block text-[11px] font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Unggah Bukti Foto (Kartu Pelajar / Swafoto Meminjam) <span class="text-rose-500">*</span>
                </label>
                <div class="flex items-center space-x-3">
                    <input type="file" 
                           name="bukti_foto" 
                           accept="image/*" 
                           id="buktiFotoInput" 
                           onchange="previewBukti(event)"
                           class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3.5 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer" 
                           required>
                </div>
                <p class="text-[10px] text-slate-400 mt-0.5">Format gambar: JPG, PNG, JPEG. Maksimal 2MB.</p>
                @error('bukti_foto')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror

                <!-- Pratinjau Foto Compact -->
                <div id="buktiPreviewBox" class="mt-2.5 hidden">
                    <img id="buktiPreviewImg" src="" alt="Bukti Foto" class="h-24 sm:h-28 rounded-xl border border-slate-200 object-cover shadow-2xs">
                </div>
            </div>

            <!-- Tombol Kirim Pengajuan -->
            <div class="pt-3 border-t border-slate-100 flex items-center justify-end space-x-2.5">
                <a href="/siswa/dashboard" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition focus:outline-none focus:ring-2 focus:ring-slate-400">
                    <x-icon name="X" class="w-3.5 h-3.5" />
                    <span>Batal</span>
                </a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-5 sm:px-6 py-2 rounded-xl shadow-xs hover:shadow transition text-xs sm:text-sm flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <x-icon name="Save" class="w-3.5 h-3.5" />
                    <span>Kirim Pengajuan Peminjaman</span>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    function previewBukti(event) {
        const file = event.target.files[0];
        const previewBox = document.getElementById('buktiPreviewBox');
        const previewImg = document.getElementById('buktiPreviewImg');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewBox.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection