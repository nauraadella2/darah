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
       Schema::create('pengujian', function (Blueprint $table) {
    $table->id();
    $table->foreignId('optimasi_id')->constrained('optimasi')->cascadeOnDelete();
    $table->foreignId('user_id')->constrained('users');
    $table->enum('golongan_darah', ['A','B','AB','O']);
    $table->decimal('mape', 5, 2); // MAPE untuk data testing (2024)
    $table->json('hasil_perbulan'); // {bulan: 1, aktual: 100, prediksi: 95, error: 5%}
    $table->timestamps();
    
    $table->index(['golongan_darah', 'user_id']);
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengujian');
    }
};
