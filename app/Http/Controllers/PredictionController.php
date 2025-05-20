<?php

namespace App\Http\Controllers;

use App\Models\OptimizedAlpha;
use App\Models\PermintaanDarah;
use App\Models\PrediksiDarah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;

class PredictionController extends Controller
{
    public function index()
    {
        // Get the last year used in training data from optimized_alphas table
        $lastTrainingYear = OptimizedAlpha::latest('periode_selesai')
            ->value('periode_selesai');

        // Generate available prediction years (from year after last training year to next year)
        $tahunPrediksiTersedia = range($lastTrainingYear + 1, date('Y') + 1);

        return view('admin.prediksi', [
            'tahunPrediksiTersedia' => $tahunPrediksiTersedia,
            'lastTrainingYear' => $lastTrainingYear
        ]);
    }

    public function hitungPrediksi(Request $request)
    {
        // 1. VALIDATION
        $lastTrainingYear = OptimizedAlpha::latest('periode_selesai')->value('periode_selesai');

        $validated = $request->validate([
            'golongan_darah' => 'required|in:A,B,AB,O',
            'tahun' => [
                'required',
                'integer',
                'min:' . ($lastTrainingYear + 1),
                'max:' . (date('Y') + 2)
            ],
            'bulan' => 'nullable|integer|min:1|max:12',
            'mode_prediksi' => 'required|in:bulanan,tahunan'
        ], [
            'tahun.min' => 'Tahun prediksi harus setelah tahun ' . $lastTrainingYear,
            'tahun.max' => 'Tahun prediksi maksimal ' . (date('Y') + 2),
            'bulan.required_if' => 'Bulan harus dipilih untuk prediksi bulanan',
        ]);

        // 2. GET OPTIMIZED ALPHA VALUE
        $alphaData = OptimizedAlpha::where('golongan_darah', $validated['golongan_darah'])
            ->latest()
            ->firstOrFail();
        $alpha = $alphaData->alpha; // Use optimized alpha instead of fixed 0.3

        // 3. GET HISTORICAL DATA
        $column = 'gol_' . strtolower($validated['golongan_darah']);
        $historicalData = PermintaanDarah::select('tahun', 'bulan', $column)
            ->whereBetween('tahun', [$alphaData->periode_mulai, $alphaData->periode_selesai])
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->pluck($column)
            ->toArray();

        if (empty($historicalData)) {
            return back()->with('error', 'Data historis tidak ditemukan');
        }

        // 4. CALCULATE EXPONENTIAL SMOOTHING
        $results = [];
        $smoothed = [];
        $forecasts = [];

        // Initialize first period
        $smoothed[0] = $historicalData[0];
        $forecasts[0] = null;
        $results[] = [
            'period' => 'Periode 1',
            'actual' => $historicalData[0],
            'smoothed' => $smoothed[0],
            'forecast' => 'N/A',
            'calculation' => 'Inisialisasi'
        ];

        // Calculate for remaining periods
        for ($i = 1; $i < count($historicalData); $i++) {
            $smoothed[$i] = $alpha * $historicalData[$i] + (1 - $alpha) * $smoothed[$i - 1];
            $forecasts[$i] = $smoothed[$i - 1];

            $results[] = [
                'period' => 'Periode ' . ($i + 1),
                'actual' => $historicalData[$i],
                'smoothed' => round($smoothed[$i], 2),
                'forecast' => round($forecasts[$i], 2),
                'calculation' => sprintf(
                    "%.2f = %.2f × %.2f + %.2f × %.2f",
                    $smoothed[$i],
                    $alpha,
                    $historicalData[$i],
                    (1 - $alpha),
                    $smoothed[$i - 1]
                )
            ];
        }

        // 5. GENERATE FUTURE FORECASTS (UPDATED VERSION)
        $lastSmoothed = end($smoothed);
        $futureForecasts = [];
        $startYear = $validated['tahun'];
        $startMonth = $validated['bulan'] ?? 1;

        // Get actual data from previous 12 months for seasonal adjustment
        $seasonalData = [];
        if (count($historicalData) >= 12) {
            $seasonalData = array_slice($historicalData, -12); // Last 12 months data
        }

        for ($i = 0; $i < 12; $i++) {
            $currentMonth = $startMonth + $i;
            $year = $startYear;

            if ($currentMonth > 12) {
                $currentMonth -= 12;
                $year++;
            }

            // Calculate seasonal adjustment if we have enough historical data
            $seasonalAdjustment = 0;
            if (!empty($seasonalData) && isset($seasonalData[$i])) {
                // Calculate the ratio between actual and smoothed value from same period last year
                $seasonalIndex = $seasonalData[$i] / $smoothed[count($smoothed) - 12 + $i];
                $seasonalAdjustment = $lastSmoothed * ($seasonalIndex - 1);
            }

            $adjustedForecast = $lastSmoothed + $seasonalAdjustment;

            $period = sprintf('%s %04d', DateTime::createFromFormat('!m', $currentMonth)->format('F'), $year);
            // dd($adjustedForecast);
            // Save to database
            PrediksiDarah::updateOrCreate(
                [
                    'golongan_darah' => $validated['golongan_darah'],
                    'tahun' => $year,
                    'bulan' => $currentMonth,
                    'user_id' => auth()->id()
                ],
                [
                    'jumlah' => round($adjustedForecast, 2),
                    'alpha' => $alpha,
                    'optimized_alpha_id' => $alphaData->id,
                    'periode_prediksi' => $period,
                    'metode' => 'Exponential Smoothing with Seasonal Adjustment',
                    'updated_at' => now()
                ]
            );

            $futureForecasts[] = [
                'period' => $period,
                'forecast' => round($adjustedForecast, 2),
                'base_forecast' => round($lastSmoothed, 2),
                'seasonal_adjustment' => round($seasonalAdjustment, 2)
            ];
        }

        return view('admin.hasil', [
            'data' => $futureForecasts,
            'bloodType' => $validated['golongan_darah'],
            'year' => $validated['tahun'],
            'alpha' => $alpha,
            'mode' => $validated['mode_prediksi'],
            'lastSmoothed' => round($lastSmoothed, 2),
            'historicalCalculations' => $results
        ]);

        // 6. RETURN RESULTS
        // return response()->json([
        //     'success' => true,
        //     'alpha_used' => $alpha,
        //     'historical_calculations' => $results,
        //     'future_forecasts' => $futureForecasts,
        //     'last_smoothed_value' => round($lastSmoothed, 2)
        // ]);

        // return redirect()->route('admin.prediksi.hasil',)
        //     ->with('success', 'Prediksi berhasil disimpan')
        //     ->with('prediction_mode', $validated['mode_prediksi']);
    }

