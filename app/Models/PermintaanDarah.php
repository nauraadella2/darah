<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermintaanDarah extends Model
{
    protected $table = 'permintaan_darah';

    protected $fillable = [
        'id',
        'tahun',
        'bulan',
        'golongan_darah', // Diubah dari gol_a, gol_b, dll
        'jumlah' // Diubah dari kolom terpisah per golongan
    ];

    // Relasi ke optimasi (jika diperlukan)
    public function optimasi()
    {
        return $this->hasMany(Optimasi::class, 'permintaan_darah_id');
    }
}