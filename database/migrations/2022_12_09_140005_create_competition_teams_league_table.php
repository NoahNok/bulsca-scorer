<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * 
     * Links a competition team to one or more leagues
     * 
     *
     * @return void
     */
    public function up()
    {
        Schema::create('competition_teams_league', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_team')->references('id')->on('competition_teams')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreignId('league')->references('id')->on('leagues')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('competition_teams_league');
    }
};
