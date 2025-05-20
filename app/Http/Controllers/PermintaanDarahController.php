<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanDarah;

class PermintaanDarahController extends Controller
{
    

    public function create()
    {
        return view('admin.input'); // Form input banyak
    }

    public function store(Request $request)
    {   
        $request->validate([
            'tahun' => 'required|integer',
            'bulan.*' => 'required|integer|min:1|max:12',
            'gol_a.*' => 'required|integer|min:0',
            'gol_b.*' => 'required|integer|min:0',
            'gol_ab.*' => 'required|integer|min:0',
            'gol_o.*' => 'required|integer|min:0',
        ]);

        $data = [];

        foreach ($request->bulan as $index => $bulan) {
            $data[] = [
                'tahun' => $request->tahun,
                'bulan' => $bulan,
                'gol_a' => $request->gol_a[$index],
                'gol_b' => $request->gol_b[$index],
                'gol_ab' => $request->gol_ab[$index],
                'gol_o' => $request->gol_o[$index],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        PermintaanDarah::insert($data);

        return redirect()->route('admin.permintaan')->with('success', 'Data berhasil disimpan!');
    }

//     public function permintaan()
// {
//     $permintaanData = PermintaanDarah::orderBy('tahun', 'desc')
//         ->orderBy('bulan', 'desc')
//         ->get();

//     // Format ulang menjadi satu baris per golongan
//     $dataPermintaan = [];
//     foreach ($permintaanData as $data) {
//         $bulanTahun = date('F', mktime(0, 0, 0, $data->bulan, 1)) . ' ' . $data->tahun;

//         $dataPermintaan[] = [
//             'tanggal' => $bulanTahun,
//             'golongan' => 'A',
//             'jumlah' => $data->gol_a,
//         ];
//         $dataPermintaan[] = [
//             'tanggal' => $bulanTahun,
//             'golongan' => 'B',
//             'jumlah' => $data->gol_b,
//         ];
//         $dataPermintaan[] = [
//             'tanggal' => $bulanTahun,
//             'golongan' => 'AB',
//             'jumlah' => $data->gol_ab,
//         ];
//         $dataPermintaan[] = [
//             'tanggal' => $bulanTahun,
//             'golongan' => 'O',
//             'jumlah' => $data->gol_o,
//         ];
//     }

//     return view('admin.permintaan', compact('dataPermintaan'));
// }
}