    /**
     * Calculate single exponential smoothing forecast
     * 
     * @param array $data Historical data points
     * @param float $alpha Smoothing factor (0 < alpha < 1)
     * @return float Forecasted value
     */

    public function show(Request $request)
    {
        // Ambil parameter dari session (yang disimpan saat redirect)
        $bloodType = session('blood_type');
        $year = session('prediction_year');

        // Jika tidak ada di session, ambil dari request
        if (!$bloodType || !$year) {
            $bloodType = $request->input('golongan_darah');
            $year = $request->input('tahun');
        }
        // dd($bloodType);
        // Validasi jika parameter masih kosong
        if (!$bloodType || !$year) {
            return back()->with('error', 'Parameter golongan darah dan tahun tidak ditemukan');
        }

        // Ambil data prediksi
        $predictions = PrediksiDarah::where('golongan_darah', $bloodType)
            ->where('tahun', $year)
            ->where('user_id', auth()->id())
            ->orderBy('bulan')
            ->get();

        // Ambil informasi alpha yang digunakan
        $alphaData = OptimizedAlpha::where('golongan_darah', $bloodType)
            ->latest()
            ->first();

        return view('admin.prediksi.hasil', [
            'predictions' => $predictions,
            'bloodType' => $bloodType,
            'year' => $year,
            'alpha' => $alphaData->alpha ?? null,
            'mode' => session('prediction_mode')
        ]);
    }

    // public function show()
    // {
    //     // Jika ingin filter spesifik (contoh):
    //     $bloodType = request('golongan_darah') ?? 'A';
    //     $year = request('tahun') ?? date('Y');
    //     $predictions = PrediksiDarah::where('golongan_darah', $bloodType)
    //         ->where('tahun', $year)
    //         ->get();

    //     return view('admin.prediksi.hasil', compact('predictions'));
    //     // $prediksi = PrediksiDarah::with('optimizedAlpha')->findOrFail($id);
    //     // $column = 'gol_' . strtolower($prediksi->golongan_darah);

    //     // // Get all predictions for this blood type in the same year
    //     // $annualPredictions = PrediksiDarah::where('golongan_darah', $prediksi->golongan_darah)
    //     //     ->where('tahun', $prediksi->tahun)
    //     //     ->orderBy('bulan')
    //     //     ->get();

    //     // $historicalData = PermintaanDarah::select('tahun', 'bulan', $column)
    //     //     ->orderBy('tahun')
    //     //     ->orderBy('bulan')
    //     //     ->get();

