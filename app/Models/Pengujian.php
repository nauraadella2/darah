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
        'alpha',
        'beta'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}