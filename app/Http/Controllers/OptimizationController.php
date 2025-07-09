<?php

namespace App\Http\Controllers;

use App\Models\PermintaanDarah;
use App\Models\Optimasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

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

        $dataTraining = PermintaanDarah::whereBetween('tahun', [
            $validated['tahun_mulai'],
            $validated['tahun_selesai']
        ])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => (int)$item->id, // <-- PASTIKAN INTEGER
                    'tahun' => (int)$item->tahun, // <-- PASTIKAN INTEGER
                    'bulan' => (int)$item->bulan, // <-- PASTIKAN INTEGER
                    'golongan_darah' => $item->golongan_darah,
                    'jumlah' => (int)$item->jumlah, // <-- PASTIKAN INTEGER
                    'created_at' => $item->created_at->toDateTimeString(),
                    'updated_at' => $item->updated_at->toDateTimeString()
                ];
            })
            ->toArray();

        try {
            $response = Http::timeout(120)
                ->asJson()
                ->post('http://127.0.0.1:5000/optimize', $dataTraining);

            $result = $response->json();

            if ($response->successful() && $result['status'] === 'success') {
                $this->saveOptimizationResults(
                    $result['optimization_details'],
                    $validated['tahun_mulai'],
                    $validated['tahun_selesai'],
                    Auth::id()
                );

                foreach (['A', 'B', 'AB', 'O'] as $golongan) {
                    $hasil[$golongan] = Optimasi::where('golongan_darah', $golongan)
                        ->latest()
                        ->first();
                }

                return $this->index();
            }

            return response()->json($result, $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Connection failed',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    protected function saveOptimizationResults($optimizationDetails, $tahunMulai, $tahunSelesai, $userId)
    {
        // 1. Hapus data lama dengan periode yang sama
        Optimasi::query()->delete();


        // 2. Mapping golongan darah yang valid
        $validGolongan = [
            'Gol. A' => 'A',
            'Gol. B' => 'B',
            'Gol. AB' => 'AB',
            'Gol. O' => 'O'
        ];

        // 3. Simpan hanya data dengan golongan darah valid
        foreach ($optimizationDetails as $golKey => $detail) {
            // Skip jika bukan golongan darah valid
            if (!isset($validGolongan[$golKey])) continue;

            // Konversi MAPE dari string "15.15%" ke float 15.15
            $mape = (float)str_replace('%', '', $detail['MAPE']);

            Optimasi::create([
                'user_id' => $userId,
                'golongan_darah' => $validGolongan[$golKey],
                'alpha' => (float)$detail['Alpha (Level)'],
                'beta' => (float)$detail['Beta (Trend)'],
                'gamma' => (float)$detail['Gamma (Seasonal)'],
                'mape' => $mape,
                'periode_mulai' => $tahunMulai,
                'periode_selesai' => $tahunSelesai,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
