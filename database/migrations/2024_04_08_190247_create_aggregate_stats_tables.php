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
        // Record placings over time, for each club etc
        Schema::create('stats_results', function (Blueprint $table) {
       
            $table->foreignId('competition')->constrained("competitions")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('team')->constrained("competition_teams")->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('league'); // O, A, B, F, etc
            $table->decimal('points');
            $table->integer('place');

            //$table->unique(['competition', 'team', 'league']);

        });

        // To show fastest times, change over time, and times per teams and clubs etc
        Schema::create('stats_times', function (Blueprint $table) {

            $table->foreignId('competition')->constrained("competitions")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('team')->constrained("competition_teams")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('event')->constrained("speed_events")->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('time'); // This will be the result time including ny pens, etc
            $table->decimal('points');
            $table->integer('place');
            
            $table->unique(['competition', 'team', 'event']);
   
        });

        Schema::create('stats_serc', function (Blueprint $table) {

            $table->foreignId('competition')->constrained("competitions")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('team')->constrained("competition_teams")->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('event')->constrained("sercs")->cascadeOnDelete()->cascadeOnUpdate();
            $table->decimal('score');
            $table->decimal('points');
            $table->integer('place');

            $table->unique(['competition', 'team', 'event']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stats_placings');
        Schema::dropIfExists('stats_times');
        Schema::dropIfExists('stats_serc');
    }
};
