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
        Schema::create('serc_marking_points', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->decimal('weight');
            $table->foreignId('judge')->references('id')->on('serc_judges')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreignId('serc')->references('id')->on('sercs')->onUpdate('CASCADE')->onDelete('CASCADE');
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
        Schema::dropIfExists('serc_marking_points');
    }
};
