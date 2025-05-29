<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsAktualToPrediksiDarahTable extends Migration
{
    public function up()
    {
        Schema::table('prediksi_darah', function (Blueprint $table) {
            $table->boolean('is_aktual')->default(false)->after('jumlah');
        });
    }

    public function down()
    {
        Schema::table('prediksi_darah', function (Blueprint $table) {
            $table->dropColumn('is_aktual');
        });
    }
}