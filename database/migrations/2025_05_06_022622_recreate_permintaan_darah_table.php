<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RecreatePermintaanDarahTable extends Migration
{
    public function up()
    {
        // Hapus tabel jika ada (dengan foreign key check disabled)
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('permintaan_darah');
        Schema::enableForeignKeyConstraints();

        // Buat tabel baru dengan struktur benar
        Schema::create('permintaan_darah', function (Blueprint $table) {
            $table->id();
            $table->year('tahun')->default(now()->year);
            $table->tinyInteger('bulan')->unsigned();
            $table->integer('gol_a')->unsigned()->default(0);
            $table->integer('gol_b')->unsigned()->default(0);
            $table->integer('gol_ab')->unsigned()->default(0);
            $table->integer('gol_o')->unsigned()->default(0);
            $table->timestamps();
            
            $table->unique(['tahun', 'bulan']); // Pastikan tidak ada duplikasi periode
        });
    }

    public function down()
    {
        Schema::dropIfExists('permintaan_darah');
    }
}