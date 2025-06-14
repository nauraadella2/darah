<?php

namespace App\Http\Controllers;

use App\Models\Pengujian;
use App\Models\PermintaanDarah;
use App\Models\PrediksiDarah;
use App\Models\Optimasi;
use Illuminate\Http\Request;

class PengujianController extends Controller
{
    public function index()
    {
        // Ambil tahun terakhir dari data permintaan
        $tahunTerakhir = PermintaanDarah::max('tahun');
        $tahun = PrediksiDarah::select('tahun')->first();

        // Ambil data aktual tahun terakhir
        $aktual = PermintaanDarah::where('tahun', $tahun['tahun'])
            ->orderBy('bulan')
            ->orderBy('golongan_darah')
            ->get();
        // dd($aktual);
        // Ambil data prediksi untuk tahun berikutnya (asumsi prediksi untuk tahun berikutnya)
        $prediksi = PrediksiDarah::where('tahun', $tahun['tahun'])
            ->orderBy('bulan')
            ->orderBy('golongan_darah')
            ->get();

        // Gabungkan data dan hitung statistik
        $hasilPengujian = [];
        $errors = [];
        $bestPrediction = ['error' => 100, 'golongan' => ''];
        $worstPrediction = ['error' => 0, 'golongan' => ''];

        foreach ($aktual as $dataAktual) {
            $dataPrediksi = $prediksi->where('bulan', $dataAktual->bulan)
                ->where('golongan_darah', $dataAktual->golongan_darah)
                ->first();

            if ($dataPrediksi) {
                $selisih = $dataPrediksi->jumlah - $dataAktual->jumlah;
                $error = ($selisih / $dataAktual->jumlah) * 100;
                $roundedError = round($error, 2);

                $hasilPengujian[] = [
                    'bulan' => $dataAktual->bulan,
                    'golongan' => $dataAktual->golongan_darah,
                    'permintaan_aktual' => $dataAktual->jumlah,
                    'hasil_prediksi' => $dataPrediksi->jumlah,
                    'selisih' => $selisih,
                    'error' => $roundedError
                ];

                // Kumpulkan error untuk rata-rata
                $errors[] = abs($roundedError);

                // Update prediksi terbaik dan terburuk
                if (abs($roundedError) < abs($bestPrediction['error'])) {
                    $bestPrediction = [
                        'error' => $roundedError,
                        'golongan' => $dataAktual->golongan_darah,
                        'bulan' => $dataAktual->bulan
                    ];
                }

                if (abs($roundedError) > abs($worstPrediction['error'])) {
                    $worstPrediction = [
                        'error' => $roundedError,
                        'golongan' => $dataAktual->golongan_darah,
                        'bulan' => $dataAktual->bulan
                    ];
                }
            }
        }

        // Hitung rata-rata error
        $averageError = count($errors) > 0 ? round(array_sum($errors) / count($errors), 2) : 0;

        return view('admin.pengujian', compact(
            'tahun',
            'hasilPengujian',
            'tahunTerakhir',
            'averageError',
            'bestPrediction',
            'worstPrediction'
        ));
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

    }
    // public function proses(Request $request)
    // {
    //     $request->validate([
    //         'tahun' => 'required|numeric|min:2023|max:2025'
    //     ]);

    //     $tahun = $request->tahun;

    //     // Hapus data pengujian lama
    //     Pengujian::where('user_id', auth()->id())
    //         ->where('tahun', $tahun)
    //         ->delete();

    //     foreach (['A', 'B', 'AB', 'O'] as $golongan) {
    //         $aktual = PermintaanDarah::where('tahun', $tahun)
    //             ->where('golongan_darah', $golongan)
    //             ->get();

    //         $prediksi = PrediksiDarah::where('tahun', $tahun)
    //             ->where('golongan_darah', $golongan)
    //             ->where('is_aktual', 0)
    //             ->get();

    //         $totalError = 0;
    //         $count = 0;

    //         foreach ($aktual as $data) {
    //             $pred = $prediksi->where('bulan', $data->bulan)->first();
    //             if ($pred) {
    //                 $error = abs(($data->jumlah - $pred->jumlah) / $data->jumlah) * 100;
    //                 $totalError += $error;
    //                 $count++;
    //             }
    //         }

    //         if ($count > 0) {
    //             Pengujian::create([
    //                 'user_id' => auth()->id(),
    //                 'golongan_darah' => $golongan,
    //                 'tahun' => $tahun,
    //                 'mape' => $totalError / $count,
    //                 'alpha' => 0.5, // Ganti dengan nilai alpha sebenarnya
    //                 'beta' => 0.5    // Ganti dengan nilai beta sebenarnya
    //             ]);
    //         }
    //     }

