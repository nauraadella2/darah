<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTime;

class PrediksiDarah extends Model
{
    use HasFactory;

    protected $table = 'prediksi_darah';

    protected $fillable = [
        'golongan_darah',
        'tahun',
        'bulan',
        'jumlah',
        'alpha',
        'optimized_alpha_id',
        'user_id'
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'jumlah' => 'float',
        'alpha' => 'float'
    ];

    public function optimizedAlpha()
    {
        return $this->belongsTo(OptimizedAlpha::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getPeriodeAttribute()
    {
        return DateTime::createFromFormat('!m', $this->bulan)->format('F') . ' ' . $this->tahun;
    }

    public function getBulanNamaAttribute()
    {
        return DateTime::createFromFormat('!m', $this->bulan)->format('F');
    }

    public function scopeGolongan($query, $golongan)
    {
        return $query->where('golongan_darah', $golongan);
    }

    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    public function scopeBulan($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }
}