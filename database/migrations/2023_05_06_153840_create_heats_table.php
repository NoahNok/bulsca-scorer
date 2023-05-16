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
        Schema::create('heats', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('event')->references('id')->on('competition_speed_events')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreignId('team')->references('id')->on('competition_teams')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->integer('heat');
            $table->integer('lane');
            $table->unique('event', 'team');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('heats');
    }
};
