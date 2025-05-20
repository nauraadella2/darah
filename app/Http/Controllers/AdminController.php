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
    $permintaanData = PermintaanDarah::orderBy('tahun', 'asc')
        ->orderBy('bulan', 'asc')
        ->get();

    // Format data untuk tabel dengan pengelompokan per tahun
    $dataPermintaan = [];
    foreach ($permintaanData as $data) {
        $dataPermintaan[] = [
            'tahun' => $data->tahun,
            'bulan' => $data->bulan,
            'tanggal' => date('F Y', mktime(0, 0, 0, $data->bulan, 1, $data->tahun)),
            'gol_a' => $data->gol_a,
            'gol_b' => $data->gol_b,
            'gol_ab' => $data->gol_ab,
            'gol_o' => $data->gol_o,
        ];
    }

    // Urutkan array berdasarkan tahun dan bulan
    usort($dataPermintaan, function($a, $b) {
        if ($a['tahun'] == $b['tahun']) {
            return $a['bulan'] - $b['bulan'];
        }
        return $a['tahun'] - $b['tahun'];
    });

    // Siapkan data untuk chart
    $chartLabels = [];
    $chartData = [
        'A' => [],
        'B' => [],
        'AB' => [],
        'O' => []
    ];

    foreach ($permintaanData->sortBy('tahun')->sortBy('bulan') as $data) {
        $label = date('M Y', mktime(0, 0, 0, $data->bulan, 1, $data->tahun));
        $chartLabels[] = $label;
        
        $chartData['A'][] = $data->gol_a;
        $chartData['B'][] = $data->gol_b;
        $chartData['AB'][] = $data->gol_ab;
        $chartData['O'][] = $data->gol_o;
    }

    // Ambil tahun tersedia untuk filter
    $tahunTersedia = PermintaanDarah::select('tahun')
        ->distinct()
        ->orderBy('tahun', 'desc')
        ->pluck('tahun')
        ->toArray();
// dd($dataPermintaan);
    return view('admin.permintaan', compact('dataPermintaan', 'chartLabels', 'chartData', 'tahunTersedia'));
}
    public function prediksi()
    {
        return view('admin.prediksi');
    }

    // public function optimasi()
    // {
    //     $dataPerTahun = PermintaanDarah::selectRaw('
    //     tahun,
    //     SUM(gol_a) as total_a,
    //     SUM(gol_b) as total_b,
    //     SUM(gol_ab) as total_ab,
    //     SUM(gol_o) as total_o,
    //     (SUM(gol_a) + SUM(gol_b) + SUM(gol_ab) + SUM(gol_o)) as total_all
    // ')
    //         ->groupBy('tahun')
    //         ->orderBy('tahun', 'DESC')
    //         ->get();

    //     // Contoh penggunaan di controller:
    //     return view('admin.optimasi', compact('dataPerTahun'));
    //     // return view('admin.optimasi', compact('dataPertahun'));
    // }

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