    //     // // Get available prediction years
    //     // $lastTrainingYear = OptimizedAlpha::latest('periode_selesai')->value('periode_selesai');
    //     // $tahunPrediksiTersedia = range($lastTrainingYear + 1, date('Y') + 1);

    //     // return view('admin.prediksi', [
    //     //     'prediksi' => [
    //     //         'id' => $prediksi->id,
    //     //         'golongan' => $prediksi->golongan_darah,
    //     //         'tahun' => $prediksi->tahun,
    //     //         'bulan' => $prediksi->bulan,
    //     //         'hasil' => $prediksi->jumlah,
    //     //         'alpha' => $prediksi->alpha,
    //     //         'history_labels' => $historicalData->map(function ($item) {
    //     //             return DateTime::createFromFormat('!m', $item->bulan)->format('M') . ' ' . $item->tahun;
    //     //         }),
    //     //         'history_data' => $historicalData->pluck($column),
    //     //         'annual_predictions' => $annualPredictions
    //     //     ],
    //     //     'tahunPrediksiTersedia' => $tahunPrediksiTersedia,
    //     //     'lastTrainingYear' => $lastTrainingYear
    //     // ]);
    // }
}


        // $historicalData = [50, 55, 60, 65, 70, 75, 80, 75, 70, 65, 60, 55];
        // $alpha = 0.3; // Smoothing constant

        // // Calculate forecast using simple exponential smoothing
        // $forecast = $this->simpleExponentialSmoothing($historicalData, $alpha);
        // dd( $forecast);

        // return [
        //     'forecast' => $forecast,
        //     'alpha' => $alpha,
        //     'historical_data' => $historicalData
        // ];

        

// try {
    //     DB::beginTransaction();

    //     // Get optimized alpha value
    //     $alphaData = OptimizedAlpha::where('golongan_darah', $validated['golongan_darah'])
    //         ->latest()
    //         ->firstOrFail();

    //     // Get historical data for the specified period
    //     $column = 'gol_' . strtolower($validated['golongan_darah']);
    //     $historicalData = PermintaanDarah::select('tahun', 'bulan', $column)
    //         ->whereBetween('tahun', [$alphaData->periode_mulai, $alphaData->periode_selesai])
    //         ->orderBy('tahun')
    //         ->orderBy('bulan')
    //         ->get();

    //     if ($historicalData->isEmpty()) {
    //         return back()->with('error', 'Data historis tidak ditemukan');
    //     }

    //     // Prepare data for SES
    //     $data = $historicalData->pluck($column)->toArray();
    //     $alpha = (float)$alphaData->alpha;

    //     if ($validated['mode_prediksi'] == 'bulanan') {
    //         if (empty($validated['bulan'])) {
    //             return back()->with('error', 'Bulan harus dipilih untuk mode prediksi bulanan');
    //         }

    //         // Calculate prediction for single month
    //         $prediction = $this->calculateSES($data, $alpha);

    //         // Save the prediction
    //         $saved = PrediksiDarah::create([
    //             'golongan_darah' => $validated['golongan_darah'],
    //             'tahun' => $validated['tahun'],
    //             'bulan' => $validated['bulan'],
    //             'jumlah' => $prediction,
    //             'alpha' => $alpha,
    //             'optimized_alpha_id' => $alphaData->id,
    //             'user_id' => auth()->id()
    //         ]);

    //         DB::commit();

    //         return redirect()->route('admin.prediksi.hasil', $saved->id)
    //             ->with('success', 'Prediksi berhasil disimpan')
    //             ->with('prediction_mode', $validated['mode_prediksi']);
    //     } else {
    //         // Annual prediction - same value for all months (as per SES)
    //         $predictionValue = $this->calculateSES($data, $alpha);

    //         // Save all predictions
    //         $savedPredictions = [];
    //         for ($month = 1; $month <= 12; $month++) {
    //             $saved = PrediksiDarah::create([
    //                 'golongan_darah' => $validated['golongan_darah'],
    //                 'tahun' => $validated['tahun'],
    //                 'bulan' => $month,
    //                 'jumlah' => $predictionValue,
    //                 'alpha' => $alpha,
    //                 'optimized_alpha_id' => $alphaData->id,
    //                 'user_id' => auth()->id()
    //             ]);
    //             $savedPredictions[] = $saved;
    //         }
    //         dd($savedPredictions);
    //         DB::commit();

    //         return redirect()->route('admin.prediksi.hasil', $savedPredictions[0]->id)
    //             ->with('success', 'Prediksi berhasil disimpan')
    //             ->with('prediction_mode', $validated['mode_prediksi']);
    //     }
    // } catch (\Exception $e) {
    //     DB::rollBack();
    //     return back()->with('error', 'Gagal menghitung prediksi: ' . $e->getMessage());
    // }
    // }