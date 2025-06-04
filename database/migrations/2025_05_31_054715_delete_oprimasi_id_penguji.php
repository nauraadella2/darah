<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengujian', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['optimasi_id']);

            // Then drop the column
            $table->dropColumn('optimasi_id');
        });
    }

    public function down(): void
    {
        Schema::table('pengujian', function (Blueprint $table) {
            $table->unsignedBigInteger('optimasi_id');
            $table->foreign('optimasi_id')
                ->references('id')
                ->on('optimasi')
                ->onDelete('cascade');
            $table->decimal('gamma');
        });
    }
};
