<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanDarah;
use App\Models\OptimizedAlpha;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{ 
 
    public function dashboard()
    {
        // Get distinct years from permintaan_darah table
        $years = PermintaanDarah::select('tahun as year')
    ->groupBy('tahun')
    ->orderBy('tahun', 'desc')
    ->pluck('year');
            
        // Get yearly summary with accurate totals
        $yearlySummary = PermintaanDarah::select([
        'tahun as year',
        DB::raw('SUM(jumlah) as total'),
        DB::raw('SUM(CASE WHEN golongan_darah = "A" THEN jumlah ELSE 0 END) as a'),
        DB::raw('SUM(CASE WHEN golongan_darah = "B" THEN jumlah ELSE 0 END) as b'),
        DB::raw('SUM(CASE WHEN golongan_darah = "AB" THEN jumlah ELSE 0 END) as ab'),
        DB::raw('SUM(CASE WHEN golongan_darah = "O" THEN jumlah ELSE 0 END) as o')
    ])
    ->groupBy('tahun')
    ->orderBy('tahun', 'desc')
    ->get();

        return view('admin.dashboard', compact('years', 'yearlySummary'));
    }

    public function getDashboardData(Request $request)
    {
        $year = $request->get('year', date('Y'));
        
        // Get monthly data for selected year
       $monthlyData = PermintaanDarah::select([
        'bulan as month',
        DB::raw('SUM(jumlah) as total')
    ])
    ->where('tahun', $year)
    ->groupBy('bulan')
    ->orderBy('bulan')
    ->get();
            
        // Fill in missing months with 0
        $filledData = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthData = $monthlyData->firstWhere('month', $i);
            $filledData[] = $monthData ? $monthData->total : 0;
        }

        $total = array_sum($filledData);

        return response()->json([
            'total' => $total,
            'monthly_data' => $filledData
        ]);
    }

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
