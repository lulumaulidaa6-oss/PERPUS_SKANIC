<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    // Mengizinkan semua kolom untuk diisi secara otomatis
    protected $guarded = [];

    /**
     * Relasi ke transaksi peminjaman buku
     */
    public function peminjamen()
    {
        return $this->hasMany(Peminjaman::class);
    }

    /**
     * Kurangi stok buku secara aman dan pastikan stok tidak minus
     */
    public function kurangiStok(int $jumlah = 1): bool
    {
        if ($this->stok < $jumlah) {
            return false;
        }

        $this->decrement('stok', $jumlah);

        return true;
    }

    /**
     * Tambahkan stok buku
     */
    public function tambahStok(int $jumlah = 1): void
    {
        $this->increment('stok', $jumlah);
    }
}
