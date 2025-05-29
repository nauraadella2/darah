<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BloodRequestDataSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $data = [
    [
        'tahun' => 2015,
        'bulan' => 1,
        'gol_a' => 263,
        'gol_b' => 452,
        'gol_o' => 502,
        'gol_ab' => 114,
        'created_at' => $now,
        'updated_at' => $now
    ],
    [
        'tahun' => 2015,
        'bulan' => 2,
        'gol_a' => 346,
        'gol_b' => 391,
        'gol_o' => 456,
        'gol_ab' => 82,
        'created_at' => $now,
        'updated_at' => $now
    ],
    [
        'tahun' => 2015,
        'bulan' => 3,
        'gol_a' => 303,
        'gol_b' => 432,
        'gol_o' => 532,
        'gol_ab' => 100,
        'created_at' => $now,
        'updated_at' => $now
    ],
    [
        'tahun' => 2015,
        'bulan' => 4,
        'gol_a' => 308,
        'gol_b' => 571,
        'gol_o' => 514,
        'gol_ab' => 114,
        'created_at' => $now,
        'updated_at' => $now
    ],
    [
        'tahun' => 2015,
        'bulan' => 5,
        'gol_a' => 271,
        'gol_b' => 376,
        'gol_o' => 488,
        'gol_ab' => 90,
        'created_at' => $now,
        'updated_at' => $now
    ],
    [
        'tahun' => 2015,
        'bulan' => 6,
        'gol_a' => 306,
        'gol_b' => 437,
        'gol_o' => 500,
        'gol_ab' => 118,
        'created_at' => $now,
        'updated_at' => $now
    ],
    [
        'tahun' => 2015,
        'bulan' => 7,
        'gol_a' => 279,
        'gol_b' => 339,
        'gol_o' => 400,
        'gol_ab' => 71,
        'created_at' => $now,
        'updated_at' => $now
    ],
    [
        'tahun' => 2015,
        'bulan' => 8,
        'gol_a' => 297,
        'gol_b' => 450,
        'gol_o' => 477,
        'gol_ab' => 71,
        'created_at' => $now,
        'updated_at' => $now
    ],
    [
        'tahun' => 2015,
        'bulan' => 9,
        'gol_a' => 270,
        'gol_b' => 378,
        'gol_o' => 491,
        'gol_ab' => 77,
        'created_at' => $now,
        'updated_at' => $now
    ],
    [
        'tahun' => 2015,
        'bulan' => 10,
        'gol_a' => 375,
        'gol_b' => 494,
        'gol_o' => 652,
        'gol_ab' => 135,
        'created_at' => $now,
        'updated_at' => $now
    ],
    [
        'tahun' => 2015,
        'bulan' => 11,
        'gol_a' => 333,
        'gol_b' => 437,
        'gol_o' => 559,
        'gol_ab' => 87,
        'created_at' => $now,
        'updated_at' => $now
    ],
    [
        'tahun' => 2015,
        'bulan' => 12,
        'gol_a' => 363,
        'gol_b' => 485,
        'gol_o' => 673,
        'gol_ab' => 99,
        'created_at' => $now,
        'updated_at' => $now
    ]
        ];

        // Insert data
        DB::table('permintaan_darah')->insert($data);

        $this->command->info('Blood request data for 2015 seeded successfully!');
    }
}