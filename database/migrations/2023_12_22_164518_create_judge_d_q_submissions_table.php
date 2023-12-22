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
        Schema::create('judge_dq_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition')->constrained('competitions');
            $table->morphs('event');
            $table->foreignId('heat_lane')->constrained('heats');
            $table->integer('turn');
            $table->integer('length');
            $table->char('code', 5);
            $table->text('details')->nullable();
            $table->string('name');
            $table->string('position');
            $table->string('seconder_name');
            $table->string('seconder_position');
            $table->boolean('resolved')->nullable()->default(null); // null - open, true - accepted, false - rejected
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
        Schema::dropIfExists('judge_dq_submissions');
    }
};
