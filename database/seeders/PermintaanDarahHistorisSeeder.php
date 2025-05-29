<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PermintaanDarahHistorisSeeder extends Seeder
{
    // Base values for each blood type
    private $baseValues = [
        'A' => 25,
        'B' => 40,
        'AB' => 8,
        'O' => 50
    ];
    
    // Seasonal factors (month => multiplier)
    private $seasonalPattern = [
        1 => 1.1,   // Januari tinggi
        2 => 1.0,
        3 => 1.05,
        4 => 0.9,    // April rendah
        5 => 0.85,   // Mei rendah
        6 => 0.95,
        7 => 1.0,
        8 => 1.1,    // Agustus tinggi
        9 => 1.0,
        10 => 1.05,
        11 => 1.15,  // November mulai naik
        12 => 1.4    // Desember puncak
    ];
    
    // Yearly growth rate
    private $growthRate = 0.1; // 10% per year

    public function run()
    {
        $startYear = 2018;
        $endYear = 2020;
        
        for ($year = $startYear; $year <= $endYear; $year++) {
            $yearFactor = pow(1 + $this->growthRate, $year - $startYear);
            
            for ($month = 1; $month <= 12; $month++) {
                $seasonalFactor = $this->seasonalPattern[$month];
                
                // Random fluctuation (±15%)
                $fluctuation = 1 + (mt_rand(-150, 150) / 1000);
                
                foreach ($this->baseValues as $type => $base) {
                    // Calculate value with trend, seasonality and fluctuation
                    $value = $base * $yearFactor * $seasonalFactor * $fluctuation;
                    
                    // Special adjustment for December peak
                    if ($month == 12) {
                        $value *= 1.3; // Extra 30% for December
                    }
                    
                    DB::table('permintaan_darah')->updateOrInsert(
                        [
                            'tahun' => $year,
                            'bulan' => $month,
                            'golongan_darah' => $type
                        ],
                        [
                            'jumlah' => round($value),
                            'created_at' => Carbon::now(),
                            'updated_at' => Carbon::now()
                        ]
                    );
                }
            }
        }
        
        $this->command->info('Seeder data historis 2018-2020 berhasil ditambahkan!');
    }
    
    // Helper function to generate more realistic fluctuations
    private function smartFluctuation($month, $type)
    {
        // More fluctuation for rare types
        $baseRange = [
            'A' => 0.9,
            'B' => 0.85,
            'AB' => 0.8,
            'O' => 0.95
        ];
        
        // Additional seasonal effect
        $seasonEffect = [
            1 => 1.05,  // January effect
            7 => 0.97,  // July effect
            12 => 1.1    // December effect
        ];
        
        $fluctuation = $baseRange[$type] ?? 0.9;
        $fluctuation *= $seasonEffect[$month] ?? 1.0;
        
        return $fluctuation + (mt_rand(0, 30) / 100);
    }
}