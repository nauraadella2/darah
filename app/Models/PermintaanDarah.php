<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermintaanDarah extends Model
{
    use HasFactory;

    protected $table = 'permintaan_darah';

    protected $fillable = [
        'tahun',
        'bulan',
        'gol_a',
        'gol_b', 
        'gol_ab',
        'gol_o'
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer'
    ];

    // Relasi ke optimized_alphas
    public function optimizedAlphas()
    {
        return $this->hasMany(OptimizedAlpha::class);
    }

    /**
     * Scope untuk filter berdasarkan tahun tertentu
     */
    public function scopeTahun($query, $tahun)
    {
        return $query->where('tahun', $tahun);
    }

    /**
     * Scope untuk filter berdasarkan bulan tertentu
     */
    public function scopeBulan($query, $bulan)
    {
        return $query->where('bulan', $bulan);
    }

    /**
     * Scope untuk urutkan berdasarkan waktu (tahun & bulan)
     */
    public function scopeUrutWaktu($query)
    {
        return $query->orderBy('tahun')->orderBy('bulan');
    }

    /**
     * Ambil total semua golongan darah di bulan-tahun ini
     */
    public function getTotalAttribute()
    {
        return $this->gol_a + $this->gol_b + $this->gol_ab + $this->gol_o;
    }
}
