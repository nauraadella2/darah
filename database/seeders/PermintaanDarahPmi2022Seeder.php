<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermintaanDarahPmi2022Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // 2022 Data
            ['tahun' => 2022, 'bulan' => 1, 'A' => 45, 'AB' => 10, 'B' => 60, 'O' => 85],
            ['tahun' => 2022, 'bulan' => 2, 'A' => 42, 'AB' => 9, 'B' => 58, 'O' => 82],
            ['tahun' => 2022, 'bulan' => 3, 'A' => 48, 'AB' => 11, 'B' => 75, 'O' => 78],
            ['tahun' => 2022, 'bulan' => 4, 'A' => 50, 'AB' => 12, 'B' => 65, 'O' => 90],
            ['tahun' => 2022, 'bulan' => 5, 'A' => 47, 'AB' => 10, 'B' => 63, 'O' => 86],
            ['tahun' => 2022, 'bulan' => 6, 'A' => 52, 'AB' => 13, 'B' => 68, 'O' => 92],
            ['tahun' => 2022, 'bulan' => 7, 'A' => 69, 'AB' => 18, 'B' => 70, 'O' => 94],
            ['tahun' => 2022, 'bulan' => 8, 'A' => 53, 'AB' => 12, 'B' => 67, 'O' => 91],
            ['tahun' => 2022, 'bulan' => 9, 'A' => 49, 'AB' => 11, 'B' => 64, 'O' => 89],
            ['tahun' => 2022, 'bulan' => 10, 'A' => 51, 'AB' => 12, 'B' => 66, 'O' => 93],
            ['tahun' => 2022, 'bulan' => 11, 'A' => 54, 'AB' => 13, 'B' => 69, 'O' => 95],
            ['tahun' => 2022, 'bulan' => 12, 'A' => 58, 'AB' => 15, 'B' => 72, 'O' => 97],
            
            // 2023 Data
            ['tahun' => 2023, 'bulan' => 1, 'A' => 44, 'AB' => 9, 'B' => 61, 'O' => 84],
            ['tahun' => 2023, 'bulan' => 2, 'A' => 40, 'AB' => 8, 'B' => 59, 'O' => 83],
            ['tahun' => 2023, 'bulan' => 3, 'A' => 60, 'AB' => 10, 'B' => 63, 'O' => 87],
            ['tahun' => 2023, 'bulan' => 4, 'A' => 49, 'AB' => 11, 'B' => 66, 'O' => 91],
            ['tahun' => 2023, 'bulan' => 5, 'A' => 43, 'AB' => 9, 'B' => 80, 'O' => 102],
            ['tahun' => 2023, 'bulan' => 6, 'A' => 50, 'AB' => 12, 'B' => 67, 'O' => 93],
            ['tahun' => 2023, 'bulan' => 7, 'A' => 56, 'AB' => 14, 'B' => 71, 'O' => 96],
            ['tahun' => 2023, 'bulan' => 8, 'A' => 52, 'AB' => 13, 'B' => 68, 'O' => 92],
            ['tahun' => 2023, 'bulan' => 9, 'A' => 48, 'AB' => 11, 'B' => 65, 'O' => 90],
            ['tahun' => 2023, 'bulan' => 10, 'A' => 53, 'AB' => 12, 'B' => 69, 'O' => 94],
            ['tahun' => 2023, 'bulan' => 11, 'A' => 57, 'AB' => 14, 'B' => 73, 'O' => 98],
            ['tahun' => 2023, 'bulan' => 12, 'A' => 61, 'AB' => 13, 'B' => 60, 'O' => 95],
            
            // 2024 Data
            ['tahun' => 2024, 'bulan' => 1, 'A' => 34, 'AB' => 8, 'B' => 71, 'O' => 87],
            ['tahun' => 2024, 'bulan' => 2, 'A' => 36, 'AB' => 9, 'B' => 68, 'O' => 86],
            ['tahun' => 2024, 'bulan' => 3, 'A' => 42, 'AB' => 10, 'B' => 70, 'O' => 89],
            ['tahun' => 2024, 'bulan' => 4, 'A' => 43, 'AB' => 11, 'B' => 70, 'O' => 99],
            ['tahun' => 2024, 'bulan' => 5, 'A' => 30, 'AB' => 6, 'B' => 39, 'O' => 51],
            ['tahun' => 2024, 'bulan' => 6, 'A' => 50, 'AB' => 15, 'B' => 69, 'O' => 99],
            ['tahun' => 2024, 'bulan' => 7, 'A' => 64, 'AB' => 12, 'B' => 56, 'O' => 98],
            ['tahun' => 2024, 'bulan' => 8, 'A' => 24, 'AB' => 19, 'B' => 66, 'O' => 84],
            ['tahun' => 2024, 'bulan' => 9, 'A' => 47, 'AB' => 12, 'B' => 70, 'O' => 80],
            ['tahun' => 2024, 'bulan' => 10, 'A' => 52, 'AB' => 13, 'B' => 68, 'O' => 93],
            ['tahun' => 2024, 'bulan' => 11, 'A' => 55, 'AB' => 14, 'B' => 72, 'O' => 97],
            ['tahun' => 2024, 'bulan' => 12, 'A' => 60, 'AB' => 15, 'B' => 75, 'O' => 100],
            
            // 2025 Data (Jan-Apr)
            ['tahun' => 2025, 'bulan' => 1, 'A' => 38, 'AB' => 9, 'B' => 67, 'O' => 88],
            ['tahun' => 2025, 'bulan' => 2, 'A' => 35, 'AB' => 8, 'B' => 65, 'O' => 85],
            ['tahun' => 2025, 'bulan' => 3, 'A' => 45, 'AB' => 11, 'B' => 70, 'O' => 90],
            ['tahun' => 2025, 'bulan' => 4, 'A' => 48, 'AB' => 12, 'B' => 72, 'O' => 92],
        ];

        foreach ($data as $entry) {
            // Golongan Darah A
            DB::table('permintaan_darah')->updateOrInsert(
                [
                    'tahun' => $entry['tahun'],
                    'bulan' => $entry['bulan'],
                    'golongan_darah' => 'A'
                ],
                [
                    'jumlah' => $entry['A'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Golongan Darah AB
            DB::table('permintaan_darah')->updateOrInsert(
                [
                    'tahun' => $entry['tahun'],
                    'bulan' => $entry['bulan'],
                    'golongan_darah' => 'AB'
                ],
                [
                    'jumlah' => $entry['AB'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Golongan Darah B
            DB::table('permintaan_darah')->updateOrInsert(
                [
                    'tahun' => $entry['tahun'],
                    'bulan' => $entry['bulan'],
                    'golongan_darah' => 'B'
                ],
                [
                    'jumlah' => $entry['B'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            // Golongan Darah O
            DB::table('permintaan_darah')->updateOrInsert(
                [
                    'tahun' => $entry['tahun'],
                    'bulan' => $entry['bulan'],
                    'golongan_darah' => 'O'
                ],
                [
                    'jumlah' => $entry['O'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}