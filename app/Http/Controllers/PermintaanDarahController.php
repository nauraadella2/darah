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
            // Check if data exists for this month and year
            $existingData = PermintaanDarah::where('tahun', $request->tahun)
                ->where('bulan', $bulan)
                ->exists();

            if ($existingData && !$request->has('confirm_overwrite')) {
                return back()->withInput()->with('confirm', [
                    'tahun' => $request->tahun,
                    'bulan' => $bulan,
                    'gol_a' => $request->gol_a[$index],
                    'gol_b' => $request->gol_b[$index],
                    'gol_ab' => $request->gol_ab[$index],
                    'gol_o' => $request->gol_o[$index]
                ]);
            }

            // Delete existing data if overwriting
            if ($existingData) {
                PermintaanDarah::where('tahun', $request->tahun)
                    ->where('bulan', $bulan)
                    ->delete();
            }

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

    public function edit($id)
    {
        // Get data by year and month (id is in format year-month)
        list($tahun, $bulan) = explode('-', $id);
        
        $data = PermintaanDarah::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->get()
            ->groupBy('golongan_darah')
            ->map(function($item) {
                return $item->first()->jumlah;
            });

        return response()->json([
            'tahun' => $tahun,
            'bulan' => $bulan,
            'gol_a' => $data['A'] ?? 0,
            'gol_b' => $data['B'] ?? 0,
            'gol_ab' => $data['AB'] ?? 0,
            'gol_o' => $data['O'] ?? 0,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|min:1|max:12',
            'gol_a' => 'required|integer|min:0',
            'gol_b' => 'required|integer|min:0',
            'gol_ab' => 'required|integer|min:0',
            'gol_o' => 'required|integer|min:0',
        ]);

        // Delete existing data
        PermintaanDarah::where('tahun', $request->tahun)
            ->where('bulan', $request->bulan)
            ->delete();

        // Insert updated data
        $golonganDarah = [
            ['golongan' => 'A', 'jumlah' => $request->gol_a],
            ['golongan' => 'B', 'jumlah' => $request->gol_b],
            ['golongan' => 'AB', 'jumlah' => $request->gol_ab],
            ['golongan' => 'O', 'jumlah' => $request->gol_o],
        ];

        $data = [];
        foreach ($golonganDarah as $gol) {
            $data[] = [
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'golongan_darah' => $gol['golongan'],
                'jumlah' => $gol['jumlah'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        PermintaanDarah::insert($data);

        return response()->json(['success' => 'Data berhasil diperbarui!']);
    }

    public function destroy($id)
    {
        list($tahun, $bulan) = explode('-', $id);
        
        PermintaanDarah::where('tahun', $tahun)
            ->where('bulan', $bulan)
            ->delete();

        return response()->json(['success' => 'Data berhasil dihapus!']);
    }

    public function storeSingle(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer',
            'bulan' => 'required|integer|min:1|max:12',
            'gol_a' => 'required|integer|min:0',
            'gol_b' => 'required|integer|min:0',
            'gol_ab' => 'required|integer|min:0',
            'gol_o' => 'required|integer|min:0',
        ]);

        // Check if data exists
        $existingData = PermintaanDarah::where('tahun', $request->tahun)
            ->where('bulan', $request->bulan)
            ->exists();

        if ($existingData && !$request->has('confirm_overwrite')) {
            return response()->json([
                'confirm' => true,
                'message' => 'Data untuk bulan dan tahun ini sudah ada. Apakah Anda ingin menimpanya?'
            ]);
        }

        // Delete existing data if exists
        if ($existingData) {
            PermintaanDarah::where('tahun', $request->tahun)
                ->where('bulan', $request->bulan)
                ->delete();
        }

        // Insert new data
        $golonganDarah = [
            ['golongan' => 'A', 'jumlah' => $request->gol_a],
            ['golongan' => 'B', 'jumlah' => $request->gol_b],
            ['golongan' => 'AB', 'jumlah' => $request->gol_ab],
            ['golongan' => 'O', 'jumlah' => $request->gol_o],
        ];

        $data = [];
        foreach ($golonganDarah as $gol) {
            $data[] = [
                'tahun' => $request->tahun,
                'bulan' => $request->bulan,
                'golongan_darah' => $gol['golongan'],
                'jumlah' => $gol['jumlah'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        PermintaanDarah::insert($data);

        return response()->json(['success' => 'Data berhasil disimpan!']);
    }
}