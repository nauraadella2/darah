<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\PermintaanDarah;
use App\Models\OptimizedAlpha;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // public function permintaan()
    // {
    //     $permintaanData = PermintaanDarah::orderBy('tahun', 'asc')
    //         ->orderBy('bulan', 'asc')
    //         ->get();

    //     // Format data untuk tabel
    //     $dataPermintaan = [];
    //     foreach ($permintaanData as $data) {
    //         $dataPermintaan[] = [
    //             'tanggal' => date('F Y', mktime(0, 0, 0, $data->bulan, 1, $data->tahun)),
    //             'gol_a' => $data->gol_a,
    //             'gol_b' => $data->gol_b,
    //             'gol_ab' => $data->gol_ab,
    //             'gol_o' => $data->gol_o,
    //         ];
    //     }

    //     // Siapkan data untuk chart
    //     $chartLabels = [];
    //     $chartData = [
    //         'A' => [],
    //         'B' => [],
    //         'AB' => [],
    //         'O' => []
    //     ];

    //     foreach ($permintaanData as $data) {
    //         $label = date('M Y', mktime(0, 0, 0, $data->bulan, 1, $data->tahun));
    //         $chartLabels[] = $label;
            
    //         $chartData['A'][] = $data->gol_a;
    //         $chartData['B'][] = $data->gol_b;
    //         $chartData['AB'][] = $data->gol_ab;
    //         $chartData['O'][] = $data->gol_o;
    //     }

    //     // Ambil tahun tersedia untuk filter
    //     $tahunTersedia = PermintaanDarah::select('tahun')
    //         ->distinct()
    //         ->orderBy('tahun', 'desc')
    //         ->pluck('tahun')
    //         ->toArray();

    //     return view('admin.permintaan', compact('dataPermintaan', 'chartLabels', 'chartData', 'tahunTersedia'));
    // }


public function permintaan()
    {
        // Ambil data dalam format yang diharapkan view
        $permintaanData = PermintaanDarah::selectRaw('
                tahun, 
                bulan,
                SUM(CASE WHEN golongan_darah = "A" THEN jumlah ELSE 0 END) as gol_a,
                SUM(CASE WHEN golongan_darah = "B" THEN jumlah ELSE 0 END) as gol_b,
                SUM(CASE WHEN golongan_darah = "AB" THEN jumlah ELSE 0 END) as gol_ab,
                SUM(CASE WHEN golongan_darah = "O" THEN jumlah ELSE 0 END) as gol_o
            ')
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'asc')
            ->orderBy('bulan', 'asc')
            ->get();

        // Format untuk tabel
        $dataPermintaan = $permintaanData->map(function ($item) {
            return [
                'tahun' => $item->tahun,
                'bulan' => $item->bulan,
                'tanggal' => date('F Y', mktime(0, 0, 0, $item->bulan, 1, $item->tahun)),
                'gol_a' => $item->gol_a,
                'gol_b' => $item->gol_b,
                'gol_ab' => $item->gol_ab,
                'gol_o' => $item->gol_o,
            ];
        });

        // Format untuk chart
        $chartLabels = $permintaanData->map(function ($item) {
            return date('M Y', mktime(0, 0, 0, $item->bulan, 1, $item->tahun));
        });

        $chartData = [
            'A' => $permintaanData->pluck('gol_a')->toArray(),
            'B' => $permintaanData->pluck('gol_b')->toArray(),
            'AB' => $permintaanData->pluck('gol_ab')->toArray(),
            'O' => $permintaanData->pluck('gol_o')->toArray(),
        ];

        $tahunTersedia = PermintaanDarah::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        return view('admin.permintaan', compact('dataPermintaan', 'chartLabels', 'chartData', 'tahunTersedia'));
    }


    public function pengujian()
    {
        return view('admin.pengujian');
    }

    public function users()
    {
        return view('admin.users');
    }

    public function input()
    {
        return view('admin.input');
    }
    public function store()
    {
        return view('admin.input');
    }
}
