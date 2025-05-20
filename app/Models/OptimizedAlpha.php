<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptimizedAlpha extends Model
{
    protected $fillable = [
    'golongan_darah', 
    'user_id',
    'permintaan_darah_id',
    'alpha',
    'mape',
    'periode_mulai',
    'periode_selesai',
    'rmse'
];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permintaanDarah()
    {
        return $this->belongsTo(PermintaanDarah::class);
    }
}