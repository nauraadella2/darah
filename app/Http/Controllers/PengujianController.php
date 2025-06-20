<?php

namespace App\Http\Controllers;

use App\Models\Pengujian;
use App\Models\PermintaanDarah;
use App\Models\PrediksiDarah;
use App\Models\Optimasi;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PengujianController extends Controller
{

    public function index()
{
    // Ambil tahun prediksi
    $tahun = PrediksiDarah::select('tahun')->first();

    // Ambil data aktual dan prediksi
    $aktual = PermintaanDarah::where('tahun', $tahun['tahun'])
        ->orderBy('bulan')
        ->orderBy('golongan_darah')
        ->get();

    $prediksi = PrediksiDarah::where('tahun', $tahun['tahun'])
        ->orderBy('bulan')
        ->orderBy('golongan_darah')
        ->get();

    $hasilPengujian = [];
    $summaryData = [
        'A' => ['total_error' => 0, 'count' => 0],
        'B' => ['total_error' => 0, 'count' => 0],
        'AB' => ['total_error' => 0, 'count' => 0],
        'O' => ['total_error' => 0, 'count' => 0]
    ];
    $bestPrediction = ['error' => PHP_FLOAT_MAX, 'golongan' => '', 'bulan' => ''];
    $worstPrediction = ['error' => 0, 'golongan' => '', 'bulan' => ''];

    foreach ($aktual as $dataAktual) {
        $dataPrediksi = $prediksi->where('bulan', $dataAktual->bulan)
            ->where('golongan_darah', $dataAktual->golongan_darah)
            ->first();

        if ($dataPrediksi) {
            // Hitung selisih absolut (tidak pernah negatif)
            $selisih = abs($dataPrediksi->jumlah - $dataAktual->jumlah);
            
            // Hitung error persentase (MAPE) dengan penanganan pembagian nol
            $error = 0;
            if ($dataAktual->jumlah != 0) {
                $error = ($selisih / $dataAktual->jumlah) * 100;
            }
            $roundedError = round($error, 2);

            $hasilPengujian[] = [
                'bulan' => $dataAktual->bulan,
                'golongan' => $dataAktual->golongan_darah,
                'permintaan_aktual' => $dataAktual->jumlah,
                'hasil_prediksi' => $dataPrediksi->jumlah,
                'selisih' => $selisih,
                'error' => $roundedError
            ];

            // Hitung summary per golongan darah
            $summaryData[$dataAktual->golongan_darah]['total_error'] += $roundedError;
            $summaryData[$dataAktual->golongan_darah]['count']++;

            // Update prediksi terbaik dan terburuk
            if ($roundedError < $bestPrediction['error']) {
                $bestPrediction = [
                    'error' => $roundedError,
                    'golongan' => $dataAktual->golongan_darah,
                    'bulan' => $dataAktual->bulan
                ];
            }

            if ($roundedError > $worstPrediction['error']) {
                $worstPrediction = [
                    'error' => $roundedError,
                    'golongan' => $dataAktual->golongan_darah,
                    'bulan' => $dataAktual->bulan
                ];
            }
        }
    }

    // Hitung rata-rata MAPE per golongan darah
    $summaryResults = [];
    foreach ($summaryData as $golongan => $data) {
        $summaryResults[] = [
            'golongan' => $golongan,
            'mape' => $data['count'] > 0 ? round($data['total_error'] / $data['count'], 2) : 0,
            'jumlah_data' => $data['count']
        ];
    }

    // Hitung MAPE keseluruhan
    $totalError = array_reduce($summaryResults, fn($carry, $item) => $carry + $item['mape'], 0);
    $mape = count($summaryResults) > 0 ? round($totalError / count($summaryResults), 2) : 0;

    return view('admin.pengujian', compact(
        'tahun',
        'hasilPengujian',
        'summaryResults',
        'mape',
        'bestPrediction',
        'worstPrediction'
    ));
}

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

        // Ambil data aktual dan prediksi
        $aktual = PermintaanDarah::where('golongan_darah', $golongan)
            ->where('tahun', $tahunTesting)
            ->orderBy('bulan')
            ->get();

        $prediksi = PrediksiDarah::where('golongan_darah', $golongan)
            ->where('tahun', $tahunTesting)
            ->where('is_aktual', 0)
            ->orderBy('bulan')
            ->get();

        // Hitung error dan MAPE
        $hasilPerBulan = [];
        $totalAktual = 0;
        $totalPrediksi = 0;
        $totalSelisih = 0;
        $totalError = 0;
        $count = 0;

        foreach ($aktual as $a) {
            $pred = $prediksi->firstWhere('bulan', $a->bulan);
            if (!$pred) continue;

            $aktualJumlah = $a->jumlah;
            $predJumlah = $pred->jumlah;
            
            // Selisih absolut
            $selisih = abs($aktualJumlah - $predJumlah);
            
            // Hitung error dengan penanganan pembagian nol
            $error = 0;
            if ($aktualJumlah != 0) {
                $error = ($selisih / $aktualJumlah) * 100;
            }

            $hasilPerBulan[] = [
                'bulan' => $a->bulan,
                'tahun' => $a->tahun,
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

        // Hitung MAPE
        $mape = $count > 0 ? round($totalError / $count, 2) : 0;

        // Simpan hasil pengujian
        Pengujian::create([
            'user_id' => auth()->id(),
            'golongan_darah' => $golongan,
            'mape' => $mape,
            'hasil_perbulan' => json_encode($hasilPerBulan),
            'permintaan_aktual' => $totalAktual,
            'hasil_prediksi' => round($totalPrediksi),
            'selisih' => $totalSelisih,
            'error' => $totalError,
        ]);

        return back()->with('success', "Pengujian selesai. MAPE: {$mape}%");
    }

    public function exportPDF()
    {
        $tahun = PrediksiDarah::select('tahun')->first();
        
        // Ambil data dan hitung metrik
        $data = PermintaanDarah::where('tahun', $tahun->tahun)
            ->orderBy('bulan')
            ->orderBy('golongan_darah')
            ->get()
            ->map(function($item) use ($tahun) {
                $prediksi = PrediksiDarah::where('tahun', $tahun->tahun)
                    ->where('bulan', $item->bulan)
                    ->where('golongan_darah', $item->golongan_darah)
                    ->first();
                
                // Selisih absolut
                $selisih = $prediksi ? abs($prediksi->jumlah - $item->jumlah) : 0;
                
                // Hitung error dengan penanganan pembagian nol
                $error = 0;
                if ($item->jumlah != 0 && $prediksi) {
                    $error = ($selisih / $item->jumlah) * 100;
                }
                
                return [
                    'bulan' => Carbon::create()->month($item->bulan)->format('F'),
                    'golongan' => $item->golongan_darah,
                    'aktual' => $item->jumlah,
                    'prediksi' => $prediksi->jumlah ?? 0,
                    'selisih' => $selisih,
                    'error' => round($error, 2)
                ];
            })
            ->groupBy('golongan');

        $pdf = Pdf::loadView('admin.pengujian_pdf', [
                'data' => $data,
                'tahun' => $tahun->tahun
            ])
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan_pengujian_'.date('YmdHis').'.pdf');
    }

    // ... (method filter() dan store() tetap sama seperti sebelumnya)


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
    

