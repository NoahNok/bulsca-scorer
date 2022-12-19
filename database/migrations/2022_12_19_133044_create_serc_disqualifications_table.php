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
        Schema::create('serc_disqualifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serc')->references('id')->on('sercs')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreignId('team')->references('id')->on('competition_teams')->onUpdate('CASCADE')->onDelete('CASCADE');
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
        Schema::dropIfExists('serc_disqualifications');
    }
};
