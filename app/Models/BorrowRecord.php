<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowRecord extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'tanggal_pinjam',
        'tenggat_waktu',
        'tanggal_dikembalikan',
        'status_peminjaman',
        'kondisi_kembali',
        'total_denda',
        'status_bayar_denda',
    ];
}
