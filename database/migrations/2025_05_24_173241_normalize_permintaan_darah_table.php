<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class NormalizePermintaanDarahTable extends Migration
{
    public function up()
    {
        // 1. Buat tabel baru berbentuk long format
        Schema::create('permintaan_darah_normalized', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan');
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O']);
            $table->integer('jumlah')->default(0);
            $table->timestamps();
            
            $table->unique(['tahun', 'bulan', 'golongan_darah']);
        });

        // 2. Migrasi data dari format wide ke long
        $records = DB::table('permintaan_darah')->get();
        
        foreach ($records as $record) {
            $data = [
                ['golongan' => 'A', 'jumlah' => $record->gol_a],
                ['golongan' => 'B', 'jumlah' => $record->gol_b],
                ['golongan' => 'AB', 'jumlah' => $record->gol_ab],
                ['golongan' => 'O', 'jumlah' => $record->gol_o],
            ];
            
            foreach ($data as $item) {
                DB::table('permintaan_darah_normalized')->insert([
                    'tahun' => $record->tahun,
                    'bulan' => $record->bulan,
                    'golongan_darah' => $item['golongan'],
                    'jumlah' => $item['jumlah'],
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                ]);
            }
        }

        // 3. Hapus tabel lama dan rename tabel baru (opsional)
        Schema::drop('permintaan_darah');
        Schema::rename('permintaan_darah_normalized', 'permintaan_darah');
    }

    public function down()
    {
        // Rollback: Kembalikan ke format wide
        Schema::create('permintaan_darah_wide', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan');
            $table->integer('gol_a')->default(0);
            $table->integer('gol_b')->default(0);
            $table->integer('gol_ab')->default(0);
            $table->integer('gol_o')->default(0);
            $table->timestamps();
            
            $table->unique(['tahun', 'bulan']);
        });

        // Migrasi data dari long ke wide
        $records = DB::table('permintaan_darah')
            ->select('tahun', 'bulan')
            ->distinct()
            ->get();

        foreach ($records as $record) {
            $data = DB::table('permintaan_darah')
                ->where('tahun', $record->tahun)
                ->where('bulan', $record->bulan)
                ->get()
                ->groupBy('golongan_darah');

            DB::table('permintaan_darah_wide')->insert([
                'tahun' => $record->tahun,
                'bulan' => $record->bulan,
                'gol_a' => $data['A']->first()->jumlah ?? 0,
                'gol_b' => $data['B']->first()->jumlah ?? 0,
                'gol_ab' => $data['AB']->first()->jumlah ?? 0,
                'gol_o' => $data['O']->first()->jumlah ?? 0,
                'created_at' => $data->first()->first()->created_at ?? now(),
                'updated_at' => $data->first()->first()->updated_at ?? now(),
            ]);
        }

        Schema::drop('permintaan_darah');
        Schema::rename('permintaan_darah_wide', 'permintaan_darah');
    }
}