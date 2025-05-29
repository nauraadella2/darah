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
        $table->float('beta')->after('alpha');
        $table->renameColumn('optimized_alpha_id', 'optimasi_id');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::table('prediksi_darah', function (Blueprint $table) {
        $table->dropColumn('beta');
        $table->renameColumn('optimasi_id', 'optimized_alpha_id');
    });
}
};
