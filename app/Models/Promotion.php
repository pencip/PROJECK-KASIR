<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'nama_promo',
        'gambar',
        'tipe_diskon',
        'nilai_diskon',
        'status',
        'alasan_verifikasi',
        'verified_by',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    // Relasi ke pembuat (Admin)
    public function creator()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Relasi ke verifikator (Manajer)
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}