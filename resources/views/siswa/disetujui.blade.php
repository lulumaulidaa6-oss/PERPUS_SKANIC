@extends('layouts.siswa')

@section('title', 'Peminjaman Sudah Disetujui (Di-ACC)')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-emerald-600 via-teal-700 to-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-semibold text-emerald-100 mb-3 border border-white/10">
                    <x-icon name="CircleCheck" class="w-3.5 h-3.5 text-emerald-300" />
                    <span>Status: Di-ACC & Sedang Dipinjam</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Peminjaman Sudah Di-ACC</h2>
                <p class="text-emerald-100/90 mt-2 text-sm sm:text-base max-w-2xl leading-relaxed">
                    Daftar buku yang sudah disetujui petugas dan sedang aktif kamu pinjam. Pastikan mengembalikan buku sebelum jatuh tempo agar bebas denda.
                </p>
            </div>
            <div class="flex flex-wrap gap-2.5">
                <a href="/siswa/menunggu" class="bg-white/15 hover:bg-white/25 text-white px-4 py-2.5 rounded-xl backdrop-blur-md border border-white/20 font-semibold text-sm transition flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-white/40">
                    <span>Lihat yang Menunggu ACC</span>
                    <x-icon name="Clock" class="w-4 h-4" />
                </a>
            </div>
        </div>
    </div>

    <!-- Kotak Pencarian Siswa -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-5">
        <form action="/siswa/disetujui" method="GET">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="Search" class="w-5 h-5" />
                    </div>
                    <input type="text" 
                           name="nama" 
                           value="{{ $nama_pencarian ?? '' }}" 
                           placeholder="Ketik namamu atau kelas untuk melihat buku yang kamu pinjam..." 
                           class="w-full pl-10 pr-10 py-3 bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 focus:border-emerald-500 rounded-xl text-slate-800 text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-emerald-500/10">
                    @if(!empty($nama_pencarian))
                        <a href="/siswa/disetujui" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" aria-label="Hapus kata kunci pencarian" title="Hapus kata kunci pencarian">
                            <x-icon name="X" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-6 py-3 rounded-xl shadow-sm text-sm shrink-0 flex items-center justify-center space-x-2 transition focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <x-icon name="Search" class="w-4 h-4" />
                    <span>Cari Buku Pinjaman Saya</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Daftar Buku yang Sudah Di-ACC (Aktif Dipinjam) -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6">
        <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-bold text-slate-800">
                    Buku yang Sedang Dipinjam Siswa (Sudah Di-ACC)
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">Segera kembalikan ke meja perpustakaan sebelum batas tanggal berakhir.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                {{ $peminjaman->count() }} Buku Aktif
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-4">
            @forelse($peminjaman as $item)
            <div class="p-3.5 sm:p-4 rounded-2xl border {{ $item->status === 'menunggu_pengembalian' ? 'border-amber-300 bg-amber-50/20' : ($item->is_terlambat ? 'border-rose-300 bg-rose-50/20' : 'border-emerald-200/80 bg-emerald-50/20') }} hover:shadow-xs transition flex flex-col justify-between space-y-3">
                <div class="flex items-start space-x-3.5">
                    <!-- Foto Cover Buku (Compact & Proporsional) -->
                    <div class="w-14 h-20 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center shadow-2xs">
                        @if($item->book && $item->book->foto)
                            <img src="{{ asset('storage/' . $item->book->foto) }}" alt="{{ $item->book->judul }}" class="w-full h-full object-cover">
                        @else
                            <x-icon name="BookOpen" class="w-7 h-7 text-slate-300" />
                        @endif
                    </div>

                    <!-- Informasi Judul & Peminjam -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-1.5 mb-1">
                            @if($item->status === 'menunggu_pengembalian')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500 text-white shadow-2xs animate-pulse">
                                    <x-icon name="Clock" class="w-3 h-3 mr-1" />
                                    <span>Menunggu Pengembalian</span>
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                    <x-icon name="BookOpen" class="w-3 h-3 mr-1" />
                                    <span>Sedang Dipinjam</span>
                                </span>
                            @endif

                            @if($item->is_terlambat)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-rose-600 text-white animate-pulse">
                                    <x-icon name="Clock" class="w-3 h-3 mr-1" />
                                    <span>Terlambat {{ $item->hari_terlambat }} Hari</span>
                                </span>
                            @elseif($item->sisa_hari !== null && $item->status === 'dipinjam')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-600">
                                    Sisa {{ $item->sisa_hari }} hari lagi
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
                        
                        <div class="mt-1.5 text-xs text-slate-500 space-y-0.5">
                            <p>Mulai Pinjam: <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y') }}</span></p>
                            <p>Batas Tempo: <span class="font-bold {{ $item->is_terlambat ? 'text-rose-600' : 'text-slate-800' }}">{{ \Carbon\Carbon::parse($item->tanggal_perkiraan_kembali)->translatedFormat('d M Y') }}</span></p>
                            @if($item->tanggal_pengajuan_kembali)
                                <p class="text-amber-700 font-medium">Diajukan Kembali: <span>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan_kembali)->translatedFormat('d M Y') }}</span></p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Bagian Tombol Aksi & Status Pengembalian -->
                <div class="pt-2.5 border-t {{ $item->status === 'menunggu_pengembalian' ? 'border-amber-200' : ($item->is_terlambat ? 'border-rose-200' : 'border-emerald-200/60') }} space-y-2">
                    
                    <div class="flex items-center justify-between text-xs">
                        @if($item->is_terlambat)
                            <span class="text-rose-600 font-bold flex items-center">
                                Denda berjalan: {{ $item->formatted_denda }}
                            </span>
                            <a href="/siswa/denda?nama={{ urlencode($item->nama_siswa) }}" class="inline-flex items-center gap-1 text-rose-700 underline font-semibold text-[11px] focus:outline-none">
                                <span>Cek Denda</span>
                                <x-icon name="ArrowLeft" class="w-3 h-3 rotate-180" />
                            </a>
                        @else
                            <span class="text-emerald-700 font-medium flex items-center">
                                <x-icon name="CircleCheck" class="w-3.5 h-3.5 mr-1 text-emerald-600" />
                                <span>Bebas denda keterlambatan</span>
                            </span>
                            <span class="text-[11px] text-slate-400">ID: #{{ $item->id }}</span>
                        @endif
                    </div>

                    @if($item->status === 'dipinjam')
                        <!-- Tombol Ajukan Pengembalian Buku (Compact & Jelas) -->
                        <button type="button" 
                                onclick="openModalAjukanKembali({{ $item->id }}, '{{ addslashes($item->book->judul ?? 'Buku') }}', '{{ addslashes($item->nama_siswa) }}', '{{ $item->kelas }}', '{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y') }}', '{{ \Carbon\Carbon::parse($item->tanggal_perkiraan_kembali)->translatedFormat('d M Y') }}')" 
                                class="w-full py-2 px-3.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold rounded-xl shadow-2xs hover:shadow text-xs sm:text-sm flex items-center justify-center space-x-1.5 transition duration-150 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <x-icon name="Upload" class="w-4 h-4" />
                            <span>Ajukan Pengembalian Buku</span>
                        </button>
                    @elseif($item->status === 'menunggu_pengembalian')
                        <!-- Tampilan Jika Sedang Menunggu Verifikasi Admin -->
                        <div class="bg-amber-50 border border-amber-200/90 rounded-xl p-2.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div>
                                <p class="text-xs font-bold text-amber-900 flex items-center">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5 animate-ping"></span>
                                    <span>Menunggu Pemeriksaan Petugas</span>
                                </p>
                                <p class="text-[11px] text-amber-700 mt-0.5">
                                    Silakan serahkan fisik buku ke meja petugas perpustakaan.
                                </p>
                            </div>
                            @if($item->foto_kembali)
                                <button type="button" 
                                        onclick="previewFotoSiswa('{{ asset('storage/' . $item->foto_kembali) }}', 'Bukti Pengembalian - {{ addslashes($item->book->judul ?? 'Buku') }}')" 
                                        class="shrink-0 px-2.5 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-bold text-xs shadow-2xs inline-flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    <x-icon name="Eye" class="w-3.5 h-3.5" />
                                    <span>Lihat Foto</span>
                                </button>
                            @endif
                        </div>
                    @endif

                </div>
            </div>
            @empty
            <div class="col-span-full p-8 sm:p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200 flex items-center justify-center mx-auto mb-3">
                    <x-icon name="BookOpen" class="w-7 h-7 text-emerald-600" />
                </div>
                <h4 class="text-base font-bold text-slate-700">Tidak Ada Buku yang Sedang Dipinjam</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Kamu sedang tidak memiliki buku yang dipinjam atau pengajuanmu masih menunggu persetujuan.
                </p>
                <div class="mt-4 flex flex-wrap justify-center gap-2">
                    <a href="/siswa/menunggu" class="inline-flex items-center gap-1 px-3.5 py-2 bg-amber-50 text-amber-800 hover:bg-amber-100 border border-amber-200 font-semibold text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-amber-400">
                        <x-icon name="Clock" class="w-3.5 h-3.5" />
                        <span>Cek Antrean Menunggu ACC</span>
                    </a>
                    <a href="/siswa/dashboard" class="inline-flex items-center gap-1 px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <x-icon name="BookOpen" class="w-3.5 h-3.5" />
                        <span>Lihat Katalog & Pinjam Buku</span>
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>

