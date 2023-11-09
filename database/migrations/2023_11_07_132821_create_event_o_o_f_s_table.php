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
        Schema::create('event_oofs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('event')->references('id')->on('competition_speed_events')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreignId('heat_lane')->references('id')->on('heats')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->integer('oof')->default(-1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_oofs');
    }
};
