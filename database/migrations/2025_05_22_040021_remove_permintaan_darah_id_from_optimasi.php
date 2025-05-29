<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('optimasi', function (Blueprint $table) {
            // 1. Hapus foreign key constraint
            $table->dropForeign('optimized_alphas_permintaan_darah_id_foreign');
            
            // 2. Hapus index
            $table->dropIndex('optimized_alphas_permintaan_darah_id_foreign');
            
            // 3. Hapus kolom
            $table->dropColumn('permintaan_darah_id');
        });
    }

    public function down()
    {
        Schema::table('optimasi', function (Blueprint $table) {
            // Untuk rollback (opsional)
            $table->unsignedBigInteger('permintaan_darah_id')->after('user_id');
            $table->foreign('permintaan_darah_id', 'optimized_alphas_permintaan_darah_id_foreign')
                  ->references('id')
                  ->on('permintaan_darah')
                  ->onDelete('cascade');
        });
    }
};