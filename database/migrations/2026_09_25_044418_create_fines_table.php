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
        Schema::create('fines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->enum('jenis_denda', ['terlambat', 'kerusakan', 'kehilangan']);
            $table->integer('nominal'); // Kita gunakan integer untuk nominal Rupiah
            $table->string('alasan')->nullable();
            $table->enum('status_pembayaran', ['belum_dibayar', 'menunggu_verifikasi', 'lunas', 'ditolak'])->default('belum_dibayar');
            $table->date('tanggal_denda');
            $table->date('tanggal_pembayaran')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fines');
    }
};