</div>

<!-- MODAL FORMULIR AJUKAN PENGEMBALIAN BUKU DENGAN UPLOAD FOTO KONDISI (Compact, Rapi & Proporsional) -->
<div id="modalAjukanKembali" class="fixed inset-0 z-50 hidden bg-slate-900/75 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl sm:rounded-3xl max-w-md w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200 max-h-[92vh] flex flex-col">
        <!-- Header Modal Compact -->
        <div class="p-3.5 sm:p-4 bg-gradient-to-r from-emerald-600 via-teal-700 to-slate-900 text-white flex items-center justify-between shrink-0">
            <div class="flex items-center space-x-2.5">
                <div class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center shadow-xs shrink-0">
                    <x-icon name="Upload" class="w-4 h-4 text-white" />
                </div>
                <div>
                    <h4 class="font-bold text-sm sm:text-base">Ajukan Pengembalian Buku</h4>
                    <p class="text-[11px] text-emerald-100">Upload bukti foto fisik buku saat dikembalikan</p>
                </div>
            </div>
            <button type="button" onclick="closeModalAjukanKembali()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition focus:outline-none focus:ring-2 focus:ring-white/40" aria-label="Tutup modal ajukan pengembalian">
                <x-icon name="X" class="w-4 h-4" />
            </button>
        </div>

        <!-- Body Form Modal Compact -->
        <form id="formAjukanKembali" method="POST" action="" enctype="multipart/form-data" class="overflow-y-auto flex-1 p-4 sm:p-5 space-y-3 scrollbar-thin">
            @csrf

            <!-- Ringkasan Buku yang Akan Dikembalikan -->
            <div class="p-3 bg-slate-50 border border-slate-200/80 rounded-xl space-y-1 text-xs text-slate-600">
                <div class="flex justify-between items-center">
                    <span class="text-slate-500">Buku:</span>
                    <strong id="modalKembaliJudul" class="text-slate-800 text-right font-bold truncate max-w-[220px]">-</strong>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500">Peminjam:</span>
                    <strong id="modalKembaliPeminjam" class="text-slate-800 font-semibold">-</strong>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-500">Jadwal:</span>
                    <span id="modalKembaliJadwal" class="text-slate-700 font-medium text-[11px]">-</span>
                </div>
            </div>

            <!-- Upload Foto Kondisi Buku (Wajib) Compact -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1 flex items-center justify-between">
                    <span>Foto Kondisi Buku <span class="text-rose-500">*</span></span>
                    <span class="text-[10px] text-slate-400 font-normal">Wajib diunggah</span>
                </label>
                
                <div class="border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-xl p-3 text-center bg-slate-50/50 transition">
                    <input type="file" 
                           name="foto_kembali" 
                           id="inputFotoKembali" 
                           accept="image/*" 
                           required 
                           onchange="previewUploadFoto(event)" 
                           class="hidden">

                    <div id="uploadPrompt" class="cursor-pointer py-2" onclick="document.getElementById('inputFotoKembali').click()">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto mb-1.5 text-base">
                            <x-icon name="Eye" class="w-5 h-5 text-emerald-600" />
                        </div>
                        <p class="text-xs font-bold text-slate-700">Klik untuk Unggah Foto Buku</p>
                        <p class="text-[10px] text-slate-400 mt-0.5">Foto jelas kondisi fisik buku (Maks. 3 MB)</p>
                    </div>

                    <!-- Tempat Preview Gambar Compact -->
                    <div id="previewContainer" class="hidden text-center space-y-1.5">
                        <img id="imagePreview" src="" alt="Pratinjau Foto" class="max-h-36 sm:max-h-40 mx-auto rounded-lg shadow-2xs border border-slate-200 object-contain">
                        <div class="flex justify-center space-x-2 pt-1">
                            <button type="button" onclick="document.getElementById('inputFotoKembali').click()" class="text-xs text-blue-600 hover:underline font-semibold focus:outline-none">
                                Ganti Foto
                            </button>
                            <span class="text-slate-300">•</span>
                            <button type="button" onclick="resetUploadFoto()" class="text-xs text-rose-600 hover:underline font-semibold focus:outline-none">
                                Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Catatan Kondisi Buku (Opsional) -->
            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                    Catatan Kondisi Buku (Opsional)
                </label>
                <textarea name="catatan_kembali" 
                          rows="2" 
                          placeholder="Contoh: Buku rapi, tidak ada halaman sobek..." 
                          class="w-full px-3 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-500/10"></textarea>
            </div>

            <!-- Konfirmasi Pengembalian Checklist Compact -->
            <div class="p-2.5 bg-emerald-50/70 border border-emerald-200 rounded-xl text-xs text-emerald-900 flex items-start space-x-2">
                <input type="checkbox" id="checkKonfirmasi" required class="mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                <label for="checkKonfirmasi" class="cursor-pointer text-[11px] leading-snug select-none">
                    Saya menyatakan foto adalah <strong>kondisi buku yang sebenarnya</strong> dan saya siap membawa buku fisik ke meja perpustakaan untuk dikonfirmasi.
                </label>
            </div>

            <!-- Tombol Aksi Compact -->
            <div class="pt-2 border-t border-slate-100 flex items-center justify-end space-x-2">
                <button type="button" onclick="closeModalAjukanKembali()" class="px-3.5 py-2 border border-slate-300 hover:bg-slate-100 text-slate-600 font-semibold text-xs rounded-xl transition focus:outline-none focus:ring-2 focus:ring-slate-400">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <x-icon name="CircleCheck" class="w-3.5 h-3.5" />
                    <span>Konfirmasi & Ajukan</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL PREVIEW FOTO KONDISI BUKU YANG SUDAH DIUNGGAH -->
