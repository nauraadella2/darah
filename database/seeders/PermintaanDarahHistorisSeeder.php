<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermintaanDarahHistorisSeeder extends Seeder
{
    /**
     * Jalankan seeder untuk dummy data tahun 2021 & Jan–Aug 2022.
     */
    public function run(): void
    {
        $data = [
            // Tahun 2021
            ['tanggal' => '01/01/2021', 'A' => 51, 'B' => 40, 'O' => 180, 'AB' => 4],
            ['tanggal' => '01/02/2021', 'A' => 48, 'B' => 42, 'O' => 165, 'AB' => 5],
            ['tanggal' => '01/03/2021', 'A' => 55, 'B' => 45, 'O' => 172, 'AB' => 4],
            ['tanggal' => '01/04/2021', 'A' => 60, 'B' => 38, 'O' => 190, 'AB' => 3],
            ['tanggal' => '01/05/2021', 'A' => 64, 'B' => 50, 'O' => 210, 'AB' => 6],
            ['tanggal' => '01/06/2021', 'A' => 58, 'B' => 47, 'O' => 195, 'AB' => 5],
            ['tanggal' => '01/07/2021', 'A' => 49, 'B' => 42, 'O' => 182, 'AB' => 6],
            ['tanggal' => '01/08/2021', 'A' => 52, 'B' => 43, 'O' => 176, 'AB' => 5],
            ['tanggal' => '01/09/2021', 'A' => 56, 'B' => 41, 'O' => 188, 'AB' => 4],
            ['tanggal' => '01/10/2021', 'A' => 62, 'B' => 44, 'O' => 201, 'AB' => 3],
            ['tanggal' => '01/11/2021', 'A' => 57, 'B' => 46, 'O' => 207, 'AB' => 4],
            ['tanggal' => '01/12/2021', 'A' => 59, 'B' => 49, 'O' => 198, 'AB' => 5],

            // Tahun 2022 (Jan–Aug)
            ['tanggal' => '01/01/2022', 'A' => 60, 'B' => 48, 'O' => 209, 'AB' => 6],
            ['tanggal' => '01/02/2022', 'A' => 65, 'B' => 45, 'O' => 222, 'AB' => 4],
            ['tanggal' => '01/03/2022', 'A' => 58, 'B' => 43, 'O' => 210, 'AB' => 5],
            ['tanggal' => '01/04/2022', 'A' => 66, 'B' => 41, 'O' => 198, 'AB' => 3],
            ['tanggal' => '01/05/2022', 'A' => 59, 'B' => 47, 'O' => 217, 'AB' => 4],
            ['tanggal' => '01/06/2022', 'A' => 61, 'B' => 46, 'O' => 226, 'AB' => 6],
            ['tanggal' => '01/07/2022', 'A' => 63, 'B' => 50, 'O' => 198, 'AB' => 5],
            ['tanggal' => '01/08/2022', 'A' => 67, 'B' => 55, 'O' => 215, 'AB' => 4],
        ];

        foreach ($data as $entry) {
            $date = Carbon::createFromFormat('d/m/Y', $entry['tanggal']);

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
