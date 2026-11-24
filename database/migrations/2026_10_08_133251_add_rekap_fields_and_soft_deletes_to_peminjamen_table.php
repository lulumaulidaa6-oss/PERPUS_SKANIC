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
            $table->text('alasan_penolakan')->nullable()->after('catatan_kembali');
            $table->string('diproses_oleh')->nullable()->after('alasan_penolakan');
            $table->text('catatan')->nullable()->after('diproses_oleh');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('peminjamen', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn(['alasan_penolakan', 'diproses_oleh', 'catatan']);
        });
    }
};
