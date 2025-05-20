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
        Schema::create('optimized_alphas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // ID user yang menghitung
            $table->foreignId('permintaan_darah_id')->constrained('permintaan_darah')->onDelete('cascade'); // ID data darah
            $table->enum('golongan_darah', ['A', 'B', 'AB', 'O']); // Kolom bahasa Indonesia
            $table->decimal('alpha', 2, 1);  // Tetap 'alpha' (istilah algoritma)
            $table->decimal('mape', 5, 2);   // Tetap 'mape' (istilah statistik)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('optimized_alphas');
    }
};
