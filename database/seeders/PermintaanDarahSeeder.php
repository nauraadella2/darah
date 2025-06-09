<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermintaanDarahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            // 2022
            ['tanggal' => 'Sep-22', 'A' => 62, 'B' => 53, 'O' => 169, 'AB' => 6],
            ['tanggal' => '01/10/2022', 'A' => 53, 'B' => 60, 'O' => 202, 'AB' => 8],
            ['tanggal' => 'Nov-22', 'A' => 58, 'B' => 52, 'O' => 138, 'AB' => 5],
            ['tanggal' => '01/12/2022', 'A' => 58, 'B' => 46, 'O' => 241, 'AB' => 2],
            
            // 2023
            ['tanggal' => 'Jan-23', 'A' => 77, 'B' => 44, 'O' => 204, 'AB' => 9],
            ['tanggal' => '01/02/2023', 'A' => 83, 'B' => 43, 'O' => 222, 'AB' => 8],
            ['tanggal' => 'Mar-23', 'A' => 41, 'B' => 39, 'O' => 146, 'AB' => 4],
            ['tanggal' => '01/04/2023', 'A' => 45, 'B' => 46, 'O' => 262, 'AB' => 6],
            ['tanggal' => 'May-23', 'A' => 80, 'B' => 50, 'O' => 239, 'AB' => 7],
            ['tanggal' => '01/06/2023', 'A' => 90, 'B' => 51, 'O' => 213, 'AB' => 7],
            ['tanggal' => 'Jul-23', 'A' => 47, 'B' => 32, 'O' => 199, 'AB' => 2],
            ['tanggal' => '01/08/2023', 'A' => 40, 'B' => 49, 'O' => 231, 'AB' => 2],
            ['tanggal' => 'Sep-23', 'A' => 71, 'B' => 47, 'O' => 167, 'AB' => 4],
            ['tanggal' => '01/10/2023', 'A' => 82, 'B' => 47, 'O' => 219, 'AB' => 3],
            ['tanggal' => 'Nov-23', 'A' => 90, 'B' => 42, 'O' => 188, 'AB' => 4],
            ['tanggal' => '01/12/2023', 'A' => 60, 'B' => 45, 'O' => 207, 'AB' => 4],
            
            // 2024
            ['tanggal' => 'Jan-24', 'A' => 42, 'B' => 35, 'O' => 200, 'AB' => 8],
            ['tanggal' => '01/02/2024', 'A' => 55, 'B' => 36, 'O' => 199, 'AB' => 2],
            ['tanggal' => 'Mar-24', 'A' => 41, 'B' => 55, 'O' => 122, 'AB' => 6],
            ['tanggal' => '01/04/2024', 'A' => 48, 'B' => 61, 'O' => 215, 'AB' => 7],
            ['tanggal' => 'May-24', 'A' => 62, 'B' => 66, 'O' => 218, 'AB' => 3],
            ['tanggal' => '01/06/2024', 'A' => 52, 'B' => 52, 'O' => 215, 'AB' => 8],
            ['tanggal' => 'Jul-24', 'A' => 44, 'B' => 49, 'O' => 199, 'AB' => 4],
            ['tanggal' => '01/08/2024', 'A' => 40, 'B' => 42, 'O' => 236, 'AB' => 5],
            ['tanggal' => 'Sep-24', 'A' => 44, 'B' => 52, 'O' => 234, 'AB' => 10],
            ['tanggal' => '01/10/2024', 'A' => 67, 'B' => 69, 'O' => 251, 'AB' => 9],
            ['tanggal' => 'Nov-24', 'A' => 58, 'B' => 55, 'O' => 231, 'AB' => 2],
            ['tanggal' => '01/12/2024', 'A' => 54, 'B' => 60, 'O' => 245, 'AB' => 8],
            
            // 2025
            ['tanggal' => 'Jan-25', 'A' => 53, 'B' => 44, 'O' => 139, 'AB' => 1],
            ['tanggal' => '01/02/2025', 'A' => 91, 'B' => 51, 'O' => 197, 'AB' => 2],
            ['tanggal' => 'Mar-25', 'A' => 60, 'B' => 51, 'O' => 200, 'AB' => 1],
        ];

        foreach ($data as $entry) {
            $date = $this->parseDate($entry['tanggal']);
            
            DB::table('permintaan_darah')->insert([
                'tahun' => $date->year,
                'bulan' => $date->month,
                'golongan_darah' => 'A',
                'jumlah' => $entry['A'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::table('permintaan_darah')->insert([
                'tahun' => $date->year,
                'bulan' => $date->month,
                'golongan_darah' => 'B',
                'jumlah' => $entry['B'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            DB::table('permintaan_darah')->insert([
                'tahun' => $date->year,
                'bulan' => $date->month,
                'golongan_darah' => 'O',
                'jumlah' => $entry['O'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            
            if (isset($entry['AB'])) {
                DB::table('permintaan_darah')->insert([
                    'tahun' => $date->year,
                    'bulan' => $date->month,
                    'golongan_darah' => 'AB',
                    'jumlah' => $entry['AB'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Parse berbagai format tanggal ke objek Carbon
     */
    private function parseDate(string $dateString): \Carbon\Carbon
    {
        if (str_contains($dateString, '-')) {
            // Format seperti "Sep-22"
            return Carbon::createFromFormat('M-y', $dateString);
        } else {
            // Format seperti "01/10/2022"
            return Carbon::createFromFormat('d/m/Y', $dateString);
        }
    }
}