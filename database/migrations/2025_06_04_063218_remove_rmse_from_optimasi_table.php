<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('optimasi', function (Blueprint $table) {
            $table->dropColumn('rmse');
        });
    }

    public function down()
    {
        Schema::table('optimasi', function (Blueprint $table) {
            $table->decimal('rmse', 10, 2)->nullable();
        });
    }
};