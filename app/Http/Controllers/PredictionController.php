<?php

namespace App\Http\Controllers;

use App\Models\Optimasi;
use App\Models\PermintaanDarah;
use App\Models\PrediksiDarah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PredictionController extends Controller
{
    public function index(Request $request)
    {
        $lastTrainingYear = Optimasi::max('periode_selesai') ?? date('Y') - 1;
        $tahunPrediksiTersedia = range($lastTrainingYear + 1, date('Y') + 1);

        // Query dasar untuk prediksi
        $query = PrediksiDarah::prediksi()
            ->select(
                'tahun',
                'bulan',
                DB::raw('SUM(CASE WHEN golongan_darah = "A" THEN jumlah ELSE 0 END) as gol_a'),
                DB::raw('SUM(CASE WHEN golongan_darah = "B" THEN jumlah ELSE 0 END) as gol_b'),
                DB::raw('SUM(CASE WHEN golongan_darah = "AB" THEN jumlah ELSE 0 END) as gol_ab'),
                DB::raw('SUM(CASE WHEN golongan_darah = "O" THEN jumlah ELSE 0 END) as gol_o')
            )
            ->groupBy('tahun', 'bulan')
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'asc');

        // Filter tahun dan bulan
        if ($request->tahun) {
            $query->where('tahun', $request->tahun);
        }

        if ($request->bulan) {
            $query->where('bulan', $request->bulan);
        }

        // Ambil semua data tanpa pagination
        $predictions = $query->get();

        // Data untuk chart
        $chartData = PrediksiDarah::prediksi()
            ->select('tahun', 'bulan', 'golongan_darah', DB::raw('SUM(jumlah) as total'))
            ->groupBy('tahun', 'bulan', 'golongan_darah')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->groupBy('golongan_darah');
        $prediksi = PrediksiDarah::all();
        // dd($prediksi);
        return view('admin.prediksi', [
            'tahunPrediksiTersedia' => $tahunPrediksiTersedia,
            'lastTrainingYear' => $lastTrainingYear,
            'predictions' => $predictions,
            'chartData' => $chartData,
            'prediksi' => $prediksi,
            'request' => $request

        ]);
    }

    public function hitungPrediksi(Request $request)
    {
        $request->validate([
            'tahun' => 'required|integer|min:' . (Optimasi::max('periode_selesai') + 1) . '|max:' . (date('Y') + 2),
            'alpha' => 'nullable|numeric|between:0.1,0.9',
            'beta' => 'nullable|numeric|between:0.1,0.9',
            'golongan' => 'nullable|in:A,B,AB,O'
        ]);

        try {
            DB::beginTransaction();

            $results = [];
            $golonganDarah = $request->golongan
                ? [$request->golongan]
                : ['A', 'B', 'AB', 'O'];

            foreach ($golonganDarah as $golongan) {
                $optimasi = Optimasi::where('golongan_darah', $golongan)
                    ->latest()
                    ->firstOrFail();

                $alpha = $request->alpha ?? $optimasi->alpha;
                $beta = $request->beta ?? $optimasi->beta;

                $historicalData = PermintaanDarah::where('golongan_darah', $golongan)
                    ->whereBetween('tahun', [$optimasi->periode_mulai, $optimasi->periode_selesai])
                    ->orderBy('tahun')
                    ->orderBy('bulan')
                    ->pluck('jumlah')
                    ->toArray();

                if (count($historicalData) < 2) {
                    throw new \Exception("Data training tidak cukup untuk golongan $golongan");
                }

                $forecasts = $this->generateForecasts($historicalData, $alpha, $beta, $request->tahun);

                // Simpan prediksi
                foreach ($forecasts as $month => $forecast) {
                    PrediksiDarah::updateOrCreate(
                        [
                            'golongan_darah' => $golongan,
                            'tahun' => $request->tahun,
                            'bulan' => $month,
                            'is_aktual' => false
                        ],
                        [
                            'jumlah' => $forecast['forecast'],
                            'alpha' => $alpha,
                            'beta' => $beta,
                            'optimasi_id' => $optimasi->id,
                            'user_id' => auth()->id()
                        ]
                    );
                }

                $results[$golongan] = [
                    'alpha' => $alpha,
                    'beta' => $beta,
                    'forecasts' => $forecasts
                ];
            }

            DB::commit();

            return redirect()->route('admin.prediksi.index')
                ->with('success', 'Prediksi berhasil dibuat')
                ->with('results', [
                    'year' => $request->tahun,
                    'forecasts' => $results,
                    'custom_params' => $request->alpha || $request->beta
                ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat prediksi: ' . $e->getMessage());
        }
    }

    private function generateForecasts($data, $alpha, $beta, $tahun)
    {
        $level = $data[0];
        $trend = $data[1] - $data[0];

        for ($i = 1; $i < count($data); $i++) {
            $newLevel = $alpha * $data[$i] + (1 - $alpha) * ($level + $trend);
            $trend = $beta * ($newLevel - $level) + (1 - $beta) * $trend;
            $level = $newLevel;
        }

        $forecasts = [];
        for ($m = 1; $m <= 12; $m++) {
            $forecast = $level + ($trend * $m);
            $forecasts[$m] = [
                'period' => date('F Y', mktime(0, 0, 0, $m, 1, $tahun)),
                'forecast' => $forecast > 0 ? round($forecast, 2) : 0
            ];
        }

        return $forecasts;
    }
}
