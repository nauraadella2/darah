<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RebuildPrediksiDarahTable extends Migration
{
    public function up()
    {
        // 1. Hapus tabel jika sudah ada (DENGAN CONDITIONAL)
        if (Schema::hasTable('prediksi_darah')) {
            Schema::drop('prediksi_darah');
        }

        // 2. Buat tabel baru dengan struktur PERMANEN
        Schema::create('prediksi_darah', function (Blueprint $table) {
            $table->id();
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O']);
            $table->integer('tahun');
            $table->integer('bulan');
            $table->decimal('jumlah', 10, 2);
            $table->boolean('is_aktual')->default(false);
            $table->decimal('alpha', 3, 2);
            $table->double('beta', 8, 2);
            $table->unsignedBigInteger('optimasi_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            // Foreign keys dengan nama EXPLICIT
            $table->foreign('optimasi_id', 'fk_prediksi_optimasi')
                  ->references('id')
                  ->on('optimasi')
                  ->onDelete('cascade');
                  
            $table->foreign('user_id', 'fk_prediksi_user')
                  ->references('id')
                  ->on('users');

            // Index penting
            $table->index(['golongan_darah', 'tahun', 'bulan'], 'idx_gol_tahun_bulan');
        });
    }

    public function down()
    {
        Schema::dropIfExists('prediksi_darah');
    }
}