<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanDarah;
use App\Models\OptimizedAlpha;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

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

//    public function pengguna()
//     {
//         return view('admin.pengguna');
//     }



public function pengguna()
{
    $users = User::orderBy('name')->get();
    $adminCount = $users->where('role', 'admin')->count();
    $petugasCount = $users->where('role', 'petugas')->count();

    return view('admin.pengguna', compact('users', 'adminCount', 'petugasCount'));
}

public function storePengguna(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'role' => 'required|in:admin,petugas',
        'password' => 'nullable|string|min:6'
    ]);

    User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'role' => $validated['role'],
        'password' => bcrypt($validated['password'] ?? 'password123')
    ]);

    return response()->json(['message' => 'Pengguna berhasil ditambahkan']);
}


public function updatePengguna(Request $request, $id)
{
    $user = User::findOrFail($id);

    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'role' => 'required|in:admin,petugas',
        'password' => 'nullable|string|min:6'
    ]);

    $updateData = $validated;
    if (!empty($validated['password'])) {
        $updateData['password'] = bcrypt($validated['password']);
    } else {
        unset($updateData['password']); // jgn ubah password kalau kosong
    }

    $user->update($updateData);

    return response()->json(['message' => 'Pengguna berhasil diperbarui']);
}


public function destroyPengguna($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return response()->json(['message' => 'Pengguna berhasil dihapus']);
}

}