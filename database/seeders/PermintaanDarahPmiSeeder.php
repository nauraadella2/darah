<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermintaanDarahPmiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            // 2023 Data
            ['tahun' => 2023, 'bulan' => 1, 'A' => 40, 'AB' => 12, 'B' => 55, 'O' => 63],
            ['tahun' => 2023, 'bulan' => 2, 'A' => 38, 'AB' => 9, 'B' => 49, 'O' => 58],
            ['tahun' => 2023, 'bulan' => 3, 'A' => 45, 'AB' => 10, 'B' => 60, 'O' => 65],
            ['tahun' => 2023, 'bulan' => 4, 'A' => 43, 'AB' => 11, 'B' => 57, 'O' => 62],
            ['tahun' => 2023, 'bulan' => 5, 'A' => 39, 'AB' => 13, 'B' => 50, 'O' => 60],
            ['tahun' => 2023, 'bulan' => 6, 'A' => 42, 'AB' => 12, 'B' => 55, 'O' => 64],
            ['tahun' => 2023, 'bulan' => 7, 'A' => 41, 'AB' => 10, 'B' => 58, 'O' => 67],
            ['tahun' => 2023, 'bulan' => 8, 'A' => 47, 'AB' => 14, 'B' => 62, 'O' => 70],
            ['tahun' => 2023, 'bulan' => 9, 'A' => 44, 'AB' => 12, 'B' => 54, 'O' => 63],
            ['tahun' => 2023, 'bulan' => 10, 'A' => 40, 'AB' => 9, 'B' => 50, 'O' => 59],
            ['tahun' => 2023, 'bulan' => 11, 'A' => 46, 'AB' => 13, 'B' => 57, 'O' => 68],
            ['tahun' => 2023, 'bulan' => 12, 'A' => 61, 'AB' => 13, 'B' => 60, 'O' => 95],
            
            // 2024 Data
            ['tahun' => 2024, 'bulan' => 1, 'A' => 34, 'AB' => 8, 'B' => 71, 'O' => 87],
            ['tahun' => 2024, 'bulan' => 2, 'A' => 36, 'AB' => 9, 'B' => 67, 'O' => 82],
            ['tahun' => 2024, 'bulan' => 3, 'A' => 42, 'AB' => 11, 'B' => 63, 'O' => 78],
            ['tahun' => 2024, 'bulan' => 4, 'A' => 38, 'AB' => 10, 'B' => 60, 'O' => 75],
            ['tahun' => 2024, 'bulan' => 5, 'A' => 40, 'AB' => 12, 'B' => 65, 'O' => 81],
            ['tahun' => 2024, 'bulan' => 6, 'A' => 44, 'AB' => 13, 'B' => 66, 'O' => 84],
            ['tahun' => 2024, 'bulan' => 7, 'A' => 41, 'AB' => 10, 'B' => 59, 'O' => 72],
            ['tahun' => 2024, 'bulan' => 8, 'A' => 39, 'AB' => 11, 'B' => 61, 'O' => 79],
            ['tahun' => 2024, 'bulan' => 9, 'A' => 37, 'AB' => 8, 'B' => 58, 'O' => 76],
            ['tahun' => 2024, 'bulan' => 10, 'A' => 45, 'AB' => 12, 'B' => 63, 'O' => 80],
            ['tahun' => 2024, 'bulan' => 11, 'A' => 46, 'AB' => 13, 'B' => 65, 'O' => 85],
            ['tahun' => 2024, 'bulan' => 12, 'A' => 49, 'AB' => 14, 'B' => 68, 'O' => 88],
        ];

        foreach ($data as $entry) {
            // Insert for blood type A
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

            // Insert for blood type AB
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

            // Insert for blood type B
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

            // Insert for blood type O
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