<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('better_judge_logs', function (Blueprint $table) {
            $table->dropForeign(['competition']);
            $table->foreign('competition')->references('id')->on('competitions')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('better_judge_logs', function (Blueprint $table) {
            $table->dropForeign(['competition']);
            $table->foreign('competition')->references('id')->on('competitions')->cascade();
        });
    }
};
