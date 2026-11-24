@extends('layouts.app')

@section('title', 'Edit Data Buku')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 overflow-hidden">
        
        <!-- Header Form -->
        <div class="p-6 bg-gradient-to-r from-slate-900 to-indigo-950 text-white flex items-center justify-between">
            <div>
                <h2 class="text-xl font-extrabold tracking-tight">Edit Data Buku</h2>
                <p class="text-xs text-slate-300 mt-1">Perbarui informasi buku atau ganti foto sampul (cover).</p>
            </div>
            <a href="/buku" class="px-3.5 py-1.5 rounded-xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-md transition inline-flex items-center space-x-1 focus:outline-none focus:ring-2 focus:ring-white/30">
                <x-icon name="ArrowLeft" class="w-3.5 h-3.5" />
                <span>Batal & Kembali</span>
            </a>
        </div>

        <!-- Form Edit Buku -->
        <form action="/buku/{{ $book->id }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Judul Buku -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Judul Buku <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="judul" 
                       value="{{ old('judul', $book->judul) }}" 
                       class="w-full px-3.5 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border @error('judul') border-rose-500 @else border-slate-300 @enderror focus:border-blue-500 rounded-xl text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
                       required>
                @error('judul')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Penulis & Penerbit (2 Kolom di Desktop) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Penulis <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="penulis" 
                           value="{{ old('penulis', $book->penulis) }}" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border @error('penulis') border-rose-500 @else border-slate-300 @enderror focus:border-blue-500 rounded-xl text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
                       required>
                    @error('penulis')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        Penerbit <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="penerbit" 
                           value="{{ old('penerbit', $book->penerbit) }}" 
                           class="w-full px-3.5 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border @error('penerbit') border-rose-500 @else border-slate-300 @enderror focus:border-blue-500 rounded-xl text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
                       required>
                    @error('penerbit')
                        <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Stok Buku -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                    Jumlah Stok Buku <span class="text-rose-500">*</span>
                </label>
                <input type="number" 
                       name="stok" 
                       value="{{ old('stok', $book->stok) }}" 
                       min="0" 
                       class="w-full sm:w-48 px-3.5 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border @error('stok') border-rose-500 @else border-slate-300 @enderror focus:border-blue-500 rounded-xl text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-blue-500/10" 
                       required>
                @error('stok')
                    <p class="text-xs text-rose-500 mt-1 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- FOTO COVER BUKU -->
            <div class="pt-2 border-t border-slate-100">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                        Foto Sampul (Cover Buku)
                    </label>
                    <span class="text-[11px] text-slate-400">Kosongkan jika tidak ingin mengganti cover</span>
                </div>

                <div class="flex flex-col sm:flex-row gap-5 items-start">
                    <!-- Area Upload Input -->
                    <div class="flex-1 w-full">
                        <label for="fotoEditInput" class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed @error('foto') border-rose-400 bg-rose-50/20 @else border-slate-300 hover:border-blue-500 bg-slate-50 hover:bg-blue-50/20 @enderror rounded-2xl cursor-pointer transition p-4 text-center group">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                                    <x-icon name="Upload" class="w-6 h-6 text-blue-600" />
                                </div>
                                <p class="text-xs font-bold text-slate-700 group-hover:text-blue-600 transition">
                                    Pilih cover baru untuk mengganti foto
                                </p>
                                <p class="text-[11px] text-slate-500 mt-1" id="fileEditInfoText">
                                    Maksimal 2MB (JPG, PNG, JPEG, WEBP)
                                </p>
                            </div>
                            <input id="fotoEditInput" 
                                   type="file" 
                                   name="foto" 
                                   accept="image/*" 
                                   class="hidden" 
                                   onchange="previewEditCoverImage(event)">
                        </label>
                        @error('foto')
                            <p class="text-xs text-rose-500 mt-1.5 font-bold flex items-center">
                                <x-icon name="AlertCircle" class="w-3.5 h-3.5 mr-1" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Kotak Pratinjau (Preview) Cover Saat Ini / Baru -->
                    <div class="w-32 h-44 rounded-2xl border border-slate-200 bg-slate-100 flex flex-col items-center justify-center overflow-hidden shrink-0 shadow-xs relative">
                        @if($book->foto)
                            <img id="coverEditPreviewImg" src="{{ asset('storage/' . $book->foto) }}" alt="{{ $book->judul }}" class="w-full h-full object-cover">
                            <span id="labelCoverStatus" class="absolute bottom-1 right-1 bg-slate-900/70 text-white text-[9px] px-1.5 py-0.5 rounded font-medium">Cover Saat Ini</span>
                        @else
                            <img id="coverEditPreviewImg" src="" alt="Pratinjau Cover" class="w-full h-full object-cover hidden">
                            <div id="coverEditPlaceholder" class="flex flex-col items-center justify-center p-3 text-center text-slate-400">
                                <x-icon name="BookOpen" class="w-8 h-8 mb-1 opacity-50" />
                                <span class="text-[10px] font-semibold leading-tight">Belum Ada Cover</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Tombol Submit -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end space-x-3">
                <a href="/buku" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 font-semibold text-xs hover:bg-slate-50 transition inline-flex items-center space-x-1 focus:outline-none focus:ring-2 focus:ring-slate-400/30">
                    <x-icon name="X" class="w-3.5 h-3.5" />
                    <span>Batal</span>
                </a>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-7 py-2.5 rounded-xl shadow-md shadow-indigo-600/20 hover:shadow-lg transition duration-150 text-xs sm:text-sm flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/30">
                    <x-icon name="Save" class="w-4 h-4" />
                    <span>Simpan Perubahan</span>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    function previewEditCoverImage(event) {
        const file = event.target.files[0];
        const preview = document.getElementById('coverEditPreviewImg');
        const placeholder = document.getElementById('coverEditPlaceholder');
        const infoText = document.getElementById('fileEditInfoText');
        const labelStatus = document.getElementById('labelCoverStatus');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                if (placeholder) placeholder.classList.add('hidden');
                if (labelStatus) labelStatus.innerText = 'Foto Baru';
                infoText.innerText = 'File baru dipilih: ' + file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
                infoText.classList.add('text-indigo-600', 'font-semibold');
            };
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection