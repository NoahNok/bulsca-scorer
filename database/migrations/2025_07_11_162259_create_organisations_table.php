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
        Schema::create('organisations', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        Schema::create('organisation_user_accesses', function (Blueprint $table) {
            $table->foreignId('organisation')->references('id')->on('organisations')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreignId('user')->references('id')->on('users')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->string('access_to'); // e.g., 'view', 'teams', 'heats_and_draws', etc...
            $table->unique(['organisation', 'user', 'access_to'], 'organisation_user_accesses_unique');
            $table->timestamps();
        });

        Schema::table('competitions', function (Blueprint $table) {
            $table->foreignId('organisation')->nullable()->constrained()->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('organisation');
        });
        Schema::dropIfExists('organisation_user_accesses');
        Schema::dropIfExists('organisations');
    }
};
