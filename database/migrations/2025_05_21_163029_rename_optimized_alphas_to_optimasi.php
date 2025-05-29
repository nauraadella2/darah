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
    Schema::rename('optimized_alphas', 'optimasi');
}

    /**
     * Reverse the migrations.
     */
    public function down()
{
    Schema::rename('optimasi', 'optimized_alphas');
}
};
