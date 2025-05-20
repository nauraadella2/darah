<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('permintaan_darah', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->tinyInteger('bulan'); // 1 = Januari, dst
            $table->integer('gol_a');
            $table->integer('gol_b');
            $table->integer('gol_ab');
            $table->integer('gol_o');
            $table->timestamps();
        
            $table->unique(['tahun', 'bulan']); // Hindari duplikat data per bulan
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permintaan_darah');
    }
};
