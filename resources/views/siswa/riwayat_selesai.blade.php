@extends('layouts.siswa')

@section('title', 'Riwayat Buku yang Pernah Dipinjam')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

    <!-- Header Banner Compact -->
    <div class="bg-gradient-to-r from-indigo-700 via-blue-800 to-slate-900 rounded-2xl sm:rounded-3xl p-5 sm:p-6 text-white shadow-lg relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-2.5 py-1 bg-white/20 backdrop-blur-md rounded-full text-[11px] font-semibold text-blue-100 mb-2 border border-white/10">
                    <x-icon name="History" class="w-3.5 h-3.5 text-blue-300" />
                    <span>Arsip Peminjaman Selesai</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold tracking-tight">Riwayat Buku yang Pernah Dipinjam</h2>
                <p class="text-blue-100/90 mt-1 text-xs sm:text-sm max-w-2xl leading-relaxed">
                    Daftar buku-buku perpustakaan yang telah selesai kamu pinjam dan sudah dikembalikan ke perpustakaan.
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="/siswa/dashboard" class="bg-white/15 hover:bg-white/25 text-white px-3.5 py-2 rounded-xl backdrop-blur-md border border-white/20 font-semibold text-xs sm:text-sm transition flex items-center space-x-1.5 focus:outline-none focus:ring-2 focus:ring-white/40">
                    <span>Pinjam Buku Lain</span>
                    <x-icon name="BookOpen" class="w-3.5 h-3.5" />
                </a>
            </div>
        </div>
    </div>

    <!-- Kotak Pencarian Siswa Compact -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-3.5 sm:p-4">
        <form action="/siswa/riwayat-selesai" method="GET">
            <div class="flex flex-col sm:flex-row gap-2.5">
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <x-icon name="Search" class="w-4 h-4" />
                    </div>
                    <input type="text" 
                           name="nama" 
                           value="{{ $nama_pencarian ?? '' }}" 
                           placeholder="Ketik namamu atau kelas untuk melihat arsip buku yang pernah kamu pinjam..." 
                           class="w-full pl-9 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-300 focus:border-indigo-500 rounded-xl text-slate-800 text-xs sm:text-sm font-medium transition focus:outline-none focus:ring-4 focus:ring-indigo-500/10">
                    @if(!empty($nama_pencarian))
                        <a href="/siswa/riwayat-selesai" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none" aria-label="Hapus kata kunci pencarian" title="Hapus kata kunci pencarian">
                            <x-icon name="X" class="w-4 h-4" />
                        </a>
                    @endif
                </div>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-5 py-2.5 rounded-xl shadow-xs text-xs sm:text-sm shrink-0 flex items-center justify-center space-x-1.5 transition focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <x-icon name="Search" class="w-3.5 h-3.5" />
                    <span>Cari Riwayat Saya</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Daftar Buku yang Pernah Dipinjam Compact -->
    <div class="bg-white rounded-2xl shadow-xs border border-slate-200/80 p-4 sm:p-5">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-sm sm:text-base font-bold text-slate-800">
                    Arsip Buku yang Sudah Dikembalikan
                </h3>
                <p class="text-[11px] sm:text-xs text-slate-500 mt-0.5">Catatan buku yang telah sukses dipinjam dan selesai dikembalikan ke perpustakaan.</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200 shrink-0">
                {{ $peminjaman->count() }} Riwayat
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5 sm:gap-4">
            @forelse($peminjaman as $item)
            <div class="p-3.5 sm:p-4 rounded-xl border border-slate-200 bg-slate-50/40 hover:bg-slate-50 transition flex flex-col justify-between space-y-3">
                <div class="flex items-start space-x-3.5">
                    <!-- Foto Cover Buku (Compact) -->
                    <div class="w-14 h-20 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center shadow-2xs">
                        @if($item->book && $item->book->foto)
                            <img src="{{ asset('storage/' . $item->book->foto) }}" alt="{{ $item->book->judul }}" class="w-full h-full object-cover">
                        @else
                            <x-icon name="BookOpen" class="w-7 h-7 text-slate-300" />
                        @endif
                    </div>

                    <!-- Informasi Judul & Peminjam -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-1.5 mb-1">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-800">
                                <x-icon name="CircleCheck" class="w-3 h-3 text-emerald-600" />
                                <span>Selesai Dikembalikan</span>
                            </span>
                        </div>

                        <h4 class="font-bold text-slate-800 text-sm sm:text-base truncate" title="{{ $item->book->judul ?? 'Buku' }}">
                            {{ $item->book->judul ?? 'Judul Buku Tidak Diketahui' }}
                        </h4>
                        <p class="text-xs text-slate-600 mt-0.5">
                            Peminjam: <strong class="text-slate-900">{{ $item->nama_siswa }}</strong> 
                            <span class="text-slate-400">({{ $item->kelas }})</span>
                        </p>
                        
                        <div class="mt-1.5 text-xs text-slate-500 space-y-0.5">
                            <p>Dipinjam: <span class="font-medium text-slate-700">{{ \Carbon\Carbon::parse($item->tanggal_pinjam)->translatedFormat('d M Y') }}</span></p>
                            <p>Dikembalikan: <span class="font-semibold text-emerald-700">{{ $item->tanggal_kembali ? \Carbon\Carbon::parse($item->tanggal_kembali)->translatedFormat('d M Y') : '-' }}</span></p>
                            @if($item->kondisi_buku_kembali)
                                <p>Kondisi: <span class="font-bold {{ $item->kondisi_buku_kembali === 'baik' ? 'text-emerald-700' : 'text-amber-700' }}">{{ ucfirst($item->kondisi_buku_kembali) }}</span></p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="pt-2.5 border-t border-slate-200/80 flex flex-wrap items-center justify-between gap-2 text-xs text-slate-500">
                    <div class="flex items-center space-x-2">
                        @if($item->denda > 0)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] sm:text-[11px] font-bold {{ $item->is_denda_lunas ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                Denda: {{ $item->formatted_denda }} ({{ strtoupper($item->status_denda) }})
                            </span>
                        @else
                            <span class="text-emerald-700 font-medium flex items-center text-xs">
                                <x-icon name="CircleCheck" class="w-3.5 h-3.5 mr-1 text-emerald-500" />
                                <span>Bebas Denda</span>
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center space-x-2">
                        @if($item->foto_kembali)
                            <button type="button" 
                                    onclick="previewFotoSelesai('{{ asset('storage/' . $item->foto_kembali) }}', 'Bukti Pengembalian - {{ addslashes($item->book->judul ?? 'Buku') }}')"
                                    class="text-blue-600 hover:text-blue-800 font-bold text-[11px] hover:underline flex items-center space-x-1 focus:outline-none">
                                <x-icon name="Eye" class="w-3.5 h-3.5" />
                                <span>Lihat Foto Pengembalian</span>
                            </button>
                        @endif
                        <span class="text-[11px] text-slate-400">ID: #{{ $item->id }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full p-8 sm:p-10 text-center">
                <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-400 border border-slate-200 flex items-center justify-center mx-auto mb-2.5">
                    <x-icon name="BookOpen" class="w-6 h-6 text-slate-400" />
                </div>
                <h4 class="text-sm sm:text-base font-bold text-slate-700">Belum Ada Riwayat Buku yang Selesai</h4>
                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                    Belum ada data buku yang pernah kamu pinjam dan sudah dikembalikan sebelumnya.
                </p>
                <div class="mt-3.5 flex flex-wrap justify-center gap-2">
                    <a href="/siswa/dashboard" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-xs transition focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <x-icon name="BookOpen" class="w-3.5 h-3.5" />
                        <span>Lihat Katalog & Pinjam Buku Sekarang</span>
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>

</div>

<!-- MODAL PREVIEW FOTO BUKTI PENGEMBALIAN (Compact) -->
<div id="modalPreviewFotoSelesai" class="fixed inset-0 z-50 hidden bg-slate-900/75 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4">
    <div class="bg-white rounded-2xl max-w-md w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="p-3.5 border-b border-slate-100 flex items-center justify-between">
            <h4 id="previewFotoSelesaiTitle" class="font-bold text-slate-800 text-xs sm:text-sm truncate">Foto Pengembalian Buku</h4>
            <button onclick="closePreviewFotoSelesai()" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition focus:outline-none focus:ring-2 focus:ring-slate-400" aria-label="Tutup modal preview foto selesai">
                <x-icon name="X" class="w-4 h-4" />
            </button>
        </div>
        <div class="p-3 bg-slate-100 flex items-center justify-center max-h-[60vh] overflow-auto">
            <img id="previewFotoSelesaiImg" src="" alt="Foto Pengembalian" class="max-w-full max-h-[50vh] rounded-lg shadow-xs object-contain">
        </div>
        <div class="p-3 bg-white border-t border-slate-100 text-right">
            <button onclick="closePreviewFotoSelesai()" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold text-xs rounded-xl transition focus:outline-none focus:ring-2 focus:ring-slate-500">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
    function previewFotoSelesai(url, title) {
        document.getElementById('previewFotoSelesaiImg').src = url;
        document.getElementById('previewFotoSelesaiTitle').innerText = title;
        document.getElementById('modalPreviewFotoSelesai').classList.remove('hidden');
    }

    function closePreviewFotoSelesai() {
        document.getElementById('modalPreviewFotoSelesai').classList.add('hidden');
    }

    const modal = document.getElementById('modalPreviewFotoSelesai');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });
    }
</script>
@endsection
