<?php

namespace App\Http\Controllers;

use App\Models\PermintaanDarah;
use App\Models\Optimasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OptimizationController extends Controller
{
    public function index()
    {
        // Ambil tahun tersedia dari data permintaan darah
        $tahunTersedia = PermintaanDarah::select('tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        // Ambil hasil optimasi terbaru per golongan darah
        $hasil = [];
        foreach (['A', 'B', 'AB', 'O'] as $golongan) {
            $hasil[$golongan] = Optimasi::where('golongan_darah', $golongan)
                ->latest()
                ->first();
        }

        return view('admin.optimasi', compact('tahunTersedia', 'hasil'));
    }

    public function hitungAlpha(Request $request)
    {
        $validated = $request->validate([
            'tahun_mulai' => 'required|integer|min:2000',
            'tahun_selesai' => 'required|integer|gte:tahun_mulai'
        ]);

        try {
            // Ambil data training berdasarkan periode yang dipilih
            $dataTraining = PermintaanDarah::whereBetween('tahun', [
                $validated['tahun_mulai'], 
                $validated['tahun_selesai']
            ])
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->groupBy('golongan_darah');

            if ($dataTraining->isEmpty()) {
                return back()->with('error', 'Tidak ada data untuk periode yang dipilih');
            }

            $results = [];

            foreach (['A', 'B', 'AB', 'O'] as $golongan) {
                if (!isset($dataTraining[$golongan])) continue;

                $values = $dataTraining[$golongan]->pluck('jumlah')->toArray();

                if (count($values) < 24) {
                    continue;
                }

                $optimized = $this->optimizeParameters($values);

                $model = Optimasi::updateOrCreate(
                    ['golongan_darah' => $golongan],
                    [
                        'user_id' => auth()->id(),
                        'alpha' => $optimized['alpha'],
                        'beta' => $optimized['beta'],
                        'mape' => $optimized['mape'],
                        'rmse' => $optimized['rmse'],
                        'periode_mulai' => $validated['tahun_mulai'],
                        'periode_selesai' => $validated['tahun_selesai']
                    ]
                );

                $results[$golongan] = $model;
            }

            return redirect()->route('admin.optimasi')
                ->with('success', 'Optimasi parameter berhasil!')
                ->with('results', $results);

        } catch (\Exception $e) {
            Log::error('Error in hitungAlpha: '.$e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat optimasi');
        }
    }

    // ... (method optimizeParameters, holtPredictions, calculateMAPE, calculateRMSE tetap sama)


    private function optimizeParameters(array $data): array
    {
        $splitPoint = (int)(count($data) * 0.8);
        $training = array_slice($data, 0, $splitPoint);
        $validation = array_slice($data, $splitPoint);

        $bestAlpha = 0.3;
        $bestBeta = 0.1;
        $lowestMape = PHP_FLOAT_MAX;
        $bestRmse = PHP_FLOAT_MAX;

        // Grid search for best parameters
        for ($alpha = 0.1; $alpha <= 0.9; $alpha += 0.1) {
            for ($beta = 0.1; $beta <= 0.9; $beta += 0.1) {
                $predictions = $this->holtPredictions($training, $alpha, $beta, count($validation));
                $mape = $this->calculateMAPE($validation, $predictions);
                $rmse = $this->calculateRMSE($validation, $predictions);

                if ($mape < $lowestMape) {
                    $lowestMape = $mape;
                    $bestAlpha = $alpha;
                    $bestBeta = $beta;
                    $bestRmse = $rmse;
                }
            }
        }

        return [
            'alpha' => round($bestAlpha, 2),
            'beta' => round($bestBeta, 2),
            'mape' => round($lowestMape, 2),
            'rmse' => round($bestRmse, 2)
        ];
    }

    private function holtPredictions(array $data, float $alpha, float $beta, int $horizon): array
    {
        $level = $data[0];
        $trend = $data[1] - $data[0];
        $predictions = [];

        // Training phase
        for ($i = 1; $i < count($data); $i++) {
            $newLevel = $alpha * $data[$i] + (1 - $alpha) * ($level + $trend);
            $trend = $beta * ($newLevel - $level) + (1 - $beta) * $trend;
            $level = $newLevel;
        }

        // Forecasting phase
        for ($m = 1; $m <= $horizon; $m++) {
            $predictions[] = $level + ($trend * $m);
        }

        return $predictions;
    }

    private function calculateMAPE(array $actual, array $predicted): float
    {
        $sum = 0;
        $count = min(count($actual), count($predicted));
        
        for ($i = 0; $i < $count; $i++) {
            if ($actual[$i] != 0) {
                $sum += abs(($actual[$i] - $predicted[$i]) / $actual[$i]);
            }
        }
        
        return ($count > 0) ? ($sum / $count) * 100 : PHP_FLOAT_MAX;
    }

    private function calculateRMSE(array $actual, array $predicted): float
    {
        $sum = 0;
        $count = min(count($actual), count($predicted));
        
        for ($i = 0; $i < $count; $i++) {
            $sum += pow($actual[$i] - $predicted[$i], 2);
        }
        
        return $count > 0 ? sqrt($sum / $count) : PHP_FLOAT_MAX;
    }
}