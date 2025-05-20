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
        $table->decimal('rmse', 10, 2)->after('mape')->nullable();
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
