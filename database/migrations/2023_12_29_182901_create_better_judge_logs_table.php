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
        Schema::create('better_judge_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competition')->constrained('competitions')->cascade();
            $table->foreignId('team')->nullable()->constrained('competition_teams')->cascade();
            $table->json('loggable_data')->nullable();
            $table->string('loggable_type', 255)->nullable();
            $table->morphs('associated');
            $table->enum('action', ['created', 'updated', 'deleted', 'other']);
            $table->string('judge_name', 255)->nullable();
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
        Schema::dropIfExists('better_judge_logs');
    }
};
