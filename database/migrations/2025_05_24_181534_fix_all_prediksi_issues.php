<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class FixAllPrediksiIssues extends Migration
{
    public function up()
    {
        // 1. Tambahkan kolom is_aktual jika belum ada (CARA AMAN)
        if (!Schema::hasColumn('prediksi_darah', 'is_aktual')) {
            Schema::table('prediksi_darah', function (Blueprint $table) {
                $table->boolean('is_aktual')->default(false)->after('jumlah');
            });
        }

        // 2. Perbaiki foreign key optimasi_id (TANPA DOCTRINE)
        DB::statement('
            ALTER TABLE prediksi_darah 
            ADD CONSTRAINT prediksi_darah_optimasi_id_foreign 
            FOREIGN KEY (optimasi_id) REFERENCES optimasi(id) 
            ON DELETE CASCADE
        ');
    }

    public function down()
    {
        // Tidak perlu rollback
    }
}