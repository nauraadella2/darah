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
            // 2012 Data
            ['tahun' => 2012, 'bulan' => 1, 'A' => 300, 'B' => 405, 'O' => 460, 'AB' => 92],
            ['tahun' => 2012, 'bulan' => 2, 'A' => 293, 'B' => 355, 'O' => 473, 'AB' => 77],
            ['tahun' => 2012, 'bulan' => 3, 'A' => 239, 'B' => 375, 'O' => 424, 'AB' => 87],
            ['tahun' => 2012, 'bulan' => 4, 'A' => 209, 'B' => 340, 'O' => 394, 'AB' => 62],
            ['tahun' => 2012, 'bulan' => 5, 'A' => 274, 'B' => 307, 'O' => 399, 'AB' => 77],
            ['tahun' => 2012, 'bulan' => 6, 'A' => 215, 'B' => 266, 'O' => 350, 'AB' => 35],
            ['tahun' => 2012, 'bulan' => 7, 'A' => 212, 'B' => 240, 'O' => 347, 'AB' => 81],
            ['tahun' => 2012, 'bulan' => 8, 'A' => 281, 'B' => 345, 'O' => 399, 'AB' => 46],
            ['tahun' => 2012, 'bulan' => 9, 'A' => 241, 'B' => 335, 'O' => 399, 'AB' => 80],
            ['tahun' => 2012, 'bulan' => 10, 'A' => 237, 'B' => 305, 'O' => 425, 'AB' => 80],
            ['tahun' => 2012, 'bulan' => 11, 'A' => 258, 'B' => 388, 'O' => 443, 'AB' => 74],
            ['tahun' => 2012, 'bulan' => 12, 'A' => 218, 'B' => 324, 'O' => 428, 'AB' => 59],
            
            // 2013 Data
            ['tahun' => 2013, 'bulan' => 1, 'A' => 278, 'B' => 355, 'O' => 524, 'AB' => 73],
            ['tahun' => 2013, 'bulan' => 2, 'A' => 303, 'B' => 387, 'O' => 462, 'AB' => 94],
            ['tahun' => 2013, 'bulan' => 3, 'A' => 307, 'B' => 434, 'O' => 539, 'AB' => 66],
            ['tahun' => 2013, 'bulan' => 4, 'A' => 349, 'B' => 414, 'O' => 460, 'AB' => 74],
            ['tahun' => 2013, 'bulan' => 5, 'A' => 330, 'B' => 409, 'O' => 492, 'AB' => 101],
            ['tahun' => 2013, 'bulan' => 6, 'A' => 245, 'B' => 267, 'O' => 374, 'AB' => 56],
            ['tahun' => 2013, 'bulan' => 7, 'A' => 276, 'B' => 390, 'O' => 462, 'AB' => 86],
            ['tahun' => 2013, 'bulan' => 8, 'A' => 243, 'B' => 302, 'O' => 392, 'AB' => 49],
            ['tahun' => 2013, 'bulan' => 9, 'A' => 305, 'B' => 390, 'O' => 509, 'AB' => 78],
            ['tahun' => 2013, 'bulan' => 10, 'A' => 300, 'B' => 374, 'O' => 554, 'AB' => 69],
            ['tahun' => 2013, 'bulan' => 11, 'A' => 368, 'B' => 499, 'O' => 528, 'AB' => 103],
            ['tahun' => 2013, 'bulan' => 12, 'A' => 309, 'B' => 359, 'O' => 525, 'AB' => 87],
            
            // 2014 Data
            ['tahun' => 2014, 'bulan' => 1, 'A' => 414, 'B' => 447, 'O' => 623, 'AB' => 131],
            ['tahun' => 2014, 'bulan' => 2, 'A' => 284, 'B' => 454, 'O' => 541, 'AB' => 97],
            ['tahun' => 2014, 'bulan' => 3, 'A' => 373, 'B' => 440, 'O' => 598, 'AB' => 93],
            ['tahun' => 2014, 'bulan' => 4, 'A' => 376, 'B' => 412, 'O' => 635, 'AB' => 80],
            ['tahun' => 2014, 'bulan' => 5, 'A' => 344, 'B' => 420, 'O' => 576, 'AB' => 87],
            ['tahun' => 2014, 'bulan' => 6, 'A' => 338, 'B' => 481, 'O' => 549, 'AB' => 73],
            ['tahun' => 2014, 'bulan' => 7, 'A' => 223, 'B' => 246, 'O' => 345, 'AB' => 56],
            ['tahun' => 2014, 'bulan' => 8, 'A' => 253, 'B' => 402, 'O' => 495, 'AB' => 63],
            ['tahun' => 2014, 'bulan' => 9, 'A' => 296, 'B' => 390, 'O' => 523, 'AB' => 112],
            ['tahun' => 2014, 'bulan' => 10, 'A' => 338, 'B' => 445, 'O' => 558, 'AB' => 90],
            ['tahun' => 2014, 'bulan' => 11, 'A' => 323, 'B' => 420, 'O' => 476, 'AB' => 92],
            ['tahun' => 2014, 'bulan' => 12, 'A' => 345, 'B' => 462, 'O' => 591, 'AB' => 114],
            
            // 2015 Data
            ['tahun' => 2015, 'bulan' => 1, 'A' => 263, 'B' => 452, 'O' => 502, 'AB' => 114],
            ['tahun' => 2015, 'bulan' => 2, 'A' => 346, 'B' => 391, 'O' => 456, 'AB' => 82],
            ['tahun' => 2015, 'bulan' => 3, 'A' => 303, 'B' => 432, 'O' => 532, 'AB' => 100],
            ['tahun' => 2015, 'bulan' => 4, 'A' => 308, 'B' => 571, 'O' => 514, 'AB' => 114],
            ['tahun' => 2015, 'bulan' => 5, 'A' => 271, 'B' => 376, 'O' => 488, 'AB' => 90],
            ['tahun' => 2015, 'bulan' => 6, 'A' => 306, 'B' => 437, 'O' => 500, 'AB' => 118],
            ['tahun' => 2015, 'bulan' => 7, 'A' => 279, 'B' => 339, 'O' => 400, 'AB' => 71],
            ['tahun' => 2015, 'bulan' => 8, 'A' => 297, 'B' => 450, 'O' => 477, 'AB' => 71],
            ['tahun' => 2015, 'bulan' => 9, 'A' => 270, 'B' => 378, 'O' => 491, 'AB' => 77],
            ['tahun' => 2015, 'bulan' => 10, 'A' => 375, 'B' => 494, 'O' => 652, 'AB' => 135],
            ['tahun' => 2015, 'bulan' => 11, 'A' => 333, 'B' => 437, 'O' => 559, 'AB' => 87],
            ['tahun' => 2015, 'bulan' => 12, 'A' => 363, 'B' => 485, 'O' => 673, 'AB' => 99],
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