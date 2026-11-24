<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            // Tambahkan baris-baris ini untuk melengkapi tabel peminjaman:
            $table->string('kode_peminjaman')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_disetujui')->nullable();
            $table->date('tanggal_pinjam')->nullable();
            $table->date('batas_pengembalian')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['menunggu', 'disetujui', 'ditolak', 'sedang_dipinjam', 'selesai', 'terlambat'])->default('menunggu');
            $table->text('catatan')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