    //     return redirect()->route('pengujian.index', ['tahun' => $tahun])
    //         ->with('success', 'Pengujian berhasil dilakukan!');
    // }
    
    public function proses(Request $request)
{
    $request->validate([
        'tahun_training' => 'required|integer',
        'tahun_testing' => 'required|integer',
        'golongan_darah' => 'required|in:A,B,AB,O',
    ]);

    $golongan = $request->golongan_darah;
    $tahunTraining = $request->tahun_training;
    $tahunTesting = $request->tahun_testing;

    // Ambil parameter hasil optimasi
    $param = Optimasi::where('golongan_darah', $golongan)
        ->where('periode_mulai', $tahunTraining)
        ->latest()
        ->first();

    if (!$param) {
        return back()->with('error', 'Parameter optimasi belum tersedia untuk golongan darah ini.');
    }

    $alpha = $param->alpha;
    $beta = $param->beta;
    $gamma = $param->gamma;

    // Ambil data aktual (testing)
    $aktual = PermintaanDarah::where('golongan_darah', $golongan)
        ->where('tahun', $tahunTesting)
        ->orderBy('tahun')
        ->orderBy('bulan')
        ->get();

    // Ambil data prediksi sesuai tahun & golongan
    $prediksi = PrediksiDarah::where('golongan_darah', $golongan)
        ->where('tahun', $tahunTesting)
        ->where('is_aktual', 0)
        ->orderBy('tahun')
        ->orderBy('bulan')
        ->get();

    // Hitung error per bulan dan MAPE
    $hasilPerBulan = [];
    $totalSelisih = 0;
    $totalAktual = 0;
    $totalPrediksi = 0;
    $totalError = 0;
    $count = 0;

    foreach ($aktual as $index => $a) {
        $bulan = $a->bulan;
        $tahun = $a->tahun;
        $aktualJumlah = $a->jumlah;

        $pred = $prediksi->firstWhere('bulan', $bulan);

        if (!$pred) continue;

        $predJumlah = $pred->jumlah;

        if ($aktualJumlah == 0) continue; // hindari pembagian nol

        $selisih = abs($aktualJumlah - $predJumlah);
        $error = ($selisih / $aktualJumlah) * 100;

        $hasilPerBulan[] = [
            'bulan' => $bulan,
            'tahun' => $tahun,
            'aktual' => $aktualJumlah,
            'prediksi' => round($predJumlah, 2),
            'selisih' => round($selisih, 2),
            'error' => round($error, 2),
        ];

        $totalAktual += $aktualJumlah;
        $totalPrediksi += $predJumlah;
        $totalSelisih += $selisih;
        $totalError += $error;
        $count++;
    }

    $mape = $count > 0 ? round($totalError / $count, 2) : 0;

    // Simpan hasil ke database
    Pengujian::create([
        'user_id' => auth()->id(),
        'golongan_darah' => $golongan,
        'mape' => $mape,
        'hasil_perbulan' => json_encode($hasilPerBulan),
        'permintaan_aktual' => (int)$totalAktual,
        'hasil_prediksi' => (int)$totalPrediksi,
        'selisih' => (int)$totalSelisih,
        'error' => $totalError,
    ]);

    return back()->with('success', "Pengujian selesai. MAPE: {$mape}%");
}


    public function store(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $tahunTerakhir = PermintaanDarah::max('tahun');

            $savedData = [];
            foreach ($request->data as $data) {
                $saved = Pengujian::create([
                    'user_id' => $user->id,
                    'golongan_darah' => $data['golongan'],
                    'mape' => abs($data['error']),
                    'hasil_perbulan' => json_encode([
                        'bulan' => $data['bulan'],
                        'permintaan_aktual' => $data['permintaan_aktual'],
                        'hasil_prediksi' => $data['hasil_prediksi'],
                        'selisih' => $data['selisih'],
                        'error' => $data['error']
                    ]),
                    'permintaan_aktual' => $data['permintaan_aktual'],
                    'hasil_prediksi' => $data['hasil_prediksi'],
                    'selisih' => $data['selisih'],
                    'error' => $data['error']
                ]);
                $savedData[] = $saved;
            }

            return response()->json([
                'success' => true,
                'message' => 'Data pengujian berhasil disimpan',
                'data' => $savedData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
