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
        Schema::dropIfExists('speed_result_penalties');
        Schema::dropIfExists('serc_penalties');
        Schema::dropIfExists('serc_disqualifications');

        Schema::table('speed_results', function (Blueprint $table) {
            $table->dropColumn('disqualification');
        });

        Schema::create('penalties', function (Blueprint $table) {
            $table->id();
            $table->integer('code');
            $table->morphs('event');
            $table->morphs('entity');
            $table->timestamps();
        });

        Schema::create('disqualifications', function (Blueprint $table) {
            $table->id();
            $table->integer('code');
            $table->morphs('event');
            $table->morphs('entity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penalties');
        Schema::dropIfExists('disqualifications');
    }
};
