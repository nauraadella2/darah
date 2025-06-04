<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('prediksi_darah', function (Blueprint $table) {
            // Hapus composite index
            $table->dropIndex('idx_gol_tahun_bulan');
        });
    }

    public function down()
    {
        Schema::table('prediksi_darah', function (Blueprint $table) {
            // Recreate the composite index jika rollback
            $table->index(['golongan_darah', 'tahun', 'bulan'], 'idx_gol_tahun_bulan');
        });
    }
};
