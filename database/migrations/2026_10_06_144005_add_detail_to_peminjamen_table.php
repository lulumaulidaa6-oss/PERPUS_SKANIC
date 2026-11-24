
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamen', function (Blueprint $table) {
            // Menambahkan detail peminjaman
            $table->string('kelas')->nullable()->after('book_id');
            $table->string('bukti_foto')->nullable()->after('kelas');
            $table->date('tanggal_perkiraan_kembali')->nullable()->after('tanggal_pinjam');

            // Menambahkan detail saat dikembalikan
            $table->string('kondisi_buku_kembali')->nullable()->after('tanggal_kembali'); // baik, rusak, hilang
            $table->integer('denda')->nullable()->after('kondisi_buku_kembali');
        });
    }

    public function down(): void
    {
        Schema::table('peminjamen', function (Blueprint $table) {
            $table->dropColumn(['kelas', 'bukti_foto', 'tanggal_perkiraan_kembali', 'kondisi_buku_kembali', 'denda']);
        });
    }
};
