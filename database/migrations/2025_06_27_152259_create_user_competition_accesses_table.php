<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_competition_accesses', function (Blueprint $table) {
            $table->foreignId('user')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreignId('competition')->references('id')->on('competitions')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->string('access_to'); // e.g., 'view', 'teams', 'heats_and_draws', etc...
            $table->unique(['user', 'competition', 'access_to'], 'user_competition_accesses_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_competition_accesses');
    }
};
