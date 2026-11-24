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
        Schema::table('peminjamen', function (Blueprint $table) {
            $table->string('foto_kembali')->nullable()->after('kondisi_buku_kembali');
            $table->text('catatan_kembali')->nullable()->after('foto_kembali');
            $table->date('tanggal_pengajuan_kembali')->nullable()->after('catatan_kembali');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjamen', function (Blueprint $table) {
            $table->dropColumn(['foto_kembali', 'catatan_kembali', 'tanggal_pengajuan_kembali']);
        });
    }
};
