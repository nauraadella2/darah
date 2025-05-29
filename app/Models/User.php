<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Add these relationships if needed
    public function optimasi()
    {
        return $this->hasMany(Optimasi::class);
    }

    public function prediksi()
    {
        return $this->hasMany(PrediksiDarah::class);
    }
}