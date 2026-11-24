<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peminjaman extends Model
{
    use HasFactory, SoftDeletes;

    // Mengizinkan semua kolom diisi secara otomatis
    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_pinjam' => 'date',
            'tanggal_kembali' => 'date',
            'tanggal_perkiraan_kembali' => 'date',
            'tanggal_pengajuan_kembali' => 'date',
            'denda' => 'integer',
        ];
    }

    /**
     * Relasi ke model User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model Book
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Hitung berapa hari keterlambatan peminjaman
     */
    public function getHariTerlambatAttribute(): int
    {
        if (! $this->tanggal_perkiraan_kembali) {
            return 0;
        }

        $due = Carbon::parse($this->tanggal_perkiraan_kembali)->startOfDay();

        if ($this->status === 'dikembalikan') {
            if (! $this->tanggal_kembali) {
                return 0;
            }
            $returned = Carbon::parse($this->tanggal_kembali)->startOfDay();

            return $returned->greaterThan($due) ? (int) abs($returned->diffInDays($due)) : 0;
        }

        if ($this->status === 'menunggu_pengembalian') {
            $submitted = $this->tanggal_pengajuan_kembali
                ? Carbon::parse($this->tanggal_pengajuan_kembali)->startOfDay()
                : Carbon::today();

            return $submitted->greaterThan($due) ? (int) abs($submitted->diffInDays($due)) : 0;
        }

        if ($this->status === 'dipinjam') {
            $today = Carbon::today();

            return $today->greaterThan($due) ? (int) abs($today->diffInDays($due)) : 0;
        }

        return 0;
    }

    /**
     * Hitung sisa hari peminjaman sebelum jatuh tempo
     */
    public function getSisaHariAttribute(): ?int
    {
        if ($this->status !== 'dipinjam' || ! $this->tanggal_perkiraan_kembali) {
            return null;
        }

        $due = Carbon::parse($this->tanggal_perkiraan_kembali)->startOfDay();
        $today = Carbon::today();

        if ($due->greaterThanOrEqualTo($today)) {
            return (int) abs($today->diffInDays($due));
        }

        return 0;
    }

    /**
     * Apakah peminjaman ini sudah terlambat
     */
    public function getIsTerlambatAttribute(): bool
    {
        return $this->hari_terlambat > 0;
    }

    /**
     * Hitung total denda (baik yang sudah tersimpan maupun denda keterlambatan berjalan)
     */
    public function getTotalDendaAttribute(): int
    {
        // Jika admin sudah mencatat denda di database, prioritaskan data tersebut
        if ($this->denda !== null) {
            return (int) $this->denda;
        }

        // Jika buku terlambat dikembalikan atau masih dipinjam lewat jatuh tempo
        if ($this->hari_terlambat > 0) {
            $setting = LibrarySetting::first();
            $tarif = $setting?->tarif_denda_harian ?? 1000;

            return (int) ($this->hari_terlambat * $tarif);
        }

        return 0;
    }

    /**
     * Format total denda ke string Rupiah
     */
    public function getFormattedDendaAttribute(): string
    {
        return 'Rp '.number_format($this->total_denda, 0, ',', '.');
    }

    /**
     * Status pembayaran denda ('bebas', 'lunas', 'belum_dibayar')
     */
    public function getStatusPembayaranDendaAttribute(): string
    {
        if ($this->total_denda <= 0) {
            return 'bebas';
        }

        return $this->status_denda === 'lunas' ? 'lunas' : 'belum_dibayar';
    }

    /**
     * Apakah denda sudah lunas
     */
    public function getIsDendaLunasAttribute(): bool
    {
        return $this->total_denda > 0 && $this->status_denda === 'lunas';
    }
}
