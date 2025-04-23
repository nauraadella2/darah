<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard() {
        return view('admin.dashboard');
    }

    public function permintaan() {
        return view('admin.permintaan');
    }

    public function prediksi() {
        return view('admin.prediksi');
    }

    public function optimasi() {
        return view('admin.optimasi');
    }

    public function pengujian() {
        return view('admin.pengujian');
    }

    public function users() {
        return view('admin.users');
    }

    public function input() {
        return view('admin.input');
    }
}

