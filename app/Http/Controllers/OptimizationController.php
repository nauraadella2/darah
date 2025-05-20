<?php

namespace App\Http\Controllers;

use App\Models\PermintaanDarah;
use App\Models\OptimizedAlpha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OptimizationController extends Controller
{
    public function index()
    {
        try {
            $tahunTersedia = PermintaanDarah::select('tahun')
                ->distinct()
                ->orderBy('tahun')
                ->get()
                ->pluck('tahun')
                ->map(fn($year) => (int)$year)
                ->unique()
                ->values()
                ->all();

            $hasil = [
                'A' => collect(),
                'B' => collect(),
                'AB' => collect(),
                'O' => collect()
            ];

            $optimizedData = OptimizedAlpha::with('permintaanDarah')
                ->latest()
                ->get()
                ->groupBy('golongan_darah');

            foreach ($optimizedData as $golongan => $data) {
                $hasil[$golongan] = $data;
            }

            return view('admin.optimasi', [
                'tahunTersedia' => $tahunTersedia,
                'hasil' => $hasil
            ]);
        } catch (\Exception $e) {
            Log::error('Error in index: ' . $e->getMessage());
            return back()->with('error', 'Error loading optimization page');
        }
    }

    public function hitungAlpha(Request $request)
    {
        $validated = $request->validate([
            'tahun_mulai' => 'required|integer|min:2000|max:' . (date('Y') + 1),
            'tahun_selesai' => 'required|integer|gte:tahun_mulai|max:' . (date('Y') + 1)
        ]);

        try {
            $dataTraining = PermintaanDarah::whereBetween('tahun', [
                $validated['tahun_mulai'], 
                $validated['tahun_selesai']
            ])
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get();

            // Data validation
            if ($dataTraining->isEmpty()) {
                $availableYears = PermintaanDarah::select('tahun')
                    ->distinct()
                    ->orderBy('tahun')
                    ->pluck('tahun')
                    ->toArray();

                return back()->with('error', sprintf(
                    'No data found for period %d-%d. Available years: %s',
                    $validated['tahun_mulai'],
                    $validated['tahun_selesai'],
                    implode(', ', $availableYears)
                ));
            }

            if ($dataTraining->count() < 12) {
                return back()->with('error', 'Minimum 12 months of training data required');
            }

            // Process optimization
            $results = [];
            $golonganDarah = [
                'A' => 'gol_a',
                'B' => 'gol_b',
                'AB' => 'gol_ab',
                'O' => 'gol_o'
            ];

            foreach ($golonganDarah as $gol => $column) {
                $values = $dataTraining->pluck($column)->filter()->values();

                if ($values->isEmpty()) {
                    Log::warning("No data for blood type {$gol}");
                    continue;
                }

                $optimized = $this->optimizeAlpha($values->toArray());
                
                $model = OptimizedAlpha::updateOrCreate(
                    ['golongan_darah' => $gol],
                    [
                        'user_id' => auth()->id(),
                        'permintaan_darah_id' => $dataTraining->first()->id,
                        'alpha' => $optimized['alpha'],
                        'mape' => $optimized['mape'],
                        'rmse' => $optimized['rmse'],
                        'periode_mulai' => $validated['tahun_mulai'],
                        'periode_selesai' => $validated['tahun_selesai']
                    ]
                );

                $results[$gol] = $model;
                Log::info("Optimized blood type {$gol}", $optimized);
            }

            return redirect()->route('admin.optimasi')
                ->with('success', 'Alpha optimization completed!')
                ->with('results', $results);

        } catch (\Exception $e) {
            Log::error('Error in hitungAlpha: ' . $e->getMessage());
            return back()->with('error', 'Error calculating optimal alpha');
        }
    }

    private function optimizeAlpha(array $data): array
    {
        $bestAlpha = 0.1;
        $lowestMape = PHP_FLOAT_MAX;
        $bestRmse = PHP_FLOAT_MAX;

        // Split data into training (80%) and validation (20%)
        $splitPoint = (int)(count($data) * 0.8);
        $training = array_slice($data, 0, $splitPoint);
        $validation = array_slice($data, $splitPoint);

        if (count($validation) < 3) {
            throw new \Exception("Insufficient validation data");
        }

        // Test alpha values from 0.05 to 0.95 in 0.05 increments
        for ($alpha = 0.05; $alpha <= 0.95; $alpha += 0.05) {
            try {
                $predictions = $this->calculatePredictions($training, $alpha);
                $mape = $this->calculateMAPE($validation, $predictions);
                $rmse = $this->calculateRMSE($validation, $predictions);

                if ($mape < $lowestMape) {
                    $lowestMape = $mape;
                    $bestAlpha = $alpha;
                    $bestRmse = $rmse;
                }
            } catch (\Exception $e) {
                Log::error("Alpha {$alpha} error: " . $e->getMessage());
            }
        }

        return [
            'alpha' => round($bestAlpha, 2),
            'mape' => round($lowestMape, 2),
            'rmse' => round($bestRmse, 2)
        ];
    }

    private function calculatePredictions(array $data, float $alpha): array
    {
        // Initialize with average of first 3 data points
        $initialValue = count($data) >= 3 
            ? array_sum(array_slice($data, 0, 3)) / 3
            : array_sum($data) / max(1, count($data));
            
        $predictions = [$initialValue];

        for ($i = 1; $i < count($data); $i++) {
            $predictions[$i] = $alpha * $data[$i - 1] + (1 - $alpha) * $predictions[$i - 1];
        }

        return $predictions;
    }

    private function calculateMAPE(array $actual, array $predicted): float
    {
        $sum = 0;
        $count = 0;

        for ($i = 1; $i < count($actual); $i++) {
            if ($actual[$i] == 0) {
                $sum += abs($predicted[$i]); // Handle zero actual values
            } else {
                $sum += abs(($actual[$i] - $predicted[$i]) / $actual[$i]);
            }
            $count++;
        }

        return ($count > 0) ? ($sum / $count) * 100 : PHP_FLOAT_MAX;
    }

    private function calculateRMSE(array $actual, array $predicted): float
    {
        $sum = 0;
        $count = 0;

        for ($i = 1; $i < count($actual); $i++) {
            $sum += pow($actual[$i] - $predicted[$i], 2);
            $count++;
        }

        return $count > 0 ? sqrt($sum / $count) : PHP_FLOAT_MAX;
    }
}