<div id="modalPreviewFotoSiswa" class="fixed inset-0 z-50 hidden bg-slate-900/75 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-3.5 border-b border-slate-100 flex items-center justify-between">
            <h4 id="previewFotoSiswaTitle" class="font-bold text-slate-800 text-xs sm:text-sm truncate">Foto Kondisi Buku</h4>
            <button onclick="closePreviewFotoSiswa()" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center focus:outline-none focus:ring-2 focus:ring-slate-400" aria-label="Tutup modal preview foto siswa">
                <x-icon name="X" class="w-4 h-4" />
            </button>
        </div>
        <div class="p-3.5 bg-slate-100 flex items-center justify-center max-h-[65vh] overflow-auto">
            <img id="previewFotoSiswaImg" src="" alt="Foto Kondisi" class="max-w-full max-h-[55vh] rounded-lg shadow-xs object-contain">
        </div>
        <div class="p-2.5 bg-white border-t border-slate-100 text-right">
            <button onclick="closePreviewFotoSiswa()" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-500">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function openModalAjukanKembali(id, judul, nama, kelas, tglPinjam, tglTempo) {
        document.getElementById('formAjukanKembali').action = '/siswa/peminjaman/' + id + '/kembalikan';
        document.getElementById('modalKembaliJudul').innerText = judul;
        document.getElementById('modalKembaliPeminjam').innerText = nama + ' (' + kelas + ')';
        document.getElementById('modalKembaliJadwal').innerText = tglPinjam + ' s/d ' + tglTempo;
        resetUploadFoto();
        document.getElementById('checkKonfirmasi').checked = false;
        document.getElementById('modalAjukanKembali').classList.remove('hidden');
    }

    function closeModalAjukanKembali() {
        document.getElementById('modalAjukanKembali').classList.add('hidden');
    }

    function previewUploadFoto(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('uploadPrompt').classList.add('hidden');
                document.getElementById('previewContainer').classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    function resetUploadFoto() {
        const input = document.getElementById('inputFotoKembali');
        input.value = '';
        document.getElementById('imagePreview').src = '';
        document.getElementById('previewContainer').classList.add('hidden');
        document.getElementById('uploadPrompt').classList.remove('hidden');
    }

    function previewFotoSiswa(url, title) {
        document.getElementById('previewFotoSiswaImg').src = url;
        document.getElementById('previewFotoSiswaTitle').innerText = title;
        document.getElementById('modalPreviewFotoSiswa').classList.remove('hidden');
    }

    function closePreviewFotoSiswa() {
        document.getElementById('modalPreviewFotoSiswa').classList.add('hidden');
    }

    // Backdrop click
    ['modalAjukanKembali', 'modalPreviewFotoSiswa'].forEach(function(modalId) {
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
