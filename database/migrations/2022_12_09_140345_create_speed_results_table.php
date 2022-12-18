<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('speed_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition_team')->references('id')->on('competition_teams')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreignId('event')->references('id')->on('competition_speed_events')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->text('result')->nullable(); // times or for rope throw it will either be a time or a number
            $table->text('disqualification')->nullable();
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
        Schema::dropIfExists('speed_results');
    }
};
