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
        Schema::create('result_schemas', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->foreignId('competition')->references('id')->on('competitions')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->text('league');
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
        Schema::dropIfExists('result_schemas');
    }
};
