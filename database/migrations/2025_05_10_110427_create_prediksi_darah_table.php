<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('prediksi_darah', function (Blueprint $table) {
            $table->id();
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O']);
            $table->integer('tahun');
            $table->integer('bulan');
            $table->decimal('jumlah', 10, 2);
            $table->decimal('alpha', 3, 2);
            $table->foreignId('optimized_alpha_id')->constrained('optimized_alphas');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
            
            $table->index(['golongan_darah', 'tahun', 'bulan']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('prediksi_darah');
    }
};