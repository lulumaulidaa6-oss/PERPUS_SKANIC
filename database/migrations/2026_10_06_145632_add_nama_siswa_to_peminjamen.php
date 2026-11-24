<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('peminjamen', function (Blueprint $table) {
            // Tambahkan kolom nama siswa dan perbolehkan user_id kosong (karena tanpa login)
            $table->string('nama_siswa')->nullable()->after('user_id');
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('peminjamen', function (Blueprint $table) {
            $table->dropColumn('nama_siswa');
        });
    }
};
