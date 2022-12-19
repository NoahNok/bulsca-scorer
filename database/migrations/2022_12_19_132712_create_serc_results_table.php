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
        Schema::create('serc_results', function (Blueprint $table) {
            $table->id();
            $table->decimal('result');
            $table->foreignId('marking_point')->references('id')->on('serc_marking_points')->onUpdate('CASCADE')->onDelete('CASCADE');
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
        Schema::dropIfExists('serc_results');
    }
};
