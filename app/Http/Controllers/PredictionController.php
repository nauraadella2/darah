<?php

namespace App\Http\Controllers;

use App\Models\Optimasi;
use App\Models\PermintaanDarah;
use App\Models\PrediksiDarah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use DateTime;
use Barryvdh\DomPDF\Facade\Pdf;

class PredictionController extends Controller
{
    public function index()
    {
        $optimasi = Optimasi::select('periode_mulai', 'periode_selesai')->first();

        if (!$optimasi) {
            return redirect()->route('admin.optimasi')
                ->with('warning', 'Data optimasi belum tersedia. Silakan lakukan proses optimasi terlebih dahulu.');
        }

        $lastTrainingYear = Optimasi::max('periode_selesai') ?? date('Y') - 1;
        $tahunPrediksiTersedia = range($lastTrainingYear + 1, date('Y') + 1);

        $prediksiData = PrediksiDarah::query()
            ->orderBy('tahun', 'desc')
            ->orderBy('bulan', 'desc')
            ->get();

        $periodeSelesai = (int) $optimasi->periode_selesai;
        $tahunBerikutnya = $periodeSelesai + 1;

        $predictions = $prediksiData->groupBy(['tahun', 'bulan'])
            ->map(function ($yearGroup) {
                return $yearGroup->map(function ($monthGroup) {
                    $grouped = [
                        'tahun' => $monthGroup->first()->tahun,
                        'bulan' => $monthGroup->first()->bulan,
                        'gol_a' => 0,
                        'gol_b' => 0,
                        'gol_ab' => 0,
                        'gol_o' => 0,
                        'created_at' => $monthGroup->first()->created_at
                    ];

                    foreach ($monthGroup as $prediction) {
                        switch ($prediction->golongan_darah) {
                            case 'A': $grouped['gol_a'] = $prediction->jumlah; break;
                            case 'B': $grouped['gol_b'] = $prediction->jumlah; break;
                            case 'AB': $grouped['gol_ab'] = $prediction->jumlah; break;
                            case 'O': $grouped['gol_o'] = $prediction->jumlah; break;
                        }
                    }

                    return (object) $grouped;
                });
            })
            ->flatten()
            ->sortBy(fn($item) => $item->tahun * 100 + $item->bulan);

        $availableMonths = $prediksiData->pluck('bulan')->unique()->sort()->values();

        return view('admin.prediksi', [
            'tahunOptimasi' => $optimasi,
            'tahunBerikutnya' => $tahunBerikutnya,
            'lastTrainingYear' => $lastTrainingYear,
            'predictions' => $predictions,
            'prediksi' => PrediksiDarah::all(),
            'availableMonths' => $availableMonths
        ]);
    }

    public function hitungPrediksi(Request $request)
    {
        $validate = $request->validate([
            'periods' => 'required|integer|min:1|max:12',
            'alpha' => 'nullable|numeric|between:0.1,0.9',
            'beta' => 'nullable|numeric|between:0.1,0.9',
            'gamma' => 'nullable|numeric|between:0.1,0.9',
            'tahun_mulai' => 'required|integer',
            'tahun_selesai' => 'required|integer',
        ]);

        try {
            $optimasi = Optimasi::all()->keyBy('golongan_darah');

            $bloodTypes = ['A', 'B', 'AB', 'O'];
            $optimizeParams = [];
            foreach ($bloodTypes as $type) {
                $optimizeParams[$type] = [
                    'Alpha' => (float)($validate['alpha'] ?? $optimasi[$type]->alpha ?? 0.3),
                    'Beta' => (float)($validate['beta'] ?? $optimasi[$type]->beta ?? 0.1),
                    'Gamma' => (float)($validate['gamma'] ?? $optimasi[$type]->gamma ?? 0.1)
                ];
            }

            $historicalData = PermintaanDarah::select(['tahun', 'bulan', 'golongan_darah', 'jumlah'])
                ->whereBetween('tahun', [$validate['tahun_mulai'], $validate['tahun_selesai']])
                ->get()
                ->map(fn($item) => [
                    'tahun' => (int)$item->tahun,
                    'bulan' => (int)$item->bulan,
                    'golongan_darah' => $item->golongan_darah,
                    'jumlah' => (int)$item->jumlah
                ])->toArray();

            $periods = (int)$request->input('periods', 6);

            $payload = [
                'data' => $historicalData,
                'optimize' => $optimizeParams
            ];

            $response = Http::post('http://localhost:5000/predict?periods=' . $periods, $payload);

            if ($response->successful()) {
                $result = $response->json();

                DB::beginTransaction();
                try {
                    $this->savePredictionsToDatabase($result, $optimizeParams, $request->user()->id ?? null);
                    DB::commit();
                    return $this->index();
                } catch (\Exception $e) {
                    DB::rollBack();
                    throw $e;
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal mendapatkan prediksi dari server',
                    'error_details' => $response->body()
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat memproses prediksi',
                'error_details' => $e->getMessage()
            ], 500);
        }
    }

    private function savePredictionsToDatabase($result, $optimizeParams, $userId = null)
    {
        PrediksiDarah::query()->delete();
        $bloodTypes = ['A', 'B', 'AB', 'O'];

        foreach ($result['predictions']['by_date'] as $prediction) {
            $date = new DateTime($prediction['date']);
            $tahun = (int)$date->format('Y');
            $bulan = (int)$date->format('n');

            foreach ($bloodTypes as $bloodType) {
                if (isset($prediction[$bloodType])) {
                    PrediksiDarah::create([
                        'golongan_darah' => $bloodType,
                        'tahun' => $tahun,
                        'bulan' => $bulan,
                        'jumlah' => $prediction[$bloodType],
                        'is_aktual' => false,
                        'alpha' => $optimizeParams[$bloodType]['Alpha'],
                        'beta' => $optimizeParams[$bloodType]['Beta'],
                        'gamma' => $optimizeParams[$bloodType]['Gamma'],
                        'user_id' => $userId
                    ]);
                }
            }
        }
    }

    public function exportPDF()
    {
        $predictions = PrediksiDarah::query()
            ->select('tahun', 'bulan', 'golongan_darah', 'jumlah')
            ->orderBy('tahun')
            ->orderBy('bulan')
            ->get()
            ->groupBy(['tahun', 'bulan'])
            ->map(function ($yearGroup) {
                return $yearGroup->map(function ($monthGroup) {
                    $monthName = \Carbon\Carbon::create()->month($monthGroup->first()->bulan)->format('F');
                    return [
                        'tahun' => $monthGroup->first()->tahun,
                        'bulan' => $monthGroup->first()->bulan,
                        'gol_a' => $monthGroup->where('golongan_darah', 'A')->first()->jumlah ?? 0,
                        'gol_b' => $monthGroup->where('golongan_darah', 'B')->first()->jumlah ?? 0,
                        'gol_ab' => $monthGroup->where('golongan_darah', 'AB')->first()->jumlah ?? 0,
                        'gol_o' => $monthGroup->where('golongan_darah', 'O')->first()->jumlah ?? 0,
                        'tanggal' => $monthName . ' ' . $monthGroup->first()->tahun
                    ];
                });
            })
            ->flatten(1)
            ->values()
            ->toArray();

        $pdf = Pdf::loadView('admin.prediksi_pdf', compact('predictions'))
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10)
            ->setOption('margin-left', 10)
            ->setOption('margin-right', 10);

        return $pdf->download('prediksi_darah_' . date('YmdHis') . '.pdf');
    }
}
