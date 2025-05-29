<?php

namespace App\Http\Controllers;

use App\Models\Pengujian;
use App\Models\PermintaanDarah;
use App\Models\PrediksiDarah;
use Illuminate\Http\Request;

class PengujianController extends Controller
{
    public function index()
    {

        $pengujian = PermintaanDarah::where('tahun', 2024)->get();
        // $prediksi = PrediksiDarah::where('tahun', 2024)->get();
        // $prediksiKeren = PrediksiDarah::orderBy('tahun', 'desc')->first();
        $tahunPrediksi = PrediksiDarah::all()->groupBy('tahun');
        // Ambil tahun terakhir dulu
        $tahunTerakhir = PrediksiDarah::max('tahun');

        // Kemudian ambil semua data dengan tahun tersebut
        $prediksi = PrediksiDarah::where('tahun', $tahunTerakhir)->get();
        // dd($prediksiKeren);
        return view('admin.pengujian', [
            'aktual' => $pengujian,
            'prediksi' => $prediksi,
            'selects' => $tahunPrediksi
        ]);
    }

    public function filter(Request $request)
    {
        $golonganDarah = $request->input('golongan_darah', 'all');
        $tahun = $request->input('tahun');
        if ($golonganDarah === 'all') {
            $prediksi = PrediksiDarah::where(['tahun' => $tahun])->get();
            $pengujian = PermintaanDarah::where('tahun', 2024)->get();
        } else {
            $prediksi = PrediksiDarah::where(['tahun' => $tahun, 'golongan_darah' => $golonganDarah,])->get();
            $pengujian = PermintaanDarah::where(['tahun' => 2024, 'golongan_darah' => $golonganDarah,])->get();
        }
        // $pengujian = PermintaanDarah::where('tahun', 2024)->get();

        return view('admin.keren', [
            'aktual' => $pengujian,
            'prediksi' => $prediksi,
        ]);
    }
    public function proses(Request $request)
    {
        $request->validate([
            'tahun' => 'required|numeric|min:2023|max:2025'
        ]);

        $tahun = $request->tahun;

        // Hapus data pengujian lama
        Pengujian::where('user_id', auth()->id())
            ->where('tahun', $tahun)
            ->delete();

        foreach (['A', 'B', 'AB', 'O'] as $golongan) {
            $aktual = PermintaanDarah::where('tahun', $tahun)
                ->where('golongan_darah', $golongan)
                ->get();

            $prediksi = PrediksiDarah::where('tahun', $tahun)
                ->where('golongan_darah', $golongan)
                ->where('is_aktual', 0)
                ->get();

            $totalError = 0;
            $count = 0;

            foreach ($aktual as $data) {
                $pred = $prediksi->where('bulan', $data->bulan)->first();
                if ($pred) {
                    $error = abs(($data->jumlah - $pred->jumlah) / $data->jumlah) * 100;
                    $totalError += $error;
                    $count++;
                }
            }

            if ($count > 0) {
                Pengujian::create([
                    'user_id' => auth()->id(),
                    'golongan_darah' => $golongan,
                    'tahun' => $tahun,
                    'mape' => $totalError / $count,
                    'alpha' => 0.5, // Ganti dengan nilai alpha sebenarnya
                    'beta' => 0.5    // Ganti dengan nilai beta sebenarnya
                ]);
            }
        }

        return redirect()->route('pengujian.index', ['tahun' => $tahun])
            ->with('success', 'Pengujian berhasil dilakukan!');
    }
}
