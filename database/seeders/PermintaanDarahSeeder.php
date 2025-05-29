<?php

namespace Database\Seeders;

use App\Models\PermintaanDarah;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PermintaanDarahSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Path ke file CSV
        $filePath = storage_path('app/Blood Request, 2012-2014.csv');
        
        // Baca file CSV
        $file = fopen($filePath, 'r');
        
        // Lewati 4 baris header
        for ($i = 0; $i < 4; $i++) {
            fgetcsv($file);
        }
        
        // Mapping nama bulan ke angka
        $bulanMapping = [
            'January' => 1,
            'February' => 2,
            'March' => 3,
            'April' => 4,
            'May' => 5,
            'June' => 6,
            'July' => 7,
            'August' => 8,
            'September' => 9,
            'October' => 10,
            'November' => 11,
            'December' => 12
        ];
        
        // Proses setiap baris data
        while (($row = fgetcsv($file)) !== false) {
            $namaBulan = trim($row[0]);
            $bulan = $bulanMapping[$namaBulan] ?? null;
            
            if (!$bulan) {
                continue; // Lewati jika bukan data bulan
            }
            
            // Data untuk tahun 2012
            $this->simpanData(2012, $bulan, [
                'gol_a' => (int)$row[1],
                'gol_b' => (int)$row[4],
                'gol_o' => (int)$row[7],
                'gol_ab' => (int)$row[10]
            ]);
            
            // Data untuk tahun 2013
            $this->simpanData(2013, $bulan, [
                'gol_a' => (int)$row[2],
                'gol_b' => (int)$row[5],
                'gol_o' => (int)$row[8],
                'gol_ab' => (int)$row[11]
            ]);
            
            // Data untuk tahun 2014
            $this->simpanData(2014, $bulan, [
                'gol_a' => (int)$row[3],
                'gol_b' => (int)$row[6],
                'gol_o' => (int)$row[9],
                'gol_ab' => (int)$row[12]
            ]);
        }
        
        fclose($file);
        $this->command->info('Seeder permintaan darah berhasil dijalankan!');
    }
    
    /**
     * Helper untuk menyimpan data
     */
    protected function simpanData($tahun, $bulan, $data)
    {
        PermintaanDarah::updateOrCreate(
            [
                'tahun' => $tahun,
                'bulan' => $bulan
            ],
            $data
        );
    }
}