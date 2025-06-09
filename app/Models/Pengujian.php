<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengujian extends Model
{
    protected $table = 'pengujian';
    protected $fillable = [
        'user_id',
        'golongan_darah',
        'tahun',
        'mape',
        'hasil_perbulan',
        'permintaan_aktual',
        'hasil_prediksi',
        'selisih',
        'error'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
