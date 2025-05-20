<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('permintaan_darah', function (Blueprint $table) {
            // Ubah kolom jadi unsigned (tidak negatif)
            $table->year('tahun')->unsigned()->change();
            $table->tinyInteger('bulan')->unsigned()->change();
            $table->integer('gol_a')->unsigned()->change();
            $table->integer('gol_b')->unsigned()->change();
            $table->integer('gol_ab')->unsigned()->change();
            $table->integer('gol_o')->unsigned()->change();

            // Tambah index (jika dibutuhkan)
            $table->index('tahun');
        });
    }

    public function down(): void
    {
        Schema::table('permintaan_darah', function (Blueprint $table) {
            $table->dropIndex(['tahun']); // hapus index
            // Ubah balik ke signed
            $table->year('tahun')->change();
            $table->tinyInteger('bulan')->change();
            $table->integer('gol_a')->change();
            $table->integer('gol_b')->change();
            $table->integer('gol_ab')->change();
            $table->integer('gol_o')->change();
        });
    }
};
