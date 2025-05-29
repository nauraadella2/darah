<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Optimasi extends Model
{
    protected $table = 'optimasi';

    protected $fillable = [
        'user_id',
        'golongan_darah',
        'alpha',
        'beta', 
        'mape',
        'rmse',
        'periode_mulai',
        'periode_selesai'
    ];

    protected $casts = [
        'alpha' => 'float',
        'beta' => 'float',
        'mape' => 'float',
        'rmse' => 'float'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPeriodeAttribute()
    {
        return $this->periode_mulai.'-'.$this->periode_selesai;
    }

    public function getStatusAkurasiAttribute()
    {
        if ($this->mape < 10) return 'Sangat Baik';
        if ($this->mape < 20) return 'Baik';
        return 'Cukup';
    }
}