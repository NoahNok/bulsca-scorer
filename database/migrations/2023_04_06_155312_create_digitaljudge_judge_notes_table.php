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
        Schema::create('digitaljudge_judge_notes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('judge')->references('id')->on('serc_judges')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreignId('team')->references('id')->on('competition_teams')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->text('note');


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
        Schema::dropIfExists('digitaljudge_judge_notes');
    }
};
