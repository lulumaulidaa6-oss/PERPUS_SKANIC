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
        Schema::create('peminjamen', function (Blueprint $table) {
            $table->id();
            // Mencatat siapa siswa yang meminjam (Terhubung ke tabel users)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Mencatat buku apa yang dipinjam (Terhubung ke tabel books)
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');

            // Mencatat tanggal pinjam dan batas/tanggal kembali
            $table->date('tanggal_pinjam');
            $table->date('tanggal_kembali')->nullable();

            // Status peminjaman (dipinjam / dikembalikan)
            $table->string('status')->default('dipinjam');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peminjamen');
    }
};
