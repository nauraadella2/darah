<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PermintaanDarah;

class PermintaanDarahController extends Controller
{
    public function create()
    {
        return view('admin.input');
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
            // Simpan per golongan darah
            $golonganDarah = [
                ['golongan' => 'A', 'jumlah' => $request->gol_a[$index]],
                ['golongan' => 'B', 'jumlah' => $request->gol_b[$index]],
                ['golongan' => 'AB', 'jumlah' => $request->gol_ab[$index]],
                ['golongan' => 'O', 'jumlah' => $request->gol_o[$index]],
            ];

            foreach ($golonganDarah as $gol) {
                $data[] = [
                    'tahun' => $request->tahun,
                    'bulan' => $bulan,
                    'golongan_darah' => $gol['golongan'],
                    'jumlah' => $gol['jumlah'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        PermintaanDarah::insert($data);

        return redirect()->route('admin.permintaan')->with('success', 'Data berhasil disimpan!');
    }
}