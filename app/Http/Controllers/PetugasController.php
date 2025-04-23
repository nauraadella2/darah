<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PetugasController extends Controller
{
    public function dashboard() {
        return view('petugas.dashboard');
    }

    public function permintaan() {
        return view('petugas.permintaan');
    }

    public function prediksi() {
        return view('petugas.prediksi');
    }
}
