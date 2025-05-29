<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DateTime;

class PrediksiDarah extends Model
{
    protected $table = 'prediksi_darah';

    protected $fillable = [
        'golongan_darah',
        'tahun', 
        'bulan',
        'jumlah',
        'is_aktual',
        'alpha',
        'beta',
        'optimasi_id',
        'user_id'
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jumlah' => 'decimal:2',
        'is_aktual' => 'boolean',
        'alpha' => 'decimal:2',
        'beta' => 'decimal:2'
    ];

    public function optimasi()
    {
        return $this->belongsTo(Optimasi::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopePrediksi($query)
    {
        return $query->where('is_aktual', false);
    }
}