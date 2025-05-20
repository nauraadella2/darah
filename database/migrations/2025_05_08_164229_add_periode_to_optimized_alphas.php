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
    Schema::table('optimized_alphas', function (Blueprint $table) {
        $table->integer('periode_mulai')->nullable()->after('mape');
        $table->integer('periode_selesai')->nullable()->after('periode_mulai');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('optimized_alphas', function (Blueprint $table) {
            //
        });
    }
};
