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
        Schema::table('prediksi_darah', function (Blueprint $table) {
            // First drop the foreign key constraint
            $table->dropForeign('fk_prediksi_optimasi');

            // Then drop the column
            $table->dropColumn('optimasi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prediksi_darah', function (Blueprint $table) {
            // Recreate the column first
            $table->unsignedBigInteger('optimasi_id')->nullable();

            // Then recreate the foreign key constraint
            $table->foreign('optimasi_id', 'fk_prediksi_optimasi')
                ->references('id')
                ->on('optimasi')
                ->onDelete('cascade');
        });
    }
};
